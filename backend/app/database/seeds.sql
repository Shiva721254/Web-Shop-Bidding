USE developmentdb;

-- Admin user (password: admin123)
-- Customer user (password: customer123)
INSERT INTO users (name, email, password, role) VALUES
    ('Admin User',      'admin@shop.com',    '$2y$12$eImiTXuWVxfM37uY4JANjQ==hashed_placeholder_admin',    'admin'),
    ('Shiva Lamichhane','customer@shop.com', '$2y$12$eImiTXuWVxfM37uY4JANjQ==hashed_placeholder_customer', 'customer');

-- Products: mix of buy_now and auction
INSERT INTO products (title, description, category, type, price, starting_price, seller_id) VALUES
    ('Vintage Mechanical Keyboard', 'A restored mechanical keyboard with original keycaps and a tested USB adapter.', 'Electronics', 'auction', NULL, 45.00, 1),
    ('Road Bike With Aluminum Frame', 'A lightweight road bike in good condition. Includes bottle cages and spare inner tubes.', 'Sports', 'auction', NULL, 180.00, 1),
    ('Handmade Oak Coffee Table', 'Solid oak coffee table made by a local woodworker. Minor signs of use.', 'Home', 'auction', NULL, 90.00, 1),
    ('Wireless Noise-Cancelling Headphones', 'Premium headphones with 30-hour battery life and foldable design.', 'Electronics', 'buy_now', 129.99, NULL, 1),
    ('Leather Laptop Bag', 'Genuine leather bag fits up to 15-inch laptops. Multiple compartments.', 'Accessories', 'buy_now', 79.99, NULL, 1);

-- Auctions for the auction-type products
INSERT INTO auctions (product_id, current_bid, ends_at, status) VALUES
    (1, 72.50,  '2026-06-08 18:00:00', 'open'),
    (2, 245.00, '2026-06-10 20:30:00', 'open'),
    (3, 110.00, '2026-06-12 19:15:00', 'open');
