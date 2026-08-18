<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include '../resources/db_config.php';
include 'avatar_upload.php';

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];
$email = $_POST['email'];
$full_name = $_POST['full_name'];
$new_password = $_POST['new_password'];

// Check for unique email
$stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
$stmt->bind_param("si", $email, $user_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    die("Email already in use.");
}

// Avatar update (shared function)
$update_avatar = uploadUserAvatar($_FILES['avatar'], $user_id, $conn);

// Password update
$pass_sql = '';
if (!empty($new_password)) {
    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $pass_sql = ", user_password='$hashed'";
}

$sql = "UPDATE users SET email=?, full_name=? $update_avatar $pass_sql WHERE user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $email, $full_name, $user_id);
$stmt->execute();

echo "Profile updated. <a href='profile.php'>Back to Profile</a>";
