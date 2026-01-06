<?php
session_start();

// Redirect to login if not authenticated
if (empty($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit;
}

// Simple logout handling (optional alternative to Login.php?logout=1)
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: Login.php');
    exit;
}

$userName = htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['user_email']);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; }
        .card { max-width: 700px; margin: 0 auto; border: 1px solid #eee; padding: 20px; border-radius:6px; }
        a.button { display:inline-block; padding:6px 10px; background:#d9534f; color:#fff; text-decoration:none; border-radius:4px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Welcome, <?php echo $userName; ?> ✅</h2>
        <p>You are now signed in.</p>

        <p>
            <a class="button" href="Login.php?logout=1">Sign out</a>
        </p>
    </div>
</body>
</html>