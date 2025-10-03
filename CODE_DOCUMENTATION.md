# 📚 Code Documentation - Bakong Payment Integration

## 🎯 Overview

This documentation explains how the Bakong KHQR payment system works in your e-commerce project. The code is now clean, well-commented, and production-ready.

## 🏗️ Architecture

```
Frontend (Vue.js)                Backend (Laravel)                Bakong API
┌─────────────────┐             ┌─────────────────┐             ┌─────────────────┐
│  Checkout Page  │────────────▶│ KHQRController  │────────────▶│ Official Bakong │
│                 │             │                 │             │   Government    │
│ - Payment Form  │             │ - Generate QR   │             │      API        │
│ - QR Display    │◀────────────│ - Check Status  │◀────────────│                 │
│ - Auto Detection│             │                 │             │ - QR Generation │
└─────────────────┘             └─────────────────┘             │ - Payment Check │
                                         │                       └─────────────────┘
                                         ▼
                                ┌─────────────────┐
                                │ BakongApiService│
                                │                 │
                                │ - API Logic     │
                                │ - Error Handle  │
                                │ - MD5 Tracking  │
                                └─────────────────┘
```

## 📁 Key Files Explained

### Backend Files

#### 1. `BakongApiService.php` - Core Payment Logic

**Purpose**: Handles all Bakong API interactions
**Key Methods**:

- `generateIndividual()` - Creates QR codes for payments
- `checkPaymentStatus()` - Verifies if payments completed
- `checkConfiguration()` - Validates API setup

**How it works**:

```php
// Generate QR Code
$result = BakongApiService::generateIndividual(
    'vuth_menghuor@aclb',    // Your Bakong account
    'MOTION CYCLE',          // Display name
    25.50,                   // Amount
    'USD',                   // Currency
    true                     // Enable tracking
);

// Check Payment
$status = BakongApiService::checkPaymentStatus($md5Hash);
// Returns: 'completed', 'pending', or 'error'
```

#### 2. `KHQRController.php` - API Endpoints

**Purpose**: Provides REST API endpoints for the frontend
**Main Endpoint**: `/api/khqr/individual-with-image`

- Validates request data
- Calls BakongApiService
- Returns JSON response to frontend

### Frontend Files

#### 1. `checkout_purchase.vue` - Main Checkout Page

**Purpose**: Final checkout step with Bakong payment
**Key Features**:

- Form validation
- QR code generation and display
- Automatic payment detection
- Real-time status updates

**Payment Flow**:

```javascript
// 1. Generate QR Code
const khqrResponse = await generateKHQRIndividualWithImage({
  bakong_account: "vuth_menghuor@aclb",
  account_name: "MOTION CYCLE",
  amount: totalAmount.value,
  currency: "USD",
  track_payment: true,
});

// 2. Display QR to customer
khqrQrImageUrl.value = generateQRImageUrl(qrString);

// 3. Start automatic detection
startPaymentPolling(khqrResponse.data.md5);

// 4. Check every 30 seconds until payment found
const result = await checkPaymentStatus(md5);
if (result.payment_status === "completed") {
  // Payment successful - process order
}
```

#### 2. `services/khqr.js` - API Communication

**Purpose**: Handles HTTP requests to backend
**Key Functions**:

- `generateKHQRIndividualWithImage()` - Create QR codes
- `checkPaymentStatus()` - Check payment completion
- `formatCurrency()` - Display currency properly

## 🔄 Payment Process Step-by-Step

### Step 1: Customer Initiates Payment

1. Customer fills checkout form
2. Selects "Bakong KHQR" payment method
3. Clicks "Complete Purchase"

### Step 2: QR Code Generation

```javascript
// Frontend sends request
const requestData = {
  bakong_account: 'vuth_menghuor@aclb',
  account_name: 'MOTION CYCLE',
  amount: 25.50,
  currency: 'USD',
  track_payment: true
}

// Backend generates QR code
const qrResult = BakongApiService::generateIndividual(...)

// Returns QR string + MD5 hash for tracking
{
  "success": true,
  "data": {
    "qr_string": "00020101021229220018vuth_menghuor@aclb...",
    "md5": "abc123def456789...",
    "amount": 25.50,
    "currency": "USD"
  }
}
```

