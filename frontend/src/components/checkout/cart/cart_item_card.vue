<template>
  <div class="cart-item">
    <div class="item-image">
      <img :src="item.product.image" :alt="item.product.name" />
    </div>

    <div class="item-details">
      <div class="item-name-row">
        <h3 class="item-name">{{ item.product.name }}</h3>
        <span v-if="displayDiscount" class="discount-badge">{{ displayDiscount }}</span>
      </div>
      <div class="item-category-brand">
        <span class="badge">{{ getCategoryName(item.product) }}</span>
        <span class="badge">{{ item.product.brand }}</span>
        <span class="badge">Color: {{ item.product.color }}</span>
      </div>
      <div class="item-price">
        <span class="current-price">${{ formatPrice(finalPrice) }}</span>
        <span v-if="hasDiscount" class="original-price"
          >${{ formatPrice(item.product.pricing) }}</span
        >
      </div>
    </div>

    <div class="item-controls">
      <div class="quantity-control">
        <button @click="$emit('decreaseQuantity', item)" class="qty-btn">-</button>
        <span class="quantity">{{ item.quantity }}</span>
        <button @click="$emit('increaseQuantity', item)" class="qty-btn">+</button>
      </div>
      <button @click="$emit('removeItem', item.id)" class="remove-btn">Remove</button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// Define the props for the component
const props = defineProps({
  item: { type: Object, required: true },
})

// Define the events the component can emit
defineEmits(['increaseQuantity', 'decreaseQuantity', 'removeItem'])

// Computed property to check if the item has a discount
const hasDiscount = computed(
  () =>
    props.item.product.discount?.length > 0 &&
    ['percent', 'fixed'].includes(props.item.product.discount[0].type),
)

// Computed property to calculate the final price after discount
const finalPrice = computed(() => {
  let price = parseFloat(props.item.product.pricing) || 0
  if (hasDiscount.value) {
    const discount = props.item.product.discount[0]
    if (discount.type === 'percent') {
      price = price * (1 - discount.value / 100)
    } else if (discount.type === 'fixed') {
      price = Math.max(0, price - discount.value)
    }
  }
  return price
})

// Computed property to display the discount text
const displayDiscount = computed(() => {
  if (!hasDiscount.value) return null
  const discount = props.item.product.discount[0]
  return discount.type === 'percent' ? `${discount.value}% OFF` : `$${discount.value} OFF`
})

// Function to format price with commas
const formatPrice = (price) => {
  const numPrice = parseFloat(price)
  return isNaN(numPrice) ? '0.00' : numPrice.toLocaleString()
}

// Function to get category name for display
const getCategoryName = (product) => {
  if (!product) {
    return 'Unknown'
  }

  // If category is an object (from API relationship), get the name
  if (product.category && typeof product.category === 'object' && product.category.name) {
    // Map category names to display names
    const categoryDisplayMap = {
      Mountain: 'Mountain Bike',
      Road: 'Road Bike',
    }
    return categoryDisplayMap[product.category.name] || product.category.name
  }

  // If category is a string, use it directly
  if (product.category && typeof product.category === 'string') {
    const categoryDisplayMap = {
      mountain: 'Mountain Bike',
      road: 'Road Bike',
    }
    return categoryDisplayMap[product.category] || product.category
  }

  return 'Unknown'
}
</script>

<style scoped>
/* Subtitle and Color */
.subtitle-color {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  color: grey;
  font-weight: 400;
  margin: 4px 0;
}

.product-brand {
  font-weight: 500;
  margin-right: 8px;
}

.product-category {
  font-weight: 500;
  margin-right: 8px;
}

.separator {
  color: grey;
  margin-right: 8px;
}

.product-color {
  font-weight: 500;
}

/* Item Name Row */
.item-name-row {
  display: flex;
  align-items: center;
  gap: 12px;
}

/* Cart Item */
.cart-item {
  display: flex;
  gap: 20px;
  padding: 20px 0;
  border-bottom: 1px solid #eee;
}

/* Item Image */
.item-image {
  position: relative;
  flex-shrink: 0;
}

.item-image img {
  width: auto;
  height: 8.5rem;
  object-fit: cover;
  border-radius: 8px;
}

/* Discount Badge */
.discount-badge {
  color: #14c9c9;
  font-size: 12px;
  font-weight: 500;
}

/* Item Details */

/* Item Details */
.item-details {
  flex: 1;
  font-family: 'Poppins', sans-serif;
}

.item-name {
  font-size: 16px;
  font-weight: 500;
  margin: 0;
  color: #333;
}

.item-category-brand {
  color: #64748b;
  font-size: 14px;
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  margin: 8px 0;
}

.badge {
  display: inline-block;
  padding: 4px 12px;
  border: 1px solid #ddd;
  border-radius: 90px;
  background-color: #f0f0f0;
  color: #333;
  font-size: 12px;
  font-weight: 500;
}

/* Item Price */
.item-price {
  display: flex;
  gap: 10px;
  align-items: center;
}

.original-price {
  text-decoration: line-through;
  color: #999;
  font-size: 14px;
}

.current-price {
  font-weight: 600;
  color: red;
  font-size: 18px;
}

/* Item Controls */
.item-controls {
  display: flex;
  flex-direction: column;
  gap: 10px;
  align-items: center;
}

/* Quantity Control */
.quantity-control {
  display: flex;
  align-items: center;
  gap: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 5px;
}

.qty-btn {
  background: none;
  border: none;
  width: 30px;
  height: 30px;
  cursor: pointer;
  font-size: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.qty-btn:hover {
  background: #f5f5f5;
}

.quantity {
  min-width: 20px;
  text-align: center;
}

/* Remove Button */
.remove-btn {
  background: none;
  border: none;
  color: #e74c3c;
  cursor: pointer;
  text-decoration: underline;
  font-size: 14px;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .cart-item {
    flex-direction: column;
    gap: 15px;
  }

  .item-controls {
    flex-direction: row;
    justify-content: space-between;
  }
}
</style>
