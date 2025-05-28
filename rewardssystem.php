<?php
$db_server = "localhost";
$db_username = "root";  
$db_password = "";
$db_database = "server_db";
$conn = mysqli_connect($db_server, $db_username, $db_password, $db_database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle redemption request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reward_name'])) {
    header('Content-Type: application/json');
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        die(json_encode(['success' => false, 'message' => 'Not logged in', 'error_code' => 'no_session']));
    }
    
    $user_id = $_SESSION['user_id'];
    $reward_name = urldecode($_POST['reward_name']);
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Get reward details
        $reward_query = "SELECT * FROM rewards WHERE name = ? AND is_active = 1 FOR UPDATE";
        $stmt = mysqli_prepare($conn, $reward_query);
        mysqli_stmt_bind_param($stmt, "s", $reward_name);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to execute reward query');
        }
        
        $reward_result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($reward_result) == 0) {
            throw new Exception('Reward not found or inactive');
        }
        
        $reward = mysqli_fetch_assoc($reward_result);
        
        // Check user's points balance
        $user_query = "SELECT points_balance FROM users WHERE user_id = ? FOR UPDATE";
        $stmt = mysqli_prepare($conn, $user_query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to execute user query');
        }
        
        $user_result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($user_result);
        
        if (!$user) {
            throw new Exception('User not found');
        }
        
        if ($user['points_balance'] < $reward['points_required']) {
            throw new Exception('Insufficient points');
        }
        
        // Calculate new balance
        $new_balance = $user['points_balance'] - $reward['points_required'];
        
        // Update user's points
        $update_points = "UPDATE users SET points_balance = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $update_points);
        mysqli_stmt_bind_param($stmt, "ii", $new_balance, $user_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to update points');
        }
        
        // Record redemption (using points_required as points_used)
        $insert_redemption = "INSERT INTO redemptions 
                             (user_id, reward_id, points_required, points_used, status) 
                             VALUES (?, ?, ?, ?, 'redeemed')";
        $stmt = mysqli_prepare($conn, $insert_redemption);
        mysqli_stmt_bind_param($stmt, "iiii", 
            $user_id, 
            $reward['reward_id'], 
            $reward['points_required'], 
            $reward['points_required']
        );
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to record redemption: ' . mysqli_error($conn));
        }
        
        // Record in points history
        $insert_history = "INSERT INTO points_history 
                          (user_id, points_earned, points_source) 
                          VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_history);
        $source = "Redemption: " . $reward['name'];
        $points_used = -$reward['points_required']; // Negative for redemption
        mysqli_stmt_bind_param($stmt, "iis", $user_id, $points_used, $source);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to record points history');
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Update session
        $_SESSION['points_balance'] = $new_balance;
        
        echo json_encode([
            'success' => true,
            'new_balance' => $new_balance,
            'message' => 'Redemption successful!'
        ]);
        exit();
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        error_log("Redemption error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'error_details' => mysqli_error($conn)
        ]);
        exit();
    }
}

// If not a redemption request, continue with normal page flow
?>