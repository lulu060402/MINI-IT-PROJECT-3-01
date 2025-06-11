<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON content type for AJAX responses
header('Content-Type: application/json');

// Database configuration
$DB_CONFIG = [
    'host' => 'localhost',
    'user' => 'root',
    'password' => '',
    'database' => 'server_db', // Recommended database name
    'charset' => 'utf8mb4'
];

// Create database connection
try {
    $conn = new mysqli(
        $DB_CONFIG['host'],
        $DB_CONFIG['user'],
        $DB_CONFIG['password'],
        $DB_CONFIG['database']
    );
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: {$conn->connect_error}");
    }
    
    // Set charset
    $conn->set_charset($DB_CONFIG['charset']);
    
    // Check if tables exist, create if they don't
    initializeDatabase($conn);
    
} catch (Exception $e) {
    die(json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'debug' => 'Connection failed to: ' . $DB_CONFIG['database']
    ]));
}

/**
 * Initializes the database structure
 */
function initializeDatabase($conn) {
    try {
        // Create bookings table if not exists
        $conn->query("CREATE TABLE IF NOT EXISTS bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            room VARCHAR(20) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            service_type ENUM('washer','dryer') NOT NULL,
            payment_method ENUM('Cash') NOT NULL,
            status ENUM('pending','confirmed','completed','cancelled') DEFAULT 'pending',
            runner_id INT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            completed_at TIMESTAMP NULL DEFAULT NULL
        )") or throw new Exception($conn->error);
      
        // Create runners table if not exists
        $conn->query("CREATE TABLE IF NOT EXISTS runners (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            room VARCHAR(20) NOT NULL,
            availability TEXT,
            status ENUM('pending','approved','rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )") or throw new Exception($conn->error);
        
    } catch (Exception $e) {
        throw new Exception("Database initialization failed: " . $e->getMessage());
    }
}


/**
 * Sanitizes and validates input data
 */
function processInput($data, $type = 'text') {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    
    // Additional type-specific validation
    switch ($type) {
        case 'email':
            if (!filter_var($data, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format");
            }
            break;
            
        case 'phone':
            if (!preg_match("/^(\+?6?01)[0-46-9]-*[0-9]{7,8}$/", $data)) {
                throw new Exception("Invalid Malaysian phone number format (e.g., 0123456789 or +60123456789)");
            }
            break;
            
    }
    
    return $data;
}

// Main request handler
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => "Invalid request method. Please submit the form."
    ]);
    if (isset($conn)) {
        $conn->close();
    }
    exit;
}

try {
    // Determine which form was submitted
    if (isset($_POST['name'], $_POST['room'], $_POST['phone'])) {
        // Process Laundry Booking Form
        $required = ['name', 'room', 'phone', 'service_type', 'payment_method'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Please fill in all required fields");
            }
        }
        
        $bookingData = [
            'name' => processInput($_POST['name']),
            'room' => processInput($_POST['room']),
            'phone' => processInput($_POST['phone'], 'phone'),
            'service_type' => processInput($_POST['service_type']),
            'payment_method' => processInput($_POST['payment_method'])
        ];
        
        // Insert booking
        $stmt = $conn->prepare("INSERT INTO bookings 
            (name, room, phone, service_type, payment_method) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", 
            $bookingData['name'],
            $bookingData['room'],
            $bookingData['phone'],
            $bookingData['service_type'],
            $bookingData['payment_method']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Booking failed: {$stmt->error}");
        }
        
        $response = [
            'success' => true,
            'message' => 'Booking successful! Your runner will contact you soon.',
            'booking_id' => $stmt->insert_id
        ];
        
        $stmt->close();
        
    } else if (isset($_POST['runner-name'], $_POST['runner-email'], $_POST['phone'])) {
        // Process Runner Application Form
        $required = ['runner-name', 'runner-email', 'phone', 'runner-room'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Please fill in all required fields");
            }
        }
        
        $runnerData = [
            'name' => processInput($_POST['runner-name']),
            'email' => processInput($_POST['runner-email'], 'email'),
            'phone' => processInput($_POST['phone'], 'phone'),
            'room' => processInput($_POST['runner-room']),
            'availability' => isset($_POST['runner-availability']) ? 
                implode(", ", $_POST['runner-availability']) : ''
        ];
        
        if (empty($runnerData['availability'])) {
            throw new Exception("Please select at least one availability period");
        }
        
        // Insert runner application
        $stmt = $conn->prepare("INSERT INTO runners 
            (name, email, phone, room, availability) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", 
            $runnerData['name'],
            $runnerData['email'],
            $runnerData['phone'],
            $runnerData['room'],
            $runnerData['availability']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Application failed: {$stmt->error}");
        }
        
        $response = [
            'success' => true,
            'message' => 'Application submitted! We will contact you within 24 hours.',
            'runner_id' => $stmt->insert_id
        ];
        
        $stmt->close();
        
    } else {
        throw new Exception("Invalid form submission. Missing required fields.");
    }
    
    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => $stmt?->error ?? null
    ]);
} finally {
    // Close connection if it exists
    if (isset($conn)) {
        $conn->close();
    }
}

?> 