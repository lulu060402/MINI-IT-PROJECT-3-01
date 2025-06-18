<?php
// Initialize response array
$response = ['success' => false, 'message' => ''];

try {
    // Database connection
    $conn = mysqli_connect("localhost", "root", "", "server_db");
    if (!$conn) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }

    // Set character set
    $conn->set_charset("utf8mb4");

    session_start();
    header('Content-Type: application/json');

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Not logged in');
    }

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input');
    }

    // Validate input
    $rewardName = trim($input['reward_name'] ?? '');
    $pointsRequired = filter_var($input['points'] ?? null, FILTER_VALIDATE_INT);

    if (empty($rewardName) || $pointsRequired === false || $pointsRequired <= 0) {
        throw new Exception('Invalid request - missing or invalid parameters');
    }

    // Verify CSRF token
    if (!isset($input['csrf_token']) || $input['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        throw new Exception('Invalid CSRF token');
    }

    // Start transaction
    $conn->begin_transaction();
    
    // 1. Verify reward exists
    $rewardStmt = $conn->prepare("SELECT reward_id, points_required FROM rewards WHERE reward_name = ?");
    if (!$rewardStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $rewardStmt->bind_param("s", $rewardName);
    if (!$rewardStmt->execute()) {
        throw new Exception("Execute failed: " . $rewardStmt->error);
    }
    
    $rewardResult = $rewardStmt->get_result();
    if ($rewardResult->num_rows === 0) {
        throw new Exception("Reward not found");
    }
    
    $reward = $rewardResult->fetch_assoc();
    $rewardId = $reward['reward_id'];
    
    // Verify points match
    if ($reward['points_required'] != $pointsRequired) {
        throw new Exception("Points don't match the reward requirements");
    }
    
    // 2. Verify user has enough points - CHANGED 'id' to your actual user_id column name
    $userStmt = $conn->prepare("SELECT points FROM users WHERE user_id = ? FOR UPDATE");
    if (!$userStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $userStmt->bind_param("i", $_SESSION['user_id']);
    if (!$userStmt->execute()) {
        throw new Exception("Execute failed: " . $userStmt->error);
    }
    
    $userResult = $userStmt->get_result();
    $user = $userResult->fetch_assoc();
    
    if ($user['points'] < $pointsRequired) {
        throw new Exception("Insufficient points");
    }
    
    // 3. Deduct points
    $updateStmt = $conn->prepare("UPDATE users SET points = points - ? WHERE user_id = ?");
    if (!$updateStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $updateStmt->bind_param("ii", $pointsRequired, $_SESSION['user_id']);
    if (!$updateStmt->execute()) {
        throw new Exception("Execute failed: " . $updateStmt->error);
    }
    
    // 4. Create redemption record
    $details = "Redeemed via web interface";
    $redemptionStmt = $conn->prepare("
        INSERT INTO reward_redemptions 
        (user_id, reward_id, points_used, status, details) 
        VALUES (?, ?, ?, 'pending', ?)
    ");
    if (!$redemptionStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $redemptionStmt->bind_param("iiis", $_SESSION['user_id'], $rewardId, $pointsRequired, $details);
    if (!$redemptionStmt->execute()) {
        throw new Exception("Execute failed: " . $redemptionStmt->error);
    }
    $redemptionId = $conn->insert_id;
    
    // 5. Record points transaction
    $description = "Redeemed reward: " . $conn->real_escape_string($rewardName);
    $transactionStmt = $conn->prepare("
        INSERT INTO points_transactions 
        (user_id, transaction_type, points_change, reference_id, description) 
        VALUES (?, 'redeem', ?, ?, ?)
    ");
    if (!$transactionStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $transactionStmt->bind_param("iiis", $_SESSION['user_id'], $pointsRequired, $redemptionId, $description);
    if (!$transactionStmt->execute()) {
        throw new Exception("Execute failed: " . $transactionStmt->error);
    }
    
    // Commit transaction
    $conn->commit();
    
    // Update session points
    $_SESSION['points'] = $user['points'] - $pointsRequired;
    
    // Return success
    $response = [
        'success' => true,
        'message' => 'Redemption successful',
        'new_balance' => $_SESSION['points'],
        'redemption_id' => $redemptionId
    ];

} catch (Exception $e) {
    // Rollback on error
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->rollback();
    }
    
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
} finally {
    // Close connection
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
    
    // Send response
    echo json_encode($response);
}