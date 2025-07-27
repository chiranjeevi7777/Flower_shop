-- Create the database and switch to it
CREATE DATABASE IF NOT EXISTS flower_shop;
USE flower_shop;

-- Users Table: Stores customer information
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer','admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin Table: Stores administrator credentials
CREATE TABLE Admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Flowers Table: Stores flower product details
CREATE TABLE Flowers (
    flower_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    color VARCHAR(50),
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table: Records orders placed by users
CREATE TABLE Orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_address VARCHAR(255),
    payment_method VARCHAR(50),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending','completed','canceled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- Order_Items Table: Contains details of each item within an order
CREATE TABLE Order_Items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    flower_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id),
    FOREIGN KEY (flower_id) REFERENCES Flowers(flower_id)
);

-- Cart Table: Stores temporary cart data for each user (persistent cart)
CREATE TABLE Cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    flower_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (flower_id) REFERENCES Flowers(flower_id),
    UNIQUE (user_id, flower_id)
);
---------------------------------------------------------------------------------------
--Insertion data

INSERT INTO Flowers (name, color, price, description, image_path) VALUES
('Rose', 'Red', 10.99, 'A symbol of love and passion.', 'images/rose.jpg'),
('Tulip', 'Yellow', 8.99, 'Bright and cheerful, ideal for spring arrangements.', 'images/tulip.jpg'),
('Orchid', 'Purple', 15.99, 'Elegant and exotic, representing beauty and luxury.', 'images/orchid.jpg'),
('Lily', 'White', 12.50, 'A pure and graceful flower, perfect for weddings.', 'images/lily.jpg'),
('Sunflower', 'Yellow', 9.50, 'Symbolizes happiness and positivity.', 'images/sunflower.jpg'),
('Daffodil', 'Yellow', 7.99, 'Represents new beginnings and prosperity.', 'images/daffodil.jpg'),
('Daisy', 'White', 6.99, 'A simple yet charming flower representing innocence.', 'images/daisy.jpg'),
('Peony', 'Pink', 14.99, 'Known for its lush petals and romantic symbolism.', 'images/peony.jpg'),
('Carnation', 'Pink', 8.25, 'Represents admiration and gratitude.', 'images/carnation.jpg'),
('Marigold', 'Orange', 5.99, 'Bright and vibrant, often used in celebrations.', 'images/marigold.jpg'),
('Hydrangea', 'Blue', 13.99, 'Large, beautiful clusters of blooms.', 'images/hydrangea.jpg'),
('Jasmine', 'White', 6.50, 'Fragrant and delicate, often used in perfumes.', 'images/jasmine.jpg'),
('Lotus', 'Pink', 11.99, 'Symbol of purity and spiritual enlightenment.', 'images/lotus.jpg'),
('Chrysanthemum', 'Red', 9.75, 'Represents longevity and joy.', 'images/chrysanthemum.jpg'),
('Bluebell', 'Blue', 7.50, 'Symbolizes gratitude and everlasting love.', 'images/bluebell.jpg'),
('Freesia', 'White', 11.50, 'Fragrant and delicate freesia.', 'images/freesia.jpg'),
('Gardenia', 'White', 13.75, 'Elegant gardenia with a strong aroma.', 'images/gardenia.jpg'),
('Snapdragon', 'Pink', 9.25, 'Vibrant snapdragon, perfect for bouquets.', 'images/snapdragon.jpg'),
('Gerbera Daisy', 'Yellow', 8.75, 'Bright and cheerful gerbera daisy.', 'images/gerbera_daisy.jpg'),
('Zinnia', 'Orange', 7.50, 'Colorful zinnia with long-lasting blooms.', 'images/zinnia.jpg'),
('Anemone', 'Blue', 10.00, 'Delicate anemone with striking petals.', 'images/anemone.jpg'),
('Iris', 'Purple', 12.00, 'Elegant iris with deep purple hues.', 'images/iris.jpg'),
('Poppy', 'Red', 8.00, 'Vivid poppy with a bold color.', 'images/poppy.jpg'),
('Gladiolus', 'Red', 10.50, 'Tall gladiolus with vibrant red spikes.', 'images/gladiolus.jpg'),
('Camellia', 'Pink', 14.00, 'Lush camellia with glossy leaves.', 'images/camellia.jpg'),
('Azalea', 'Pink', 9.50, 'Soft and delicate azalea.', 'images/azalea.jpg'),
('Begonia', 'Red', 7.00, 'Unique begonia with beautiful foliage.', 'images/begonia.jpg'),
('Dahlia', 'Purple', 11.25, 'Intricate dahlia with layered petals.', 'images/dahlia.jpg'),
('Delphinium', 'Blue', 13.50, 'Tall, graceful delphinium.', 'images/delphinium.jpg'),
('Fuchsia', 'Purple', 12.75, 'Striking fuchsia with vibrant hues.', 'images/fuchsia.jpg'),
('Hyacinth', 'Blue', 10.00, 'Fragrant hyacinth ideal for spring arrangements.', 'images/hyacinth.jpg'),
('Magnolia', 'White', 15.00, 'Elegant magnolia with large, fragrant blooms.', 'images/magnolia.jpg'),
('Mimosa', 'Yellow', 9.00, 'Soft mimosa with feathery foliage.', 'images/mimosa.jpg'),
('Petunia', 'Purple', 6.50, 'Colorful petunia ideal for garden beds.', 'images/petunia.jpg'),
('Ranunculus', 'Orange', 10.25, 'Delicate ranunculus with layered petals.', 'images/ranunculus.jpg'),
('Statice', 'Purple', 8.25, 'Vibrant statice for dried bouquets.', 'images/statice.jpg'),
('Verbena', 'Red', 7.75, 'Low-growing verbena with bright clusters.', 'images/verbena.jpg'),
('Violet', 'Blue', 5.50, 'Delicate violet with soft petals.', 'images/violet.jpg'),
('Wisteria', 'Purple', 16.00, 'Graceful wisteria with cascading blossoms.', 'images/wisteria.jpg'),
('Amaryllis', 'Red', 14.50, 'Showy amaryllis with bold, trumpet-shaped blooms.', 'images/amaryllis.jpg'),
('Bells of Ireland', 'Green', 11.00, 'Unique green spires of Bells of Ireland.', 'images/bells_of_ireland.jpg'),
('Bird of Paradise', 'Orange', 20.00, 'Exotic bird of paradise with striking appearance.', 'images/bird_of_paradise.jpg'),
('Bougainvillea', 'Magenta', 9.00, 'Vibrant bougainvillea with a cascade of colorful bracts.', 'images/bougainvillea.jpg'),
('Clematis', 'Blue', 13.00, 'Elegant clematis perfect for trellises.', 'images/clematis.jpg'),
('Cosmos', 'Pink', 7.25, 'Light and airy cosmos with soft petals.', 'images/cosmos.jpg'),
('Echinacea', 'Purple', 10.00, 'Robust echinacea ideal for attracting pollinators.', 'images/echinacea.jpg'),
('Flax', 'Blue', 8.00, 'Subtle blue flax for garden accents.', 'images/flax.jpg'),
('Forget-me-not', 'Blue', 6.75, 'Tiny forget-me-not flowers symbolizing true love.', 'images/forget_me_not.jpg'),
('Geranium', 'Red', 7.80, 'Hardy geranium with vibrant red flowers.', 'images/geranium.jpg'),
('Hibiscus', 'Red', 12.50, 'Tropical hibiscus with large, colorful blooms.', 'images/hibiscus.jpg'),
('Kalanchoe', 'Orange', 8.50, 'Succulent kalanchoe with bright, clustered flowers.', 'images/kalanchoe.jpg'),
('Lavender', 'Purple', 9.25, 'Fragrant lavender ideal for relaxation and aromatherapy.', 'images/lavender.jpg'),
('Liatris', 'Purple', 10.00, 'Tall liatris with spiky, clustered blooms.', 'images/liatris.jpg'),
('Lisianthus', 'Pink', 11.75, 'Elegant lisianthus with rose-like petals.', 'images/lisianthus.jpg'),
('Lobelia', 'Blue', 7.00, 'Delicate lobelia with small, vibrant flowers.', 'images/lobelia.jpg'),
('Maranta', 'Green', 8.00, 'Decorative maranta with patterned leaves.', 'images/maranta.jpg'),
('Ornamental Onion', 'Purple', 9.50, 'Unique ornamental onion with striking flowers.', 'images/ornamental_onion.jpg'),
('Phlox', 'Pink', 10.00, 'Clustered phlox with a sweet fragrance.', 'images/phlox.jpg'),
('Primrose', 'Yellow', 7.25, 'Bright primrose with early spring blooms.', 'images/primrose.jpg'),
('Queen Anne\'s Lace', 'White', 6.50, 'Delicate queen Anne\'s lace with lacy petals.', 'images/queen_annes_lace.jpg'),
('Scabiosa', 'Blue', 8.25, 'Scabiosa with a unique pincushion shape.', 'images/scabiosa.jpg'),
('Sedum', 'Green', 5.75, 'Succulent sedum, great for rock gardens.', 'images/sedum.jpg'),
('Snapdragon Yellow', 'Yellow', 8.00, 'Vibrant yellow snapdragon perfect for mixed bouquets.', 'images/snapdragon_yellow.jpg'),
('Statice Pink', 'Pink', 7.50, 'Bright statice ideal for dried arrangements.', 'images/statice_pink.jpg'),
('Yarrow', 'White', 6.25, 'Hardy yarrow with clusters of small flowers.', 'images/yarrow.jpg'),
('Sweet Pea', 'Pink', 8.75, 'Delicate sweet pea with a lovely fragrance.', 'images/sweet_pea.jpg'),
('Coreopsis', 'Yellow', 7.00, 'Cheerful coreopsis with bright, daisy-like blooms.', 'images/coreopsis.jpg');
