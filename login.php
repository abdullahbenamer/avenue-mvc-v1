<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ✅ BLOCK MOBILE FIRST (must be before any output!)
function isMobileDevice() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $mobileAgents = ['iPhone','Android','webOS','BlackBerry','iPod','Opera Mini','IEMobile','Mobile'];
    foreach ($mobileAgents as $device) {
        if (stripos($userAgent, $device) !== false) {
            return true;
        }
    }
    return false;
}

if (isMobileDevice()) {
    header("Location: login_mobile_blocked.html");
    exit();
}

// ✅ THEN continue with normal session logic
session_start();
include 'resources/db_config.php';

if (isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['user_name'];
    $password = $_POST['user_password'];

    $sql = "SELECT * FROM users WHERE user_name=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['user_password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $username;
            $_SESSION['user_role'] = $user['user_role'];
            $_SESSION['user_avatar'] = $user['avatar'];

            if (isset($_POST['remember_me'])) {
                setcookie('user_name', $username, time() + (86400 * 30), "/", "", true, true);
                setcookie('user_password', $password, time() + (86400 * 30), "/", "", true, true);
            } else {
                setcookie('user_name', '', time() - 3600, "/");
                setcookie('user_password', '', time() - 3600, "/");
            }

            header("Location: index.php");
            exit();
        } else {
            $error = "❌ Invalid password. Please try again";
        }
    } else {
        $error = "❌ Invalid username. Please try again";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <!-- ✅ Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Login</title>
    <link rel="stylesheet" href="resources/styles.css">
</head>
<body>

<header>
<div style="color: yellow; text-align:center;"><!-- Empty black bar on top --></div></header>
<div><a href="index.php">
    <img src="resources/logo.png" alt="AVENUE INTERNATIONAL"></a></div>
<div class="about">
    
<?php if (isset($error)): ?>
    <div class="error-msg">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>
</div>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <!-- Centered Button -->
        <div class="mx-auto">
            <a href="search/login_instructor.php" class="btn btn-lg" 
   style="text-decoration: none; background-color: #28a745; color: #fff; border: none;">
                <i class="fas fa-chalkboard-teacher"></i> <b>Instructor?</b>  Go to Instructors Login page!
            </a>
        </div>
    </div>
</nav>
<!-- HTML login form -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="input-group">
        <label for="user_name">Username:</label>
        <input type="text" id="user_name" name="user_name" value="<?php echo isset($_COOKIE['user_name']) ? $_COOKIE['user_name'] : ''; ?>">
</div>
<div class="input-group">
    <label for="user_password">Password:</label>
    <div style="position: relative; width: 100%;">
        <input type="password" id="user_password" name="user_password" 
               value="<?php echo isset($_COOKIE['user_password']) ? $_COOKIE['user_password'] : ''; ?>" 
               style="width: 100%; padding-right: 40px;">

        <!-- Eye toggle button -->
        <span onclick="togglePassword()" 
              style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
            <i class="fas fa-eye" id="toggleIcon"></i>
        </span>
    </div>
</div>

        <div style="display: inline-block;">
            <input type="checkbox" name="remember_me" id="remember_me" <?php echo isset($_COOKIE['user_name']) ? 'checked' : ''; ?>>
            <label for="remember_me">Remember Me</label>
        </div>
       
 </div>
  
        <button class="btn btn-primary" type="submit"><i class="fas fa-user fa-lg me-2"></i> <b>User</b> Login</button>
    </div>
     </div>
</form>

<div class="about">
    <p>To access the application, please <b>Log in</b>.</p>
    <p>Kindly contact the administrator to obtain your credentials.</p>
    <p>For assistance: +90 (534) 921 6965, cto@avenueinternational.net</p>
    </div>
<script>
function togglePassword() {
    const passwordField = document.getElementById("user_password");
    const toggleIcon = document.getElementById("toggleIcon");

    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
    } else {
        passwordField.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
    }
}
</script>
<?php
include 'resources/footer.php';
?>