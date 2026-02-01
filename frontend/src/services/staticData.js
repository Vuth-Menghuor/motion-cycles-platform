import { staticBikes, staticCategories, staticBrands, staticColors } from '@/data/staticBikes'

export const staticDataService = {
  // Get all products with filtering
  getProducts: (params = {}) => {
    return new Promise((resolve) => {
      setTimeout(() => {
        let filteredBikes = [...staticBikes]

        // Apply search filter
        if (params.search) {
          const searchTerm = params.search.toLowerCase()
          filteredBikes = filteredBikes.filter(bike =>
            bike.name.toLowerCase().includes(searchTerm) ||
            bike.brand.toLowerCase().includes(searchTerm) ||
            bike.description.toLowerCase().includes(searchTerm)
          )
        }

        // Apply category filter
        if (params.category_id) {
          filteredBikes = filteredBikes.filter(bike =>
            bike.category_id === parseInt(params.category_id)
          )
        }

        // Apply brand filter
        if (params.brand) {
          const brands = params.brand.split(',')
          filteredBikes = filteredBikes.filter(bike =>
            brands.includes(bike.brand)
          )
        }

        // Apply color filter
        if (params.color) {
          const colors = params.color.split(',')
          filteredBikes = filteredBikes.filter(bike =>
            colors.includes(bike.color)
          )
        }

        // Apply price range filter
        if (params.min_price || params.max_price) {
          filteredBikes = filteredBikes.filter(bike => {
            const price = bike.pricing
            const minPrice = params.min_price ? parseFloat(params.min_price) : 0
            const maxPrice = params.max_price ? parseFloat(params.max_price) : Infinity
            return price >= minPrice && price <= maxPrice
          })
        }

        // Apply discount filter
        if (params.has_discount === 'true') {
          filteredBikes = filteredBikes.filter(bike => bike.discount !== null)
        } else if (params.has_discount === 'false') {
          filteredBikes = filteredBikes.filter(bike => bike.discount === null)
        }

        // Pagination
        const perPage = params.per_page ? parseInt(params.per_page) : 20
        const currentPage = params.page ? parseInt(params.page) : 1
        const totalItems = filteredBikes.length
        const totalPages = Math.ceil(totalItems / perPage)
        const startIndex = (currentPage - 1) * perPage
        const endIndex = startIndex + perPage
        const paginatedBikes = filteredBikes.slice(startIndex, endIndex)

        resolve({
          data: {
            data: paginatedBikes,
            current_page: currentPage,
            last_page: totalPages,
            per_page: perPage,
            total: totalItems
          }
        })
      }, 100) // Small delay to simulate API call
    })
  },

  // Get single product by ID
  getProduct: (id) => {
    return new Promise((resolve) => {
      setTimeout(() => {
        const bike = staticBikes.find(bike => bike.id === parseInt(id))
        resolve({
          data: bike || null
        })
      }, 50)
    })
  },

  // Get all categories
  getCategories: () => {
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve({
          data: staticCategories
        })
      }, 50)
    })
  },

  // Get category by ID
  getCategory: (id) => {
    return new Promise((resolve) => {
      setTimeout(() => {
        const category = staticCategories.find(cat => cat.id === parseInt(id))
        resolve({
          data: category || null
        })
      }, 50)
    })
  },

  // Get available brands
  getBrands: () => {
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve({
          data: staticBrands
        })
      }, 50)
    })
  },

  // Get available colors
  getColors: () => {
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve({
          data: staticColors
        })
      }, 50)
    })
  },

  // Get product reviews
  getProductReviews: (productId) => {
    return new Promise((resolve) => {
      setTimeout(() => {
        // Generate mock reviews for each product
        const mockReviews = [
          {
            id: 1,
            user_name: 'John Doe',
            rating: 5,
            comment: 'Excellent bike! Great performance and build quality.',
            created_at: '2024-01-15T10:30:00Z'
          },
          {
            id: 2,
            user_name: 'Jane Smith',
            rating: 4,
            comment: 'Very comfortable for long rides. Highly recommended!',
            created_at: '2024-01-20T14:45:00Z'
          },
          {
            id: 3,
            user_name: 'Mike Johnson',
            rating: 5,
            comment: 'Best electric bike I\'ve owned. Battery life is amazing!',
            created_at: '2024-01-25T09:15:00Z'
          }
        ]

        resolve({
          data: mockReviews
        })
      }, 100)
    })
  },
}