<?php 
// include '../resources/db_config.php'; 
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <link rel="shortcut icon" href="icon.ico" type="image/x-icon" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!--<link rel="stylesheet" href="resources/styles.css">-->
     <link rel="stylesheet" href="styles.css">

      <title>CONTRACT</title>
</head>

<body>
    <header>
    <nav class="nav-links">
            <ul>
                <li><a href="../index.php">HOME <i class="fa-solid fa-home fa-1x"></i></a></li>
                <!--<li id="login"><a href="../login.php">Login</a></li>-->
                <li id="logout"><a href="../logout.php">logout</a></li>
                
            </ul>
        </nav>

        <!-- Responsive navbar -->
        <div id="hamburger-icon" onclick="toggleMobileMenu(this)">
            <div class="bar1"></div>
            <div class="bar2"></div>
            <div class="bar3"></div>
            <ul class="mobile-menu">
                <li><a href="../index.php">Home</a></li>
                <!--<li id="login"><a href="../login.php">Login</a></li>-->
                <li id="logout"><a href="../logout.php">Logout</a></li>
                

            </ul>
        </div>  <!-- End of responsive navbar -->
    </header>

    <div><a href="index.php">
            <img src="../resources/harvest_logo.png" width="200" alt="HARVEST LOGO"></a></div>