### Step 3: Customer Scans & Pays

1. QR code displayed to customer
2. Customer opens banking app (ACLEDA, ABA, etc.)
3. Scans QR code
4. Completes payment in banking app

### Step 4: Automatic Payment Detection

```javascript
// Frontend starts checking every 30 seconds
const checkPayment = async () => {
  const result = await checkPaymentStatus(md5Hash);

  if (result.payment_status === "completed") {
    // ✅ Payment detected!
    // Process order, clear cart, show success
    processSuccessfulPayment();
  } else {
    // ⏳ Still waiting, check again in 30 seconds
    setTimeout(checkPayment, 30000);
  }
};
```

### Step 5: Order Completion

1. Payment detected automatically
2. Order data saved to localStorage
3. Cart cleared
4. Customer redirected to success page

## 🔧 Configuration

### Environment Variables (.env)

```bash
# Bakong API Configuration (Production)
BAKONG_USE_MOCK=false                    # Use real API
BAKONG_TEST_MODE=false                   # Production mode
BAKONG_APP_ID=vuth_menghuor@aclb        # Your Bakong account
BAKONG_BASE_URL=https://api-bakong.nbc.gov.kh  # Official API URL
BAKONG_API_TOKEN=your_jwt_token_here     # Your API token
```

## 🛠️ Development vs Production

### Development Mode

- `BAKONG_TEST_MODE=true` - Generates test QR codes
- `simulatePayment()` - Manual payment simulation
- QR codes don't work with real banking apps

### Production Mode (Current)

- `BAKONG_TEST_MODE=false` - Real QR codes
- Connects to official Bakong API
- QR codes work with all banking apps
- Automatic payment detection

## 🐛 Debugging

### Debug Tools Available

1. **Debug Page**: `http://localhost:5173/debug/bakong`

   - Test API connection
   - Generate test QR codes
   - Check payment status manually

2. **API Endpoints**:
   - `GET /api/bakong-debug/test-connection`
   - `POST /api/bakong-debug/generate-test-qr`
   - `POST /api/bakong-debug/test-payment-check`

### Common Issues & Solutions

#### "403 CloudFront Error"

- **Cause**: Wrong API URL or invalid token
- **Solution**: Check `.env` has correct `BAKONG_BASE_URL`

#### "Payment Not Detected"

- **Cause**: Test mode enabled or API token expired
- **Solution**: Ensure `BAKONG_TEST_MODE=false`

#### "QR Code Not Working"

- **Cause**: Mock mode enabled
- **Solution**: Set `BAKONG_USE_MOCK=false`

## 📊 Code Quality Features

### Error Handling

```php
// Backend - Comprehensive error handling
try {
    $response = $bakong->checkTransactionByMD5($md5, $testMode);
} catch (\Exception $e) {
    if (strpos($e->getMessage(), '403') !== false) {
        return [
            'success' => false,
            'error' => 'API Access Denied - Check configuration'
        ];
    }
}
```

### Logging

```php
// All important actions are logged
Log::info('🏦 Generating KHQR Payment', [
    'amount' => $amount,
    'currency' => $currency,
    'tracking_enabled' => $trackPayment
]);
```

### Input Validation

```php
// Laravel validation ensures data integrity
$validated = $request->validate([
    'bakong_account' => 'required|string',
    'amount' => 'required|numeric|min:0',
    'currency' => 'nullable|string|in:KHR,USD',
]);
```

## 🚀 Performance Optimizations

1. **Efficient Polling**: Checks payment every 30 seconds (not constantly)
2. **Caching**: Payment details cached for quick verification
3. **Timeout Handling**: Automatic fallback after 10 minutes
4. **Error Recovery**: Graceful handling of API failures

## 📈 Monitoring & Analytics

### Key Metrics to Track

- QR generation success rate
- Payment detection time
- API response times
- Error rates by type

### Logs to Monitor

- `storage/logs/laravel.log` - All payment activity
- Payment generation requests
- Payment detection attempts
- API errors and responses

---

**🎉 Your Bakong payment system is now fully documented and production-ready!**
