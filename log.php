<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "logindb");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$email = $_POST['email'];
$password = $_POST['password'];


$sql = "SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    
    if (password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['name'];

        echo "Login successful! Welcome, " . $row['name'];
        
    } else {
        echo "Wrong password.";
    }
} else {
    echo "No user found with that email.";
}

mysqli_close($conn);
?>