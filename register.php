<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db_server = "localhost";
    $db_user = "root";
    $db_password = "";
    $db_name = "logindb";

    $conn = mysqli_connect($db_server, $db_user, $db_password, $db_name);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')";

    try {
        mysqli_query($conn, $sql);
        header("Location: signup.php?success=1");
        exit();
    } catch (mysqli_sql_exception $e) {
        header("Location: signup.php?error=username");
        exit();
    }

    mysqli_close($conn);

} else {
    header("Location: signup.php");
    exit();
}
?>
