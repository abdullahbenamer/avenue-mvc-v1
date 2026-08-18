<?php
function uploadUserAvatar($file, $user_id, $conn) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return '';
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        die("Invalid file type. Only JPG, PNG, and GIF are allowed.");
    }

  	$upload_dir = __DIR__ . '/uploads/avatars/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Fetch current avatar to delete old one if needed
    $stmt = $conn->prepare("SELECT avatar FROM users WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($old_avatar);
    $stmt->fetch();
    $stmt->close();

    if ($old_avatar && $old_avatar !== 'uploads/avatars/default_male.png') {
        $old_path = __DIR__ . '/../' . $old_avatar;
        if (file_exists($old_path)) {
            unlink($old_path);
        }
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
    $target_path = $upload_dir . $new_filename;

    if (!move_uploaded_file($file['tmp_name'], $target_path)) {
        die("Error uploading file.");
    }

    // Save clean relative path
    $relative_path = 'uploads/avatars/' . $new_filename;

    $conn->query("UPDATE users SET avatar='$relative_path' WHERE user_id=$user_id");

    return ", avatar='$relative_path'";
}
