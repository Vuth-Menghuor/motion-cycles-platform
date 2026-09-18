<?php

namespace App\Services;

use KHQR\BakongKHQR;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BakongApiService
{
    public static function generateIndividual(
        string $bakongAccount,
        string $accountName,
        float $amount,
        string $currency = 'USD',
        bool $trackPayment = false
    ): array {
        try {
            $qrData = self::generateWithOfficialSdk($bakongAccount, $accountName, $amount, $currency);

            return self::buildResult($qrData, $bakongAccount, $accountName, $amount, $currency, $trackPayment);

        } catch (\Exception $e) {
            Log::error('KHQR generation failed', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Failed to generate KHQR: ' . $e->getMessage()
            ];
        }
    }

    private static function generateWithOfficialSdk(string $bakongAccount, string $accountName, float $amount, string $currency): array
    {
        $scriptPath = base_path('scripts/generate-khqr.cjs');
        $process = proc_open(
            ['node', $scriptPath],
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes
        );

        if (!is_resource($process)) {
            throw new \RuntimeException('Unable to start the official KHQR SDK');
        }

        fwrite($pipes[0], json_encode([
            'bakong_account' => $bakongAccount,
            'account_name' => $accountName,
            'amount' => $amount,
            'currency' => $currency,
            'expiration_minutes' => env('KHQR_EXPIRATION_MINUTES', 15),
        ], JSON_THROW_ON_ERROR));
        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        $response = json_decode($stdout, true);
        if ($exitCode !== 0 || !is_array($response) || !($response['success'] ?? false)) {
            $message = $response['message'] ?? trim($stderr) ?: 'Official KHQR SDK failed to generate a QR code';
            throw new \RuntimeException($message);
        }

        return [
            'qr' => $response['qr'],
            'md5' => $response['md5'],
            'expires_at' => $response['expires_at'],
        ];
    }

    private static function buildResult(array $qrData, string $bakongAccount, string $accountName, float $amount, string $currency, bool $trackPayment): array
    {
        $result = [
            'success' => true,
            'qr_string' => $qrData['qr'],
            'bakong_account' => $bakongAccount,
            'account_name' => $accountName,
            'amount' => $amount,
            'currency' => $currency,
            'currency_symbol' => $currency === 'KHR' ? '៛' : '$'
        ];

        if ($trackPayment && isset($qrData['md5'])) {
            $result['md5'] = $qrData['md5'];
            $result['tracking_enabled'] = true;
            Cache::put("qr_details_{$qrData['md5']}", [
                'qr_string' => $qrData['qr'],
                'amount' => $amount,
                'currency' => $currency,
                'bakong_account' => $bakongAccount,
                'account_name' => $accountName,
                'created_at' => now()->toISOString()
            ], now()->addHours(2));
        }

        return $result;
    }

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

            $bakong = new BakongKHQR(env('BAKONG_API_TOKEN'));
            
            try {
                $response = $bakong->checkTransactionByMD5($md5, false); // Always production mode
            } catch (\Exception $e) {
                Log::error('Bakong API Error', [
                    'md5' => $md5,
                    'error' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                
                if (strpos($e->getMessage(), '403') !== false || strpos($e->getMessage(), 'CloudFront') !== false) {
                    return [
                        'success' => false,
                        'payment_status' => 'error',
                        'error' => 'API Access Denied - Your API token may be invalid or expired.',
                        'suggestions' => [
                            'Verify your BAKONG_API_TOKEN is valid and not expired',
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
            
            $paymentFound = false;
            $transactionData = null;
            
            if (is_object($response)) {
                if (isset($response->status)) {
                    $statusCode = is_object($response->status) ? 
                        ($response->status->code ?? null) : 
                        ($response->status['code'] ?? $response->status ?? null);
                    
                    if ($statusCode === 0 || $statusCode === '0') {
                        if (isset($response->data) && !empty($response->data)) {
                            $paymentFound = true;
                            $transactionData = $response->data;
                        }
                    }
                } elseif (isset($response->data) && !empty($response->data)) {
                    $paymentFound = true;
                    $transactionData = $response->data;
                } elseif (isset($response->success) && $response->success) {
                    $paymentFound = true;
                    $transactionData = $response;
                }
            } elseif (is_array($response)) {
                if (isset($response['status'])) {
                    $statusCode = $response['status']['code'] ?? $response['status'] ?? null;
                    
                    if ($statusCode === 0 || $statusCode === '0') {
                        if (isset($response['data']) && !empty($response['data'])) {
                            $paymentFound = true;
                            $transactionData = $response['data'];
                        }
                    }
                } elseif (isset($response['data']) && !empty($response['data'])) {
                    $paymentFound = true;
                    $transactionData = $response['data'];
                } elseif (isset($response['success']) && $response['success']) {
                    $paymentFound = true;
                    $transactionData = $response;
                }
            } elseif (is_string($response)) {
                $decoded = json_decode($response, true);
                if ($decoded && is_array($decoded)) {
                    return self::checkPaymentStatus($md5);
                }
            }
            
            if ($paymentFound) {
                return [
                    'success' => true,
                    'payment_status' => 'completed',
                    'transaction_data' => $transactionData,
                    'source' => 'bakong_api',
                    'detected_at' => now()->toISOString()
                ];
            }
            
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

    public static function checkConfiguration(): array
    {
        $issues = [];
        $recommendations = [];
        
        $token = env('BAKONG_API_TOKEN');
        if (empty($token)) {
            $issues[] = 'BAKONG_API_TOKEN is not set';
            $recommendations[] = 'Add your Bakong API token to .env file';
        } elseif (strlen($token) < 50) {
            $issues[] = 'BAKONG_API_TOKEN appears to be invalid (too short)';
            $recommendations[] = 'Verify your Bakong API token is correct';
        }
        
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
                'has_token' => !empty($token),
                'has_app_id' => !empty($appId),
                'api_base_url' => env('BAKONG_BASE_URL', 'https://api-bakong.nbc.org.kh')
            ]
        ];
    }
}
