-- Table for storing gift information
CREATE TABLE IF NOT EXISTS gift (
  gift_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  image VARCHAR(255) NOT NULL,
  quantity INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert some sample gifts
INSERT INTO gift (name, image, quantity) VALUES
('T-Shirt', 'tshirt.jpg', 50),
('Coffee Mug', 'mug.jpg', 30),
('Notebook', 'notebook.jpg', 100),
('Pen Drive', 'pendrive.jpg', 25),
('Water Bottle', 'bottle.jpg', 40);
