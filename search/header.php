<?php
if (!isset($_SESSION['user_name'])) {
  header("Location: ../login.php"); // Redirect if not logged in
  exit();
}

$default_avatar = "../admin/uploads/avatars/default_male.png";
$avatar_filename = $_SESSION['user_avatar'] ?? null;

$avatar_path = (!empty($avatar_filename) && file_exists("/admin/" . $avatar_filename)) ? "/admin/" . $avatar_filename : $default_avatar;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
     <link rel="shortcut icon" href="icon.ico" type="image/x-icon" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400;500;600;700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;1,300&family=Tajawal:wght@200;300;400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <!-- Fontawesome from local files -->
  <link href="fa/css/all.min.css" rel="stylesheet">
  <style>
    .nav-list {
  display: flex;
  align-items: center;
  list-style: none;
  padding: 0;
  margin: 0;
  gap: 10px;
}

.nav-list .logout {
  margin-left: auto;
}

.header-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
  /*object-position: top;*/
  /*object-position: 50% 20%;*/
  border: 1px solid #fff;
}

li.user-info {
  display: flex;
  align-items: center;
  gap: 10px;
  color: white;
  padding-right: 15px;
}
  </style>
  <title>Instructors Database</title>
  <!-- Custom Styles -->
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <div class="instructor-header">
      <div class="instructor-nav">
        <nav>
          <ul>
            <li><a href="../index.php">[START PAGE] <i class="fa fa-home fa-1x"></i></a></li>
            <li>|</li>
            <li><a href="search.php">SEARCH <i class="fa fa-search fa-1x"></i></a></li>
            <li>|</li>
            <li><a href="create.php">NEW <i class="fa fa-plus fa-1x"></i></a></li>
            <li>|</li>
            <li><a href="index.php">INSTRUCTORS <i class="fa-solid fa-person-chalkboard fa-1x"></i></a></li>
            <li class="white-text right" style="display: flex; align-items: center; gap: 8px; padding-right: 10px;">
                 
    <?php if (isset($_SESSION['user_role'])): ?>
<?php
$default_avatar = "../admin/uploads/avatars/default_male.jpg";

// Initialize values so they’re always defined
$avatar_from_session = $_SESSION['user_avatar'] ?? '';
$avatar_web_path = '';
$avatar_full_server_path = '';

if (!empty($avatar_from_session)) {
    // Normalize and clean the avatar path
    $avatar_relative_path = ltrim($avatar_from_session, '/');
    $avatar_web_path = "../admin/" . $avatar_relative_path;
    $avatar_full_server_path = realpath(__DIR__ . "/../admin/" . $avatar_relative_path);

    $avatar_path = ($avatar_full_server_path && file_exists($avatar_full_server_path)) ? $avatar_web_path : $default_avatar;
} else {
    $avatar_path = $default_avatar;
}
?>
<li class="user-info">
      <img src="<?= htmlspecialchars($avatar_path) ?>" alt="avatar" class="header-avatar">
    <span>
        User: <b><?= htmlspecialchars($_SESSION['user_name']) ?></b> ||
        Role: <b><?= htmlspecialchars($_SESSION['user_role']) ?></b>
    </span>
</li>
<li id="logout"><a href="../logout.php">logout</a></li>
<?php endif; ?>
          </ul>
        </nav>
      </div>
    </div>
    <!-- PORTABLE DEVICES Responsive Menue -->
    <div id="hamburger-icon" onclick="toggleMobileMenu(this)">
      <div class="bar1"></div>
      <div class="bar2"></div>
      <div class="bar3"></div>
      <ul class="mobile-menu">
        <li><a href="../index.php">HOME <i class="fa fa-home fa-1x"></i></a></li>
        <li><a href="search.php">SEARCH <i class="fa fa-search fa-1x"></i></a></li>
        <li><a href="create.php">NEW <i class="fa fa-plus fa-1x"></i></a></li>
        <li><a href="index.php">INSTRUCTORS <i class="fa-solid fa-person-chalkboard fa-1x"></i></a></li>
       
      </ul>
    </div>

            </div>
     </header>
  <div class="logo">
    <img width="300" src="../resources/logo.png" alt="/resources/logo.png">
  </div>