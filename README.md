# Specijal: Ice Cream Shop

Slastičarna Specijal is a single-page web application for an ice cream shop. 
Customers can browse products, place orders and write reviews. Admins manage products, categories and all orders.

**Status:** Milestone 1 (static frontend, project structure, draft ERD).

## Entities and relationships
- **users**: id, name, email, password, role (admin or customer)
- **categories**: id, name
- **products**: id, name, description, price, stock
- **product_categories**: links products and categories (many-to-many)
- **orders**: belongs to a user, has a status and a total
- **order_items**: links orders and products, with quantity and unit price
- **reviews**: a user rates a product

A user has many orders and many reviews. An order has many order items. A product can be in many categories and have many reviews.

Draft ERD: [docs/erd.png](docs/erd.png)

## Roles
- **Admin**: full CRUD on all entities
- **Customer**: browses products, places and views own orders, writes reviews


## Technology stack
- Frontend: HTML, CSS, JavaScript, Bootstrap 5 (SPA with hash routing)
- Local server: XAMPP

## External API
Planned: weather data on the dashboard, used to suggest flavors.

## Running locally
1. Clone the repository into the XAMPP `htdocs` folder and start Apache.
2. Open `http://localhost/WEB-ice_cream_shop/frontend/`

## Project structure
- `frontend/`: css, js, views (one HTML file per page), assets
- `backend/`: routes, services, dao, middleware, config, logs
- `docs/`: ERD and AI log

