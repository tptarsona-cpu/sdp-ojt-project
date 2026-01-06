<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit();
}
$userName = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<style>
body { font-family: Arial; background:#f4f6f8; }
.card { max-width:600px; margin:60px auto; padding:20px; background:#fff; border-radius:6px; }
a.button { padding:8px 12px; background:#d9534f; color:#fff; text-decoration:none; border-radius:4px; }
</style>
</head>
<body>
<div class="card">
<h2>Welcome, <?php echo htmlspecialchars($userName); ?> ✅</h2>
<p>You are now signed in.</p>
<a class="button" href="Login.php?logout=1">Logout</a>
</div>
</body>
</html>
