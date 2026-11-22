# AGENTS.md

## Project
Specijal: a web application for an ice cream shop. Customers browse products, place orders and write reviews. Admins manage products, categories and all orders.
Roles: admin, customer.
Entities: users, categories, products, product_categories, orders, order_items, reviews.

## Technology stack
- Frontend: HTML, CSS, plain JavaScript (fetch), Bootstrap 5, single-page application (SPA) with a hash router
- Backend: PHP with FlightPHP (REST API), PDO, MySQL
- Local server: XAMPP (installed in C:\xampp2)

## Folder structure
- frontend/: css/, js/, views/ (one HTML file per view), assets/
- backend/: index.php, .htaccess, config/, middleware/, logs/, utils/, rest/ (routes/, services/, dao/), public/v1/docs/ (Swagger)
- database/: specijal.sql (schema and sample data)
- docs/: AI_LOG.md, erd.png and other documentation

## Frontend rules
- Each view is in its own file in frontend/views/ and is loaded once by js/app.js.
- Routes are registered in the `routes` object in js/app.js.
- Use Bootstrap 5 classes; avoid inline styles. Custom styles go in css/style.css.
- Theme: orange (#ff8a3d), baby blue (#cdeafe), cream background. Custom classes use the prefix "specijal".

## Frontend rules (Milestone 4)
- Requests to the API go only through RestClient (utils/rest-client.js), called from the service files in frontend/services/. Views contain no AJAX code.
- Every view ends with `<script>NameService.init();</script>`. app.js inserts views with jQuery .html(), so this script runs.
- The JWT is stored in localStorage as "user_token" and sent as `Authorization: Bearer <token>`. A 401 sends the user to #login, a 403 shows a Toastr message.
- Role-based UI uses the CSS classes guest-only, auth-only and admin-only (set in UserService.refreshNav). Hiding something in the UI is not security; the backend decides.
- No hard-coded data in views: everything comes from the API.

## Backend rules
- Requests go: route -> service -> DAO -> database.
- Only services call DAO classes. Routes never call DAO classes directly.
- All SQL uses PDO prepared statements.
- Routes return JSON with correct HTTP status codes.

## Backend rules (Milestone 3)
- Routes only read the request, call a service and return JSON. No SQL, no business rules, no try/catch.
- Services hold the rules and are the only layer that calls DAOs. A broken rule throws ValidationException (400) or NotFoundException (404); an external API failure throws ExternalServiceException (503).
- Errors are turned into JSON by the one error handler in index.php.
- When a route changes, update its Paths class in public/v1/docs/paths/ in the same commit.
- The external API (Open-Meteo weather) is called only from WeatherService. Its settings are in backend/config/config.php.

## Backend rules (Milestone 4)
- Public routes are only GET /, POST /auth/login and POST /auth/register. All other routes are in the protected group in index.php, where AuthMiddleware checks the token (401).
- Roles are checked by RoleMiddleware on the route (403), compared with the constants in utils/Roles.php, never with typed strings. Ownership is checked in the service with check_owner() (403).
- The user id and the role come from the token (Flight::get('user')), never from the request body. Registration always creates a customer.
- Never read the Authorization header inside a route; AuthMiddleware does it.
- Logging only through Logger::info() and Logger::error(). RequestLogMiddleware logs every request, ErrorMiddleware turns every exception into JSON without technical details and writes errors to error.log. Never log a password or a token.
- The JWT secret (at least 32 characters) is in backend/config/config.php, which is not committed.


## Conventions
- PHP classes: PascalCase (UserService, UserDao). Files are named after the class.
- JavaScript and PHP variables: camelCase. Database tables and columns: snake_case.

## Rules for AI assistants
- Do not add new libraries or frameworks without asking me first.
- Do not change the database schema without asking me first.
- Never put passwords, keys or secrets in code; they belong in backend/config/config.php.
- Do not edit files in backend/logs/.
- Make small changes and explain what you changed and why.  

## Database and DAO rules
- Every table has an id INT AUTO_INCREMENT primary key.
- Each DAO extends BaseDao and lists its writable columns (not id or created_at).
- SQL only inside DAO classes, always with PDO prepared statements.
- UserDao does not select the password hash except in get_by_email() for login.
- Database settings come from backend/config/config.php (not committed).