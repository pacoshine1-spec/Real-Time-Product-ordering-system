CREATE DATABASE IF NOT EXISTS ordering_system;
USE ordering_system;

DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('customer','admin','staff') DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT,
  category VARCHAR(80) DEFAULT 'Meals',
  price DECIMAL(10,2) NOT NULL,
  image VARCHAR(255) DEFAULT NULL,
  stock INT DEFAULT 0,
  status ENUM('available','unavailable') DEFAULT 'available',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  status ENUM('pending','preparing','ready','completed','cancelled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password, role) VALUES
('System Admin', 'admin@example.com', '$2y$12$qPN6NE2pfyrNT9pBfz73VeVVJ5ja3YQFgLZfeRvEJoBbRPmv69xqm', 'admin'),
('Staff User', 'staff@example.com', '$2y$12$U6BickDxNwEeIGpdiSQf2.ctZHwlL52NfQ9y.W3mIfbEq/GTR4YBe', 'staff'),
('Demo Customer', 'customer@example.com', '$2y$12$Rze59mTKk8.S3Plax0tZwu6tUFoazeYV43Bvp/ajrnC0XUcVBsGYC', 'customer');

INSERT INTO products (name, description, category, price, image, stock, status) VALUES
('Signature Burger', 'Juicy beef patty, cheddar, fresh lettuce, tomatoes, and house burger sauce.', 'Burgers', 119.00, 'signature-burger.png', 50, 'available'),
('Cheesy Bacon Burger', 'Smoky bacon strips, double cheese, pickles, and caramelized onions.', 'Burgers', 149.00, 'bacon-burger.png', 35, 'available'),
('Crispy Fries', 'Golden potato fries served hot and lightly salted.', 'Sides', 55.00, 'crispy-fries.png', 80, 'available'),
('Loaded Nachos', 'Crunchy nachos topped with cheese sauce, salsa, and seasoned meat.', 'Sides', 129.00, 'loaded-nachos.png', 30, 'available'),
('Chicken Rice Meal', 'Crispy fried chicken served with steamed rice and gravy.', 'Meals', 109.00, 'chicken-rice.png', 45, 'available'),
('Spicy Chicken Wings', 'Six-piece wings tossed in a sweet and spicy glaze.', 'Meals', 159.00, 'chicken-wings.png', 28, 'available'),
('Creamy Carbonara', 'Pasta in rich cream sauce with ham, mushrooms, and parmesan.', 'Pasta', 139.00, 'carbonara.png', 25, 'available'),
('Spaghetti Supreme', 'Filipino-style spaghetti with savory meat sauce and cheese.', 'Pasta', 99.00, 'spaghetti.png', 40, 'available'),
('Iced Tea', 'Refreshing house-blend iced tea served chilled.', 'Drinks', 39.00, 'iced-tea.png', 100, 'available'),
('Blue Lemonade', 'Cool citrus lemonade with a bright blue twist.', 'Drinks', 49.00, 'blue-lemonade.png', 75, 'available'),
('Chocolate Milkshake', 'Thick chocolate shake topped with whipped cream.', 'Drinks', 89.00, 'choco-milkshake.png', 32, 'available'),
('Classic Sundae', 'Vanilla soft serve with chocolate syrup and cookie crumbs.', 'Desserts', 69.00, 'sundae.png', 38, 'available'),
('Classic Hotdog', 'Soft bun with juicy hotdog, ketchup, mustard, and fresh toppings.', 'Snacks', 59.00, 'classic-hotdog.png', 45, 'available'),
('Beef Shawarma', 'Warm pita wrap filled with beef strips, vegetables, and garlic sauce.', 'Wraps', 119.00, 'beef-shawarma.png', 35, 'available'),
('Club Sandwich', 'Triple-layer sandwich with chicken, egg, lettuce, tomato, and mayo.', 'Sandwiches', 129.00, 'club-sandwich.png', 30, 'available'),
('Tuna Sandwich', 'Creamy tuna spread with crisp lettuce on toasted bread.', 'Sandwiches', 89.00, 'tuna-sandwich.png', 42, 'available'),
('Garlic Pepper Beef', 'Tender beef slices in garlic pepper sauce served with rice.', 'Meals', 149.00, 'garlic-pepper-beef.png', 28, 'available'),
('Pork Sisig Rice Meal', 'Sizzling pork sisig served with rice and calamansi.', 'Meals', 139.00, 'pork-sisig.png', 33, 'available'),
('Halo-Halo Special', 'Classic Filipino shaved ice dessert with milk, toppings, and ube.', 'Desserts', 99.00, 'halo-halo.png', 40, 'available'),
('Mango Graham Cup', 'Layers of mango, cream, and graham crumbs in a chilled cup.', 'Desserts', 79.00, 'mango-graham.png', 36, 'available'),
('Strawberry Smoothie', 'Cold blended strawberry drink with creamy finish.', 'Drinks', 95.00, 'strawberry-smoothie.png', 44, 'available'),
('Caramel Frappe', 'Iced blended coffee with caramel drizzle and whipped topping.', 'Drinks', 109.00, 'caramel-frappe.png', 34, 'available');
