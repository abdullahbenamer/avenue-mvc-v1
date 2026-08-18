<?php
session_start();
include 'db_config.php';

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
$user = $result->fetch_assoc();
?>

<h2>My Profile</h2>
<form action="process_profile_update.php" method="POST" enctype="multipart/form-data">
  Email: <input type="email" name="email" value="<?= $user['email'] ?>" required><br>
  Full Name: <input type="text" name="full_name" value="<?= $user['full_name'] ?>"><br>
  New Password: <input type="password" name="new_password"><br>
  Change Avatar: <input type="file" name="avatar"><br>
  <button type="submit">Update Profile</button>
</form>
<br>