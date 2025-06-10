<?php

session_start();

$error = '';

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

// Database connection

$DB_CONFIG = [

'host' => 'localhost',

'user' => 'root',

'password' => '',

'database' => 'server_db',

'charset' => 'utf8mb4'

];

try {

$conn = new mysqli(

$DB_CONFIG['host'],

$DB_CONFIG['user'],

$DB_CONFIG['password'],

$DB_CONFIG['database']

);

if ($conn->connect_error) {

throw new Exception("Database connection failed: " . $conn->connect_error);

}

$conn->set_charset($DB_CONFIG['charset']);

$phone = trim($_POST['phone']);

// Validate phone format (same as in processInput function)

if (!preg_match("/^(\+?6?01)[0-46-9]-*[0-9]{7,8}$/", $phone)) {

throw new Exception("Invalid phone number format");

}

// Look up runner by phone

$stmt = $conn->prepare("SELECT id, status FROM runners WHERE phone = ?");

$stmt->bind_param("s", $phone);

$stmt->execute();

$result = $stmt->get_result();

$runner = $result->fetch_assoc();

$stmt->close();

$conn->close();

if ($runner) {

if ($runner['status'] === 'approved') {

// Set session and redirect

$_SESSION['runner_id'] = $runner['id'];

header("Location: runner_dash.php");

exit;

} else if ($runner['status'] === 'pending') {

$error = "Your application is still pending approval.";

} else if ($runner['status'] === 'rejected') {

$error = "Your application has been rejected. Please contact support.";

}

} else {

$error = "No runner found with that phone number.";

}

} catch (Exception $e) {

$error = $e->getMessage();

}

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Runner Login</title>

<style>

body {

font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

background-color: #f5f5f5;

display: flex;

justify-content: center;

align-items: center;

height: 100vh;

margin: 0;

}

.login-container {

background: white;

padding: 30px;

border-radius: 8px;

box-shadow: 0 4px 10px rgba(0,0,0,0.1);

width: 350px;

text-align: center;

}

h2 {

color: #2c3e50;

margin-bottom: 25px;

}



.form-group {

margin-bottom: 20px;

text-align: left;

}

label {

display: block;

margin-bottom: 8px;

font-weight: 600;

color: #34495e;

}

input[type="tel"] {

width: 100%;

padding: 12px;

border: 1px solid #ddd;

border-radius: 4px;

font-size: 16px;

box-sizing: border-box;

}

.btn {
 background-color: #3498db;
 color: white;
border: none;
 padding: 12px 20px;
border-radius: 4px;
    cursor: pointer;
font-size: 16px;
 font-weight: 600;
width: 100%;
transition: background-color 0.3s;
display: block;
 text-align: center;
 text-decoration: none;
box-sizing: border-box;
}
.btn:hover {
background-color: #2980b9;
}
.btn-back {
background-color: #7f8c8d;
  margin-top: 10px;
}
.btn-back:hover {
background-color: #95a5a6;
}
.error {

color: #e74c3c;

margin-top: 10px;

}

</style>

</head>
 

<body>

    

<div class="login-container">

<h2>Runner Login</h2>

<?php if ($error): ?>

<div class="error"><?php echo $error; ?></div>

<?php endif; ?>

<form method="POST" action="runner_login.php">

<div class="form-group">

<label for="phone">Phone Number</label>

<input type="tel" id="phone" name="phone" required

placeholder="e.g., 0123456789"

pattern="^(\+?6?01)[0-46-9]-*[0-9]{7,8}$">

</div>

<button type="submit" class="btn">Login</button>

</form>
 <a href="booking.php" class="btn btn-back">Back </a>
</div>

</body>


</html>