<?php
session_start();

// DEMO MODE (no database)
$demo_mode = true;
$demo_email = 'test@example.com';
$demo_password = 'password';

// Logout
if if(isset($_GET['logout'])) {
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
<style>
body { font-family: Arial; background:#f4f6f8; }
.card { max-width:400px; margin:80px auto; padding:20px; background:#fff; border-radius:6px; }
input { width:100%; padding:10px; margin:8px 0; }
button { padding:10px; width:100%; background:#007bff; color:#fff; border:none; }
.error { color:red; }
</style>
</head>
<body>
<div class="card">
<h2>Login</h2>
<?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
<form method="POST">
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Login</button>
</form>
<p><small>Demo: test@example.com / password</small></p>
</div>
</body>
</html>
