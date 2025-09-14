# E-Commerce Storefront

A complete single-vendor e-commerce platform built with Laravel 11 (PHP backend) and Vue.js 3 + TypeScript (frontend SPA).

## Features

### 🛒 Core E-Commerce Features

-   **Product Catalog**: Browse products with categories, search, and filtering
-   **Shopping Cart**: Add/remove items, quantity management, persistent cart
-   **Wishlist**: Save favorite products for later
-   **User Authentication**: Registration, login, logout with Laravel Sanctum
-   **Order Management**: Complete checkout process, order history
-   **Admin Panel**: Full CRUD operations for products and categories

### 🏗️ Technical Stack

-   **Backend**: Laravel 11, PHP 8.2+, MySQL/MariaDB
-   **Frontend**: Vue.js 3, TypeScript, Vite, TailwindCSS
-   **State Management**: Pinia stores for cart, wishlist, auth
-   **API**: RESTful API with Laravel Sanctum authentication
-   **UI**: Responsive design with TailwindCSS

## Quick Start

### Prerequisites

-   PHP 8.2 or higher
-   Composer
-   Node.js 18+ and npm
-   MySQL/MariaDB database

### Backend Setup

1. **Clone and Install Dependencies**

    ```bash
    git clone <repository-url>
    cd TSH
    composer install
    ```

2. **Environment Configuration**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    Update `.env` with your database credentials:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=ecommerce_store
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```

3. **Database Setup**

    ```bash
    php artisan migrate
    php artisan db:seed  # Optional: seed with sample data
    ```

4. **Start Laravel Server**
    ```bash
    php artisan serve
    ```

### Frontend Setup

1. **Navigate to Frontend Directory**

    ```bash
    cd frontend-vue
    npm install
    ```

2. **Environment Configuration**
   Create `.env` file:

    ```env
    VITE_API_BASE_URL=http://localhost:8000/api
    ```

3. **Start Development Server**

    ```bash
    npm run dev
    ```

4. **Build for Production**
    ```bash
    npm run build
    ```

## API Endpoints

### Public Endpoints

-   `GET /api/products` - List all products
-   `GET /api/products/{id}` - Get product details
-   `GET /api/categories` - List all categories

### Authenticated Endpoints (Require Bearer Token)

-   `GET /api/cart` - Get user's cart
-   `POST /api/cart` - Add item to cart
-   `PUT /api/cart/{id}` - Update cart item
-   `DELETE /api/cart/{id}` - Remove cart item
-   `GET /api/wishlist` - Get user's wishlist
-   `POST /api/wishlist` - Add product to wishlist
-   `DELETE /api/wishlist/{id}` - Remove from wishlist
-   `GET /api/orders` - Get user's orders
-   `POST /api/orders` - Create new order
-   `GET /api/orders/{id}` - Get order details

### Admin Endpoints (Require Admin Bearer Token)

-   `GET /api/admin/products` - List all products (admin)
-   `POST /api/admin/products` - Create product
-   `PUT /api/admin/products/{id}` - Update product
-   `DELETE /api/admin/products/{id}` - Delete product
-   `GET /api/admin/categories` - List all categories (admin)
-   `POST /api/admin/categories` - Create category
-   `PUT /api/admin/categories/{id}` - Update category
-   `DELETE /api/admin/categories/{id}` - Delete category

## Database Schema

### Tables

-   **users**: User accounts with role (user/admin)
-   **categories**: Product categories with nested structure
-   **products**: Product catalog with category relationships
-   **orders**: Customer orders
-   **order_items**: Individual items within orders
-   **wishlists**: User wishlists (pivot table)
-   **carts**: Shopping cart items (session-based)

### Key Relationships

-   Products belong to Categories
-   Categories can have parent/child relationships
-   Orders have many OrderItems
-   OrderItems belong to Products
-   Users have Wishlists and Carts

## Authentication

The application uses Laravel Sanctum for API authentication:

1. **Registration**: `POST /api/register`
2. **Login**: `POST /api/login` (returns Bearer token)
3. **Logout**: `POST /api/logout` (requires Bearer token)

Store the token in localStorage and include in Authorization header for authenticated requests.

## Admin Access

To create an admin user, manually update a user's role in the database:

```sql
UPDATE users SET role = 'admin' WHERE email = 'admin@example.com';
```

Admin users can access `/admin/products` and `/admin/categories` routes.

## Example Users

You can use the following example users for testing:

| Email             | Password | Role  |
| ----------------- | -------- | ----- |
| admin@example.com | password | admin |
| user@example.com  | password | user  |

## Development

### Running Tests

```bash
# Backend tests
php artisan test

# Frontend tests (if implemented)
cd frontend-vue
npm run test
```

### Code Quality

```bash
# Laravel code style
./vendor/bin/pint

# Frontend linting
cd frontend-vue
npm run lint
```

### API Documentation

Use tools like Postman or Insomnia to test API endpoints. Import the provided collection file if available.

## Deployment

### Backend Deployment

1. Set up production environment variables
2. Run migrations: `php artisan migrate --force`
3. Set proper file permissions
4. Configure web server (Apache/Nginx) to serve Laravel

### Frontend Deployment

1. Build the frontend: `npm run build`
2. Serve static files from `dist/` directory
3. Configure reverse proxy to serve API calls to Laravel backend

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For issues and questions, please create an issue in the repository or contact the development team.
