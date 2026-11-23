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

## Conventions
- PHP classes: PascalCase (UserService, UserDao). Files are named after the class.
- JavaScript and PHP variables: camelCase. Database tables and columns: snake_case.

## Rules for AI assistants
- Do not add new libraries or frameworks without asking me first.
- Do not change the database schema without asking me first.
- Never put passwords, keys or secrets in code; they belong in backend/config/config.php.
- Do not edit files in backend/logs/.
- Do only what the milestone asks. Do not add features early
- Make small changes and explain what you changed and why.  

## Database and DAO rules
- Every table has an id INT AUTO_INCREMENT primary key.
- Each DAO extends BaseDao and lists its writable columns (not id or created_at).
- SQL only inside DAO classes, always with PDO prepared statements.
- UserDao does not select the password hash except in get_by_email() for login.
- Database settings come from backend/config/config.php (not committed).