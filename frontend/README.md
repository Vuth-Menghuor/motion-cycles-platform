# Bike E-commerce Frontend

A modern Vue.js 3 frontend for a bike e-commerce platform with responsive design and seamless user experience.

## Features

- **Product Catalog**: Browse bikes and accessories with filtering and search
- **User Authentication**: Login/register with secure token management
- **Shopping Cart**: Add/remove products with quantity management
- **Order Management**: View order history and track payments
- **KHQR Payment Integration**: Cambodian payment system with QR code generation
- **Responsive Design**: Mobile-first design with Tailwind CSS
- **Real-time Updates**: Live payment status checking

## Tech Stack

- **Vue.js 3**: Progressive JavaScript framework
- **Vite**: Fast build tool and development server
- **Pinia**: State management for Vue.js
- **Vue Router**: Official router for Vue.js
- **Tailwind CSS**: Utility-first CSS framework
- **Axios**: HTTP client for API requests
- **QRCode.js**: QR code generation library

## Installation

1. **Clone the repository**

   ```bash
   git clone <repository-url>
   cd bike-ecommerce-project/frontend
   ```

2. **Install dependencies**

   ```bash
   npm install
   ```

3. **Environment setup**

   ```bash
   cp .env.example .env
   # Update API_BASE_URL to point to your backend
   ```

4. **Start development server**
   ```bash
   npm run dev
   ```

## Project Structure

```
src/
├── assets/          # Static assets (images, styles)
├── components/      # Reusable Vue components
├── layouts/         # Layout components
├── router/          # Vue Router configuration
├── services/        # API service layer
├── stores/          # Pinia state management
├── views/           # Page components
├── App.vue          # Root component
└── main.js          # Application entry point
```

## Key Components

### Pages

- **Home**: Product catalog with featured bikes
- **Product Details**: Individual product view with specifications
- **Cart**: Shopping cart management
- **Checkout**: Order placement and payment
- **Orders**: Order history and tracking
- **Login/Register**: User authentication

### Stores (Pinia)

- **auth**: User authentication state
- **cart**: Shopping cart management
- **products**: Product catalog state
- **orders**: Order management

### Services

- **api.js**: Axios configuration and interceptors
- **auth.js**: Authentication API calls
- **products.js**: Product-related API calls
- **orders.js**: Order management API calls

## Development

```bash
# Development server
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview

# Lint code
npm run lint

# Fix linting issues
npm run lint:fix
```

## Environment Variables

Create a `.env` file in the frontend directory:

```env
VITE_API_BASE_URL=http://localhost:8000/api
VITE_APP_NAME="Bike E-commerce"
```

## API Integration

The frontend communicates with the Laravel backend API:

- **Base URL**: Configured via `VITE_API_BASE_URL`
- **Authentication**: Bearer token stored in localStorage
- **Error Handling**: Global error interceptors for API responses

## Payment Flow

1. **Add to Cart**: Users browse and add products to cart
2. **Checkout**: Review order and select payment method
3. **KHQR Generation**: Generate QR code for payment
4. **Payment Verification**: Real-time payment status checking
5. **Order Confirmation**: Automatic order status updates

## Responsive Design

- **Mobile-first**: Optimized for mobile devices
- **Tablet**: Adaptive layout for tablets
- **Desktop**: Full-featured desktop experience
- **Accessibility**: WCAG compliant design

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Contributing

1. Follow Vue.js style guide
2. Use ESLint configuration
3. Write meaningful commit messages
4. Test on multiple devices/browsers

## License

This project is licensed under the MIT License.
