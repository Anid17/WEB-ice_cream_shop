# Specijal: Ice Cream Shop

Slastičarna Specijal is a single-page web application for an ice cream shop.
Customers can browse products, place orders and write reviews. Admins manage products,
categories, users and all orders. The backend is a REST API (PHP, FlightPHP, MySQL) protected
with JWT authentication and role-based access; the frontend is a single-page application
that talks to the API with AJAX.

**Status:** Milestone 4 (authentication, authorization, middleware, logging, connected frontend).

## Entities and relationships
- **users**: id, name, email, password (bcrypt hash), role (admin or customer)
- **categories**: id, name
- **products**: id, name, description, price, stock
- **product_categories**: links products and categories (many-to-many)
- **orders**: belongs to a user, has a status and a total
- **order_items**: links orders and products, with quantity and unit price
- **reviews**: a user rates a product

A user has many orders and many reviews. An order has many order items. A product can be in many
categories and have many reviews.

ERD: [docs/erd.png](docs/erd.png)

## Roles and test accounts
| Role | Account | Password |
|---|---|---|
| Admin | admin@specijal.ba | Admin123! |
| Customer | ali@example.com | Customer123! |

New users who register always get the role **customer**.

| Resource | Customer | Admin |
|---|---|---|
| categories, products, product-categories | read | read, create, edit, delete |
| users | only own profile (`/auth/me`) | everything, but not their own account |
| orders | create and read own orders | all orders; only admins change a status or delete |
| order-items | manage items of own open orders | everything |
| reviews | read all, write as themselves, edit and delete own | everything (moderation) |
| weather | read | read |

## Authentication and authorization
- Passwords are stored as bcrypt hashes (`password_hash`).
- `POST /auth/login` and `POST /auth/register` are public and return `{user, token}`.
- The token is a JWT (HS256) that expires after 24 hours. The client sends it in the header
  `Authorization: Bearer <token>`.
- `AuthMiddleware` checks the token on every protected route (401 if missing, invalid or expired).
- `RoleMiddleware` checks the role (403 if the role is not allowed). Ownership of an object is
  checked in the service (`check_owner()`, 403).
- The frontend stores the token, sends it with every AJAX request, redirects to the login page on 401,
  shows a message on 403 and shows menu items according to the role.

## Middleware and logging
- `RequestLogMiddleware`: writes one line per request to `backend/logs/info.log` (method, URL, status,
  user, role, time).
- `ErrorMiddleware`: turns every exception into a JSON answer without technical details.
- Info log: requests, logins, registrations and external API calls.
- Error log: unhandled exceptions, database errors, failed logins, rejected requests (401/403),
  unknown URLs (404) and external API failures.
- Log files are in `backend/logs/` and are not committed.

## External API
The dashboard shows a flavor tip based on the current temperature, which comes from the Open-Meteo API
(no API key) through `GET /weather`. The call has a timeout; if the API is down, the endpoint answers
503 with a friendly message, the failure is written to the error log and the rest of the application
keeps working.

## Technology stack
- Frontend: HTML, CSS, JavaScript, Bootstrap 5, jQuery, Toastr (SPA with hash routing)
- Backend: PHP, FlightPHP, PDO, firebase/php-jwt, swagger-php (OpenAPI)
- Database: MySQL
- Local server: XAMPP

## Running locally
1. Clone the repository into the XAMPP `htdocs` folder and start Apache and MySQL.
2. In the `backend` folder run `composer install`.
3. Import `database/specijal.sql` in phpMyAdmin (it creates the `specijal` database).
4. Copy `backend/config/config.example.php` to `backend/config/config.php` and set your MySQL
   password and your own `JWT_SECRET` (at least 32 random characters, for example the output of
   `php -r "echo bin2hex(random_bytes(32));"`). `config.php` is not committed.
5. Open `http://localhost/WEB-ice_cream_shop/frontend/` and log in with a test account.
6. Swagger documentation: `http://localhost/WEB-ice_cream_shop/backend/public/v1/docs/`
   (log in with `POST /auth/login`, click **Authorize** and paste the token without the word `Bearer`).

## Endpoints
`categories`, `products`, `product-categories`, `users`, `orders`, `order-items` and `reviews` each
have GET (list and one), POST, PUT and DELETE. Extras: `GET /products?search=` and `?category_id=`,
`GET /products/{id}/reviews`, `GET /orders?user_id=`, `GET /orders/{id}/items`, `GET /weather`,
`POST /auth/register`, `POST /auth/login`, `GET /auth/me`, `PUT /auth/me`.
The full list with the 401/403 answers is in Swagger.

## Project structure
- `frontend/`: css, js (router), views (one HTML file per page), utils (constants, REST client),
  services (AJAX calls), assets
- `backend/`: `rest/` (dao, services, routes), middleware, utils, config, logs, `public/v1/docs` (Swagger)
- `database/`: `specijal.sql` (schema and sample data)
- `docs/`: ERD and AI log

## Deployed application
The link will be added in Milestone 5.