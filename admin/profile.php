<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); // Always first
require_once '../resources/db_config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_name']) || !isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT user_name, email, full_name, avatar FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

include '../resources/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>resources/styles.css">
    
</head>
<body>
<br>
<div class="about_title" style="margin: 1rem 25%;">
    <h3>My Profile <i class="fa-solid fa-user fa-2xl"></i></h3>
</div>
<br>

<form action="process_profile_update.php" method="POST" enctype="multipart/form-data" style="margin: auto; width: 60%;">
    <div class="input-group">
        <label>User Name</label>
        <input type="text" value="<?= htmlspecialchars($user['user_name']) ?>" disabled>
    </div>
    <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    </div>
    <div class="input-group">
        <label>Full Name</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>">
    </div>
    <div class="input-group">
        <label>New Password</label>
        <input type="password" name="new_password" placeholder="Leave blank to keep current password">
    </div>
    <div class="input-group">
        <label>Avatar</label>
        <?php if (!empty($user['avatar'])): ?>
            <img src="<?= htmlspecialchars($user['avatar']) ?>" style="margin-bottom: 0.5rem; width: 60px; height: 60px; border-radius: 50%; border: 1px solid #333; object-fit: cover;">
        <?php endif; ?>
        <input type="file" name="avatar">
    </div>

    <button type="submit" class="btn btn-primary">Update Profile</button>
</form>

<br>
<?php include '../resources/footer.php'; ?>