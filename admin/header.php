<?php
 include '../resources/auth_check.php'; 
 include '../resources/db_config.php'; 
 include '../resources/config.php'; 

// Fetch avatar path
$avatar_path = '';
$current_username = $_SESSION['user_name'];

$query = "SELECT avatar FROM users WHERE user_name = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 's', $current_username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $avatar);
if (mysqli_stmt_fetch($stmt)) {
$avatar_path = BASE_URL . 'admin/' . ($avatar ?: 'uploads/avatars/default_male.png');
}
mysqli_stmt_close($stmt);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <link rel="shortcut icon" href="icon.ico" type="image/x-icon" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>resources/styles.css">
    
    <style>
    .nav-links ul {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: space-between;
    gap: 5px;
}

.nav-links li {
    white-space: nowrap; /* Prevent long items from wrapping text */
    display: flex;
    align-items: center;
}

.nav-links li h1 {
    font-size: 1.1rem;
    margin: 0;
   }

.header-avatar {
    /*margin-bottom: 0.2rem;*/
    margin-top: 0.2rem;
    display: block;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    object-fit: cover;
    border: 1px solid #333;
}

    ul {
        list-style: none; 
        padding: 0; 
        margin: 0;
    }
    
    .white-text {
    color: white;
        }
        
        .right {
            text-align: right; 
        }
        
               </style>
     <link rel="stylesheet" href="styles.css">

      <title>MASTER DATA ADMINISTRATION</title>
</head>

<body>
    <header>
    <nav class="nav-links">
            <ul>
                <li><a style="color: yellow; font-weight: 600;" href="<?= BASE_URL ?>index.php">[START PAGE] <i class="fa-solid fa-home fa-1x"></i></a></li>
                 <li><a href="<?= BASE_URL ?>admin/courses.php">COURSES <i class="fa-solid fa-book fa-1x"></i></a></li>
                 <li><a href="<?= BASE_URL ?>admin/customers.php">CUSTOMERS <i class="fa-solid fa-building fa-1x"></i></a></li>
                     <li><a href="<?= BASE_URL ?>admin/countries.php">COUNTRIES <i class="fa-solid fa-globe fa-1x"></i></a></li>
                     <li><a href="venues.php">VENUES <i class="fa-solid fa-location-dot fa-lx"></i></a></li>
                       <li><a href="<?= BASE_URL ?>admin/users.php">USERS <i class="fa-solid fa-users fa-1x"></i></a></li>
                     
               <?php if (!isset($_SESSION['user_role'])): ?>
                 <li><a href="<?= BASE_URL ?>login.php">Login</a></li>
                <?php else: ?>
                <li><a style="color: yellow; font-weight: 600;" href="../logout.php">Logout <i class="fa-solid fa-right-from-bracket"></i></a></li>
                <?php endif; ?>
                <!--<li><a href="<?//= BASE_URL ?>admin/index.php">MASTER DATA <i class="fa-solid fa-database fa-1x"></i></a></li>-->
              <li class="white-text right" style="display: flex; align-items: center; gap: 8px;">
                <img src="<?= htmlspecialchars($avatar_path) ?>" alt="avatar" class="header-avatar">
            <span>
            User: <b><?= htmlspecialchars($_SESSION['user_name']) ?></b> || 
            Role: <b><?= htmlspecialchars($_SESSION['user_role']) ?></b>
        </span>
           </li>
                <li><a style="color:yellow;font-weight:bold;" href="<?= BASE_URL ?>admin/profile.php" class="nav-link"><i class="fa fa-user-circle" style="font-size: 1.5rem;"></i> My Profile [edit]</a></li>
               </ul>
        </nav>
    </header>

    <div><a href="index.php">
            <img src="<?= BASE_URL ?>resources/logo.png" alt="AVENUE INTERNATIONAL"></a></div>