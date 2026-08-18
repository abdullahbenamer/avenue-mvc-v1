<?php 
include 'db_config.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="styles.css">

    <title>Work flow-Tasks</title>
</head>

<body>
    <header>
    <nav class="nav-links">
            <ul>
                <li><a href="index.php">HOME <i class="fa-solid fa-home fa-1x"></i></a></li>
                 <li><a href="read_orders.php">ORDERS <i class="fa-solid fa-list fa-1x"></i></a></li>
                <li><a href="read_doc.php">QUOTATIONS <i class="fa-solid fa-list fa-1x"></i></a> </li>
                <li><a href="read_cert.php">CERTIFICATES <i class="fa-solid fa-award fa-1x"></i></a></li>
                <li><a href="read_inv.php">INVOICES <i class="fa-solid fa-list"></i></a></li>
                <li><a href="search/">INSTRUCTORS <i class="fa-solid fa-graduation-cap"></i></a></li>
                <li id="login"><a href="login.php">Login</a></li>
                <li id="logout"><a href="logout.php">logout</a></li>
                <li><a href="admin/index.php">ADMIN</a></li>
            </ul>
        </nav>

        <!-- Responsive navbar -->
        <div id="hamburger-icon" onclick="toggleMobileMenu(this)">
            <div class="bar1"></div>
            <div class="bar2"></div>
            <div class="bar3"></div>
            <ul class="mobile-menu">
                <li><a href="read_orders.php">ORDERS</a></li>
                <li><a href="read_doc.php">QUOTATIONS</a></li>
                <li><a href="read_cert.php">CERTIFICATES</a></li>
                <li><a href="read_inv.php">INVOICES</a></li>
                <li><a href="search/">INSTRUCTORS</a></li>
                <li id="login"><a href="login.php">Login</a></li>
                <li id="logout"><a href="logout.php">Logout</a></li>
                <li><a href="admin/index.php">ADMIN</a></li>

            </ul>
        </div>  <!-- End of responsive navbar -->
    </header>

    <div><a href="index.php">
            <img src="resources/logo.png" alt="AVENUE INTERNATIONAL"></a></div>