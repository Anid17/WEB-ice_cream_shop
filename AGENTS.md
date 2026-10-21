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
- backend/: routes/, services/, dao/, middleware/, config/, logs/
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

## Conventions
- PHP classes: PascalCase (UserService, UserDao). Files are named after the class.
- JavaScript and PHP variables: camelCase. Database tables and columns: snake_case.

## Rules for AI assistants
- Do not add new libraries or frameworks without asking me first.
- Do not change the database schema without asking me first.
- Never put passwords, keys or secrets in code; they belong in backend/config/config.php, which is not committed. Update config.example.php instead.
- Do not edit files in backend/logs/.
- Do only what the milestone asks. Do not add features early (for example the external API before Milestone 3).
- Make small changes and explain what you changed and why.  