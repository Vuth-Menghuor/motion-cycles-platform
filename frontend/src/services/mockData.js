// Mock data for when backend API is not available
export const mockProducts = [
  {
    id: 1,
    title: "Bianchi T-Tronik C Type - Sunrace (2023)",
    description: "Premium mountain bike with advanced suspension system",
    price: 14400.00,
    original_price: 16000.00,
    discount_percentage: 10,
    brand: "Bianchi",
    color: "Black/Red",
    category_id: 1,
    category: {
      id: 1,
      name: "Mountain Bikes",
      slug: "mountain-bikes"
    },
    image: "/images/bikes/bianchi-t-tronik.jpg",
    images: ["/images/bikes/bianchi-t-tronik.jpg"],
    stock_quantity: 15,
    is_active: true,
    average_rating: 4.5,
    review_count: 23,
    specifications: {
      travel: "160mm / 170mm",
      wheels: '29" DT Swiss EX1700',
      weight: "33.3 lb (15.1 kg)",
      frame: "Carbon",
      fork: "RockShox Pike",
      groupset: "Shimano XT"
    },
    created_at: "2024-01-15T10:00:00Z",
    updated_at: "2024-01-15T10:00:00Z"
  },
  {
    id: 2,
    title: "Trek Slash 9.8 XT Carbon",
    description: "High-performance enduro bike for technical trails",
    price: 6299.99,
    original_price: 6999.99,
    discount_percentage: 10,
    brand: "Trek",
    color: "Blue/White",
    category_id: 1,
    category: {
      id: 1,
      name: "Mountain Bikes",
      slug: "mountain-bikes"
    },
    image: "/images/bikes/trek-slash.jpg",
    images: ["/images/bikes/trek-slash.jpg"],
    stock_quantity: 8,
    is_active: true,
    average_rating: 4.7,
    review_count: 31,
    specifications: {
      travel: "150mm / 160mm",
      wheels: '29" Bontrager Line Comp',
      weight: "31.2 lb (14.1 kg)",
      frame: "Carbon",
      fork: "RockShox Pike",
      groupset: "Shimano XT"
    },
    created_at: "2024-01-20T10:00:00Z",
    updated_at: "2024-01-20T10:00:00Z"
  },
  {
    id: 3,
    title: "Specialized Epic Hardtail Pro",
    description: "Lightweight hardtail mountain bike for cross-country racing",
    price: 3500.00,
    original_price: 4000.00,
    discount_percentage: 12,
    brand: "Specialized",
    color: "Red/Black",
    category_id: 1,
    category: {
      id: 1,
      name: "Mountain Bikes",
      slug: "mountain-bikes"
    },
    image: "/images/bikes/specialized-epic.jpg",
    images: ["/images/bikes/specialized-epic.jpg"],
    stock_quantity: 12,
    is_active: true,
    average_rating: 4.3,
    review_count: 18,
    specifications: {
      travel: "100mm / 110mm",
      wheels: '29" Roval Control',
      weight: "24.5 lb (11.1 kg)",
      frame: "Carbon",
      fork: "RockShox SID",
      groupset: "Shimano SLX"
    },
    created_at: "2024-02-01T10:00:00Z",
    updated_at: "2024-02-01T10:00:00Z"
  },
  {
    id: 4,
    title: "Cannondale Scalpel Carbon SE",
    description: "Ultra-light trail bike with incredible climbing ability",
    price: 4200.00,
    original_price: 4500.00,
    discount_percentage: 7,
    brand: "Cannondale",
    color: "Green/White",
    category_id: 1,
    category: {
      id: 1,
      name: "Mountain Bikes",
      slug: "mountain-bikes"
    },
    image: "/images/bikes/cannondale-scalpel.jpg",
    images: ["/images/bikes/cannondale-scalpel.jpg"],
    stock_quantity: 6,
    is_active: true,
    average_rating: 4.6,
    review_count: 27,
    specifications: {
      travel: "120mm / 130mm",
      wheels: '29" WTB STP',
      weight: "26.8 lb (12.2 kg)",
      frame: "Carbon",
      fork: "RockShox SID",
      groupset: "Shimano Deore"
    },
    created_at: "2024-02-10T10:00:00Z",
    updated_at: "2024-02-10T10:00:00Z"
  },
  {
    id: 5,
    title: "Giant Trance X Advanced Pro",
    description: "Versatile trail bike that excels on all types of terrain",
    price: 3800.00,
    original_price: 4200.00,
    discount_percentage: 10,
    brand: "Giant",
    color: "Orange/Black",
    category_id: 1,
    category: {
      id: 1,
      name: "Mountain Bikes",
      slug: "mountain-bikes"
    },
    image: "/images/bikes/giant-trance.jpg",
    images: ["/images/bikes/giant-trance.jpg"],
    stock_quantity: 9,
    is_active: true,
    average_rating: 4.4,
    review_count: 22,
    specifications: {
      travel: "140mm / 150mm",
      wheels: '29" Giant AM',
      weight: "29.1 lb (13.2 kg)",
      frame: "Aluminum",
      fork: "RockShox Pike",
      groupset: "Shimano SLX"
    },
    created_at: "2024-02-15T10:00:00Z",
    updated_at: "2024-02-15T10:00:00Z"
  },
  {
    id: 6,
    title: "Santa Cruz Hightower Carbon C",
    description: "Premium e-bike with powerful motor assistance",
    price: 8500.00,
    original_price: 9500.00,
    discount_percentage: 11,
    brand: "Santa Cruz",
    color: "Purple/White",
    category_id: 2,
    category: {
      id: 2,
      name: "Electric Bikes",
      slug: "electric-bikes"
    },
    image: "/images/bikes/santa-cruz-hightower.jpg",
    images: ["/images/bikes/santa-cruz-hightower.jpg"],
    stock_quantity: 4,
    is_active: true,
    average_rating: 4.8,
    review_count: 35,
    specifications: {
      travel: "150mm / 160mm",
      wheels: '29" Reserve',
      weight: "47.2 lb (21.4 kg)",
      frame: "Carbon",
      motor: "Bosch Performance Line CX",
      battery: "625Wh",
      groupset: "Shimano XT"
    },
    created_at: "2024-02-20T10:00:00Z",
    updated_at: "2024-02-20T10:00:00Z"
  },
  {
    id: 7,
    title: "Rad Power Bikes RadCity 5 Plus",
    description: "Urban electric bike perfect for city commuting",
    price: 1999.00,
    original_price: 2199.00,
    discount_percentage: 9,
    brand: "Rad Power Bikes",
    color: "Silver/Blue",
    category_id: 2,
    category: {
      id: 2,
      name: "Electric Bikes",
      slug: "electric-bikes"
    },
    image: "/images/bikes/rad-power-radcity.jpg",
    images: ["/images/bikes/rad-power-radcity.jpg"],
    stock_quantity: 18,
    is_active: true,
    average_rating: 4.2,
    review_count: 45,
    specifications: {
      travel: "N/A",
      wheels: '27.5" Kenda',
      weight: "58.4 lb (26.5 kg)",
      frame: "Aluminum",
      motor: "500W Rear Hub",
      battery: "672Wh",
      range: "Up to 80 miles"
    },
    created_at: "2024-03-01T10:00:00Z",
    updated_at: "2024-03-01T10:00:00Z"
  },
  {
    id: 8,
    title: "Trek Allant+ 7 Stagger",
    description: "Comfortable hybrid electric bike for daily commuting",
    price: 3250.00,
    original_price: 3500.00,
    discount_percentage: 7,
    brand: "Trek",
    color: "Black/Gray",
    category_id: 2,
    category: {
      id: 2,
      name: "Electric Bikes",
      slug: "electric-bikes"
    },
    image: "/images/bikes/trek-allant.jpg",
    images: ["/images/bikes/trek-allant.jpg"],
    stock_quantity: 11,
    is_active: true,
    average_rating: 4.5,
    review_count: 28,
    specifications: {
      travel: "N/A",
      wheels: '700c Bontrager',
      weight: "49.8 lb (22.6 kg)",
      frame: "Aluminum",
      motor: "Bosch Active Line Plus",
      battery: "400Wh",
      range: "Up to 100 miles"
    },
    created_at: "2024-03-05T10:00:00Z",
    updated_at: "2024-03-05T10:00:00Z"
  }
]

