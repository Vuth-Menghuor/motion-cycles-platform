<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfo;
use KHQR\Models\MerchantInfo;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\BakongApiService;

/**
 * KHQR Payment Controller
 * 
 * Handles API endpoints for Bakong KHQR payment system:
 * - Generate QR codes for payments
 * - Check payment status
 * - Decode and verify QR codes
 * 
 * All endpoints return JSON responses for the frontend.
 */
class KHQRController extends Controller
{
    /**
     * Generate KHQR for individual payment
     */
    public function generateIndividual(Request $request)
    {
        try {
            $validated = $request->validate([
                'bakong_account' => 'required|string',
                'account_name' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'currency' => 'nullable|string|in:KHR,USD',
            ]);

            // Set currency based on user input with proper default handling
            $currency = strtoupper($validated['currency'] ?? 'USD');
            $currencyCode = ($currency === 'USD') ? KHQRData::CURRENCY_USD : KHQRData::CURRENCY_KHR;

            $individualInfo = new IndividualInfo(
                $validated['bakong_account'],
                $validated['account_name'],
                'PHNOM PENH',
                $currencyCode,
                $validated['amount']
            );

            $khqrString = BakongKHQR::generateIndividual($individualInfo);

            return response()->json([
                'success' => true,
                'data' => [
                    'qr_string' => $khqrString,
                    'bakong_account' => $validated['bakong_account'],
                    'account_name' => $validated['account_name'],
                    'amount' => $validated['amount'],
                    'currency' => $currency,
                    'currency_symbol' => $currency === 'KHR' ? '៛' : '$'                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate KHQR for merchant payment
     */
    public function generateMerchant(Request $request)
    {
        try {
            $validated = $request->validate([
                'bakong_account' => 'required|string',
                'merchant_name' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'currency' => 'nullable|string|in:KHR,USD',
                'bill_number' => 'nullable|string',
                'store_label' => 'nullable|string',
            ]);

            // Set currency based on user input with proper default handling
            $currency = strtoupper($validated['currency'] ?? 'USD');
            $currencyCode = ($currency === 'USD') ? KHQRData::CURRENCY_USD : KHQRData::CURRENCY_KHR;

            $merchantInfo = new MerchantInfo(
                $validated['bakong_account'],
                $validated['merchant_name'],
                'PHNOM PENH',
                $currencyCode,
                $validated['amount'],
                $validated['bill_number'] ?? null,
                null, // Terminal ID
                $validated['store_label'] ?? null
            );

            $khqrString = BakongKHQR::generateMerchant($merchantInfo);

            return response()->json([
                'success' => true,
                'data' => [
                    'qr_string' => $khqrString,
                    'bakong_account' => $validated['bakong_account'],
                    'merchant_name' => $validated['merchant_name'],
                    'amount' => $validated['amount'],
                    'currency' => $currency,
                    'currency_symbol' => $currency === 'KHR' ? '៛' : '$',
                    'bill_number' => $validated['bill_number'] ?? null,
                    'store_label' => $validated['store_label'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Decode KHQR string
     */
    public function decode(Request $request)
    {
        try {
            $validated = $request->validate([
                'qr_string' => 'required|string'
            ]);

            $decoded = BakongKHQR::decode($validated['qr_string']);

            return response()->json([
                'success' => true,
                'data' => $decoded
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Verify KHQR string
     */
    public function verify(Request $request)
    {
        try {
            $validated = $request->validate([
                'qr_string' => 'required|string'
            ]);

            $isValid = BakongKHQR::verify($validated['qr_string']);

            return response()->json([
                'success' => true,
                'is_valid' => $isValid,
                'message' => $isValid ? 'QR code is valid' : 'QR code is invalid'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Generate KHQR for Frontend Checkout (Main Endpoint)
     * 
     * This is the primary endpoint used by the frontend checkout process.
     * It generates a QR code that customers can scan with their banking apps
     * and optionally enables real-time payment tracking.
     * 
     * @param Request $request Contains payment details:
     *                        - bakong_account: Merchant's Bakong ID
     *                        - account_name: Display name for merchant
     *                        - amount: Payment amount
     *                        - currency: "USD" or "KHR" 
     *                        - track_payment: Enable real-time detection
     * 
     * @return JsonResponse QR code string and tracking information
     */
    public function generateIndividualWithImage(Request $request)
    {
        try {
            $validated = $request->validate([
                'bakong_account' => 'required|string',
                'account_name' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'currency' => 'nullable|string|in:KHR,USD',
                'include_image' => 'nullable|boolean',
                'qr_size' => 'nullable|integer|min:50|max:1000',
                'qr_format' => 'nullable|string|in:png,svg',
                'qr_margin' => 'nullable|integer|min:0|max:50',
                'track_payment' => 'nullable|boolean',
            ]);

            $currency = $validated['currency'] ?? 'USD';
            $trackPayment = $validated['track_payment'] ?? false;

            // Use the BakongApiService with optional payment tracking
            $result = BakongApiService::generateIndividual(
                $validated['bakong_account'],
                $validated['account_name'],
                $validated['amount'],
                $currency,
                $trackPayment
            );

            if (!$result['success']) {
                return response()->json($result, 400);
            }

            $response = [
                'success' => true,
                'data' => [
                    'qr_string' => $result['qr_string'],
                    'bakong_account' => $result['bakong_account'],
                    'account_name' => $result['account_name'],
                    'amount' => $result['amount'],
                    'currency' => $result['currency'],
                    'currency_symbol' => $result['currency_symbol']
                ]
            ];

            // Add tracking info if available
            if (isset($result['md5'])) {
                $response['data']['md5'] = $result['md5'];
                $response['data']['tracking_enabled'] = $result['tracking_enabled'] ?? true;
            }

            // Generate QR image if requested
            if ($validated['include_image'] ?? false) {
                $qrSize = $validated['qr_size'] ?? 300;
                $qrFormat = $validated['qr_format'] ?? 'svg';
                $qrMargin = $validated['qr_margin'] ?? 10;

                $qrImage = QrCode::format($qrFormat)
                    ->size($qrSize)
                    ->margin($qrMargin)
                    ->errorCorrection('M')
                    ->generate($result['qr_string']);

                // Convert to base64 data URI for easy display
                $base64 = base64_encode($qrImage);
                $dataUri = "data:image/{$qrFormat}+xml;base64," . $base64;
                
                $response['data']['qr_image'] = [
                    'format' => $qrFormat,
                    'size' => $qrSize,
                    'margin' => $qrMargin,
                    'base64' => $base64,
                    'data_uri' => $dataUri,
                    'svg_content' => $qrImage
                ];
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate QR Image from KHQR string
     */
    public function generateQrImage(Request $request)
    {
        try {
            $validated = $request->validate([
                'qr_string' => 'required|string',
                'size' => 'nullable|integer|min:50|max:1000',
                'format' => 'nullable|string|in:png,svg',
                'margin' => 'nullable|integer|min:0|max:50',
            ]);

            $size = $validated['size'] ?? 300;
            $format = $validated['format'] ?? 'png';
            $margin = $validated['margin'] ?? 10;

            $qrImage = QrCode::format($format)
                ->size($size)
                ->margin($margin)
                ->errorCorrection('M')
                ->generate($validated['qr_string']);

            if ($format === 'png') {
                return response($qrImage, 200)
                    ->header('Content-Type', 'image/png')
                    ->header('Content-Disposition', 'inline; filename="qr-code.png"');
            } else {
                return response($qrImage, 200)
                    ->header('Content-Type', 'image/svg+xml')
                    ->header('Content-Disposition', 'inline; filename="qr-code.svg"');
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Check payment status by MD5 hash
     */
    public function checkPaymentStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'md5' => 'required|string'
            ]);

            $result = BakongApiService::checkPaymentStatus($validated['md5']);
            
            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simulate payment completion for testing (development only)
     */
    public function simulatePayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'md5' => 'required|string'
            ]);

            $result = BakongApiService::simulatePayment($validated['md5']);
            
            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


}