<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../resources/db_config.php';

if (!isset($_SESSION['user_name']) || ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT')) {
    header("Location: <?= BASE_URL ?>unauthorized.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="admin_css/user_module.css">
      </head>
<body>

<?php include 'header.php'; ?>
<br>
<div class="about_title" style="margin: 1rem 25%;">
    <h3>Manage Users <i class="fa-solid fa-users fa-2xl"></i></h3>
    </div>
    <br>
<div style="text-align: center;">
    <a href="add_user.php" class="btn-add">Add New User <i class="fa-solid fa-plus fa-1x"></i></a>
</div>
<br>
<div class="grid-container">
    <div class="grid-header">Username</div>
      <div class="grid-header">Email</div>
    <div class="grid-header">Full Name</div>
      <div class="grid-header">Role</div>
    <div class="grid-header">Avatar</div>
    <div class="grid-header">Actions</div>

    <?php
    $row_count = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $row_class = ($row_count % 2 == 0) ? "row-even" : "row-odd";
        ?>
        <div class="grid-item <?= $row_class ?>"><?= htmlspecialchars($row['user_name']) ?></div>
        <div class="grid-item <?= $row_class ?>"><?= htmlspecialchars($row['email']) ?></div>
        <div class="grid-item <?= $row_class ?>"><?= htmlspecialchars($row['full_name']) ?></div>
        <div class="grid-item <?= $row_class ?>"><?= htmlspecialchars($row['user_role']) ?></div>
        <div class="grid-item <?= $row_class ?>">
            <?php if (!empty($row['avatar'])): ?>
                <img src="<?= htmlspecialchars($row['avatar']) ?>" class="avatar-preview">
            <?php else: ?>
                <span>No Avatar</span>
            <?php endif; ?>
        </div>
        <div class="grid-item <?= $row_class ?>">
            <a href="edit_user.php?user_id=<?= $row['user_id'] ?>" class="btn-edit">Edit</a>
            <a href="delete_user.php?user_id=<?= $row['user_id'] ?>" class="btn-delete" onclick="return confirm('Are you sure?');">Delete</a>
        </div>
        <?php
        $row_count++;
    }
    ?>
</div>

<?php include 'footer.php'; ?>
