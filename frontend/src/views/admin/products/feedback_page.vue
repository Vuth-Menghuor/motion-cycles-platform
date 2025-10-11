<template>
  <div class="feedback-page">
    <nav class="breadcrumb">
      <span class="breadcrumb-item active">Feedback Management</span>
    </nav>

    <FeedbackFilters
      v-model:customerSearch="customerSearch"
      v-model:ratingFilter="ratingFilter"
      v-model:categoryFilter="categoryFilter"
      v-model:brandFilter="brandFilter"
      v-model:dateFilter="dateFilter"
      @clearFilters="clearFilters"
    />

    <div class="main-content" :class="{ 'two-column': selectedProduct }">
      <div class="feedback-content-group">
        <div v-if="selectedProduct" class="product-section">
          <FeedbackProductCard :product="selectedProduct" />
        </div>

        <div class="feedback-section">
          <FeedbackList
            :feedbacks="paginatedFeedback"
            @view="viewFeedback"
            @respond="respondFeedback"
            @delete="deleteFeedback"
            @toggle-select="toggleSelectFeedback"
          />
        </div>
      </div>

      <BulkActions
        :selectedCount="selectedFeedback.length"
        @mark-reviewed="bulkMarkReviewed"
        @delete-selected="bulkDelete"
      />

      <FeedbackPagination
        :currentPage="currentPage"
        :totalItems="totalItems"
        :itemsPerPage="ITEMS_PER_PAGE"
        @page-change="goToPage"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import FeedbackFilters from '@/components/admin/feedback/FeedbackFilters.vue'
import FeedbackList from '@/components/admin/feedback/FeedbackList.vue'
import BulkActions from '@/components/admin/feedback/BulkActions.vue'
import FeedbackPagination from '@/components/admin/feedback/FeedbackPagination.vue'
import FeedbackProductCard from '@/components/admin/feedback/FeedbackProductCard.vue'

const ITEMS_PER_PAGE = 10
const currentPage = ref(1)
const router = useRouter()

// Filter reactive data
const ratingFilter = ref('')
const categoryFilter = ref('')
const brandFilter = ref('')
const dateFilter = ref('')
const customerSearch = ref('')

// Sample data for generating feedback
const sampleData = {
  products: [
    { name: 'Trail Pro Carbon', category: 'Mountain Bike', brand: 'Trek' },
    { name: 'Road Race Elite', category: 'Road Bike', brand: 'Giant' },
    { name: 'Mountain Bike Aluminum', category: 'Mountain Bike', brand: 'Specialized' },
    { name: 'Gravel Sport', category: 'Road Bike', brand: 'Cannondale' },
    { name: 'Time Trial Aero', category: 'Road Bike', brand: 'Bianchi' },
  ],
  customers: ['John Smith', 'Sarah Johnson', 'Mike Davis', 'Emma Wilson', 'Chris Brown'],
  comments: [
    'Great bike! Excellent quality and performance.',
    'Good value for money. Would recommend to friends.',
    'Delivery was fast and packaging was secure.',
    'Bike arrived damaged. Customer service was helpful.',
    'Perfect fit and comfortable ride. Very satisfied.',
  ],
  statuses: ['Pending', 'Reviewed', 'Responded'],
}

const sampleProducts = {
  'Trail Pro Carbon': {
    name: 'Trail Pro Carbon',
    description: 'High-performance carbon trail bike designed for aggressive riding.',
    price: 2499.99,
    rating: 4.8,
    image: 'https://picsum.photos/400/200?random=1',
    category: 'Mountain Bike',
    brand: 'Trek',
  },
  'Road Race Elite': {
    name: 'Road Race Elite',
    description: 'Professional road racing bike with aerodynamic design.',
    price: 3299.99,
    rating: 4.9,
    image: 'https://picsum.photos/400/200?random=2',
    category: 'Road Bike',
    brand: 'Giant',
  },
  'Mountain Bike Aluminum': {
    name: 'Mountain Bike Aluminum',
    description: 'Durable aluminum mountain bike perfect for all-terrain adventures.',
    price: 899.99,
    rating: 4.2,
    image: 'https://picsum.photos/400/200?random=3',
    category: 'Mountain Bike',
    brand: 'Specialized',
  },
  'Gravel Sport': {
    name: 'Gravel Sport',
    description: 'Versatile gravel bike for mixed terrain adventures.',
    price: 1899.99,
    rating: 4.6,
    image: 'https://picsum.photos/400/200?random=4',
    category: 'Road Bike',
    brand: 'Cannondale',
  },
  'Time Trial Aero': {
    name: 'Time Trial Aero',
    description: 'Aerodynamic time trial bike for speed and performance.',
    price: 4199.99,
    rating: 4.9,
    image: 'https://picsum.photos/400/200?random=5',
    category: 'Road Bike',
    brand: 'Bianchi',
  },
}

// Utility functions
const generateId = () =>
  `F${Math.floor(Math.random() * 10000)
    .toString()
    .padStart(4, '0')}B`
const randomItem = (array) => array[Math.floor(Math.random() * array.length)]

