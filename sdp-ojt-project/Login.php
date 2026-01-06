<?php
session_start();

// DEMO MODE (no database)
$demo_mode = true;
$demo_email = 'test@example.com';
$demo_password = 'password';

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: Login.php");
    exit();
}

$error = "";

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($demo_mode) {
        if ($email === $demo_email && $password === $demo_password) {
            $_SESSION['username'] = 'Demo User';
            header("Location: MainDashboard.php");
            exit();
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="design_files/Design.css">
</head>
<body>
<div class="login-wrapper">
  <div class="card">
    <div class="login-top">
      <div class="login-icon" aria-hidden="true">
        <!-- user svg -->
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm0 2c-3.33 0-10 1.67-10 5v3h20v-3c0-3.33-6.67-5-10-5z"/>
        </svg>
      </div>
      <h2>Welcome back</h2>
      <p class="subtitle">Sign in to your account</p>
    </div>
    <?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
    <form method="POST" class="login-form">
      <label class="input-label" for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="Email" required>
      <label class="input-label" for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Password" required>
      <button type="submit" class="btn">Login</button>
    </form>
    <p class="demo-note"><small>Demo: test@example.com / password</small></p>
  </div>
</div>
</body>
</html>
