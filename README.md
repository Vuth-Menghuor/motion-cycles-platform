# Bike E-commerce Platform

A full-stack e-commerce platform for bike sales with PostgreSQL database, Vue.js frontend, and Laravel backend API. Features KHQR payment integration for Cambodian market.

## 🏗️ Architecture

- **Backend**: Laravel 11 API with PostgreSQL database
- **Frontend**: Vue.js 3 with Vite, Pinia state management
- **Database**: PostgreSQL with proper relationships
- **Authentication**: Laravel Sanctum
- **Payments**: KHQR API integration
- **Deployment**: Docker containerization ready

## 📁 Project Structure

```
bike-ecommerce-project/
├── backend/              # Laravel API
│   ├── app/             # Application code
│   ├── database/        # Migrations & seeders
│   ├── routes/          # API routes
│   └── config/          # Configuration files
├── frontend/            # Vue.js SPA
│   ├── src/            # Source code
│   ├── public/         # Static assets
│   └── dist/           # Build output
├── docker-compose.yml   # Docker services
├── Dockerfile          # Backend container
├── nginx.conf          # Nginx configuration
└── README.md           # This file
```

## 🚀 Quick Start

### Prerequisites

- **PHP 8.3+** with PostgreSQL extension
- **Node.js 18+** and npm
- **PostgreSQL 13+** database server
- **Composer** (PHP dependency manager)

### 1. Clone and Setup

```bash
git clone <repository-url>
cd bike-ecommerce-project
```

### 2. Backend Setup

```bash
cd backend

# Install PHP dependencies
composer install

# Install Node dependencies (for frontend assets)
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Database Setup

```bash
# Update .env with your PostgreSQL credentials
# Example:
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=bike_ecommerce
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

### 4. Frontend Setup

```bash
cd ../frontend

# Install dependencies
npm install

# Copy environment file
cp .env.example .env

# Update API base URL if needed
# VITE_API_BASE_URL=http://localhost:8000/api
```

### 5. Start Development Servers

```bash
# Terminal 1: Start Laravel backend
cd backend
php artisan serve

# Terminal 2: Start Vue.js frontend
cd frontend
npm run dev
```

### 6. Access the Application

- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000
- **API Documentation**: http://localhost:8000/api/documentation

## 🐳 Docker Deployment (Optional)

For production deployment using Docker:

```bash
# Build and start all services
docker-compose up -d

# Run migrations in container
docker-compose exec backend php artisan migrate
docker-compose exec backend php artisan db:seed
```

## 🔑 Default Credentials

**Admin Account:**

- Email: `admin@gmail.com`
- Password: `admin12`

## 📊 Features

### 🛒 E-commerce Core

- Product catalog with categories
- Shopping cart functionality
- Order management system
- User authentication & profiles

### 💳 Payment Integration

- KHQR payment system
- QR code generation
- Real-time payment verification
- Order status tracking

### 🎨 Frontend Features

- Responsive design (mobile-first)
- Modern UI with Tailwind CSS
- Real-time updates
- Intuitive user experience

### 🔧 Backend Features

- RESTful API design
- PostgreSQL database
- Laravel Sanctum authentication
- Comprehensive error handling

## 🛠️ Development

### Backend Commands

```bash
cd backend

# Run tests
php artisan test

# Generate API documentation
php artisan api:generate

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### Frontend Commands

```bash
cd frontend

# Development server
npm run dev

# Build for production
npm run build

# Lint code
npm run lint
```

## 📚 API Endpoints

### Public Endpoints

- `GET /api/public/products` - Product catalog
- `GET /api/public/categories` - Product categories

### Authentication Required

- `POST /api/auth/login` - User login
- `POST /api/orders` - Create order
- `GET /api/orders` - User orders

### Admin Endpoints

- `POST /api/products` - Create product
- `PATCH /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product

## 🗄️ Database Schema

- **users**: User accounts and authentication
- **categories**: Product categories (Mountain Bike, Road Bike, etc.)
- **products**: Bike inventory with specifications
- **orders**: Customer orders with payment tracking

## 🔒 Security

- Laravel Sanctum for API authentication
- Input validation and sanitization
- CSRF protection
- Secure password hashing
- Rate limiting on API endpoints

## 📈 Performance

- Database query optimization
- Asset bundling and minification
- Lazy loading for components
- Caching strategies implemented

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

## 📞 Support

For support or questions:

- Create an issue in the repository
- Check the documentation in `/backend/README.md` and `/frontend/README.md`
- Review the API documentation at `/api/documentation`
