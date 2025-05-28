<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>signup page</title>
    <link rel="stylesheet" href="signuppage.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
</head>

<body>
  <div class="container">

    <!--left tit-->
    <div class="left">
      <h1>Create Your Free Account</h1>
      <p>"Simplify your laundry experience with ease and convenience."</p>
    </div>

    <!--right tit-->
    <div class="right">

      <h1>Sign Up</h1>

            <!--login-->

      <div class="login">
        <p>Already have an account? <a href="login.php">Log in</a></p>
      </div>

      <!--debugg-->

      <?php 
      session_start();
      if (!empty($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_SESSION['error']); ?></p>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <?php if (!empty($_SESSION['success'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success']; ?></p>
        <?php unset($_SESSION['success']); ?>
      <?php endif; ?>


    <!--form-->

      <form action="register.php" method="POST">    

        <div class="input-field">
          <input type="text" name="name" placeholder="Username" required>

        </div>

        <div class="input-field">
          <input type="email" name="email" placeholder="Your Email" required>

        </div>
        <div class="input-field">
          <input type="password" name="password" placeholder="Create Password" required>
        </div>
        <button class="signup-btn" type="submit">Create an Account</button>
      </form>


    </div>
  </div>
</body>
</html>