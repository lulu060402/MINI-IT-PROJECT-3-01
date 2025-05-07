<?php
// Database configuration
$host = 'localhost';
$dbname = 'laundry_rewards';
$username = 'root';
$password = ''; // Default XAMPP password is empty

try {
    // Create connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected successfully<br>";

    // Temporarily disable foreign key checks
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
    echo "Foreign key checks disabled<br>";

    // Check if rewards table exists
    $tableExists = $conn->query("SHOW TABLES LIKE 'rewards'")->rowCount() > 0;
    
    if ($tableExists) {
        // Clear existing data (using DELETE instead of TRUNCATE to maintain auto-increment)
        $conn->exec("DELETE FROM rewards");
        echo "Cleared existing rewards<br>";
        
        // Reset auto-increment
        $conn->exec("ALTER TABLE rewards AUTO_INCREMENT = 1");
        echo "Reset auto-increment<br>";
    } else {
        // Create rewards table if it doesn't exist
        $createTable = "CREATE TABLE rewards (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            points_cost INT NOT NULL,
            stock INT NOT NULL,
            image VARCHAR(255)
        )";
        
        $conn->exec($createTable);
        echo "Created rewards table successfully.<br>";
    }

    // Insert rewards data
    $insertData = "INSERT INTO rewards (name, description, points_cost, stock, image) VALUES
            ('Maggie Hot Cup', 'Instant noodle cup', 50, 20, 'maggie.jpg'),
            ('Gardenia Bread', 'Fresh loaf of bread', 30, 15, 'bread.jpg'),
            ('Free Towel', 'Premium laundry towel', 100, 10, 'towel.jpg'),
            ('Free 100 PLUS', 'Energy drink', 40, 25, 'drink.jpg'),
            ('Free Ice Cream', 'Vanilla ice cream cup', 60, 8, 'icecream.jpg'),
            ('Milo Drink', 'Chocolate malt drink', 45, 12, 'milo.jpg')";
    
    $conn->exec($insertData);
    $rowCount = $conn->query("SELECT COUNT(*) FROM rewards")->fetchColumn();
    echo "Rewards inserted successfully!<br>";
    echo "Total rows inserted: $rowCount<br>";

    // Re-enable foreign key checks
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "Foreign key checks re-enabled<br>";
    
} catch(PDOException $e) {
    echo "<div style='color:red;'><strong>Error:</strong> " . $e->getMessage() . "</div>";
    // Re-enable foreign key checks in case of error
    if(isset($conn)) {
        $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
    }
}

// Close connection
if(isset($conn)) {
    $conn = null;
}
?>