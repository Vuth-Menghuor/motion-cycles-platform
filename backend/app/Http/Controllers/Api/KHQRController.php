<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfo;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\BakongApiService;
use Illuminate\Support\Facades\Log;

class KHQRController extends Controller
{
    /**
     * Generate QR code for individual payments
     */
    public function generateIndividual(Request $request)
    {
        try {
            // Step 1: Validate input data
            $validated = $request->validate([
                'bakong_account' => 'required|string',
                'account_name' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'currency' => 'nullable|string|in:KHR,USD',
            ]);

            // Step 2: Set default currency and get currency code
            $currency = strtoupper($validated['currency'] ?? 'USD');
            $currencyCode = ($currency === 'USD') ? KHQRData::CURRENCY_USD : KHQRData::CURRENCY_KHR;

            // Step 3: Create individual info object
            $individualInfo = new IndividualInfo(
                $validated['bakong_account'],
                $validated['account_name'],
                'PHNOM PENH',
                $currencyCode,
                (float) $validated['amount']
            );

            // Step 4: Generate QR string
            $khqrString = BakongKHQR::generateIndividual($individualInfo);

            // Step 5: Return success response
            return response()->json([
                'success' => true,
                'data' => [
                    'qr_string' => $khqrString,
                    'bakong_account' => $validated['bakong_account'],
                    'account_name' => $validated['account_name'],
                    'amount' => $validated['amount'],
                    'currency' => $currency,
                    'currency_symbol' => $currency === 'KHR' ? '៛' : '$'
                ]
            ]);

        } catch (\Exception $e) {
            // Step 6: Handle errors
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate QR code for merchant payments
     */
    public function generateMerchant(Request $request)
    {
        try {
            // Step 1: Validate input data
            $validated = $request->validate([
                'bakong_account' => 'required|string',
                'merchant_name' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'currency' => 'nullable|string|in:KHR,USD',
                'bill_number' => 'nullable|string',
                'store_label' => 'nullable|string',
            ]);

            // Step 2: Use service to generate QR
            $result = BakongApiService::generateIndividual(
                $validated['bakong_account'],
                $validated['merchant_name'],
                $validated['amount'],
                $validated['currency'] ?? 'USD',
                false
            );

            // Step 3: Check if generation was successful
            if (!$result['success']) {
                return response()->json($result, 400);
            }

            // Step 4: Return success response
            return response()->json([
                'success' => true,
                'data' => [
                    'qr_string' => $result['qr_string'],
                    'bakong_account' => $result['bakong_account'],
                    'merchant_name' => $result['account_name'],
                    'amount' => $result['amount'],
                    'currency' => $result['currency'],
                    'currency_symbol' => $result['currency_symbol'],
                    'bill_number' => $validated['bill_number'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
            // Step 5: Handle errors
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Decode QR string to get payment information
     */
    public function decode(Request $request)
    {
        try {
            // Step 1: Validate input
            $validated = $request->validate([
                'qr_string' => 'required|string'
            ]);

            // Step 2: Decode the QR string
            $decoded = BakongKHQR::decode($validated['qr_string']);

            // Step 3: Return decoded data
            return response()->json([
                'success' => true,
                'data' => $decoded
            ]);

        } catch (\Exception $e) {
            // Step 4: Handle errors
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Verify if QR code is valid
     */
    public function verify(Request $request)
    {
        try {
            // Step 1: Validate input
            $validated = $request->validate([
                'qr_string' => 'required|string'
            ]);

            // Step 2: Verify the QR string
            $isValid = BakongKHQR::verify($validated['qr_string']);

            // Step 3: Return verification result
            return response()->json([
                'success' => true,
                'is_valid' => $isValid,
                'message' => $isValid ? 'QR code is valid' : 'QR code is invalid'
            ]);

        } catch (\Exception $e) {
            // Step 4: Handle errors
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Generate QR code with optional image for individual payments
     */
    public function generateIndividualWithImage(Request $request)
    {
        try {
            // Step 1: Validate input data
            $validated = $request->validate([
                'bakong_account' => 'required|string',
                'account_name' => 'required|string',
                'amount' => 'required|numeric|min:0.01',
                'currency' => 'nullable|string|in:KHR,USD',
                'include_image' => 'nullable|boolean',
                'qr_size' => 'nullable|integer|min:50|max:1000',
                'qr_format' => 'nullable|string|in:png,svg',
                'qr_margin' => 'nullable|integer|min:0|max:50',
                'track_payment' => 'nullable|boolean',
            ]);

            // Step 2: Log the request
            Log::info('KHQR Generation Request', $validated);

            // Step 3: Set defaults
            $currency = $validated['currency'] ?? 'USD';
            $trackPayment = $validated['track_payment'] ?? false;

            // Step 4: Generate QR using service
            $result = BakongApiService::generateIndividual(
                $validated['bakong_account'],
                $validated['account_name'],
                $validated['amount'],
                $currency,
                $trackPayment
            );

            // Step 5: Check if generation failed
            if (!$result['success']) {
                Log::error('KHQR Generation Failed', $result);
                return response()->json($result, 400);
            }

            // Step 6: Prepare response data
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

            // Step 7: Add tracking info if available
            if (isset($result['md5'])) {
                $response['data']['md5'] = $result['md5'];
                $response['data']['tracking_enabled'] = $result['tracking_enabled'] ?? true;
            }

            // Step 8: Generate QR image if requested
            if ($validated['include_image'] ?? false) {
                $qrSize = $validated['qr_size'] ?? 300;
                $qrFormat = $validated['qr_format'] ?? 'svg';
                $qrMargin = $validated['qr_margin'] ?? 10;

                $qrImage = QrCode::format($qrFormat)
                    ->size($qrSize)
                    ->margin($qrMargin)
                    ->errorCorrection('M')
                    ->generate($result['qr_string']);

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

            // Step 9: Return response
            return response()->json($response);

        } catch (\Exception $e) {
            // Step 10: Handle errors
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate QR code image from QR string
     */
    public function generateQrImage(Request $request)
    {
        try {
            // Step 1: Validate input
            $validated = $request->validate([
                'qr_string' => 'required|string',
                'size' => 'nullable|integer|min:50|max:1000',
                'format' => 'nullable|string|in:png,svg',
                'margin' => 'nullable|integer|min:0|max:50',
            ]);

            // Step 2: Set defaults
            $size = $validated['size'] ?? 300;
            $format = $validated['format'] ?? 'png';
            $margin = $validated['margin'] ?? 10;

            // Step 3: Generate QR image
            $qrImage = QrCode::format($format)
                ->size($size)
                ->margin($margin)
                ->errorCorrection('M')
                ->generate($validated['qr_string']);

            // Step 4: Return image response
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
            // Step 5: Handle errors
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Check payment status using MD5 hash
     */
    public function checkPaymentStatus(Request $request)
    {
        try {
            // Step 1: Validate input
            $validated = $request->validate([
                'md5' => 'required|string'
            ]);

            // Step 2: Check payment status via service
            $result = BakongApiService::checkPaymentStatus($validated['md5']);

            // Step 3: Return result
            return response()->json($result);

        } catch (\Exception $e) {
            // Step 4: Handle errors
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}