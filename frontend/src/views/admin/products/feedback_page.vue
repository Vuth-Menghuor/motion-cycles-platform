<template>
  <div class="feedback-page">
    <!-- Filters Section -->
    <FeedbackFilters
      v-model:customerSearch="customerSearch"
      v-model:ratingFilter="ratingFilter"
      v-model:categoryFilter="categoryFilter"
      v-model:brandFilter="brandFilter"
      v-model:dateFilter="dateFilter"
      @clearFilters="clearFilters"
    />

    <!-- Main Content -->
    <div class="main-content" :class="{ 'two-column': selectedProduct }">
      <!-- Feedback Content Group -->
      <div class="feedback-content-group">
        <!-- Product Card (when product is selected) -->
        <div v-if="selectedProduct" class="product-section">
          <FeedbackProductCard :product="selectedProduct" />
        </div>

        <!-- Feedback Section -->
        <div class="feedback-section">
          <!-- Feedback Cards List -->
          <FeedbackList
            :feedbacks="paginatedFeedback"
            @view="viewFeedback"
            @respond="respondFeedback"
            @delete="deleteFeedback"
            @toggle-select="toggleSelectFeedback"
          />
        </div>
      </div>

      <!-- Bulk Actions -->
      <BulkActions
        :selectedCount="selectedFeedback.length"
        @mark-reviewed="bulkMarkReviewed"
        @delete-selected="bulkDelete"
      />

      <!-- Pagination -->
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

// Components
import FeedbackFilters from '@/components/admin/feedback/FeedbackFilters.vue'
import FeedbackList from '@/components/admin/feedback/FeedbackList.vue'
import BulkActions from '@/components/admin/feedback/BulkActions.vue'
import FeedbackPagination from '@/components/admin/feedback/FeedbackPagination.vue'
import FeedbackProductCard from '@/components/admin/feedback/FeedbackProductCard.vue'

// Constants
const ITEMS_PER_PAGE = 10

// Reactive state
const currentPage = ref(1)
const router = useRouter()

// Filter states
const ratingFilter = ref('')
const categoryFilter = ref('')
const brandFilter = ref('')
const dateFilter = ref('')
const customerSearch = ref('')

// Sample data generation
const generateSampleFeedback = () => {
  const products = [
    { name: 'Trail Pro Carbon', category: 'mountain', brand: 'Trek' },
    { name: 'Road Race Elite', category: 'road', brand: 'Giant' },
    { name: 'Mountain Bike Aluminum', category: 'mountain', brand: 'Specialized' },
    { name: 'Gravel Sport', category: 'road', brand: 'Cannondale' },
    { name: 'Time Trial Aero', category: 'road', brand: 'Santa Cruz' },
  ]

  const customers = ['John Smith', 'Sarah Johnson', 'Mike Davis', 'Emma Wilson', 'Chris Brown']
  const avatars = [
    'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face',
    'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=40&h=40&fit=crop&crop=face',
    'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&crop=face',
    'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=40&h=40&fit=crop&crop=face',
    'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=40&h=40&fit=crop&crop=face',
  ]
  const comments = [
    'Great bike! Excellent quality and performance.',
    'Good value for money. Would recommend to friends.',
    'Delivery was fast and packaging was secure.',
    'Bike arrived damaged. Customer service was helpful.',
    'Perfect fit and comfortable ride. Very satisfied.',
  ]
  const statuses = ['Pending', 'Reviewed', 'Responded']

  return Array.from({ length: 25 }, () => {
    const product = products[Math.floor(Math.random() * products.length)]
    const date = new Date()
    date.setDate(date.getDate() - Math.floor(Math.random() * 30))

    return {
      id: `F${Math.floor(Math.random() * 10000)
        .toString()
        .padStart(4, '0')}B`,
      productName: product.name,
      category: product.category,
      brand: product.brand,
      customerName: customers[Math.floor(Math.random() * customers.length)],
      customerAvatar: avatars[Math.floor(Math.random() * avatars.length)],
      rating: Math.floor(Math.random() * 5) + 1,
      comment: comments[Math.floor(Math.random() * comments.length)],
      date: date.toISOString().split('T')[0],
      status: statuses[Math.floor(Math.random() * statuses.length)],
      selected: false,
    }
  })
}

