-- Specijal ice cream shop
CREATE DATABASE IF NOT EXISTS specijal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE specijal;

DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS product_categories;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       VARCHAR(20)  NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_users_role CHECK (role IN ('admin', 'customer'))
);

CREATE TABLE categories (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE products (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    description TEXT          NULL,
    price       DECIMAL(8,2)  NOT NULL,
    stock       INT           NOT NULL DEFAULT 0,
    CONSTRAINT chk_products_price CHECK (price >= 0),
    CONSTRAINT chk_products_stock CHECK (stock >= 0)
);

CREATE TABLE product_categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    product_id  INT NOT NULL,
    category_id INT NOT NULL,
    CONSTRAINT uq_product_category UNIQUE (product_id, category_id),
    CONSTRAINT fk_pc_product  FOREIGN KEY (product_id)  REFERENCES products(id)   ON DELETE CASCADE,
    CONSTRAINT fk_pc_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT           NOT NULL,
    status       VARCHAR(20)   NOT NULL DEFAULT 'pending',
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT chk_orders_status CHECK (status IN ('pending', 'preparing', 'delivered', 'cancelled'))
);

CREATE TABLE order_items (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    order_id   INT          NOT NULL,
    product_id INT          NOT NULL,
    quantity   INT          NOT NULL,
    unit_price DECIMAL(8,2) NOT NULL,
    CONSTRAINT fk_items_order   FOREIGN KEY (order_id)   REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(id),
    CONSTRAINT chk_items_quantity CHECK (quantity > 0)
);

CREATE TABLE reviews (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT       NOT NULL,
    product_id INT       NOT NULL,
    rating     INT       NOT NULL,
    comment    TEXT      NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_user    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    CONSTRAINT fk_reviews_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 5)
);

-- Sample data. Passwords are bcrypt hashes: admin = Admin123!, customers = Customer123!
INSERT INTO users (name, email, password, role) VALUES
('Anid Specijal', 'admin@specijal.ba', '$2y$10$JjRbh2xLU2uMKsJQldDk.u4RKLc5gCBw6w3zE1sQQKYLZ07WuOKMC', 'admin'),
('Ali ',    'ali@example.com',  '$2y$10$NVrj9sOjQlWr2Tbn3L7c/OOYp.iYq91AtLbJTQyaxMmLm1e1sTULe', 'customer'),
('Anid A ',    'anid@example.com', '$2y$10$NVrj9sOjQlWr2Tbn3L7c/OOYp.iYq91AtLbJTQyaxMmLm1e1sTULe', 'customer');

INSERT INTO categories (name) VALUES ('Classic'), ('Sorbet'), ('Vegan'), ('Sundae');

INSERT INTO products (name, description, price, stock) VALUES
('Vanilla Bean',      'Creamy vanilla made with real beans.',   2.50, 40),
('Strawberry Sorbet', 'Fresh strawberries, no dairy.',          2.80, 25),
('Chocolate Fudge',   'Rich chocolate with fudge ribbons.',     3.00, 30),
('Banana Split',      'Three scoops with banana and cream.',    5.50, 15);

INSERT INTO product_categories (product_id, category_id) VALUES
(1, 1), (2, 2), (2, 3), (3, 1), (4, 4);

INSERT INTO orders (user_id, status, total_amount) VALUES
(2, 'delivered', 8.00),
(3, 'pending',   8.40);

INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES
(1, 1, 2, 2.50),
(1, 3, 1, 3.00),
(2, 2, 3, 2.80);

INSERT INTO reviews (user_id, product_id, rating, comment) VALUES
(2, 1, 5, 'The best vanilla I have ever had.'),
(3, 2, 4, 'Very fresh, a bit too sweet.'),
(2, 3, 5, 'Perfect chocolate flavor.');