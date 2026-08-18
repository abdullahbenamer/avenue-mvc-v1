<?php
session_start();
include 'db_config.php';

$user_id = $_SESSION['user_id'];
$email = $_POST['email'];
$full_name = $_POST['full_name'];
$new_password = $_POST['new_password'] ?? '';
$avatar = $_FILES['avatar'];

// Check for email uniqueness
$stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
$stmt->bind_param("si", $email, $user_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    die("Email already in use.");
}

// Handle avatar upload
if ($avatar['name']) {
    $ext = pathinfo($avatar['name'], PATHINFO_EXTENSION);
    $new_avatar_name = "avatar_" . $user_id . "." . $ext;
    move_uploaded_file($avatar['tmp_name'], "photo_uploads/" . $new_avatar_name);
    $avatar_sql = ", avatar = '$new_avatar_name'";
} else {
    $avatar_sql = "";
}

// Change password if provided
if (!empty($new_password)) {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $pass_sql = ", user_password = '$hashed_password'";
} else {
    $pass_sql = "";
}

// Final update
$sql = "UPDATE users SET email = ?, full_name = ? $avatar_sql $pass_sql WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $email, $full_name, $user_id);
$stmt->execute();

echo "Profile updated.";
?>