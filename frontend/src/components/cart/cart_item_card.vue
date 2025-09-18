<template>
  <div class="cart-item">
    <div class="item-image">
      <img :src="item.image" :alt="item.name" />
    </div>

    <div class="item-details">
      <div class="item-name-row">
        <h3 class="item-name">{{ item.name }}</h3>
        <span v-if="displayDiscount" class="discount-badge">{{ displayDiscount }}</span>
      </div>
      <div class="subtitle-color">
        <span class="product-subtitle">{{ item.subtitle }}</span>
        <span class="separator">|</span>
        <span class="product-color">Color {{ item.color }}</span>
      </div>
      <div class="item-price">
        <span v-if="item.originalPrice" class="original-price">${{ item.originalPrice }}</span>
        <span class="current-price">${{ item.price }}</span>
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
import { defineProps, defineEmits, computed } from 'vue'

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
})

defineEmits(['increaseQuantity', 'decreaseQuantity', 'removeItem'])

const displayDiscount = computed(() => {
  if (!props.item.discount) return null

  if (props.item.discount.type === 'percent') {
    return `Save ${props.item.discount.value}%`
  } else if (props.item.discount.type === 'fixed') {
    return `Save $${props.item.discount.value}`
  }
  return null
})
</script>

<style scoped>
.subtitle-color {
  display: flex;
  align-items: center;
  gap: 6px;
  color: grey;
  font-weight: 400;
  margin: 4px 0;
}
.subtitle-color span {
  font-size: 13px;
}
.product-color {
  font-weight: 500;
}
.subtitle-color {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  color: grey;
  font-weight: 400;
}
.item-name-row {
  display: flex;
  align-items: center;
  gap: 12px;
}
.cart-item {
  display: flex;
  gap: 20px;
  padding: 20px 0;
  border-bottom: 1px solid #eee;
}

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

.discount-badge {
  color: #14c9c9;
  font-size: 12px;
  font-weight: 500;
}

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

.item-controls {
  display: flex;
  flex-direction: column;
  gap: 10px;
  align-items: center;
}

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

.remove-btn {
  background: none;
  border: none;
  color: #e74c3c;
  cursor: pointer;
  text-decoration: underline;
  font-size: 14px;
}

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