// Sample product data
const sampleProducts = {
  'Trail Pro Carbon': {
    name: 'Trail Pro Carbon',
    description: 'High-performance carbon trail bike designed for aggressive riding.',
    price: 2499.99,
    rating: 4.8,
    image: 'https://via.placeholder.com/400x200?text=Trail+Pro+Carbon',
    category: 'mountain',
    brand: 'Trek',
  },
  'Road Race Elite': {
    name: 'Road Race Elite',
    description: 'Professional road racing bike with aerodynamic design.',
    price: 3299.99,
    rating: 4.9,
    image: 'https://via.placeholder.com/400x200?text=Road+Race+Elite',
    category: 'road',
    brand: 'Giant',
  },
  'Mountain Bike Aluminum': {
    name: 'Mountain Bike Aluminum',
    description: 'Durable aluminum mountain bike perfect for all-terrain adventures.',
    price: 899.99,
    rating: 4.2,
    image: 'https://via.placeholder.com/400x200?text=Mountain+Bike+Aluminum',
    category: 'mountain',
    brand: 'Specialized',
  },
  'Gravel Sport': {
    name: 'Gravel Sport',
    description: 'Versatile gravel bike for mixed terrain adventures.',
    price: 1899.99,
    rating: 4.6,
    image: 'https://via.placeholder.com/400x200?text=Gravel+Sport',
    category: 'road',
    brand: 'Cannondale',
  },
  'Time Trial Aero': {
    name: 'Time Trial Aero',
    description: 'Aerodynamic time trial bike for speed and performance.',
    price: 4199.99,
    rating: 4.9,
    image: 'https://via.placeholder.com/400x200?text=Time+Trial+Aero',
    category: 'road',
    brand: 'Santa Cruz',
  },
}

// ===========================================
// DATA
// ===========================================

const feedback = ref(generateSampleFeedback())

// ===========================================
// COMPUTED PROPERTIES
// ===========================================

const totalItems = computed(() => filteredFeedback.value.length)

const filteredFeedback = computed(() => {
  return feedback.value.filter((item) => {
    const matches = [
      !ratingFilter.value || item.rating.toString() === ratingFilter.value,
      !categoryFilter.value || item.category === categoryFilter.value,
      !brandFilter.value || item.brand === brandFilter.value,
      !dateFilter.value || item.date === dateFilter.value,
      !customerSearch.value ||
        item.customerName.toLowerCase().includes(customerSearch.value.toLowerCase()),
    ]
    return matches.every(Boolean)
  })
})

const paginatedFeedback = computed(() => {
  const start = (currentPage.value - 1) * ITEMS_PER_PAGE
  const end = start + ITEMS_PER_PAGE
  return filteredFeedback.value.slice(start, end)
})

const selectedFeedback = computed(() => feedback.value.filter((item) => item.selected))

const selectedProduct = computed(() => {
  if (filteredFeedback.value.length === 0) return null

  // Count product occurrences
  const productCounts = {}
  filteredFeedback.value.forEach((item) => {
    productCounts[item.productName] = (productCounts[item.productName] || 0) + 1
  })

  // Find most common product
  const mostCommonProduct = Object.keys(productCounts).reduce((a, b) =>
    productCounts[a] > productCounts[b] ? a : b,
  )

  return sampleProducts[mostCommonProduct] || sampleProducts['Trail Pro Carbon']
})

// ===========================================
// METHODS
// ===========================================

const toggleSelectFeedback = (feedbackId) => {
  const item = feedback.value.find((f) => f.id === feedbackId)
  if (item) {
    item.selected = !item.selected
  }
}

const goToPage = (page) => {
  if (page >= 1 && page <= Math.ceil(totalItems.value / ITEMS_PER_PAGE)) {
    currentPage.value = page
  }
}

// ===========================================
// NAVIGATION METHODS
// ===========================================

const viewFeedback = (feedbackId) => {
  console.log('View feedback:', feedbackId)
  router.push(`/admin/feedback/view/${feedbackId}`)
}

const respondFeedback = (feedbackId) => {
  console.log('Respond to feedback:', feedbackId)
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
  }
}

// ===========================================
// BULK OPERATIONS METHODS
// ===========================================

const bulkMarkReviewed = () => {
  selectedFeedback.value.forEach((item) => {
    item.status = 'Reviewed'
  })
}

const bulkDelete = () => {
  const selectedIds = selectedFeedback.value.map((item) => item.id)
  if (confirm(`Delete ${selectedIds.length} selected feedback items?`)) {
    feedback.value = feedback.value.filter((item) => !item.selected)
    if (paginatedFeedback.value.length === 0 && currentPage.value > 1) {
      currentPage.value--
    }
  }
}

// ===========================================
// UTILITY METHODS
// ===========================================

const clearFilters = () => {
  ratingFilter.value = ''
  categoryFilter.value = ''
  brandFilter.value = ''
  dateFilter.value = ''
  customerSearch.value = ''
  currentPage.value = 1
}

// ===========================================
// WATCHERS
// ===========================================

// Watchers
watch(currentPage, () => {
  // Reset page when changing pages
})

// Watch filters to reset to page 1
watch([ratingFilter, categoryFilter, brandFilter, dateFilter, customerSearch], () => {
  currentPage.value = 1
})
</script>

// =========================================== // STYLES //
===========================================

<style scoped>
/* Page Layout */
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

/* Responsive Design */
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
