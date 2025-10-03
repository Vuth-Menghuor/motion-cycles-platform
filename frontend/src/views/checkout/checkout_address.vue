<template>
  <Navigation_header
    :disableAnimation="true"
    :colors="{
      headerBg: 'white',
      boxShadowHeader: '0 4px 20px rgba(0, 0, 0, 0.1)',
      menuIcon: 'black',
      logoName: 'black',
      searchBorder: 'rgba(0, 0, 0, 0.2)',
      searchBg: 'white',
      cartIcon: 'black',
      userIcon: 'black',
      userBorderBtn: 'rgba(0, 0, 0, 0.2)',
      userBgBtn: 'white',
      brandName: 'black',
      brandBorder: '#e5e5e5',
      brandBg: 'white',
    }"
  />

  <div class="checkout-address-container">
    <div class="checkout-content">
      <div class="address-section">
        <Indecator_process :currentStep="2" />

        <div class="address-header">
          <h2>Save address</h2>
          <button @click="showAddressForm = true" class="add-address-btn">
            <Icon icon="gg:add" class="add-icon" />
            <span>Add new address</span>
          </button>
        </div>

        <div class="addresses-list">
          <Address_card
            v-for="address in addresses"
            :key="address.id"
            :address="address"
            :is-selected="selectedAddressId === address.id"
            @select="selectAddress"
            @edit="editAddress"
            @remove="askRemoveAddress"
          />

          <Confirm_dialog
            :visible="showConfirm"
            title="Remove Address"
            message="Are you sure want to remove this address?"
            @confirm="confirmRemove"
            @cancel="closeConfirm"
          />
        </div>

        <div class="checkout-actions">
          <button @click="returnToCart" class="btn btn-secondary">Return to cart</button>
          <button @click="continueToPayment" class="btn btn-primary" :disabled="!selectedAddressId">
            Continue with shipping
          </button>
        </div>
      </div>

      <Checkout_summary
        :cart-items="cartItems"
        :promo-code="promoCode"
        @update:promo-code="updatePromoCode"
        class="checkout-summary-sticky"
      />
    </div>
  </div>

  <!-- Address Form Modal -->
  <Address_form
    :is-visible="showAddressForm"
    :editing-address="editingAddress"
    @close="closeAddressForm"
    @save="saveAddress"
    @update="updateAddress"
  />
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { storeToRefs } from 'pinia'

import Address_form from '@/components/checkout/address/address_form.vue'
import Address_card from '@/components/checkout/address/address_card.vue'
import Navigation_header from '@/components/navigation_header.vue'
import Indecator_process from '@/components/checkout/cart/indecator_process.vue'
import Checkout_summary from '@/components/checkout/cart/checkout_summary.vue'
import { Icon } from '@iconify/vue'
import Confirm_dialog from '@/components/checkout/address/confirm_dialog.vue'

const router = useRouter()
const cartStore = useCartStore()
const { cartItems } = storeToRefs(cartStore)

const promoCode = ref('BOOKRIDE50')
const showAddressForm = ref(false)
const editingAddress = ref(null)
const selectedAddressId = ref(1)
const showConfirm = ref(false)
const addressToRemove = ref(null)

const addresses = ref([
  {
    id: 1,
    name: 'Vuth Menghuor',
    fullAddress: '#12, Monivong Blvd, Daun Penh, Phnom Penh, Cambodia',
    streetAddress: '#12, Monivong Blvd',
    district: 'Daun Penh',
    city: 'Phnom Penh',
    province: 'Phnom Penh',
    country: 'Cambodia',
    phone: '+855 12 345 678',
    isDefault: true,
  },
  {
    id: 2,
    name: 'Vuth Menghuor',
    fullAddress: '#45, Street 2004, Sen Sok, Phnom Penh, Cambodia',
    streetAddress: '#45, Street 2004',
    district: 'Sen Sok',
    city: 'Phnom Penh',
    province: 'Phnom Penh',
    country: 'Cambodia',
    phone: '+855 12 345 678',
    isDefault: false,
  },
  {
    id: 3,
    name: 'Vuth Menghuor',
    fullAddress: '#77, Street 6A, Chroy Changvar, Phnom Penh, Cambodia',
    streetAddress: '#77, Street 6A',
    district: 'Chroy Changvar',
    city: 'Phnom Penh',
    province: 'Phnom Penh',
    country: 'Cambodia',
    phone: '+855 12 345 678',
    isDefault: false,
  },
  {
    id: 4,
    name: 'Vuth Menghuor',
    fullAddress: '#48, Street 6A, Sre Ambel, Koh Kong, Cambodia',
    streetAddress: '#48, Street 6A',
    district: 'Sre Ambel',
    city: 'Koh Kong',
    province: 'Koh Kong',
    country: 'Cambodia',
    phone: '+855 12 345 678',
    isDefault: false,
  },
])

