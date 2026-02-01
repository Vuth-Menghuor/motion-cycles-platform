export const staticBikes = [
  {
    id: 1,
    name: 'Cannondale Trail Neo 2 Electric Mountain Bike',
    title: 'Cannondale Trail Neo 2 Electric Mountain Bike',
    description:
      'A powerful and versatile electric mountain bike built for off-road adventure. Equipped with a 500Wh Bosch battery, advanced aluminum frame, and smooth suspension for ultimate comfort.',
    pricing: 2599.99,
    price: 2599.99,
    category_id: 1,
    brand: 'Cannondale',
    color: 'Forest Green',
    quality: 'Premium',
    rating: 4.7,
    review_count: 180,
    discount: {
      type: 'percent',
      value: 10,
      code: 'CANNON10',
    },
    specs: [
      { label: 'Range', text: '100km', icon: 'mdi:battery' },
      { label: 'Motor', text: '500W Bosch Drive Unit', icon: 'mdi:engine' },
      { label: 'Weight', text: '22.8kg', icon: 'mdi:scale-bathroom' },
    ],
    image: '/images/mount_1.png',
    additionalImages: [
      '/images/mount_2.png',
      '/images/mount_2_alt1.png',
      '/images/mount_2_alt2.png',
      '/images/mount_2_alt3.png',
      '/images/mount_2_alt4.png',
      '/images/mount_2_alt5.png',
      '/images/mount_2_alt6.png',
      '/images/mount_2_alt7.png',
      '/images/mount_2_alt8.png',
      '/images/mount_3.png'
    ],
    quantity: 10,
  },
  {
    id: 2,
    name: 'Bianchi Aria E-Road Carbon Electric Bike',
    title: 'Bianchi Aria E-Road Carbon Electric Bike',
    description:
      'A sleek, aerodynamic electric road bike combining performance and style. Designed with a lightweight carbon frame and integrated battery system for smooth long-distance rides.',
    pricing: 2999.99,
    price: 2999.99,
    category_id: 2,
    brand: 'Bianchi',
    color: 'Celeste Blue',
    quality: 'Premium',
    rating: 4.9,
    review_count: 310,
    discount: {
      type: 'percent',
      value: 15,
      code: 'BIANCHI15'
    },
    specs: [
      { label: 'Range', text: '120km', icon: 'mdi:battery' },
      { label: 'Motor', text: '250W Ebikemotion X35+', icon: 'mdi:engine' },
      { label: 'Weight', text: '11.8kg', icon: 'mdi:scale-bathroom' }
    ],
    image: '/images/road_1.png',
    additionalImages: [
      '/images/road_2.png',
      '/images/road_3.png',
      '/images/trekking_1.png',
      '/images/trekking_2.png',
      '/images/mount_2.png',
      '/images/mount_2_alt1.png',
      '/images/mount_2_alt2.png',
      '/images/mount_2_alt3.png'
    ],
    quantity: 8
  },
  {
    id: 3,
    name: 'Giant Explore E+ 3 Electric Trekking Bike',
    title: 'Giant Explore E+ 3 Electric Trekking Bike',
    description: 'Built for comfort and endurance, the Giant Explore E+ 3 offers smooth power delivery, strong battery life, and an ergonomic design perfect for city rides or weekend adventures.',
    pricing: 2250.00,
    price: 2250.00,
    category_id: 3,
    brand: 'Giant',
    color: 'Matte Black',
    quality: 'Standard',
    rating: 4.5,
    review_count: 95,
    discount: null,
    specs: [
      { label: 'Range', text: '80km', icon: 'mdi:battery' },
      { label: 'Motor', text: '250W Giant SyncDrive', icon: 'mdi:engine' },
      { label: 'Weight', text: '24.5kg', icon: 'mdi:scale-bathroom' }
    ],
    image: '/images/trekking_1.png',
    additionalImages: [
      '/images/trekking_2.png',
      '/images/road_1.png',
      '/images/road_2.png',
      '/images/mount_2_alt5.png',
      '/images/mount_2_alt6.png',
      '/images/mount_2_alt7.png',
      '/images/mount_2_alt8.png',
      '/images/mount_1.png'
    ],
    quantity: 15
  },
  {
    id: 4,
    name: 'Trek Slash 9.8 XT Carbon Mountain Bike',
    title: 'Trek Slash 9.8 XT Carbon Mountain Bike',
    description: 'The ultimate trail bike with 150mm of rear travel and 160mm up front. Built for aggressive riding with carbon frame and top-tier components.',
    pricing: 6299.99,
    price: 6299.99,
    category_id: 1,
    brand: 'Trek',
    color: 'Matte Black',
    quality: 'Premium',
    rating: 4.8,
    review_count: 245,
    discount: {
      type: 'percent',
      value: 5,
      code: 'TREK5',
    },
    specs: [
      { label: 'Travel', text: '150mm / 160mm', icon: 'mdi:bike' },
      { label: 'Wheels', text: '29" Bontrager Line Comp', icon: 'mdi:wheel' },
      { label: 'Weight', text: '31.2 lb (14.1 kg)', icon: 'mdi:scale-bathroom' },
    ],
    image: '/images/trek_slash.png',
    additionalImages: [
      '/images/trek_slash_alt1.png',
      '/images/trek_slash_alt2.png',
      '/images/mount_1.png',
      '/images/mount_2_alt7.png',
      '/images/mount_2_alt8.png',
      '/images/mount_2.png',
      '/images/mount_2_alt1.png',
      '/images/mount_2_alt2.png',
      '/images/mount_3.png'
    ],
    quantity: 5
  },
  {
    id: 5,
    name: 'Specialized Turbo Levo Expert Electric Mountain Bike',
    title: 'Specialized Turbo Levo Expert Electric Mountain Bike',
    description: 'The benchmark in e-MTB performance with 150mm of travel, custom motor tune, and premium components. Perfect for riders who demand the best.',
    pricing: 8999.99,
    price: 8999.99,
    category_id: 1,
    brand: 'Specialized',
    color: 'Satin Carbon / Black',
    quality: 'Premium',
    rating: 4.9,
    review_count: 420,
    discount: {
      type: 'fixed',
      value: 500,
      code: 'SPECIALIZED500'
    },
    specs: [
      { label: 'Range', text: '130km', icon: 'mdi:battery' },
      { label: 'Motor', text: '625W Specialized Turbo', icon: 'mdi:engine' },
      { label: 'Weight', text: '21.8kg', icon: 'mdi:scale-bathroom' }
    ],
    image: '/images/specialized_levo.png',
    additionalImages: [
      '/images/specialized_levo_alt1.png',
      '/images/specialized_levo_alt2.png',
      '/images/mount_3.png',
      '/images/trek_slash.png',
      '/images/trekking_1.png',
      '/images/mount_2_alt3.png',
      '/images/mount_2_alt4.png',
      '/images/mount_2_alt5.png',
      '/images/mount_2_alt6.png'
    ],
    quantity: 3
  },
  {
    id: 6,
    name: 'Cube Kathmandu Hybrid City Bike',
    title: 'Cube Kathmandu Hybrid City Bike',
    description: 'A versatile hybrid bike designed for urban commuting and leisure rides. Features comfortable geometry, reliable components, and practical accessories.',
    pricing: 899.99,
    price: 899.99,
    category_id: 4,
    brand: 'Cube',
    color: 'Urban Grey',
    quality: 'Standard',
    rating: 4.2,
    review_count: 67,
    discount: null,
    specs: [
      { label: 'Frame', text: 'Aluminum 6061', icon: 'mdi:bike' },
      { label: 'Gears', text: 'Shimano Altus 8-speed', icon: 'mdi:cog' },
      { label: 'Weight', text: '13.2kg', icon: 'mdi:scale-bathroom' }
    ],
    image: '/images/cube_kathmandu.png',
    additionalImages: [
      '/images/cube_kathmandu_alt1.png',
      '/images/road_1.png',
      '/images/road_2.png',
      '/images/trekking_2.png',
      '/images/mount_2_alt3.png',
      '/images/mount_2.png',
      '/images/mount_2_alt1.png',
      '/images/mount_2_alt2.png',
      '/images/mount_1.png'
    ],
    quantity: 20
  }
];

export const staticCategories = [
  { id: 1, name: 'Mountain Bike', description: 'Off-road and trail riding bikes' },
  { id: 2, name: 'Road Bike', description: 'High-performance road cycling' },
  { id: 3, name: 'Trekking Bike', description: 'Comfortable bikes for mixed terrain' },
  { id: 4, name: 'Electric Bike', description: 'Pedal-assist and electric bikes' },
];

export const staticBrands = [
  'Cannondale',
  'Bianchi',
  'Giant',
  'Trek',
  'Specialized',
  'Cube',
];

export const staticColors = [
  'Forest Green',
  'Celeste Blue',
  'Matte Black',
  'Satin Carbon / Black',
  'Urban Grey',
];