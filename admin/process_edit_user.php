<?php
session_start();
include '../resources/db_config.php';
include 'avatar_upload.php';

if ($_SESSION['user_role'] !== 'ADMIN') {
    die("Access denied.");
}

$user_id = $_POST['user_id'];
$user_name = $_POST['user_name'];
$email = $_POST['email'];
$full_name = $_POST['full_name'];
$user_role = $_POST['user_role'];
$new_password = $_POST['new_password'];

// Unique check
$stmt = $conn->prepare("SELECT user_id FROM users WHERE (user_name = ? OR email = ?) AND user_id != ?");
$stmt->bind_param("ssi", $user_name, $email, $user_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    die("Username or email already in use.");
}

// Avatar update (shared function)
$update_avatar = uploadUserAvatar($_FILES['avatar'], $user_id, $conn);

// Password update
$update_password = '';
if (!empty($new_password)) {
    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $update_password = ", user_password='$hashed'";
}

$sql = "UPDATE users SET user_name=?, email=?, full_name=?, user_role=? $update_avatar $update_password WHERE user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $user_name, $email, $full_name, $user_role, $user_id);
$stmt->execute();

header("Location: users.php");
exit();