// Generate sample feedback
const generateSampleFeedback = () => {
  return Array.from({ length: 25 }, () => {
    const product = randomItem(sampleData.products)
    const date = new Date()
    date.setDate(date.getDate() - Math.floor(Math.random() * 30))

    return {
      id: generateId(),
      productName: product.name,
      category: product.category,
      brand: product.brand,
      customerName: randomItem(sampleData.customers),
      rating: Math.floor(Math.random() * 5) + 1,
      comment: randomItem(sampleData.comments),
      date: date.toISOString().split('T')[0],
      status: randomItem(sampleData.statuses),
      selected: false,
    }
  })
}

const feedback = ref(generateSampleFeedback())

// Computed properties
const totalItems = computed(() => filteredFeedback.value.length)

const filteredFeedback = computed(() => {
  return feedback.value.filter((item) => {
    let matches = true

    if (ratingFilter.value && item.rating.toString() !== ratingFilter.value) {
      matches = false
    }

    if (categoryFilter.value && item.category !== categoryFilter.value) {
      matches = false
    }

    if (brandFilter.value && item.brand !== brandFilter.value) {
      matches = false
    }

    if (dateFilter.value && item.date !== dateFilter.value) {
      matches = false
    }

    if (
      customerSearch.value &&
      !item.customerName.toLowerCase().includes(customerSearch.value.toLowerCase())
    ) {
      matches = false
    }

    return matches
  })
})

const paginatedFeedback = computed(() => {
  const start = (currentPage.value - 1) * ITEMS_PER_PAGE
  return filteredFeedback.value.slice(start, start + ITEMS_PER_PAGE)
})

const selectedFeedback = computed(() => feedback.value.filter((item) => item.selected))

const selectedProduct = computed(() => {
  if (filteredFeedback.value.length === 0) {
    return null
  } else {
    const productCounts = {}
    filteredFeedback.value.forEach((item) => {
      productCounts[item.productName] = (productCounts[item.productName] || 0) + 1
    })

    const mostCommonProduct = Object.keys(productCounts).reduce((a, b) => {
      if (productCounts[a] > productCounts[b]) {
        return a
      } else {
        return b
      }
    })

    return sampleProducts[mostCommonProduct] || sampleProducts['Trail Pro Carbon']
  }
})

// Feedback methods
const toggleSelectFeedback = (feedbackId) => {
  const item = feedback.value.find((f) => f.id === feedbackId)
  if (item) {
    item.selected = !item.selected
  } else {
    // Item not found, do nothing
  }
}

const goToPage = (page) => {
  if (page >= 1 && page <= Math.ceil(totalItems.value / ITEMS_PER_PAGE)) {
    currentPage.value = page
  } else {
    // Page is out of range, do nothing
  }
}

const viewFeedback = (feedbackId) => {
  router.push(`/admin/feedback/view/${feedbackId}`)
}

const respondFeedback = (feedbackId) => {
  router.push(`/admin/feedback/respond/${feedbackId}`)
}

const deleteFeedback = (feedbackId) => {
  if (confirm(`Delete feedback ${feedbackId}?`)) {
    const index = feedback.value.findIndex((item) => item.id === feedbackId)
    if (index > -1) {
      feedback.value.splice(index, 1)
      if (paginatedFeedback.value.length === 0 && currentPage.value > 1) {
        currentPage.value--
      }
    }
  } else {
    // User cancelled deletion
  }
}

const bulkMarkReviewed = () => {
  selectedFeedback.value.forEach((item) => (item.status = 'Reviewed'))
}

const bulkDelete = () => {
  const count = selectedFeedback.value.length
  if (confirm(`Delete ${count} selected feedback items?`)) {
    feedback.value = feedback.value.filter((item) => !item.selected)
    if (paginatedFeedback.value.length === 0 && currentPage.value > 1) {
      currentPage.value--
    }
  } else {
    // User cancelled bulk deletion
  }
}

const clearFilters = () => {
  ratingFilter.value = ''
  categoryFilter.value = ''
  brandFilter.value = ''
  dateFilter.value = ''
  customerSearch.value = ''
  currentPage.value = 1
}

// Watchers
watch([ratingFilter, categoryFilter, brandFilter, dateFilter, customerSearch], () => {
  currentPage.value = 1
})
</script>

<style scoped>
.feedback-page {
  font-family: 'Poppins', sans-serif;
}

.main-content {
  display: flex;
  flex-direction: column;
  height: calc(90vh - 200px);
}

.feedback-content-group {
  display: flex;
  gap: 24px;
  flex: 1;
  overflow-y: scroll;
}

.feedback-section {
  flex: 1;
}

.main-content.two-column .product-section {
  flex: 0 0 400px;
  position: sticky;
  top: 0;
  height: fit-content;
}

.main-content.two-column .feedback-section {
  flex: 1;
}

.breadcrumb {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
  padding: 12px 16px;
  border-radius: 5px;
  font-size: 13px;
  color: #666;
  background-color: white;
  border: 1px solid #e9ecef;
  font-family: 'Poppins', sans-serif;
}

.breadcrumb-item {
  color: grey;
  text-decoration: none;
  cursor: pointer;
  transition: color 0.3s;
}

.breadcrumb-item.active {
  color: #ff9934;
  font-weight: 400;
  cursor: default;
}

@media (max-width: 1024px) {
  .feedback-content-group {
    flex-direction: column;
  }

  .main-content.two-column .product-section {
    flex: none;
    margin-bottom: 24px;
  }
}
</style>
