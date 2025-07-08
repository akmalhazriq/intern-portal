<?php
include('include/dbconn.php');

// Check if announcement table exists
$result = mysqli_query($dbconn, "SHOW TABLES LIKE 'announcement'");
if (mysqli_num_rows($result) > 0) {
    echo "Announcement table exists\n";
    
    // Show table structure
    $structure = mysqli_query($dbconn, "DESCRIBE announcement");
    echo "Table structure:\n";
    while ($row = mysqli_fetch_assoc($structure)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "Announcement table does not exist. Creating it...\n";
    
    $create_table = "CREATE TABLE announcement (
        announcement_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        sender_type ENUM('admin', 'company') NOT NULL,
        sender_id INT NOT NULL,
        sender_name VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_active TINYINT(1) DEFAULT 1
    )";
    
    if (mysqli_query($dbconn, $create_table)) {
        echo "Announcement table created successfully\n";
    } else {
        echo "Error creating table: " . mysqli_error($dbconn) . "\n";
    }
}

mysqli_close($dbconn);
?>
