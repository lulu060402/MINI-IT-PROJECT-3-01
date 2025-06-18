<?php
// Database configuration
 $db = new mysqli('localhost', 'root', '', 'server_db');

if ($db->connect_error) {
    die("Database error: " . $db->connect_error);}

// Start session (if you're using session-based authentication)
session_start();

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: runner_management.php?error=invalid_request");
    exit();
}

// Validate admin authentication (if you implement authentication)
/*
if (!isset($_SESSION['admin_logged_in']) {
    header("Location: runner_management.php?error=unauthorized");
    exit();
}
*/

// Get and validate input
$runner_id = filter_input(INPUT_POST, 'runner_id', FILTER_VALIDATE_INT);
$new_status = filter_input(INPUT_POST, 'new_status', FILTER_SANITIZE_STRING);

if (!$runner_id || !in_array($new_status, ['pending', 'approved', 'rejected'])) {
    header("Location: runner_management.php?error=invalid_input");
    exit();
}

try {
    // Update runner status in database
    $stmt = $db->prepare("UPDATE runners SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $runner_id);
    $stmt->execute();
    
    if ($stmt->affected_rows === 1) {
        // Success - redirect back with success message
        
        // Optional: Log this action
        // logStatusChange($runner_id, $new_status, $_SESSION['admin_id']);
        
        header("Location: runner_management.php?success=1");
    } else {
        // No rows affected - runner not found
        header("Location: runner_management.php?error=runner_not_found");
    }
    exit();
    
} catch (Exception $e) {
    // Log the error for debugging
    error_log("Error updating runner status: " . $e->getMessage());
    
    header("Location: runner_management.php?error=database_error");
    exit();
}


?>