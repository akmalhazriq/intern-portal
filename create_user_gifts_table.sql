-- Table for tracking which users have received gifts
CREATE TABLE IF NOT EXISTS user_gift (
  user_gift_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  gift_id INT NOT NULL,
  claimed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  seen TINYINT(1) NOT NULL DEFAULT 0,
  FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
  FOREIGN KEY (gift_id) REFERENCES gift(gift_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
