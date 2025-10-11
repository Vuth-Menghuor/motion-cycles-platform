<template>
  <div class="invoice-container">
    <!-- Invoice Header -->
    <div class="invoice-header">
      <div class="company-info">
        <h1 class="company-name">MOTION CYCLE</h1>
        <p class="company-address">Phnom Penh, Cambodia</p>
        <p class="company-contact">Phone: +855 12 345 678 | Email: info@motioncycle.com</p>
      </div>
      <div class="invoice-details">
        <h2 class="invoice-title">INVOICE</h2>
        <div class="invoice-meta">
          <p><strong>Invoice #:</strong> {{ invoiceNumber }}</p>
          <p><strong>Date:</strong> {{ transactionDate }}</p>
          <p><strong>Payment Method:</strong> {{ paymentMethod }}</p>
        </div>
      </div>
    </div>

    <!-- Bill To Section -->
    <div class="bill-to-section">
      <h3>Bill To:</h3>
      <div class="customer-info">
        <p>
          <strong>{{ customerName }}</strong>
        </p>
        <p>Phone: {{ customerPhone }}</p>
      </div>
    </div>

    <!-- Items Table -->
    <div class="items-section">
      <table class="items-table">
        <thead>
          <tr>
            <th class="item-description">Description</th>
            <th class="item-qty">Qty</th>
            <th class="item-price">Unit Price</th>
            <th class="item-discount">Discount</th>
            <th class="item-total">Total</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in items" :key="item.id" class="item-row">
            <td class="item-description">
              <div class="item-info">
                <strong>{{ item.name || item.title }}</strong>
                <div class="item-details">{{ item.subtitle }} | Color: {{ item.color }}</div>
              </div>
            </td>
            <td class="item-qty">{{ item.quantity }}</td>
            <td class="item-price">${{ formatPrice(item.price) }}</td>
            <td class="item-discount">
              <span v-if="hasDiscount(item)"> -${{ formatPrice(getDiscountAmount(item)) }} </span>
              <span v-else>-</span>
            </td>
            <td class="item-total">${{ formatPrice(getItemTotal(item)) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
      <div class="summary-table">
        <div class="summary-row">
          <span>Subtotal:</span>
          <span>${{ formatPrice(subtotal) }}</span>
        </div>
        <div class="summary-row" v-if="itemDiscounts > 0">
          <span>Item Discounts:</span>
          <span class="discount-amount">-${{ formatPrice(itemDiscounts) }}</span>
        </div>
        <div class="summary-row" v-if="promoDiscount > 0">
          <span>Promo Discount:</span>
          <span class="discount-amount">-${{ formatPrice(promoDiscount) }}</span>
        </div>
        <div class="summary-row">
          <span>Shipping:</span>
          <span>${{ formatPrice(shipping) }}</span>
        </div>
        <div class="summary-row total-row">
          <span><strong>Total:</strong></span>
          <span
            ><strong>${{ formatPrice(total) }}</strong></span
          >
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="invoice-footer">
      <p class="thank-you">Thank you for your business!</p>
      <p class="terms">
        Payment is due within 30 days. Please retain this invoice for your records.
      </p>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'

// Define props for the invoice component
defineProps({
  invoiceNumber: { type: String, required: true },
  transactionDate: { type: String, required: true },
  paymentMethod: { type: String, required: true },
  customerName: { type: String, required: true },
  customerPhone: { type: String, required: true },
  items: { type: Array, required: true },
  subtotal: { type: Number, required: true },
  itemDiscounts: { type: Number, default: 0 },
  promoDiscount: { type: Number, default: 0 },
  shipping: { type: Number, required: true },
  total: { type: Number, required: true },
})

// Function to check if item has discount
const hasDiscount = (item) => {
  return item.discount && (item.discount.type === 'percent' || item.discount.type === 'fixed')
}

// Function to get discount amount for an item
const getDiscountAmount = (item) => {
  if (!hasDiscount(item)) {
    return 0
  }
  if (item.discount.type === 'percent') {
    return (item.price * item.discount.value) / 100
  } else {
    return item.discount.value
  }
}

// Function to get discounted price for an item
const getDiscountedPrice = (item) => item.price - getDiscountAmount(item)

// Function to get total for an item (discounted price * quantity)
const getItemTotal = (item) => getDiscountedPrice(item) * item.quantity

// Function to format price with commas
const formatPrice = (price) => price.toLocaleString()
</script>

<style scoped>
/* Invoice Container */
.invoice-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 40px;
  background: white;
  font-family: 'Arial', sans-serif;
  font-size: 14px;
  line-height: 1.4;
  color: #333;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
}

/* Invoice Header */
.invoice-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 40px;
  padding-bottom: 20px;
  border-bottom: 2px solid #333;
}

.company-info {
  flex: 1;
}

.company-name {
  font-size: 28px;
  font-weight: bold;
  color: #333;
  margin: 0 0 8px 0;
}

.company-address,
.company-contact {
  margin: 4px 0;
  color: #666;
  font-size: 12px;
}

.invoice-details {
  text-align: right;
}

.invoice-title {
  font-size: 36px;
  font-weight: bold;
  color: #333;
  margin: 0 0 20px 0;
}

.invoice-meta p {
  margin: 4px 0;
  font-size: 12px;
}

/* Bill To Section */
.bill-to-section {
  margin-bottom: 30px;
}

.bill-to-section h3 {
  font-size: 16px;
  margin: 0 0 10px 0;
  color: #333;
}

.customer-info p {
  margin: 4px 0;
  font-size: 14px;
}

/* Items Section */
.items-section {
  margin-bottom: 30px;
}

.items-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

.items-table th,
.items-table td {
  padding: 12px 8px;
  text-align: left;
  border-bottom: 1px solid #e0e0e0;
}

.items-table th {
  background: #f8f8f8;
  font-weight: bold;
  font-size: 12px;
  text-transform: uppercase;
  color: #666;
}

.item-description {
  width: 40%;
}

.item-qty {
  width: 10%;
  text-align: center;
}

.item-price,
.item-discount,
.item-total {
  width: 15%;
  text-align: right;
}

.item-info strong {
  display: block;
  font-size: 14px;
  color: #333;
}

.item-details {
  font-size: 12px;
  color: #666;
  margin-top: 4px;
}

/* Summary Section */
.summary-section {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 40px;
}

.summary-table {
  width: 300px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  font-size: 14px;
}

.summary-row.total-row {
  border-top: 2px solid #333;
  padding-top: 12px;
  margin-top: 8px;
  font-size: 16px;
}

.discount-amount {
  color: #e74c3c;
}

/* Invoice Footer */
.invoice-footer {
  text-align: center;
  border-top: 1px solid #e0e0e0;
  padding-top: 20px;
}

.thank-you {
  font-size: 16px;
  font-weight: bold;
  color: #333;
  margin: 0 0 8px 0;
}

.terms {
  font-size: 12px;
  color: #666;
  margin: 0;
}

/* Print Styles */
@media print {
  .invoice-container {
    box-shadow: none;
    border: none;
    padding: 20px;
  }
}
</style>
