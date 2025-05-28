<?php
$db = new mysqli('localhost', 'root', '', 'server_db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $problem_type = htmlspecialchars($_POST['problem_type']);
    $description = htmlspecialchars($_POST['description']);
    $urgency = htmlspecialchars($_POST['urgency']);
    $username = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : 'Anonymous';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';

    // File upload handling
    $screenshot_path = null;
    if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['screenshot']['type'];
        
        if(in_array($file_type, $allowed_types)) {
            $target_dir = "uploads/";
            if(!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            $file_name = uniqid() . '_' . basename($_FILES['screenshot']['name']);
            $target_file = $target_dir . $file_name;
            
            if(move_uploaded_file($_FILES['screenshot']['tmp_name'], $target_file)) {
                $screenshot_path = $target_file;
            }
        }
    }

    // Insert report
    $stmt = $db->prepare("INSERT INTO reports 
        (problem_type, description, urgency, username, email, screenshot_path)
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssss', 
        $problem_type,
        $description,
        $urgency,
        $username,
        $email,
        $screenshot_path
    );
    
    if($stmt->execute()) {
        header("Location: xiexie.html");
    } else {
        header("Location: report.php?error=1");
    }
    exit;
}