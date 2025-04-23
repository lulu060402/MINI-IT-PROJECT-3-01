<?php
session_start();

// connect sql
$conn = mysqli_connect("localhost", "root", "", "logindb");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = $_POST['password'];


$sql = "SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($conn, $sql);


if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    
    if (password_verify($password, $row['password'])) {
        // yayy correct de
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['name'];
        //go dashboard
        header("Location: dashboard.php"); 
        exit();
        

    } else {
        // wrong pass
        header("Location: login.php?error=password");
        exit();
    }
} else {
    // email cannot find
    header("Location: login.php?error=email");
    exit();
}

mysqli_close($conn);
?>