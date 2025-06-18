<?php
// Database configuration
$host = '127.0.0.1';
$dbname = 'server_db';
$username = 'root';
$password = '';

try {
    // Create connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL to create machines table
    $createTableSQL = "CREATE TABLE IF NOT EXISTS `machines` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `hostel_block` int(11) NOT NULL DEFAULT 1,
        `name` varchar(50) NOT NULL,
        `status` enum('available','in_use','paused','ready_to_collect') DEFAULT 'available',
        `timer_end` datetime DEFAULT NULL,
        `duration` int(11) DEFAULT 30,
        `paused_time` datetime DEFAULT NULL,
        `remaining_time` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    // Execute table creation
    $conn->exec($createTableSQL);
    echo "Table 'machines' created successfully.<br>";

    // Insert sample data
    $insertDataSQL = "INSERT INTO `machines` 
        (`id`, `hostel_block`, `name`, `status`, `timer_end`, `duration`, `paused_time`, `remaining_time`) 
        VALUES 
        (1, 1, 'HB1 Washer 1', 'available', NULL, 60, NULL, NULL),
        (2, 1, 'HB1 Washer 2', 'available', NULL, 45, NULL, NULL),
        (3, 1, 'HB1 Dryer 1', 'available', NULL, 2, NULL, NULL),
        (4, 1, 'HB1 Dryer 2', 'available', NULL, 60, NULL, NULL),
        (6, 1, 'HB1 Premium Elite Pro Washer', 'available', NULL, 60, NULL, NULL),
        (7, 2, 'HB2 Washer 1', 'in_use', '2025-05-15 05:07:08', 45, NULL, NULL),
        (8, 2, 'HB2 Dryer 1', 'in_use', '2025-05-14 22:05:40', 45, NULL, NULL),
        (9, 3, 'HB3 Washer 1', 'in_use', '2025-05-15 05:22:22', 60, NULL, NULL),
        (10, 3, 'HB3 Dryer 1', 'in_use', '2025-05-15 05:03:50', 5, NULL, NULL),
        (11, 4, 'HB4 Washer 1', 'available', NULL, 2, NULL, NULL),
        (12, 4, 'HB4 Dryer 1', 'in_use', '2025-05-15 04:28:43', 5, NULL, NULL)";

    // Execute data insertion
    $conn->exec($insertDataSQL);
    echo "Sample data inserted successfully.<br>";

    // Set auto-increment value
    $alterSQL = "ALTER TABLE `machines` AUTO_INCREMENT = 14";
    $conn->exec($alterSQL);
    echo "Auto-increment set to 14.<br>";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
$conn = null;
?>