const askRemoveAddress = (addressId) => {
  addressToRemove.value = addressId
  showConfirm.value = true
}

const confirmRemove = () => {
  addresses.value = addresses.value.filter((addr) => addr.id != addressToRemove.value)
  if (selectedAddressId.value === addressToRemove.value) {
    selectedAddressId.value = addresses.value.length > 0 ? addresses.value[0].id : null
  }
  closeConfirm()
}

const closeConfirm = () => {
  showConfirm.value = false
  addressToRemove.value = null
}

const selectAddress = (address) => {
  selectedAddressId.value = address.id
}

const editAddress = (address) => {
  editingAddress.value = { ...address }
  showAddressForm.value = true
}

const closeAddressForm = () => {
  showAddressForm.value = false
  editingAddress.value = null
}

const saveAddress = (newAddress) => {
  addresses.value.push(newAddress)
  selectedAddressId.value = newAddress.id
  closeAddressForm()
}

const updateAddress = (updatedAddress) => {
  const index = addresses.value.findIndex((addr) => addr.id === updatedAddress.id)
  if (index !== -1) {
    addresses.value[index] = { ...updatedAddress }
  }
  closeAddressForm()
}

const updatePromoCode = (newCode) => {
  promoCode.value = newCode
}

const navigationAndScroll = (path) => {
  router.push(path).then(() => {
    setTimeout(() => {
      window.scrollTo(0, 0)
    }, 100)
  })
}

const returnToCart = () => {
  navigationAndScroll('/checkout/cart')
}

const continueToPayment = () => {
  if (selectedAddressId.value) {
    navigationAndScroll('/checkout/payment')
  }
}
</script>

<style scoped>
.checkout-address-container {
  max-width: 1350px;
  margin: 0 auto;
  padding: 20px;
  margin-top: 150px;
  font-family: 'Poppins', sans-serif;
}

.checkout-content {
  display: grid;
  grid-template-columns: 1fr 390px;
  gap: 30px;
  margin-bottom: 60px;
}

.address-section {
  max-width: 1000px;
  border-right: 1px solid #d9d9d9;
  padding-right: 50px;
}

.address-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.add-icon {
  font-size: 20px;
  margin-bottom: 1px;
}

.address-header h2 {
  font-size: 18px;
  font-weight: 500;
  color: #2d2d2d;
  margin: 0;
}

.add-address-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: none;
  border: none;
  color: #14c9c9;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  padding: 8px 0;
  font-family: 'Poppins', sans-serif;
  transition: opacity 0.2s ease;
}

.addresses-list {
  margin-bottom: 40px;
}

.checkout-actions {
  display: flex;
  gap: 16px;
}

.btn {
  padding: 14px 32px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
  font-family: 'Poppins', sans-serif;
}

.btn-secondary {
  background: white;
  color: #2d2d2d;
  border: 1px solid #d1d1d1;
}

.btn-secondary:hover {
  background: #f5f5f5;
}

.btn-primary {
  background: #e8fffb;
  color: #2d2d2d;
  border: 1px solid #14c9c9;
}

.btn-primary:hover {
  background: #b5f3e7;
}

.btn-primary:disabled {
  background: #f5f5f5;
  color: #a0a0a0;
  cursor: not-allowed;
}

.checkout-summary-sticky {
  position: sticky;
  top: 10rem;
  align-self: flex-start;
  height: fit-content;
}

/* Responsive */
@media (max-width: 768px) {
  .checkout-content {
    grid-template-columns: 1fr;
  }

  .address-section {
    border-right: none;
    padding-right: 0;
  }

  .address-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }

  .checkout-actions {
    flex-direction: column;
  }

  .newsletter-form {
    flex-direction: column;
  }
}
</style>
