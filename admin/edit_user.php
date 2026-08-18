<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

include '../resources/db_config.php';


if (!isset($_SESSION['user_name']) || ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT')) {
    header("Location: ../unauthorized.php");
    exit();
}

$user_id = (int)$_GET['user_id'];
$result = $conn->query("SELECT * FROM users WHERE user_id = $user_id");

if (!$result || $result->num_rows === 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="admin_css/user_module.css">
</head>
<body>
<?php include 'header.php'; ?>
<br>

<div class="about_title" style="margin: 1rem 25%;">
    <h3>Edit User <i class="fa-solid fa-user-pen fa-2xl"></i></h3>
</div>
<br>

<form action="process_edit_user.php" method="POST" enctype="multipart/form-data" style="margin: auto; width: 60%;">
  <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">

  <div class="input-group">
      <label>Username</label>
      <input type="text" name="user_name" value="<?= htmlspecialchars($user['user_name']) ?>" required>
  </div>

  <div class="input-group">
      <label>Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
  </div>

  <div class="input-group">
      <label>Role</label>
      <select name="user_role" required>
          <option <?= $user['user_role'] == 'ADMIN' ? 'selected' : '' ?>>ADMIN</option>
          <option <?= $user['user_role'] == 'ACCOUNTANT' ? 'selected' : '' ?>>ACCOUNTANT</option>
          <option <?= $user['user_role'] == 'USER' ? 'selected' : '' ?>>USER</option>
          <option <?= $user['user_role'] == 'MANAGER' ? 'selected' : '' ?>>MANAGER</option>
          <option <?= $user['user_role'] == 'INSTRUCTOR' ? 'selected' : '' ?>>INSTRUCTOR</option>
           <option <?= $user['user_role'] == 'GUEST' ? 'selected' : '' ?>>GUEST</option>
      </select>
  </div>

  <div class="input-group">
      <label>Full Name</label>
      <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>">
  </div>

  <div class="input-group">
      <label>New Password <small>[Leave blank to keep current password]</small></label>
      <input type="password" name="new_password" placeholder="Leave intact to keep current password">
  </div>

<?php if (!empty($user['avatar'])): ?>
            <img src="<?= htmlspecialchars($user['avatar']) ?>" style="margin-bottom: 0.5rem; width: 60px; height: 60px; border-radius: 50%; border: 1px solid #333; object-fit: cover;">
        <?php endif; ?>
  <div class="input-group">
      <label>Change Avatar <small>[Leave intact to keep current Avatar]</small></label>
      <input type="file" name="avatar">
  </div>

  <button type="submit" class="btn btn-primary">Update</button>
</form>

<br>
<?php include 'footer.php'; ?>