export const mockCategories = [
  {
    id: 1,
    name: "Mountain Bikes",
    slug: "mountain-bikes",
    description: "Bikes designed for off-road cycling and mountain trails",
    image: "/images/categories/mountain-bikes.jpg",
    is_active: true,
    created_at: "2024-01-01T00:00:00Z",
    updated_at: "2024-01-01T00:00:00Z"
  },
  {
    id: 2,
    name: "Electric Bikes",
    slug: "electric-bikes",
    description: "Electric-assisted bikes with motor and battery power",
    image: "/images/categories/electric-bikes.jpg",
    is_active: true,
    created_at: "2024-01-01T00:00:00Z",
    updated_at: "2024-01-01T00:00:00Z"
  },
  {
    id: 3,
    name: "Road Bikes",
    slug: "road-bikes",
    description: "High-performance bikes designed for paved roads and racing",
    image: "/images/categories/road-bikes.jpg",
    is_active: true,
    created_at: "2024-01-01T00:00:00Z",
    updated_at: "2024-01-01T00:00:00Z"
  },
  {
    id: 4,
    name: "Hybrid Bikes",
    slug: "hybrid-bikes",
    description: "Versatile bikes that combine features of road and mountain bikes",
    image: "/images/categories/hybrid-bikes.jpg",
    is_active: true,
    created_at: "2024-01-01T00:00:00Z",
    updated_at: "2024-01-01T00:00:00Z"
  }
]

