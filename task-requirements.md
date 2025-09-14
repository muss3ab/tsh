# Advanced Basic E-Commerce Storefront

## 🎯 Role

You are tasked with building a **single-vendor storefront web app**.

The project tests your ability to:

1. Design and implement a Laravel backend API.
2. Build a Vue.js SPA frontend.
3. Handle authentication, state management, data relationships, and validation.
4. Deliver clean, maintainable, and pragmatic code.

## ⚙️ 1. Project Setup

1. Create a new Laravel project.
2. Create a Vue 3 SPA frontend (Vite preferred).
3. Use **MariaDB** for the database.
4. Use **Laravel Sanctum** for authentication.
5. Use **TailwindCSS** for styling.

## 🗄️ 2. Database & Models

Create migrations and Eloquent models for:

1. **User** : Already exists. Add a role column (user, admin).
2. **Category** : name, slug, parent_id (nullable for nested categories).
3. **Product** : name, description, price, image_url, inventory_count, belongs to a Category.
4. **Order** : belongs to User, fields → total_price, status (cart, pending, completed, cancelled),
   shipping_address, shipping_phone.
5. **OrderItem** : belongs to Order + Product, fields → quantity, price (at purchase time).
6. **Wishlist** : pivot table between User and Product.

Relationships to implement:

-   Order::items()
-   OrderItem::product()

-   Product::category()
-   Category::children()
-   User::wishlist()

## 🔗 3. Backend API Requirements

### 3.1 Public Endpoints

-   GET /api/products → List all products. Supports filters:
    o category_id (including nested children).
    o min_price and max_price.
    o search (by product name).
-   GET /api/products/{product} → Show product details.
-   GET /api/categories → Return nested category tree.

### 3.2 Protected Endpoints (Require Authentication)

**Cart Management:**

-   GET /api/cart → Get current cart.
-   POST /api/cart → Add product (product_id, quantity).
    o If product already exists in cart → increment quantity.
-   PATCH /api/cart/{orderItem} → Update quantity.
-   DELETE /api/cart/{orderItem} → Remove item.

**Checkout:**

-   POST /api/checkout → Convert cart to pending/completed.
    o Requires shipping_address and shipping_phone.
    o Reject if any product’s inventory is insufficient.
    o Decrement product inventory after success.

**Wishlist:**

-   GET /api/wishlist → Return wishlist items.
-   POST /api/wishlist → Add product.
-   DELETE /api/wishlist/{product} → Remove product.

**Orders:**

-   GET /api/orders → List all past orders for the user.
-   GET /api/orders/{order} → Show order details (with items).

### 3.3 Admin Endpoints (Require Admin Role)

-   GET /api/admin/products → List all products (with category info).
-   POST /api/admin/products → Create a product.
-   PATCH /api/admin/products/{product} → Update a product.
-   DELETE /api/admin/products/{product} → Delete a product.
-   GET /api/admin/categories → List categories.
-   POST /api/admin/categories → Create a category.
-   PATCH /api/admin/categories/{category} → Update category.
-   DELETE /api/admin/categories/{category} → Delete category.

### 3.4 Backend Requirements

-   **Authorization** :
    o Users → Only manage their own carts/orders.
    o Admin → Full access to CRUD products and categories.
-   **Validation** : Form Requests for all write operations.
-   **API Resources** : Format responses consistently for Products, Categories, Orders.

## 🖥️ 4. Frontend Requirements (Vue.js SPA)

### 4.1 Public Views

-   **Product Listing Page**
    o Grid of products (image, name, price).
    o Filters: category, price range, search box.
    o “Add to Cart” button.
-   **Single Product Page**
    o Show product details.
    o Add to Cart.
    o Add to Wishlist.

### 4.2 Authenticated Views

-   **Cart Page**
    o List cart items.
    o Adjust quantity or remove items.
    o Show subtotal.
    o Checkout form (address + phone).
-   **Wishlist Page**
    o Display wishlist items.

```
o Option: “Move to Cart.”
```

-   **Orders Page**
    o List past orders.
    o Show status + purchased items.
-   **Login & Register Pages**
    o Standard forms using Sanctum.

### 4.3 Admin Views

-   **Admin Dashboard**
    o Accessible only to admin users.
-   **Product Management Page**
    o List all products.
    o Create, edit, and delete products.
    o Assign categories.
-   **Category Management Page**
    o List all categories (nested).
    o Create, edit, and delete categories.

### 4.4 State Management (Pinia)

-   Store **auth state** (with role awareness: user vs admin).
-   Store **cart state** (synced with backend).
-   Store **wishlist state**.
-   Navbar shows cart + wishlist counters reactively.

### 4.5 UI/UX

-   Use TailwindCSS for styling.
-   Notifications for API success/errors.
-   Navbar with links: Products, Cart, Wishlist, Orders.
-   Admin users see extra menu items for managing Products & Categories.

## 📦 5. Deliverables

-   A GitHub repository with:
    o Backend + Frontend code.
    o A **README** with setup instructions.
-   Clean commit history.
-   Code should feel production-like (clear, consistent, documented).
