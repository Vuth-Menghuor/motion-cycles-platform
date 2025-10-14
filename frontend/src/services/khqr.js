import axios from 'axios'

// API Configuration
const api = axios.create({
  baseURL: 'http://localhost:8100/api', // Backend API base URL
  timeout: 10000, // 10-second timeout for requests
})

/**
 * Bakong KHQR Payment Service
 *
 * This service handles all frontend interactions with the Bakong payment system:
 * - Generate QR codes for payments
 * - Check payment status in real-time
 * - Currency formatting utilities
 * - Payment simulation for development testing
 *
 * @author Motion Cycle E-commerce Team
 * @version 1.0 Production Ready
 */

// ============================================
// CURRENCY UTILITIES
// ============================================

/**
 * Get currency symbol for display
 * @param {string} currency - "USD" or "KHR"
 * @returns {string} Currency symbol
 */
export function getCurrencySymbol(currency) {
  if (currency === 'KHR') {
    return '៛'
  } else {
    return '$'
  }
}

/**
 * Format amount with proper currency symbol
 * @param {number} amount - Amount to format
 * @param {string} currency - "USD" or "KHR"
 * @returns {string} Formatted currency string
 */
export function formatCurrency(amount, currency = 'USD') {
  const symbol = getCurrencySymbol(currency)
  if (currency === 'KHR') {
    return `${symbol}${amount.toLocaleString()}` // KHR: ៛1,000
  } else {
    return `${symbol}${amount.toFixed(2)}` // USD: $25.50
  }
}

// ============================================
// MAIN PAYMENT FUNCTIONS
// ============================================

/**
 * Generate KHQR Payment Code (Primary Function)
 *
 * This is the main function used by the checkout process to create
 * QR codes that customers can scan with their banking apps.
 *
 * @param {Object} data Payment information:
 *   - bakong_account: Merchant's Bakong ID
 *   - account_name: Display name for merchant
 *   - amount: Payment amount
 *   - currency: "USD" or "KHR"
 *   - track_payment: Enable real-time detection
 */
export const generateKHQRIndividualWithImage = async (data) => {
  try {
    const response = await api.post('/khqr/individual-with-image', data)
    return response.data
  } catch (error) {
    console.error('❌ KHQR generation error:', error)
    throw error
  }
}

// Basic KHQR generation functions (kept for compatibility)
export async function generateKHQRIndividual({
  bakong_account,
  account_name,
  amount,
  currency = 'USD',
}) {
  const response = await api.post('/khqr/individual', {
    bakong_account,
    account_name,
    amount,
    currency,
  })
  return response.data
}

export async function generateKHQRMerchant({
  bakong_account,
  merchant_name,
  amount,
  currency = 'USD',
  bill_number,
  store_label,
}) {
  const response = await api.post('/khqr/merchant', {
    bakong_account,
    merchant_name,
    amount,
    currency,
    bill_number,
    store_label,
  })
  return response.data
}

export async function decodeKHQR(qr_string) {
  const response = await api.post('/khqr/decode', {
    qr_string,
  })
  return response.data
}

export async function verifyKHQR(qr_string) {
  const response = await api.post('/khqr/verify', {
    qr_string,
  })
  return response.data
}

// ============================================
// PAYMENT DETECTION FUNCTIONS
// ============================================

/**
 * Check Payment Status (Real-time Detection)
 *
 * Checks if a payment has been completed by querying the Bakong API.
 * This connects to the official Bakong system to detect real payments
 * made through banking apps like ACLEDA, ABA, etc.
 *
 * @param {string} md5 - MD5 hash from QR generation
 * @returns {Object} Payment status: { payment_status: 'completed'|'pending'|'error' }
 */
export const checkPaymentStatus = async (md5) => {
  try {
    const response = await api.post('/khqr/check-payment-status', { md5 })
    return response.data
  } catch (error) {
    console.error('❌ Payment status check error:', error)
    throw error
  }
}

/**
 * Simulate Payment (Development Only)
 *
 * Simulates a payment completion for testing purposes.
 * Only works in development environment.
 *
 * @param {string} md5 - MD5 hash from QR generation
 * @returns {Object} Simulation result
 */
export const simulatePayment = async (md5) => {
  try {
    const response = await api.post('/khqr/simulate-payment', { md5 })
    return response.data
  } catch (error) {
    console.error('❌ Payment simulation error:', error)
    throw error
  }
}
