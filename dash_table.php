<?php
/* 
 * COMPREHENSIVE DATABASE SETUP SCRIPT
 * Features:
 * 1. Conditional database reset (safe for production)
 * 2. Prevents duplicate rewards
 * 3. Detailed error reporting
 * 4. Foreign key integrity checks
 */

// ========================
// CONFIGURATION SECTION
// ========================
$DB_CONFIG = [
    'host' => 'localhost',
    'user' => 'root',
    'password' => '',
    'database' => 'server_db',
    'charset' => 'utf8mb4'
];

// Set this to TRUE only when you need a complete database reset
$RESET_DATABASE = false;

// Sample rewards data (will be inserted if table is empty or after reset)
$SAMPLE_REWARDS = [
    ['Free maggie hot cup', '1x MAGGIE KARI, 1x MAGGIE TOMYAM', 5000, 100],
    ['GARDENIA BREAD', '1x CHOCOLATE, 1x CORN', 500, 50],
    ['FREE TOWEL', '1x TOWEL (random colour)', 1000, 30],
    ['Free 100 PLUS', '1x 100 PLUS (can choose any flavour)', 500, 200],
    ['FREE ICE CREAM', '1x CUP ICE CREAM', 350, 150],
    ['DIY MILO/COFFEE PACKETS', '1x MILO PACKET, 1x COFFEE PACKET', 800, 80]
];

// ========================
// HELPER FUNCTIONS
// ========================
function showSuccess($message) {
    echo "<div style='color:green; margin:10px; padding:10px; border:1px solid green;'>✅ $message</div>";
}

function showWarning($message) {
    echo "<div style='color:orange; margin:10px; padding:10px; border:1px solid orange;'>⚠️ $message</div>";
}

function showError($message) {
    echo "<div style='color:red; margin:10px; padding:10px; border:1px solid red;'>❌ $message</div>";
}

function verifyTableExists($conn, $tableName) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$tableName'");
    return (mysqli_num_rows($result) > 0);
}

