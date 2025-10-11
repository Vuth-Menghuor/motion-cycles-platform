import axios from 'axios'

// Create API instance with base URL and timeout
const api = axios.create({
  baseURL: 'http://localhost:8100/api',
  timeout: 15000,
})

/**
 * Order Management Service
 * Handles order creation, payment tracking, and invoice generation
 */

// Function to create a new order
export const createOrder = async (orderData) => {
  try {
    const response = await api.post('/orders', orderData)
    return response.data
  } catch (error) {
    console.error('Order creation error:', error)
    throw error
  }
}

// Function to check the payment status of an order
export const checkOrderPaymentStatus = async (orderId) => {
  try {
    const response = await api.post(`/orders/${orderId}/check-payment`)
    return response.data
  } catch (error) {
    console.error('Order payment status check error:', error)
    throw error
  }
}

// Function to confirm payment for an order
export const confirmOrderPayment = async (orderId, notes = null) => {
  try {
    const response = await api.post(`/orders/${orderId}/confirm-payment`, {
      notes: notes,
    })
    return response.data
  } catch (error) {
    console.error('Order payment confirmation error:', error)
    throw error
  }
}

// Function to get details of a specific order
export const getOrder = async (orderId) => {
  try {
    const response = await api.get(`/orders/${orderId}`)
    return response.data
  } catch (error) {
    console.error('Get order error:', error)
    throw error
  }
}

// Function to list orders with optional filters
export const listOrders = async (filters = {}) => {
  try {
    const params = new URLSearchParams()

    // Add filters to query parameters if provided
    if (filters.payment_status) {
      params.append('payment_status', filters.payment_status)
    }
    if (filters.order_status) {
      params.append('order_status', filters.order_status)
    }
    if (filters.per_page) {
      params.append('per_page', filters.per_page)
    }
    if (filters.page) {
      params.append('page', filters.page)
    }

    const response = await api.get(`/orders?${params.toString()}`)
    return response.data
  } catch (error) {
    console.error('List orders error:', error)
    throw error
  }
}

/**
 * Enhanced Order Creation with Integrated Payment
 * This replaces the direct KHQR generation in the checkout flow
 */
export const createOrderWithPayment = async ({
  customerName,
  customerPhone,
  customerEmail = null,
  items,
  subtotal,
  discountAmount = 0,
  shippingAmount = 0,
  totalAmount,
  currency = 'USD',
  promoCode = null,
  paymentMethod,
  bakongAccount = null,
  accountName = null,
}) => {
  try {
    // Prepare order data
    const orderData = {
      customer_name: customerName,
      customer_phone: customerPhone,
      customer_email: customerEmail,
      items: items.map((item) => ({
        id: item.id,
        name: item.name || item.title,
        price: item.price,
        quantity: item.quantity,
        subtitle: item.subtitle,
        color: item.color,
        image: item.image,
      })),
      subtotal: subtotal,
      discount_amount: discountAmount,
      shipping_amount: shippingAmount,
      total_amount: totalAmount,
      currency: currency,
      promo_code: promoCode,
      payment_method: paymentMethod,
    }

    // Add Bakong-specific fields if payment method is Bakong KHQR
    if (paymentMethod === 'Bakong KHQR') {
      orderData.bakong_account = bakongAccount || 'vuth_menghuor@aclb'
      orderData.account_name = accountName || 'MOTION CYCLE'
    }

    const response = await api.post('/orders', orderData)
    console.log('Order created successfully:', response.data)
    return response.data
  } catch (error) {
    console.error('Enhanced order creation error:', error)
    throw error
  }
}

/**
 * Poll for payment completion
 * Returns a promise that resolves when payment is completed or rejects on timeout
 */
export const pollForPaymentCompletion = async (
  orderId,
  maxAttempts = 20,
  intervalMs = 30000,
  onProgress = null,
) => {
  return new Promise((resolve, reject) => {
    let attempts = 0

    const checkPayment = async () => {
      try {
        attempts++

        // Call progress callback if provided
        if (onProgress) {
          onProgress(attempts, maxAttempts)
        }

        console.log(`🔍 Payment check attempt ${attempts}/${maxAttempts} for order ${orderId}`)

        const result = await checkOrderPaymentStatus(orderId)

        // Check if payment is completed
        if (result.success && result.payment_status === 'completed') {
          console.log('🎉 Payment completed for order:', orderId)
          resolve(result)
          return
        }

        // Continue polling if not completed and within limits
        if (attempts < maxAttempts) {
          console.log(`⏰ Scheduling next check in ${intervalMs / 1000} seconds...`)
          setTimeout(checkPayment, intervalMs)
        } else {
          console.log('⏰ Polling timeout reached')
          reject(new Error('Payment polling timeout reached'))
        }
      } catch (error) {
        console.error('💥 Payment polling error:', error)
        reject(error)
      }
    }

    // Start first check after initial delay
    setTimeout(checkPayment, intervalMs)
  })
}

// Export all functions as default
export default {
  createOrder,
  checkOrderPaymentStatus,
  confirmOrderPayment,
  getOrder,
  listOrders,
  createOrderWithPayment,
  pollForPaymentCompletion,
}
