<?php

namespace App\Services;

use KHQR\BakongKHQR;
use KHQR\Models\IndividualInfo;
use KHQR\Helpers\KHQRData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Bakong Payment API Service
 * 
 * This service handles all interactions with the Bakong KHQR payment system:
 * - Generates QR codes for payments (Individual & Merchant)
 * - Tracks payment status in real-time using MD5 hashes
 * - Manages payment verification through official Bakong API
 * - Provides simulation tools for development/testing
 * 
 * @author Motion Cycle E-commerce Team
 * @version 1.0 Production Ready
 */
class BakongApiService
{
    /**
     * Generate KHQR Individual Payment Code
     * 
     * Creates a QR code for individual payments that customers can scan with their banking apps.
     * 
     * @param string $bakongAccount  The merchant's Bakong account ID (e.g., "merchant@aclb")
     * @param string $accountName    Display name for the merchant (e.g., "MOTION CYCLE")  
     * @param float  $amount         Payment amount (e.g., 25.50)
     * @param string $currency       Currency code: "USD" or "KHR" (default: "USD")
     * @param bool   $trackPayment   Enable real-time payment tracking (default: false)
     * 
     * @return array Response with QR string, MD5 hash for tracking, and payment details
     * 
     * @example
     * $result = BakongApiService::generateIndividual(
     *     'vuth_menghuor@aclb',
     *     'MOTION CYCLE', 
     *     25.50,
     *     'USD',
     *     true
     * );
     */
    public static function generateIndividual(
        string $bakongAccount,
        string $accountName,
        float $amount,
        string $currency = 'USD',
        bool $trackPayment = false
    ): array {
        try {
            // Step 1: Convert currency to KHQR format
            $currencyCode = ($currency === 'USD') ? KHQRData::CURRENCY_USD : KHQRData::CURRENCY_KHR;
            
            // Step 2: Create payment information for KHQR generation
            $individualInfo = new IndividualInfo(
                $bakongAccount,         // Merchant's Bakong account (e.g., "merchant@aclb")
                $accountName,           // Display name shown to customer
                'PHNOM PENH',          // Merchant city (required by Bakong)
                null,                  // Acquiring bank (optional)
                null,                  // Additional account info (optional)
                $currencyCode,         // Currency: USD or KHR
                $amount                // Payment amount
            );

            // Log the payment generation for debugging
            Log::info('🏦 Generating KHQR Payment', [
                'bakong_account' => $bakongAccount,
                'account_name' => $accountName,
                'amount' => $amount,
                'currency' => $currency,
                'tracking_enabled' => $trackPayment
            ]);

            $qrString = '';
            $md5Hash = null;

            // Generate QR with Bakong API if tracking enabled
            if ($trackPayment && env('BAKONG_API_TOKEN')) {
                try {
                    $bakong = new BakongKHQR(env('BAKONG_API_TOKEN'));
                    
                    // Use false for production to generate real trackable QR codes
                    $testMode = env('BAKONG_TEST_MODE', true);
                    Log::info('Generating KHQR with API', [
                        'test_mode' => $testMode,
                        'amount' => $amount,
                        'currency' => $currency,
                        'bakong_account' => $bakongAccount
                    ]);
                    
                    $apiResponse = $bakong->generateIndividual($individualInfo, $testMode);
                    
                    Log::info('Raw Bakong API Response', [
                        'response_type' => gettype($apiResponse),
                        'response_preview' => is_string($apiResponse) ? substr($apiResponse, 0, 200) : null,
                        'response_full' => $apiResponse
                    ]);
                    
                    // Extract QR and MD5 from API response with enhanced parsing
                    if (is_object($apiResponse)) {
                        if (isset($apiResponse->data)) {
                            if (is_array($apiResponse->data)) {
                                $qrString = $apiResponse->data['qr'] ?? $apiResponse->data['qrCode'] ?? '';
                                $md5Hash = $apiResponse->data['md5'] ?? $apiResponse->data['hash'] ?? '';
                            } elseif (is_object($apiResponse->data)) {
                                $qrString = $apiResponse->data->qr ?? $apiResponse->data->qrCode ?? '';
                                $md5Hash = $apiResponse->data->md5 ?? $apiResponse->data->hash ?? '';
                            }
                        } elseif (isset($apiResponse->qr) || isset($apiResponse->qrCode)) {
                            // Sometimes response is direct
                            $qrString = $apiResponse->qr ?? $apiResponse->qrCode ?? '';
                            $md5Hash = $apiResponse->md5 ?? $apiResponse->hash ?? '';
                        }
                    } elseif (is_array($apiResponse)) {
                        if (isset($apiResponse['data'])) {
                            $qrString = $apiResponse['data']['qr'] ?? $apiResponse['data']['qrCode'] ?? '';
                            $md5Hash = $apiResponse['data']['md5'] ?? $apiResponse['data']['hash'] ?? '';
                        } else {
                            $qrString = $apiResponse['qr'] ?? $apiResponse['qrCode'] ?? '';
                            $md5Hash = $apiResponse['md5'] ?? $apiResponse['hash'] ?? '';
                        }
                    }
                    
                    Log::info('Extracted from Bakong API', [
                        'qr_string_length' => strlen($qrString),
                        'md5_hash' => $md5Hash,
                        'md5_length' => strlen($md5Hash ?? ''),
                        'test_mode' => $testMode
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error('Bakong API generation failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    Log::warning('Falling back to local generation');
                }
            }

            // Fallback to local generation
            if (empty($qrString)) {
                Log::info('Using local KHQR generation');
                $khqrResponse = BakongKHQR::generateIndividual($individualInfo);
                
                Log::info('Local KHQR Response', [
                    'response_type' => gettype($khqrResponse),
                    'response_preview' => is_string($khqrResponse) ? substr($khqrResponse, 0, 100) : json_encode($khqrResponse, JSON_PARTIAL_OUTPUT_ON_ERROR)
                ]);
                
                // Extract QR string from KHQRResponse object
                if (is_object($khqrResponse) && property_exists($khqrResponse, 'data')) {
                    // KHQRResponse object with data property (array)
                    if (is_array($khqrResponse->data) && isset($khqrResponse->data['qr'])) {
                        $qrString = $khqrResponse->data['qr'];
                        if (isset($khqrResponse->data['md5'])) {
                            $md5Hash = $khqrResponse->data['md5'];
                        }
                    }
                } elseif (is_array($khqrResponse) && isset($khqrResponse['data']['qr'])) {
                    // Array response format
                    $qrString = $khqrResponse['data']['qr'];
                    if (isset($khqrResponse['data']['md5'])) {
                        $md5Hash = $khqrResponse['data']['md5'];
                    }
                } else {
                    // Fallback: assume response is the QR string directly
                    $qrString = (string)$khqrResponse;
                }
                
                // Validate QR contains amount by decoding it
                if ($qrString) {
                    try {
                        $decoded = BakongKHQR::decode($qrString);
                        $decodedAmount = 'not found';
                        $decodedCurrency = 'not found';
                        
                        // Handle KHQRResponse object
                        if (is_object($decoded) && property_exists($decoded, 'data') && is_array($decoded->data)) {
                            // Amount might be in accountInformation for individual payments
                            $decodedAmount = $decoded->data['accountInformation'] ?? $decoded->data['transactionAmount'] ?? 'not found';
                            $decodedCurrency = $decoded->data['transactionCurrency'] ?? 'not found';
                        }
                        
                        Log::info('QR Decode Test', [
                            'decoded_amount' => $decodedAmount,
                            'expected_amount' => $amount,
                            'decoded_currency' => $decodedCurrency,
                            'expected_currency' => $currency,
                            'amount_match' => (string)$decodedAmount === (string)$amount
                        ]);
                    } catch (\Exception $e) {
                        Log::warning('Could not decode generated QR for validation', ['error' => $e->getMessage()]);
                    }
                }
                
                // Generate MD5 from QR string
                if (!$md5Hash && $qrString) {
                    $md5Hash = hash('md5', $qrString);
                }
            }

            if (empty($qrString)) {
                throw new \Exception('Failed to generate QR string');
            }
            
            $result = [
                'success' => true,
                'qr_string' => $qrString,
                'bakong_account' => $bakongAccount,
                'account_name' => $accountName,
                'amount' => $amount,
                'currency' => $currency,
                'currency_symbol' => $currency === 'KHR' ? '៛' : '$'
            ];

            // Add MD5 for tracking
            if ($trackPayment && $md5Hash) {
                $result['md5'] = $md5Hash;
                $result['tracking_enabled'] = true;
                
                // Store details for verification
                Cache::put("qr_details_{$md5Hash}", [
                    'qr_string' => $qrString,
                    'amount' => $amount,
                    'currency' => $currency,
                    'bakong_account' => $bakongAccount,
                    'account_name' => $accountName,
                    'created_at' => now()->toISOString()
                ], now()->addHours(2));
            }

            return $result;
            
        } catch (\Exception $e) {
            Log::error('KHQR generation failed', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Failed to generate KHQR: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check Payment Status (Real-time Detection)
     * 
     * Verifies if a payment has been completed by checking the Bakong API using MD5 hash.
     * This method connects to the official Bakong API to detect real payments made through
     * banking apps like ACLEDA, ABA, etc.
     * 
     * @param string $md5 The MD5 hash generated when creating the QR code
     * 
     * @return array Payment status response with one of these statuses:
     *               - 'completed': Payment found and verified
     *               - 'pending': Payment not yet completed  
     *               - 'error': API error or configuration issue
     * 
     * @example
     * $status = BakongApiService::checkPaymentStatus('abc123def456...');
     * if ($status['payment_status'] === 'completed') {
     *     // Payment successful - process order
     * }
     */
    public static function checkPaymentStatus(string $md5): array
    {
        try {
            if (!env('BAKONG_API_TOKEN')) {
                Log::warning('No Bakong API token configured');
                return [
                    'success' => true,
                    'payment_status' => 'pending',
                    'message' => 'No API token configured'
                ];
            }

            // Check simulated payments in development first
            if (app()->environment('local')) {
                $simulatedPayment = Cache::get("payment_completed_{$md5}");
                if ($simulatedPayment) {
                    Log::info('Using simulated payment', ['md5' => $md5]);
                    return [
                        'success' => true,
                        'payment_status' => 'completed',
                        'transaction_data' => $simulatedPayment,
                        'source' => 'simulation'
                    ];
                }
            }

            // Initialize Bakong API
            $bakong = new BakongKHQR(env('BAKONG_API_TOKEN'));
            
            Log::info('🔍 Checking payment status with Bakong API', [
                'md5' => $md5,
                'md5_length' => strlen($md5),
                'test_mode' => env('BAKONG_TEST_MODE', true),
                'api_token_present' => !empty(env('BAKONG_API_TOKEN'))
            ]);
            
            // Call Bakong API to check transaction
            // Note: Use false for production mode to check real payments
            $testMode = env('BAKONG_TEST_MODE', true);
            
            try {
                $response = $bakong->checkTransactionByMD5($md5, $testMode);
                
                Log::info('📡 Bakong API Response', [
                    'md5' => $md5,
                    'test_mode' => $testMode,
                    'response_type' => gettype($response),
                    'response_preview' => is_string($response) ? substr($response, 0, 500) : null,
                    'response_full' => $response
                ]);
            } catch (\Exception $e) {
                Log::error('❌ Bakong API Error', [
                    'md5' => $md5,
                    'test_mode' => $testMode,
                    'error' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                
                // Handle specific error cases
                if (strpos($e->getMessage(), '403') !== false || strpos($e->getMessage(), 'CloudFront') !== false) {
                    return [
                        'success' => false,
                        'payment_status' => 'error',
                        'error' => 'API Access Denied - This usually means you are in test mode but trying to check real payments, or your API token is invalid/expired.',
                        'suggestions' => [
                            'Set BAKONG_TEST_MODE=false in .env file to check real payments',
                            'Verify your BAKONG_API_TOKEN is valid and not expired',
                            'Make sure you generated the QR in the same mode you are checking it',
                            'Contact Bakong API support if the issue persists'
                        ],
                        'technical_error' => $e->getMessage()
                    ];
                }
                
                return [
                    'success' => false,
                    'payment_status' => 'error',
                    'error' => 'API error: ' . $e->getMessage(),
                    'suggestions' => [
                        'Check your internet connection',
                        'Verify API configuration',
                        'Try again in a few minutes'
                    ]
                ];
            }
            
            // Enhanced response parsing
            $paymentFound = false;
            $transactionData = null;
            
            // Handle different response formats
            if (is_object($response)) {
                Log::info('Processing object response', ['properties' => array_keys(get_object_vars($response))]);
                
                // Check for success status with data
                if (isset($response->status)) {
                    $statusCode = is_object($response->status) ? 
                        ($response->status->code ?? null) : 
                        ($response->status['code'] ?? $response->status ?? null);
                    
                    Log::info('Status code found', ['status_code' => $statusCode]);
                    
                    // Status code 0 typically means success in Bakong API
                    if ($statusCode === 0 || $statusCode === '0') {
                        if (isset($response->data) && !empty($response->data)) {
                            $paymentFound = true;
                            $transactionData = $response->data;
                            Log::info('✅ Payment found with status code 0');
                        }
                    }
                } elseif (isset($response->data) && !empty($response->data)) {
                    // Sometimes response might not have status but has data
                    $paymentFound = true;
                    $transactionData = $response->data;
                    Log::info('✅ Payment found with data but no status');
                } elseif (isset($response->success) && $response->success) {
                    // Alternative success format
                    $paymentFound = true;
                    $transactionData = $response;
                    Log::info('✅ Payment found with success flag');
                }
            } elseif (is_array($response)) {
                Log::info('Processing array response', ['keys' => array_keys($response)]);
                
                if (isset($response['status'])) {
                    $statusCode = $response['status']['code'] ?? $response['status'] ?? null;
                    
                    Log::info('Array status code found', ['status_code' => $statusCode]);
                    
                    if ($statusCode === 0 || $statusCode === '0') {
                        if (isset($response['data']) && !empty($response['data'])) {
                            $paymentFound = true;
                            $transactionData = $response['data'];
                            Log::info('✅ Payment found in array format');
                        }
                    }
                } elseif (isset($response['data']) && !empty($response['data'])) {
                    $paymentFound = true;
                    $transactionData = $response['data'];
                    Log::info('✅ Payment found in array with data');
                } elseif (isset($response['success']) && $response['success']) {
                    $paymentFound = true;
                    $transactionData = $response;
                    Log::info('✅ Payment found in array with success');
                }
            } elseif (is_string($response)) {
                // Try to decode JSON string response
                $decoded = json_decode($response, true);
                if ($decoded && is_array($decoded)) {
                    Log::info('Decoded JSON string response', ['decoded' => $decoded]);
                    return self::checkPaymentStatus($md5); // Recursive call with decoded data
                }
            }
            
            if ($paymentFound) {
                Log::info('🎉 Payment confirmed!', [
                    'md5' => $md5,
                    'transaction_data_type' => gettype($transactionData),
                    'has_transaction_data' => !empty($transactionData)
                ]);
                
                return [
                    'success' => true,
                    'payment_status' => 'completed',
                    'transaction_data' => $transactionData,
                    'source' => 'bakong_api',
                    'detected_at' => now()->toISOString()
                ];
            }
            
            Log::info('⏳ Payment not found yet', ['md5' => $md5]);
            return [
                'success' => true,
                'payment_status' => 'pending',
                'message' => 'Payment not completed yet',
                'checked_at' => now()->toISOString()
            ];
            
        } catch (\Exception $e) {
            Log::error('💥 Payment check failed', [
                'md5' => $md5,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => true,
                'payment_status' => 'pending',
                'message' => 'API error: ' . $e->getMessage(),
                'error_at' => now()->toISOString()
            ];
        }
    }

    /**
     * Simulate payment for testing
     */
    public static function simulatePayment(string $md5): array
    {
        if (!app()->environment('local')) {
            return ['success' => false, 'message' => 'Only available in development'];
        }

        Cache::put("payment_completed_{$md5}", [
            'status' => 'completed',
            'completed_at' => now()->toISOString(),
            'simulated' => true,
            'transaction_id' => 'SIM_' . time()
        ], now()->addMinutes(10));

        Log::info('Payment simulated', ['md5' => $md5]);
        return ['success' => true, 'message' => 'Payment simulated'];
    }

    /**
     * Check Bakong API configuration and provide recommendations
     */
    public static function checkConfiguration(): array
    {
        $issues = [];
        $recommendations = [];
        
        // Check API token
        $token = env('BAKONG_API_TOKEN');
        if (empty($token)) {
            $issues[] = 'BAKONG_API_TOKEN is not set';
            $recommendations[] = 'Add your Bakong API token to .env file';
        } elseif (strlen($token) < 50) {
            $issues[] = 'BAKONG_API_TOKEN appears to be invalid (too short)';
            $recommendations[] = 'Verify your Bakong API token is correct';
        }
        
        // Check test mode configuration
        $testMode = env('BAKONG_TEST_MODE', true);
        $useMock = env('BAKONG_USE_MOCK', true);
        
        if ($testMode && $useMock) {
            $recommendations[] = 'You are in full test mode - QR codes will not work with real banking apps';
            $recommendations[] = 'To enable real payments: Set BAKONG_TEST_MODE=false and BAKONG_USE_MOCK=false';
        } elseif ($testMode && !$useMock) {
            $recommendations[] = 'Mixed mode detected - may cause issues';
            $recommendations[] = 'For real payments, set both BAKONG_TEST_MODE=false and BAKONG_USE_MOCK=false';
        } elseif (!$testMode && $useMock) {
            $recommendations[] = 'Production mode with mock enabled - inconsistent configuration';
            $recommendations[] = 'Set BAKONG_USE_MOCK=false for production mode';
        }
        
        // Check app ID
        $appId = env('BAKONG_APP_ID');
        if (empty($appId)) {
            $issues[] = 'BAKONG_APP_ID is not set';
            $recommendations[] = 'Add your Bakong App ID to .env file';
        }
        
        return [
            'configuration_valid' => empty($issues),
            'issues' => $issues,
            'recommendations' => $recommendations,
            'current_settings' => [
                'test_mode' => $testMode,
                'use_mock' => $useMock,
                'has_token' => !empty($token),
                'has_app_id' => !empty($appId),
                'api_base_url' => env('BAKONG_BASE_URL', 'https://api-bakong.nbc.org.kh')
            ]
        ];
    }
}