-- FSPO Ltd Database Schema
-- Run this in phpMyAdmin or MySQL CLI: mysql -u root -p < database.sql

CREATE DATABASE IF NOT EXISTS fspo_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fspo_db;

-- Users table (clients + admins)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('client','admin') DEFAULT 'client',
    avatar VARCHAR(255) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    price DECIMAL(12,2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    featured TINYINT(1) DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Orders
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    payment_method ENUM('mtn_momo','airtel_money','bank_bk','bank_equity','bank_cogebanque','bank_kcb','bank_access','bank_gt','bank_ab') NOT NULL,
    payment_status ENUM('pending','paid','failed') DEFAULT 'pending',
    order_status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    delivery_address TEXT,
    notes TEXT,
    transaction_ref VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Wishlist
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wish (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Cart
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_cart (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Contact messages
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─── SEED DATA ───────────────────────────────────────────────

-- Admin user (password: admin123)
INSERT INTO users (name, email, phone, password, role) VALUES
('FSPO Admin', 'admin@fspoltd.rw', '+250785723677', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Demo client (password: client123)
INSERT INTO users (name, email, phone, password, role) VALUES
('Jean Claude', 'client@gmail.com', '+250788000001', '$2y$10$TKh8H1.PbfQQGT74zq4hU5kX2/Y3Vku8OGBiOQwSVaRjBEtMKf3ey', 'client');

-- Categories
INSERT INTO categories (name, slug, description, image) VALUES
('Building Materials', 'building-materials', 'Cement, gypsum boards, wallpapers and construction materials', 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=400&h=300&fit=crop'),
('Electricals', 'electricals', 'Switches, sockets, cables, circuit breakers and electrical fittings', 'https://images.unsplash.com/photo-1565814329452-e1efa11c5b89?w=400&h=300&fit=crop'),
('Hand Tools', 'hand-tools', 'Hammers, screwdrivers, wrenches, pliers and hand tools', 'https://images.unsplash.com/photo-1530124566582-a618bc2615dc?w=400&h=300&fit=crop'),
('Hardware', 'hardware', 'Door locks, hinges, bolts, screws and hardware accessories', 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=300&fit=crop'),
('Light', 'light', 'LED bulbs, hanging lights, panel lights and lighting solutions', 'https://images.unsplash.com/photo-1524484485831-a92ffc0de03f?w=400&h=300&fit=crop'),
('Plumbing Supplies', 'plumbing-supplies', 'Pipes, taps, valves, water heaters and plumbing fittings', 'https://images.unsplash.com/photo-1585704032915-c3400ca199e7?w=400&h=300&fit=crop');

-- Products
INSERT INTO products (category_id, name, slug, description, price, stock, image, featured) VALUES
(1, 'Gypsum Board 9mm', 'gypsum-board-9mm', 'High quality 9mm gypsum board for interior walls and ceilings. Lightweight and fire-resistant.', 9000, 150, 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=500&h=400&fit=crop', 1),
(1, 'Wallpaper 5.3m x 10m', 'wallpaper-53m-10m', 'Decorative wallpaper roll 5.3m x 10m. Available in various patterns. Easy to apply.', 35000, 80, 'https://images.unsplash.com/photo-1558618047-3d08b8c34b30?w=500&h=400&fit=crop', 0),
(2, 'Switch Wall KIMEI', 'switch-wall-kimei', 'Durable wall switch, KIMEI brand. Single gang. Weather resistant.', 2000, 300, 'https://images.unsplash.com/photo-1565814329452-e1efa11c5b89?w=500&h=400&fit=crop', 0),
(2, 'Doorbell Push Button', 'doorbell-push-button', 'Waterproof doorbell push button. Easy installation. Compatible with most doorbell systems.', 4000, 120, 'https://images.unsplash.com/photo-1558618047-3d08b8c34b30?w=500&h=400&fit=crop', 0),
(3, 'Combination Pliers 8"', 'combination-pliers-8', 'Heavy duty combination pliers 8 inch. Chrome vanadium steel. Comfortable grip handles.', 8500, 90, 'https://images.unsplash.com/photo-1530124566582-a618bc2615dc?w=500&h=400&fit=crop', 1),
(3, 'Claw Hammer 500g', 'claw-hammer-500g', 'Professional claw hammer 500g. Fibreglass handle for shock absorption. Balanced weight.', 7000, 110, 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=500&h=400&fit=crop', 0),
(4, 'Parka Door Lock Complete', 'parka-door-lock-complete', 'Complete door lock set, Parka brand. Includes handle, latch and all fittings. Durable brass finish.', 30000, 60, 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500&h=400&fit=crop', 1),
(4, 'Union PPR 3/4', 'union-ppr-3-4', 'PPR union fitting 3/4 inch. High pressure rated. For hot and cold water systems.', 700, 500, 'https://images.unsplash.com/photo-1585704032915-c3400ca199e7?w=500&h=400&fit=crop', 0),
(5, 'LED Panel Light 9W', 'led-panel-light-9w', 'Energy saving LED panel light 9W. Cool white 6000K. Square recessed ceiling mount.', 8000, 200, 'https://images.unsplash.com/photo-1524484485831-a92ffc0de03f?w=500&h=400&fit=crop', 1),
(5, 'Hanging Light 3-Bulb Brown Glass', 'hanging-light-3-bulb', 'Elegant round hanging chandelier with brown glass shades. Fits 3 bulbs (E27). Diameter 45cm.', 45000, 25, 'https://images.unsplash.com/photo-1524484485831-a92ffc0de03f?w=500&h=400&fit=crop', 1),
(5, 'Tronic LED Bulb 5W', 'tronic-led-bulb-5w', 'Energy efficient LED bulb 5W. E27 base. Warm white 3000K. 50,000 hour lifespan.', 1500, 400, 'https://images.unsplash.com/photo-1565814329452-e1efa11c5b89?w=500&h=400&fit=crop', 0),
(6, 'Water Tap Sink', 'water-tap-sink', 'Stainless steel kitchen sink tap. Single lever mixer. 360° swivel spout. Easy installation.', 15000, 75, 'https://images.unsplash.com/photo-1585704032915-c3400ca199e7?w=500&h=400&fit=crop', 1),
(6, 'Water Tap SANWA 3/4', 'water-tap-sanwa-34', 'SANWA brand gate valve tap, 3/4 inch. Durable brass body. For main water supply lines.', 5000, 180, 'https://images.unsplash.com/photo-1585704032915-c3400ca199e7?w=500&h=400&fit=crop', 0),
(6, 'Water Tap BOSINI 3/4', 'water-tap-bosini-34', 'BOSINI brand water tap, 3/4 inch. Chrome plated. Suitable for outdoor and indoor use.', 8000, 140, 'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?w=500&h=400&fit=crop', 0),
(6, 'Water Heater 130L', 'water-heater-130l', 'Electric water heater 130 litre capacity. Energy saving mode. Safety thermostat. 5 year warranty.', 130000, 15, 'https://images.unsplash.com/photo-1558618047-3d08b8c34b30?w=500&h=400&fit=crop', 1),
(6, 'Water Meter PRC', 'water-meter-prc', 'PRC brand cold water meter. Class B accuracy. DN15 (1/2 inch) connection. RURA approved.', 15000, 50, 'https://images.unsplash.com/photo-1585704032915-c3400ca199e7?w=500&h=400&fit=crop', 0),
(6, 'AOMEIKANG Bathroom Faucet Rectangular', 'aomeikang-bathroom-faucet', 'Modern rectangular bathroom sink faucet. Single hole mount. Chrome finish. Ceramic disc cartridge.', 35000, 40, 'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?w=500&h=400&fit=crop', 1),
(6, 'Angle Valve FARLX', 'angle-valve-farlx', 'FARLX brand angle valve 1/2 inch. Chrome plated brass. For toilet cistern and under-sink connections.', 4000, 200, 'https://images.unsplash.com/photo-1585704032915-c3400ca199e7?w=500&h=400&fit=crop', 0),
(6, 'Urinal Bowl Ceramic MILLANO', 'urinal-bowl-millano', 'MILLANO ceramic wall-hung urinal bowl. White gloss finish. Includes fixing kit.', 45000, 20, 'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?w=500&h=400&fit=crop', 0),
(6, 'Water Proof Lamp 2Ft', 'waterproof-lamp-2ft', 'IP65 waterproof fluorescent lamp fitting 2 foot. For wet areas, car parks and outdoor use.', 12000, 85, 'https://images.unsplash.com/photo-1524484485831-a92ffc0de03f?w=500&h=400&fit=crop', 0);