// ========================
// MAIN EXECUTION
// ========================
try {
    // 1. DATABASE CONNECTION
    $conn = mysqli_connect(
        $DB_CONFIG['host'],
        $DB_CONFIG['user'],
        $DB_CONFIG['password'],
        $DB_CONFIG['database']
    );
    
    if (!$conn) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }
    mysqli_set_charset($conn, $DB_CONFIG['charset']);
    showSuccess("Connected to database successfully");

    // 2. FOREIGN KEY CHECKS (temporarily disable)
    if (!mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0")) {
        throw new Exception("Could not disable foreign key checks");
    }

    // 3. CONDITIONAL DATABASE RESET
    if ($RESET_DATABASE) {
        showWarning("Database reset requested - dropping tables...");
        
        // Drop tables in reverse dependency order
        $tablesToDrop = [
            'reward_redemptions',  // Depends on users and rewards
            'points_transactions', // Depends on users
            'rewards',            // Independent
            'users'               // Independent
        ];
        
        foreach ($tablesToDrop as $table) {
            if (!mysqli_query($conn, "DROP TABLE IF EXISTS $table")) {
                throw new Exception("Failed to drop $table: " . mysqli_error($conn));
            }
            showSuccess("Dropped table: $table");
        }
    }

    // 4. TABLE CREATION
    $tables = [
        'users' => [
            'sql' => "CREATE TABLE IF NOT EXISTS users (
                user_id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                points INT DEFAULT 0,  
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET={$DB_CONFIG['charset']}",
            'description' => "Stores user accounts and point balances"
        ],
        
        'rewards' => [
            'sql' => "CREATE TABLE IF NOT EXISTS rewards (
                reward_id INT AUTO_INCREMENT PRIMARY KEY,
                reward_name VARCHAR(100) NOT NULL UNIQUE,
                description TEXT,
                points_required INT NOT NULL,
                stock_quantity INT NOT NULL,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET={$DB_CONFIG['charset']}",
            'description' => "Stores available rewards and their point costs"
        ],
        
        'reward_redemptions' => [
            'sql' => "CREATE TABLE IF NOT EXISTS reward_redemptions (
                redemption_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                reward_id INT NOT NULL,
                redemption_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                points_used INT NOT NULL,
                status ENUM('pending', 'claimed', 'expired', 'cancelled') DEFAULT 'pending',
                details TEXT,
                claim_code VARCHAR(20),
                claimed_at TIMESTAMP NULL,
                FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (reward_id) REFERENCES rewards(reward_id) ON DELETE CASCADE ON UPDATE CASCADE,
                INDEX (user_id),
                INDEX (reward_id),
                INDEX (status),
                INDEX (claim_code)
            ) ENGINE=InnoDB DEFAULT CHARSET={$DB_CONFIG['charset']}",
            'description' => "Tracks reward redemptions by users"
        ],
        
        'points_transactions' => [
            'sql' => "CREATE TABLE IF NOT EXISTS points_transactions (
                transaction_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                points_change INT NOT NULL,
                transaction_type ENUM('earn', 'redeem', 'adjustment', 'expiry') NOT NULL,
                transaction_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                description VARCHAR(255),
                reference_id INT COMMENT 'ID of related redemption',
                FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
                INDEX (user_id),
                INDEX (transaction_time),
                INDEX (transaction_type)
            ) ENGINE=InnoDB DEFAULT CHARSET={$DB_CONFIG['charset']}",
            'description' => "Logs all points transactions"
        ]
    ];

    foreach ($tables as $tableName => $tableDef) {
        if (!mysqli_query($conn, $tableDef['sql'])) {
            throw new Exception("Failed to create $tableName: " . mysqli_error($conn));
        }
        
        if (!verifyTableExists($conn, $tableName)) {
            throw new Exception("Verification failed: $tableName not created");
        }
        
        showSuccess("Created table: $tableName ({$tableDef['description']})");
    }

    // 5. SAMPLE DATA INSERTION
    $rewardCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM rewards"))['count'];
    
    if ($RESET_DATABASE || $rewardCount == 0) {
        // Using TRUNCATE instead of DELETE for better performance
        mysqli_query($conn, "TRUNCATE TABLE rewards");
        
        $stmt = mysqli_prepare($conn, "INSERT INTO rewards 
            (reward_name, description, points_required, stock_quantity) 
            VALUES (?, ?, ?, ?)");
            
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($conn));
        }
        
        $insertedCount = 0;
        foreach ($SAMPLE_REWARDS as $reward) {
            mysqli_stmt_bind_param($stmt, "ssii", $reward[0], $reward[1], $reward[2], $reward[3]);
            if (mysqli_stmt_execute($stmt)) {
                $insertedCount++;
            } else {
                // Ignore duplicate errors (shouldn't happen due to TRUNCATE)
                if (mysqli_errno($conn) != 1062) {
                    throw new Exception("Reward insertion failed: " . mysqli_stmt_error($stmt));
                }
            }
        }
        mysqli_stmt_close($stmt);
        showSuccess("Inserted $insertedCount sample rewards");
    } else {
        showWarning("Skipping reward insertion - table already contains $rewardCount rewards");
    }

    // 6. FINAL CHECKS
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
    
    // Verify foreign keys
    $foreignKeys = [];
    $result = mysqli_query($conn, "SELECT 
        TABLE_NAME, COLUMN_NAME, 
        REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = '{$DB_CONFIG['database']}' 
        AND REFERENCED_TABLE_NAME IS NOT NULL");
    
    while ($row = mysqli_fetch_assoc($result)) {
        $foreignKeys[] = $row;
    }
    
    if (count($foreignKeys) < 3) {
        showWarning("Expected 3 foreign keys, found " . count($foreignKeys));
    } else {
        showSuccess("Verified foreign key constraints");
    }

    // 7. FINAL STATUS REPORT
    echo "<h3>Database Status Summary</h3>";
    echo "<ul>";
    foreach ($tables as $tableName => $tableDef) {
        $count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM $tableName"))['c'];
        echo "<li><strong>$tableName</strong>: $count records</li>";
    }
    echo "</ul>";

} catch (Exception $e) {
    showError("SETUP FAILED: " . $e->getMessage());
    
    // Attempt to re-enable foreign key checks if connection exists
    if (isset($conn)) {
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
    }
    
    exit(1); // Exit with error code
} finally {
    if (isset($conn)) {
        mysqli_close($conn);
        showSuccess("Database connection closed");
    }
}
?>