// Mock data for products in the Motion Cycles Platform
// This data can be used for frontend development and testing

export const mockCategories = [
  {
    id: 1,
    name: "Sports Bikes",
    description: "High-performance motorcycles designed for speed and agility"
  },
  {
    id: 2,
    name: "Cruiser Bikes",
    description: "Comfortable motorcycles for long-distance cruising"
  },
  {
    id: 3,
    name: "Adventure Bikes",
    description: "Off-road capable motorcycles for adventure riding"
  },
  {
    id: 4,
    name: "Electric Bikes",
    description: "Eco-friendly electric motorcycles"
  },
  {
    id: 5,
    name: "Scooters",
    description: "Lightweight and fuel-efficient scooters"
  }
];

export const mockProducts = [
  {
    id: 1,
    title: "Yamaha R1",
    subtitle: "Ultimate supersport motorcycle with cutting-edge technology",
    price: 18500.00,
    brand: "Yamaha",
    color: "Blue",
    quality: "Premium",
    rating: 4.8,
    reviewCount: 156,
    badge: ["New", "Best Seller"],
    discount: [
      {
        type: "percentage",
        value: 10,
        description: "10% off for first-time buyers"
      }
    ],
    specs: {
      engine: "998cc inline-4",
      power: "200 hp",
      torque: "113 Nm",
      weight: "199 kg",
      topSpeed: "299 km/h"
    },
    image: "/images/products/yamaha-r1-main.jpg",
    additionalImages: [
      "/images/products/yamaha-r1-1.jpg",
      "/images/products/yamaha-r1-2.jpg",
      "/images/products/yamaha-r1-3.jpg"
    ],
    category: mockCategories[0],
    description: "The Yamaha R1 is a masterpiece of engineering, featuring advanced electronics, aerodynamic design, and unmatched performance. Perfect for track days and spirited road riding.",
    quantity: 5,
    created_at: "2024-01-15T10:00:00Z",
    updated_at: "2024-01-20T14:30:00Z"
  },
  {
    id: 2,
    title: "Harley-Davidson Fat Boy",
    subtitle: "Iconic cruiser with powerful V-twin engine",
    price: 19500.00,
    brand: "Harley-Davidson",
    color: "Black",
    quality: "Premium",
    rating: 4.6,
    reviewCount: 203,
    badge: ["Classic", "Popular"],
    discount: [],
    specs: {
      engine: "1746cc V-twin",
      power: "90 hp",
      torque: "155 Nm",
      weight: "304 kg",
      topSpeed: "180 km/h"
    },
    image: "/images/products/harley-fatboy-main.jpg",
    additionalImages: [
      "/images/products/harley-fatboy-1.jpg",
      "/images/products/harley-fatboy-2.jpg"
    ],
    category: mockCategories[1],
    description: "The Harley-Davidson Fat Boy combines classic styling with modern performance. Its Milwaukee-Eight engine delivers smooth power for an unforgettable riding experience.",
    quantity: 3,
    created_at: "2024-01-10T09:00:00Z",
    updated_at: "2024-01-18T16:45:00Z"
  },
  {
    id: 3,
    title: "BMW R 1250 GS Adventure",
    subtitle: "Ultimate adventure motorcycle for global exploration",
    price: 22500.00,
    brand: "BMW",
    color: "White",
    quality: "Premium",
    rating: 4.9,
    reviewCount: 89,
    badge: ["Adventure", "Touring"],
    discount: [
      {
        type: "fixed",
        value: 500,
        description: "Save $500 on accessories package"
      }
    ],
    specs: {
      engine: "1254cc boxer twin",
      power: "136 hp",
      torque: "143 Nm",
      weight: "268 kg",
      topSpeed: "200 km/h"
    },
    image: "/images/products/bmw-gs-adventure-main.jpg",
    additionalImages: [
      "/images/products/bmw-gs-adventure-1.jpg",
      "/images/products/bmw-gs-adventure-2.jpg",
      "/images/products/bmw-gs-adventure-3.jpg"
    ],
    category: mockCategories[2],
    description: "Built for the world's most demanding adventures, the BMW R 1250 GS Adventure features enhanced suspension, larger fuel tank, and comprehensive electronics for any terrain.",
    quantity: 7,
    created_at: "2024-01-12T11:30:00Z",
    updated_at: "2024-01-22T13:15:00Z"
  },
  {
    id: 4,
    title: "Zero S ZF14.4",
    subtitle: "High-performance electric motorcycle",
    price: 19500.00,
    brand: "Zero Motorcycles",
    color: "Red",
    quality: "Premium",
    rating: 4.4,
    reviewCount: 67,
    badge: ["Electric", "Eco-Friendly"],
    discount: [
      {
        type: "percentage",
        value: 15,
        description: "15% off with government rebate"
      }
    ],
    specs: {
      motor: "ZF14.4 brushless",
      power: "110 hp",
      torque: "190 Nm",
      range: "257 km",
      topSpeed: "201 km/h"
    },
    image: "/images/products/zero-s-main.jpg",
    additionalImages: [
      "/images/products/zero-s-1.jpg",
      "/images/products/zero-s-2.jpg"
    ],
    category: mockCategories[3],
    description: "The Zero S electric motorcycle delivers instant torque and zero-emission performance. With advanced battery technology and regenerative braking, it's the future of motorcycling.",
    quantity: 4,
    created_at: "2024-01-08T14:20:00Z",
    updated_at: "2024-01-19T10:00:00Z"
  },
  {
    id: 5,
    title: "Honda PCX 160",
    subtitle: "Reliable and efficient automatic scooter",
    price: 4500.00,
    brand: "Honda",
    color: "White",
    quality: "Standard",
    rating: 4.2,
    reviewCount: 312,
    badge: ["Best Seller", "Fuel Efficient"],
    discount: [],
    specs: {
      engine: "156.9cc single-cylinder",
      power: "15.8 hp",
      torque: "14.7 Nm",
      weight: "131 kg",
      fuelEconomy: "45 km/l"
    },
    image: "/images/products/honda-pcx-main.jpg",
    additionalImages: [
      "/images/products/honda-pcx-1.jpg",
      "/images/products/honda-pcx-2.jpg"
    ],
    category: mockCategories[4],
    description: "The Honda PCX 160 offers comfortable riding, excellent fuel economy, and Honda's legendary reliability. Perfect for daily commuting in urban environments.",
    quantity: 12,
    created_at: "2024-01-05T08:45:00Z",
    updated_at: "2024-01-17T12:30:00Z"
  },
  {
    id: 6,
    title: "Kawasaki Ninja ZX-10R",
    subtitle: "Champion-winning superbike with race-proven technology",
    price: 17500.00,
    brand: "Kawasaki",
    color: "Green",
    quality: "Premium",
    rating: 4.7,
    reviewCount: 134,
    badge: ["Racing", "Performance"],
    discount: [
      {
        type: "percentage",
        value: 5,
        description: "5% off for track day packages"
      }
    ],
    specs: {
      engine: "998cc inline-4",
      power: "203 hp",
      torque: "114 Nm",
      weight: "206 kg",
      topSpeed: "299 km/h"
    },
    image: "/images/products/kawasaki-ninja-main.jpg",
    additionalImages: [
      "/images/products/kawasaki-ninja-1.jpg",
      "/images/products/kawasaki-ninja-2.jpg",
      "/images/products/kawasaki-ninja-3.jpg"
    ],
    category: mockCategories[0],
    description: "The Kawasaki Ninja ZX-10R is a multiple World Superbike champion, featuring advanced aerodynamics, electronics, and engine performance that rivals the best in the world.",
    quantity: 6,
    created_at: "2024-01-14T13:00:00Z",
    updated_at: "2024-01-21T15:45:00Z"
  },
  {
    id: 7,
    title: "Ducati Multistrada V4",
    subtitle: "Versatile adventure bike with superbike DNA",
    price: 24500.00,
    brand: "Ducati",
    color: "Red",
    quality: "Premium",
    rating: 4.8,
    reviewCount: 98,
    badge: ["Adventure", "Versatile"],
    discount: [],
    specs: {
      engine: "1158cc V4",
      power: "170 hp",
      torque: "125 Nm",
      weight: "240 kg",
      topSpeed: "250 km/h"
    },
    image: "/images/products/ducati-multistrada-main.jpg",
    additionalImages: [
      "/images/products/ducati-multistrada-1.jpg",
      "/images/products/ducati-multistrada-2.jpg"
    ],
    category: mockCategories[2],
    description: "The Ducati Multistrada V4 combines the performance of a superbike with the versatility of an adventure motorcycle. Four riding modes adapt to any terrain or condition.",
    quantity: 3,
    created_at: "2024-01-11T10:15:00Z",
    updated_at: "2024-01-23T11:20:00Z"
  },
  {
    id: 8,
    title: "Vespa Primavera 150",
    subtitle: "Iconic Italian scooter with retro styling",
    price: 5800.00,
    brand: "Vespa",
    color: "Yellow",
    quality: "Premium",
    rating: 4.3,
    reviewCount: 187,
    badge: ["Retro", "Stylish"],
    discount: [
      {
        type: "fixed",
        value: 200,
        description: "Save $200 on customization"
      }
    ],
    specs: {
      engine: "155cc single-cylinder",
      power: "13 hp",
      torque: "12 Nm",
      weight: "114 kg",
      fuelEconomy: "40 km/l"
    },
    image: "/images/products/vespa-primavera-main.jpg",
    additionalImages: [
      "/images/products/vespa-primavera-1.jpg",
      "/images/products/vespa-primavera-2.jpg"
    ],
    category: mockCategories[4],
    description: "The Vespa Primavera combines timeless Italian design with modern technology. Its automatic transmission and comfortable riding position make it perfect for city exploration.",
    quantity: 8,
    created_at: "2024-01-07T12:00:00Z",
    updated_at: "2024-01-16T14:10:00Z"
  }
];

// Helper functions for working with mock data
export const getProductById = (id) => {
  return mockProducts.find(product => product.id === parseInt(id));
};

export const getProductsByCategory = (categoryId) => {
  return mockProducts.filter(product => product.category.id === parseInt(categoryId));
};

export const getProductsByBrand = (brand) => {
  return mockProducts.filter(product => product.brand.toLowerCase() === brand.toLowerCase());
};

export const searchProducts = (query) => {
  const searchTerm = query.toLowerCase();
  return mockProducts.filter(product =>
    product.title.toLowerCase().includes(searchTerm) ||
    product.subtitle.toLowerCase().includes(searchTerm) ||
    product.brand.toLowerCase().includes(searchTerm) ||
    product.description.toLowerCase().includes(searchTerm)
  );
};

export const getFeaturedProducts = () => {
  return mockProducts.filter(product => product.badge.includes("Best Seller"));
};

export const getDiscountedProducts = () => {
  return mockProducts.filter(product => product.discount.length > 0);
};
