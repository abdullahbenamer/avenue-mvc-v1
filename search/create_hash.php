<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$hashed_password = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = trim($_POST['password'] ?? '');
    if ($password !== '') {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    } else {
        $hashed_password = 'Please enter a password.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Password Hasher</title>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Password Hasher</h2>
    <form method="post">
        <label for="password">Enter Password:</label><br>
        <input type="text" id="password" name="password" required>
        <button type="submit">Hash</button>
    </form>

    <?php if ($hashed_password): ?>
        <p><strong>Hashed Password:</strong> <?= htmlspecialchars($hashed_password) ?></p>
    <?php endif; ?>
</body>
</html>
