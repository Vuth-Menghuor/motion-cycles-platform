// Mock data for development and demo purposes
export const mockProducts = [
  {
    id: 1,
    name: "Honda CBR 600RR",
    description: "High-performance sport bike with advanced aerodynamics and powerful engine.",
    pricing: 12999.99,
    original_price: 13999.99,
    discount_percentage: 7,
    brand: "Honda",
    category: "Sport Bikes",
    category_id: 1,
    color: "Red",
    engine_capacity: "600cc",
    fuel_type: "Petrol",
    mileage: "25 kmpl",
    top_speed: "260 km/h",
    weight: "186 kg",
    image: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500",
    images: [
      "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500",
      "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500",
      "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500"
    ],
    stock_quantity: 5,
    is_available: true,
    rating: 4.5,
    review_count: 23,
    badge: {
      text: "Best Seller",
      icon: "mdi:fire",
      gradient: "linear-gradient(135deg, #ff6b35, #f7931e)"
    },
    specifications: {
      engine: "599cc inline-4",
      power: "118 hp",
      torque: "66 Nm",
      transmission: "6-speed manual",
      fuel_capacity: "18L"
    },
    created_at: "2024-01-15T10:00:00Z",
    updated_at: "2024-01-20T14:30:00Z"
  },
  {
    id: 2,
    name: "Yamaha R1",
    description: "Flagship superbike with cutting-edge technology and racing heritage.",
    pricing: 18999.99,
    original_price: 19999.99,
    discount_percentage: 5,
    brand: "Yamaha",
    category: "Sport Bikes",
    category_id: 1,
    color: "Blue",
    engine_capacity: "1000cc",
    fuel_type: "Petrol",
    mileage: "20 kmpl",
    top_speed: "299 km/h",
    weight: "199 kg",
    image: "https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=500",
    images: [
      "https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=500",
      "https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=500",
      "https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=500"
    ],
    stock_quantity: 3,
    is_available: true,
    rating: 4.7,
    review_count: 45,
    badge: {
      text: "Premium",
      icon: "mdi:crown",
      gradient: "linear-gradient(135deg, #ffd700, #ffb347)"
    },
    specifications: {
      engine: "998cc inline-4",
      power: "197 hp",
      torque: "112 Nm",
      transmission: "6-speed manual",
      fuel_capacity: "17L"
    },
    created_at: "2024-01-10T09:00:00Z",
    updated_at: "2024-01-18T11:15:00Z"
  },
  {
    id: 3,
    name: "Kawasaki Ninja ZX-10R",
    description: "Ultimate sportbike designed for track and street performance.",
    pricing: 16999.99,
    brand: "Kawasaki",
    category: "Sport Bikes",
    category_id: 1,
    color: "Green",
    engine_capacity: "1000cc",
    fuel_type: "Petrol",
    mileage: "18 kmpl",
    top_speed: "299 km/h",
    weight: "205 kg",
    image: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500",
    images: [
      "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500",
      "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500",
      "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500"
    ],
    stock_quantity: 7,
    is_available: true,
    rating: 4.6,
    review_count: 38,
    badge: {
      text: "New Arrival",
      icon: "mdi:star",
      gradient: "linear-gradient(135deg, #667eea, #764ba2)"
    },
    specifications: {
      engine: "998cc inline-4",
      power: "203 hp",
      torque: "114 Nm",
      transmission: "6-speed manual",
      fuel_capacity: "17L"
    },
    created_at: "2024-01-25T08:30:00Z",
    updated_at: "2024-01-25T08:30:00Z"
  },
  {
    id: 4,
    name: "BMW S 1000 RR",
    description: "German engineering meets Japanese performance in this ultimate superbike.",
    pricing: 22999.99,
    original_price: 24999.99,
    discount_percentage: 8,
    brand: "BMW",
    category: "Sport Bikes",
    category_id: 1,
    color: "Black",
    engine_capacity: "1000cc",
    fuel_type: "Petrol",
    mileage: "16 kmpl",
    top_speed: "303 km/h",
    weight: "197 kg",
    image: "https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=500",
    images: [
      "https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=500",
      "https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=500",
      "https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=500"
    ],
    stock_quantity: 2,
    is_available: true,
    rating: 4.8,
    review_count: 67,
    badge: {
      text: "Top Rated",
      icon: "mdi:trophy",
      gradient: "linear-gradient(135deg, #ff9a9e, #fecfef)"
    },
    specifications: {
      engine: "999cc inline-4",
      power: "205 hp",
      torque: "113 Nm",
      transmission: "6-speed manual",
      fuel_capacity: "16.5L"
    },
    created_at: "2024-01-05T12:00:00Z",
    updated_at: "2024-01-22T16:45:00Z"
  },
  {
    id: 5,
    name: "Ducati Panigale V4",
    description: "Italian masterpiece combining style, performance, and innovation.",
    pricing: 25999.99,
    brand: "Ducati",
    category: "Sport Bikes",
    category_id: 1,
    color: "Red",
    engine_capacity: "1103cc",
    fuel_type: "Petrol",
    mileage: "14 kmpl",
    top_speed: "306 km/h",
    weight: "198 kg",
    image: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500",
    images: [
      "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500",
      "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500",
      "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500"
    ],
    stock_quantity: 4,
    is_available: true,
    rating: 4.9,
    review_count: 89,
    badge: {
      text: "Luxury",
      icon: "mdi:diamond",
      gradient: "linear-gradient(135deg, #a8edea, #fed6e3)"
    },
    specifications: {
      engine: "1103cc V4",
      power: "214 hp",
      torque: "124 Nm",
      transmission: "6-speed manual",
      fuel_capacity: "16L"
    },
    created_at: "2024-01-08T14:20:00Z",
    updated_at: "2024-01-19T09:10:00Z"
  },
  {
    id: 6,
    name: "Suzuki GSX-R750",
    description: "Legendary middleweight sportbike with proven performance.",
    pricing: 11999.99,
    original_price: 12999.99,
    discount_percentage: 8,
    brand: "Suzuki",
    category: "Sport Bikes",
    category_id: 1,
    color: "Blue",
    engine_capacity: "750cc",
    fuel_type: "Petrol",
    mileage: "22 kmpl",
    top_speed: "245 km/h",
    weight: "190 kg",
    image: "https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=500",
    images: [
      "https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=500",
      "https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=500",
      "https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=500"
    ],
    stock_quantity: 6,
    is_available: true,
    rating: 4.4,
    review_count: 31,
    specifications: {
      engine: "750cc inline-4",
      power: "148 hp",
      torque: "86 Nm",
      transmission: "6-speed manual",
      fuel_capacity: "17L"
    },
    created_at: "2024-01-12T11:30:00Z",
    updated_at: "2024-01-21T13:25:00Z"
  },
  {
    id: 7,
    name: "Harley-Davidson Street Glide",
    description: "Iconic cruiser with premium comfort and classic Harley style.",
    pricing: 29999.99,
    brand: "Harley-Davidson",
    category: "Cruisers",
    category_id: 2,
    color: "Black",
    engine_capacity: "1746cc",
    fuel_type: "Petrol",
    mileage: "15 kmpl",
    top_speed: "180 km/h",
    weight: "379 kg",
    image: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500",
    images: [
      "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500",
      "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500",
      "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500"
    ],
    stock_quantity: 3,
    is_available: true,
    rating: 4.3,
    review_count: 52,
    badge: {
      text: "Classic",
      icon: "mdi:motorcycle",
      gradient: "linear-gradient(135deg, #434343, #000000)"
    },
    specifications: {
      engine: "1746cc V-Twin",
      power: "96 hp",
      torque: "155 Nm",
      transmission: "6-speed manual",
      fuel_capacity: "22.7L"
    },
    created_at: "2024-01-03T10:15:00Z",
    updated_at: "2024-01-17T15:40:00Z"
  },
  {
    id: 8,
    name: "Triumph Bonneville T120",
    description: "Modern classic with retro styling and contemporary performance.",
    pricing: 13999.99,
    original_price: 14999.99,
    discount_percentage: 7,
    brand: "Triumph",
    category: "Cruisers",
    category_id: 2,
    color: "Silver",
    engine_capacity: "1200cc",
    fuel_type: "Petrol",
    mileage: "21 kmpl",
    top_speed: "190 km/h",
    weight: "236 kg",
    image: "https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=500",
    images: [
      "https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=500",
      "https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=500",
      "https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=500"
    ],
    stock_quantity: 8,
    is_available: true,
    rating: 4.5,
    review_count: 28,
    specifications: {
      engine: "1200cc parallel-twin",
      power: "79 hp",
      torque: "105 Nm",
      transmission: "6-speed manual",
      fuel_capacity: "14.5L"
    },
    created_at: "2024-01-14T13:45:00Z",
    updated_at: "2024-01-23T10:20:00Z"
  }
]

export const mockCategories = [
  {
    id: 1,
    name: "Sport Bikes",
    description: "High-performance motorcycles designed for speed and agility",
    image: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300",
    product_count: 6
  },
  {
    id: 2,
    name: "Cruisers",
    description: "Comfortable motorcycles for long-distance touring",
    image: "https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=300",
    product_count: 2
  }
]

// Mock API response structure
export const createMockResponse = (data, meta = {}) => ({
  data,
  ...meta,
  success: true
})

// Simulate API delay
export const delay = (ms = 500) => new Promise(resolve => setTimeout(resolve, ms))