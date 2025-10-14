<template>
  <div class="cart-item">
    <div class="item-image">
      <img :src="item.image" :alt="item.title || item.name" />
    </div>

    <div class="item-details">
      <div class="item-name-row">
        <h3 class="item-name">{{ item.title || item.name }}</h3>
        <span v-if="displayDiscount" class="discount-badge">{{ displayDiscount }}</span>
      </div>
      <div class="subtitle-color">
        <span class="product-brand">{{ item.brand }}</span>
        <span class="separator"> | </span>
        <span class="product-color">Color: {{ item.color }}</span>
      </div>
      <div class="item-price">
        <span v-if="hasDiscount" class="original-price"
          >${{ formatPrice(item.originalPrice || item.price) }}</span
        >
        <span class="current-price">${{ formatPrice(finalPrice) }}</span>
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
    props.item.discount &&
    Array.isArray(props.item.discount) &&
    props.item.discount.length > 0 &&
    (props.item.discount[0].type === 'percent' || props.item.discount[0].type === 'fixed'),
)

// Computed property to calculate the final price after discount
const finalPrice = computed(() => {
  // The price is already discounted in the cart store, so just return it
  return parseFloat(props.item.price) || 0
})

// Computed property to display the discount text
const displayDiscount = computed(() => {
  if (!hasDiscount.value) {
    return null
  }
  const discount = props.item.discount[0] // Take the first discount
  if (discount.type === 'percent') {
    return `${discount.value}% OFF`
  } else {
    return `$${discount.value} OFF`
  }
})

// Function to format price with commas
const formatPrice = (price) => {
  const numPrice = parseFloat(price)
  return isNaN(numPrice) ? '0.00' : numPrice.toLocaleString()
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

/* Item Price */
.item-price {
  display: flex;
  gap: 10px;
  align-items: flex-end;
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
