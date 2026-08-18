<?php
session_start();
require_once '../resources/db_config.php';
if (!isset($_SESSION['user_name']) || 
   ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT')) {
    header("Location: ../unauthorized.php");
    exit();
}

//// === add_user.php ===
if (isset($_POST['add_user'])) {
    $user_name = $_POST['user_name'];
     $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $user_role = $_POST['user_role'];
    $user_password = password_hash($_POST['user_password'], PASSWORD_DEFAULT);

    $avatar = null;
    if (!empty($_FILES['avatar']['name'])) {
        $avatar_name = time() . '_' . basename($_FILES["avatar"]["name"]);
        $avatar_path = "uploads/avatars/" . $avatar_name;
        move_uploaded_file($_FILES["avatar"]["tmp_name"], $avatar_path);
        $avatar = $avatar_path;
    }

    $sql = "INSERT INTO users (user_name, email, full_name, user_role, user_password, avatar)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ssssss', $user_name, $email, $full_name, $user_role, $user_password, $avatar);
    mysqli_stmt_execute($stmt);
    header("Location: users.php");
    exit();
}
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
    <h3><i class="fa-solid fa-plus fa-2xl"></i> Add User <i class="fa-solid fa-user fa-2xl"></i></h3>
    </div>
    <br>
<form method="POST" enctype="multipart/form-data">
     <div class="input-group">
        <label for="count_name">User Name</label>
    <input type="text" name="user_name" placeholder="Username" required>
     </div>
      <div class="input-group">
        <label for="count_name">Email</label>
    <input type="email" name="email" placeholder="Email" required><br>
     </div>
     <div class="input-group">
        <label for="count_name">Full Name</label>
    <input type="text" name="full_name" placeholder="Full Name">
    </div>
     <div class="input-group">
        <label for="count_name">Password</label>
    <input type="password" name="user_password" placeholder="Password" required>
    </div>
     <div class="input-group">
        <label for="count_name">User Role</label>
    <select name="user_role">
         <option>ACCOUNTANT</option>
        <option>ADMIN</option>
        <option>INSTRUCTOR</option>
        <option>MANAGER</option>
        <option>USER</option>
        
    </select>
    </div>
     <div class="input-group">
        <label for="count_name">Avatar</label>
    <input type="file" name="avatar">
    </div>
  
            <button class="btn btn-primary" type="submit" name="add_user">Add User</button>
          
</form>

<br>

<?php include 'footer.php'; ?>