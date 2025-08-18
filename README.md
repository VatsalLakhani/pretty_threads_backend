# Pretty Threads API (Laravel)

Production-ready REST API for the Pretty Threads Flutter app. Includes authentication, catalog (categories, products), admin endpoints (users, products, categories, payments), media upload, and seeders with dummy data.

## Requirements
- PHP 8.2+
- Composer
- MySQL/MariaDB or SQLite
- Laravel 11.x

## Quick Start
1) Install dependencies
```
composer install
cp .env.example .env
php artisan key:generate
```

2) Configure DB in `.env`
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pretty_threads
DB_USERNAME=root
DB_PASSWORD=secret
APP_URL=http://127.0.0.1:8000
```

3) Migrate and seed
```
php artisan migrate
php artisan db:seed
```
Seeders create:
- Admin user: email `admin@example.com`, password `password` (see `database/seeders/AdminUserSeeder.php`)
- Categories/subcategories and products (including extra dummy data)

Create storage symlink for images (once):
```
php artisan storage:link
```

4) Run API
```
php artisan serve
```

## Auth
- Register: `POST /api/auth/register`
- Login: `POST /api/auth/login`
- Logout: `POST /api/auth/logout` (auth required)

Blocked users cannot log in (403). Admin flag/blocked flag live on `users` table (`is_admin`, `is_blocked`).

## Public Catalog
- Categories (roots or all, include children):
  - `GET /api/categories?only=roots|all&with=children|subcategories|true`
- Products (active only, filters):
  - `GET /api/products?category_id=ID&category_slug=slug&per_page=20`
- One-call full catalog:
  - `GET /api/catalog?only=roots|all&with_children=1&with_products=0|1`
  - When `with_products=1`, each category node (and its children) includes a `products` array. The response also includes a flat `products` list.

## Admin API (auth + admin middleware)
Base path: `/api/admin`

- Users
  - `GET /api/admin/users?search=&per_page=20`
  - `POST /api/admin/users/{id}/block`
  - `POST /api/admin/users/{id}/unblock`

- Categories
  - `GET /api/admin/categories`
  - `POST /api/admin/categories`
  - `PUT /api/admin/categories/{id}`
  - `DELETE /api/admin/categories/{id}`

- Products
  - `GET /api/admin/products?search=&category_id=&per_page=20`
  - `POST /api/admin/products`
  - `PUT /api/admin/products/{id}`
  - `DELETE /api/admin/products/{id}`
  - Image upload (both paths for compatibility):
    - `POST /api/admin/products/{id}/image`
    - `POST /api/products/{id}/image`

- Payments
  - `GET /api/admin/payments?status=&per_page=20`
  - `GET /api/admin/payments/{id}`
  - `PUT /api/admin/payments/{id}/status` (pending|paid|failed|refunded)
  - `POST /api/admin/payments/{id}/refund` (stub, records in `meta.refunds`)

## Notes
- Media upload expects multipart form-data field name `image`.
- Pagination uses Laravel length-aware paginator JSON.
- CORS: configure in `config/cors.php` if consuming from mobile/web hosts.
  - Example allow localhost/dev hosts:
    - `paths`: ["api/*"]
    - `allowed_origins`: ["http://127.0.0.1:8000", "http://10.0.2.2:8000", "http://localhost:3000", "http://localhost:8080"]
  - Set `APP_URL` to your public base URL so generated URLs are correct.

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Laravel API Authentication

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## 🚀 API Authentication System

This project implements a complete JWT-based authentication system for Laravel with the following features:
- User registration with email verification
- User login with token generation
- Protected routes using Laravel Sanctum
- Password reset functionality
- User profile management

### 📋 Requirements
- PHP 8.2 or higher
- Composer
- MySQL 5.7+ or MariaDB 10.3+
- Node.js & NPM (for frontend assets if needed)

### 🛠 Installation

1. **Clone the repository**
   ```bash
   git clone [your-repository-url]
   cd your-project
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install NPM dependencies** (if needed)
   ```bash
   npm install
   ```

4. **Create environment file**
   ```bash
   cp .env.example .env
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Configure database**
   Update your `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

7. **Run migrations**
   ```bash
   php artisan migrate
   ```

8. **Generate JWT secret** (if using JWT)
   ```bash
   php artisan jwt:secret
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

## 🔐 Authentication Endpoints

### Register a New User
```http
POST /api/auth/register
```

**Request Body:**
```json
{
    "full_name": "John Doe",
    "email": "john@example.com",
    "phone_number": "1234567890",
    "password": "password123",
    "password_confirmation": "password123",
    "full_address": "123 Main St",
    "city": "New York",
    "pincode": "10001"
}
```

**Response (Success - 201):**
```json
{
    "status": "success",
    "message": "User registered successfully",
    "user": {
        "full_name": "John Doe",
        "email": "john@example.com",
        "phone_number": "1234567890",
        "full_address": "123 Main St",
        "city": "New York",
        "pincode": "10001",
        "updated_at": "2025-08-14T12:00:00.000000Z",
        "created_at": "2025-08-14T12:00:00.000000Z",
        "id": 1
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz"
}
```

### User Login
```http
POST /api/auth/login
```

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (Success - 200):**
```json
{
    "status": "success",
    "message": "Login successful",
    "user": {
        "id": 1,
        "full_name": "John Doe",
        "email": "john@example.com",
        "phone_number": "1234567890",
        "full_address": "123 Main St",
        "city": "New York",
        "pincode": "10001",
        "email_verified_at": null,
        "created_at": "2025-08-14T12:00:00.000000Z",
        "updated_at": "2025-08-14T12:00:00.000000Z"
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz"
}
```

### User Logout
```http
POST /api/auth/logout
```

**Headers:**
```
Authorization: Bearer your_token_here
Accept: application/json
```

**Response (Success - 200):**
```json
{
    "status": "success",
    "message": "Successfully logged out"
}
```

### Get Authenticated User Profile
```http
GET /api/auth/me
```

**Headers:**
```
Authorization: Bearer your_token_here
Accept: application/json
```

**Response (Success - 200):**
```json
{
    "status": "success",
    "user": {
        "id": 1,
        "full_name": "John Doe",
        "email": "john@example.com",
        "phone_number": "1234567890",
        "full_address": "123 Main St",
        "city": "New York",
        "pincode": "10001",
        "email_verified_at": null,
        "created_at": "2025-08-14T12:00:00.000000Z",
        "updated_at": "2025-08-14T12:00:00.000000Z"
    }
}
```

## 🔧 Testing the API

You can test the API endpoints using tools like Postman, cURL, or any HTTP client:

### Example using cURL:

**Register a new user:**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "full_name": "Test User",
    "email": "test@example.com",
    "phone_number": "1234567890",
    "password": "password123",
    "password_confirmation": "password123",
    "full_address": "123 Test St",
    "city": "Test City",
    "pincode": "123456"
  }'
```

**Login:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

**Get user profile (after login):**
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer your_token_here" \
  -H "Accept: application/json"
```

## 🛡️ Security

- All passwords are hashed using bcrypt
- API routes are protected with Laravel Sanctum authentication
- CSRF protection is enabled for web routes
- Rate limiting is implemented for API endpoints

## 📝 License

This project is open-source and available under the [MIT License](LICENSE).

## 👥 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📧 Contact

For any questions or support, please contact [your-email@example.com](mailto:your-email@example.com)

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
