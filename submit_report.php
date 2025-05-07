<?php

$db_host = 'localhost';
$db_name = 'report';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $problem_type = filter_input(INPUT_POST, 'problem_type', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $urgency = filter_input(INPUT_POST, 'urgency', FILTER_SANITIZE_STRING);
    

    $screenshot_path = null;
    if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_name = uniqid() . '_' . basename($_FILES['screenshot']['name']);
        $target_path = $upload_dir . $file_name;
        
 
        $image_info = getimagesize($_FILES['screenshot']['tmp_name']);
        if ($image_info !== false) {
            if (move_uploaded_file($_FILES['screenshot']['tmp_name'], $target_path)) {
                $screenshot_path = $target_path;
            }
        }
    }
    

    try {
        $stmt = $pdo->prepare("INSERT INTO problem_reports 
                              (name, email, subject, problem_type, description, urgency, screenshot_path, created_at) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        
        $stmt->execute([$name, $email, $subject, $problem_type, $description, $urgency, $screenshot_path]);
        

        $to = 'support@yourdomain.com';
        $email_subject = "New Problem Report: $subject";
        $email_body = "A new problem has been reported:\n\n" .
                      "Name: $name\n" .
                      "Email: $email\n" .
                      "Problem Type: $problem_type\n" .
                      "Urgency: $urgency\n\n" .
                      "Description:\n$description";
        
        mail($to, $email_subject, $email_body);

        
//thank ni
        header('Location: xiexie.html');
        exit();
    } catch (PDOException $e) {
        die("Error saving report: " . $e->getMessage());
    }
} else {

    header('Location: report.html');
    exit();
}
?>