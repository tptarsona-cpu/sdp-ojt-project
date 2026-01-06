<?php
session_start();

// -------- CONFIG: update these to match your DB ----------
// Set $demo_mode = true to run without a database (handy for local testing).
// Demo credentials: email = test@example.com, password = password
$demo_mode = true; // set to false when you want to use a real DB
$demo_email = 'test@example.com';
$demo_password = 'password';
$db_host = '127.0.0.1';
$db_name = 'my_app_db';
$db_user = 'db_user';
$db_pass = 'db_pass';
// ---------------------------------------------------------

// Handle logout request
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: Login.php');
    exit;
}

$errors = [];

// Generate (or check) CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic CSRF protection
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $errors[] = 'Invalid request (CSRF token mismatch).';
    }

    // Validate inputs
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$email) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (empty($password)) {
        $errors[] = 'Please enter your password.';
    }

    if (empty($errors)) {
        try {
            // Use PDO with prepared statements to avoid SQL injection
            $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
            $pdo = new PDO($dsn, $db_user, $db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            $stmt = $pdo->prepare('SELECT id, email, password_hash, display_name FROM users WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Authentication successful
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['display_name'] ?? $user['email'];

                header('Location: MainDashboard.php');
                exit;
            } else {
                // Generic message (do not reveal whether email exists)
                $errors[] = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            // Database-specific error: show friendly guidance for XAMPP users
            $errors[] = 'Database connection failed. Ensure XAMPP MySQL is running and the database "' . htmlspecialchars($db_name) . '" exists.';
            // If you want debug details locally, set $debug = true above; we add optional debug output here
            if (!empty($debug)) {
                $errors[] = 'Debug: ' . htmlspecialchars($e->getMessage());
            }
        } catch (Exception $e) {
            // In production, log the error instead of echoing
            $errors[] = 'Login failed: ' . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; }
        form { max-width: 360px; margin: 0 auto; }
        .error { background:#ffd6d6; padding:8px; margin-bottom:12px; border-radius:4px; }
        label { display:block; margin-top:12px; }
        input[type="email"], input[type="password"] { width:100%; padding:8px; }
        button { margin-top:16px; padding:8px 12px; }
    </style>
</head>
<body>
    <h2>Sign in</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

        <label for="email">Email</label>
        <input id="email" name="email" type="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>

        <button type="submit">Login</button>
    </form>

    <p style="max-width:360px;margin:24px auto;font-size:0.9em;color:#666;">Need an account? Create users directly in the database or add a registration form.</p>

    <?php if (!empty($demo_mode)): ?>
        <div style="max-width:360px;margin:12px auto;padding:10px;background:#eef6ff;border:1px solid #ccdefa;border-radius:4px;">
            <strong>Demo mode:</strong> Use <code>test@example.com</code> / <code>password</code> to sign in (no DB required).
        </div>
    <?php endif; ?>

    <hr style="max-width:360px;margin:24px auto;">

    <h4 style="max-width:360px;margin:0 auto;">Developer notes</h4>
    <pre style="max-width:680px;background:#f7f7f7;padding:12px;border-radius:4px;">-- Example SQL to create a users table:
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  display_name VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- To add a sample user using PHP's password_hash():
-- Run this in a PHP script or interactive shell:
-- <?php echo password_hash('MyTestPassword123', PASSWORD_DEFAULT); ?>
-- Insert the produced hash into the password_hash column for the user.
</pre>
</body>
</html>
