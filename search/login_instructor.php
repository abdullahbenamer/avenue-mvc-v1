<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include '../resources/db_config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT inst_id, full_name, password FROM instructors WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

if (!isset($_SESSION['inst_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];

    $stmt = $conn->prepare("SELECT inst_id, full_name FROM instructors WHERE remember_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['inst_id'] = $user['inst_id'];
        $_SESSION['full_name'] = $user['full_name'];
        header("Location: instructor_profile.php");
        exit();
    }
}
        
        if (password_verify($password, $user['password'])) {
    // ✅ SUCCESS: set session and redirect
    $_SESSION['inst_id'] = $user['inst_id'];
    $_SESSION['full_name'] = $user['full_name'];

    // ✅ If Remember Me checked
    if (isset($_POST['remember_me'])) {
        // Create a token
        $token = bin2hex(random_bytes(16));

        // Store token in DB (new column: remember_token)
        $stmt = $conn->prepare("UPDATE instructors SET remember_token = ? WHERE inst_id = ?");
        $stmt->bind_param("si", $token, $user['inst_id']);
        $stmt->execute();

        // Store token in cookie (valid for 30 days)
        setcookie("remember_token", $token, time() + (86400 * 30), "/", "", false, true);
    }

    header("Location: instructor_profile.php");
    exit();
} else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No instructor found with that email.";
    }
}
?>
<!-- Bootstrap Styled Login Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instructor Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- ✅ Include Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="logo text-center mt-3">
        <img width="300" src="../resources/logo.png" alt="Logo">
    </div>

    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 400px;">
            <div class="card-body">
                <!-- ✅ Icon added before title -->
                <h4 class="card-title text-center">
                    <i class="fas fa-chalkboard-user fa-lg me-2"></i>Instructor Login
                </h4>
<br>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                
                <form method="POST" autocomplete="off">
    <div class="mb-3">
        Email:
        <input type="email" name="email" id="login_email" 
               placeholder="Login Email" required 
               autocomplete="new-email" class="form-control">
    </div>
   <div class="mb-3 position-relative">
    Password:
    <div class="input-group">
        <input type="password" name="password" id="login_password" 
               placeholder="Login Password" required 
               autocomplete="new-password" class="form-control">
        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
            <i class="fa-solid fa-eye"></i>
        </button>
    </div>
</div>

<div class="mb-3 form-check">
    <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
    <label class="form-check-label" for="remember_me">Remember Me!</label>
</div>

    <button type="submit" class="btn btn-primary w-100">Login</button>
</form>
<br>
  <hr>
 <p><i class="fas fa-chalkboard-user fa-lg me-2"></i> I am new here! <a href="https://avenueinternational.net/instructors/">Add a new profile</a></p>

 <p style="font-style: italic;">You will be notified once your basic profile approved to update it</p>
 <p><small style="color: red;">Please <strong>don't create duplicate account</strong> if you have one.</small></p>
            <hr>
            <p>For assistance<br>
<i class="fa-solid fa-mobile"></i> +90 (545) 508 6099 or <br>
<i class="fa-solid fa-envelope"></i> <a href="https://avenueinternational.net/instructors/#footer-contactus">Contact Us</a></p>
<hr>
            </div>
            
</div>
        
</div>
<script>
document.getElementById("togglePassword").addEventListener("click", function () {
    const passwordField = document.getElementById("login_password");
    const icon = this.querySelector("i");

    if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        passwordField.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
});
</script>
    <br><br><br>
    <?php include('footer.php'); ?>
