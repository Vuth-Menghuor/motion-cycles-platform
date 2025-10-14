# Bike E-commerce API

A Laravel-based REST API for a bike e-commerce platform with PostgreSQL database and KHQR payment integration.

## Features

-   **Product Management**: CRUD operations for bikes and accessories
-   **Category Management**: Organize products by categories (Mountain Bike, Road Bike, etc.)
-   **User Authentication**: Sanctum-based authentication system
-   **Order Management**: Complete order lifecycle with payment processing
-   **KHQR Payment Integration**: Cambodian payment system integration
-   **PostgreSQL Database**: Robust database with proper relationships

## Tech Stack

-   **Laravel 11**: PHP framework
-   **PostgreSQL**: Database
-   **Sanctum**: API authentication
-   **KHQR API**: Payment processing

## Installation

1. **Clone the repository**

    ```bash
    git clone <repository-url>
    cd bike-ecommerce-project/backend
    ```

2. **Install dependencies**

    ```bash
    composer install
    npm install
    ```

3. **Environment setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Database setup**

    - Ensure PostgreSQL is running
    - Update `.env` with your database credentials
    - Run migrations and seeders:

    ```bash
    php artisan migrate
    php artisan db:seed
    ```

5. **Start the server**
    ```bash
    php artisan serve
    ```

## API Endpoints

### Public Endpoints

-   `GET /api/public/products` - Get all products
-   `GET /api/public/products/{id}` - Get product details
-   `GET /api/public/categories` - Get all categories

### Authentication

-   `POST /api/auth/register` - User registration
-   `POST /api/auth/login` - User login
-   `POST /api/auth/logout` - User logout (authenticated)

### Protected Endpoints (Require Authentication)

-   `GET /api/products` - Get all products (admin)
-   `POST /api/products` - Create product
-   `GET /api/products/{id}` - Get product details
-   `PATCH /api/products/{id}` - Update product
-   `DELETE /api/products/{id}` - Delete product

-   `GET /api/categories` - Get all categories
-   `POST /api/categories` - Create category
-   `GET /api/categories/{id}` - Get category details
-   `PATCH /api/categories/{id}` - Update category
-   `DELETE /api/categories/{id}` - Delete category

### Orders & Payments

-   `POST /api/orders` - Create order
-   `GET /api/orders` - List user orders
-   `GET /api/orders/{id}` - Get order details
-   `POST /api/orders/{id}/check-payment` - Check payment status
-   `POST /api/orders/{id}/confirm-payment` - Confirm payment

### KHQR Integration

-   `POST /api/khqr/individual` - Generate individual QR
-   `POST /api/khqr/merchant` - Generate merchant QR
-   `POST /api/khqr/check-payment-status` - Check payment status

## Database Schema

-   **users**: User accounts with authentication
-   **categories**: Product categories (Mountain Bike, Road Bike)
-   **products**: Bike products with specifications
-   **orders**: Customer orders with payment tracking
-   **personal_access_tokens**: API authentication tokens

## Default Admin Account

-   **Email**: admin@gmail.com
-   **Password**: admin12

## Development

```bash
# Run tests
php artisan test

# Run linter
./vendor/bin/phpcs

# Generate API documentation
php artisan api:generate
```

## License

This project is licensed under the MIT License.
