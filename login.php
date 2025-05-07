<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login page</title>
    <link rel="stylesheet" href="signuppage.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
</head>

<body>
  <div class="container">

    <!--left tit-->
    <div class="left">
      <h1>Welcome<br>Back!</h1>
      <p>"Simplify your laundry experience with ease and convenience."</p>
    </div>

    <!--right tit-->
    <div class="right">

      <h1>Log In</h1>

            <!--login-->

      <div class="login">
        <p>Haven't got an account? <a href="signup.php">Sign Up Now!</a></p>
      </div>

      <div class="errorcon">
    <?php if (isset($_GET['error'])): ?>
            <?php if ($_GET['error'] === 'password'): ?>
              <p style="color: red;">Incorrect password. Please try again.</p>
            <?php elseif ($_GET['error'] === 'email'): ?>
              <p style="color: red;">No account found with this email.</p>
            <?php endif; ?>
        <?php endif; ?>
      
            </div>
    <!--forms-->

      <form action="log.php" method="POST">

        <div class="input-field">
            <input type="email" name="email" placeholder="Email" required>
          </div>
        <div class="input-field">
          <input type="password" name="password" placeholder="Password" required>
        </div>
        <button class="signup-btn" type="submit">Login</button>
      </form>


    </div>
  </div>
</body>
</html>