// Helper function to filter and paginate mock products
export const getFilteredProducts = (params = {}) => {
  let filteredProducts = [...mockProducts]

  // Apply search filter
  if (params.search) {
    const searchTerm = params.search.toLowerCase()
    filteredProducts = filteredProducts.filter(product =>
      product.title.toLowerCase().includes(searchTerm) ||
      product.description.toLowerCase().includes(searchTerm) ||
      product.brand.toLowerCase().includes(searchTerm)
    )
  }

  // Apply category filter
  if (params.category_id) {
    filteredProducts = filteredProducts.filter(product =>
      product.category_id === parseInt(params.category_id)
    )
  }

  // Apply brand filter
  if (params.brand) {
    const brands = params.brand.split(',')
    filteredProducts = filteredProducts.filter(product =>
      brands.includes(product.brand)
    )
  }

  // Apply color filter
  if (params.color) {
    const colors = params.color.split(',')
    filteredProducts = filteredProducts.filter(product =>
      colors.includes(product.color)
    )
  }

  // Apply price range filter
  if (params.min_price !== undefined && params.min_price !== null) {
    filteredProducts = filteredProducts.filter(product =>
      product.price >= parseFloat(params.min_price)
    )
  }
  if (params.max_price !== undefined && params.max_price !== null) {
    filteredProducts = filteredProducts.filter(product =>
      product.price <= parseFloat(params.max_price)
    )
  }

  // Apply discount filter
  if (params.has_discount !== undefined) {
    const hasDiscount = params.has_discount === 'true'
    filteredProducts = filteredProducts.filter(product =>
      (product.discount_percentage > 0) === hasDiscount
    )
  }

  // Apply rating filter
  if (params.min_rating !== undefined && params.min_rating !== null) {
    filteredProducts = filteredProducts.filter(product =>
      product.average_rating >= parseFloat(params.min_rating)
    )
  }

  // Apply sorting
  if (params.sort_by) {
    const sortOrder = params.sort_order === 'desc' ? -1 : 1
    filteredProducts.sort((a, b) => {
      let aValue, bValue

      switch (params.sort_by) {
        case 'price':
          aValue = a.price
          bValue = b.price
          break
        case 'rating':
          aValue = a.average_rating
          bValue = b.average_rating
          break
        case 'name':
          aValue = a.title.toLowerCase()
          bValue = b.title.toLowerCase()
          break
        case 'newest':
          aValue = new Date(a.created_at)
          bValue = new Date(b.created_at)
          break
        default:
          return 0
      }

      if (aValue < bValue) return -1 * sortOrder
      if (aValue > bValue) return 1 * sortOrder
      return 0
    })
  }

  // Apply pagination
  const perPage = params.per_page || 20
  const page = params.page || 1
  const startIndex = (page - 1) * perPage
  const endIndex = startIndex + perPage
  const paginatedProducts = filteredProducts.slice(startIndex, endIndex)

  return {
    data: paginatedProducts,
    current_page: page,
    last_page: Math.ceil(filteredProducts.length / perPage),
    per_page: perPage,
    total: filteredProducts.length
  }
}