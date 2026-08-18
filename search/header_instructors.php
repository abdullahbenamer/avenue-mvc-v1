<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Instructor Navbar</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- ✅ Add this line to load Font Awesome -->
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">-->
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Instructor Portal</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#instructorNavbar" aria-controls="instructorNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="instructorNavbar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if (!isset($_SESSION['inst_id'])): ?>
          <li class="nav-item">
            <a class="nav-link" href="login_instructor.php">Login</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="logout_instructor.php">Logout</a>
          </li>
        <?php endif; ?>

        <li class="nav-item">
          <a class="nav-link" href="instructor_profile.php">My Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="edit_profile.php">Edit Profile</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
