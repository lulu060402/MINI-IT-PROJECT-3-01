<?php

$host = 'localhost';
$dbname = 'logindb';
$name = 'root'; 
$password = ''; 


error_reporting(E_ALL);
ini_set('display_errors', 1);


$error = '';
$success = '';

try {
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $name, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];


        if (empty($name) || empty($email) || empty($password)) {
            $error = 'Please fill in all fields.';

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';

        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long.';

        } else {

            $stmt = $pdo->prepare("SELECT * FROM users WHERE name = ? OR email = ?");
            $stmt->execute([$name, $email]);
            $existingUser = $stmt->fetch();

            if ($existingUser) {
                $error = 'Username or email already exists.';
            } else {

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


                $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, $hashedPassword]);


                $success = 'Registration successful! You can now <a href="login.php">login</a>.';
                

            }
        }
    }
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
} catch (Exception $e) {
    $error = 'Error: ' . $e->getMessage();
}


if (!empty($error) || !empty($success)) {


    session_start();
    $_SESSION['error'] = $error;
    $_SESSION['success'] = $success;
    header('Location: signup.php');
    exit();
}
?>