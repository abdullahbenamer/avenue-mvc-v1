<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'db_config.php';
include_once 'config.php';// for Base URL

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
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="shortcut icon" href="icon.ico" type="image/x-icon" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <link rel="stylesheet" href="<?= BASE_URL ?>resources/styles.css">
        
          <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">-->
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!--<link rel="stylesheet" href="resources/styles.css">-->
        
        <style>
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

            .header-avatar {
            margin-bottom: 0.5rem;
            display: block;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            object-fit: cover;
            border: 1px solid #fff;
            }
            
        </style>

        <title>AVENUE INTERNATIONAL</title>
    </head>

    <body>
        <header>
            <nav class="navbar">
                <ul class="main-menu">
                    <li><a href="<?= BASE_URL ?>index.php">HOME <i class="fa-solid fa-home"></i></a></li>

                    <!-- CUSTOMER -->
                    <li class="has-submenu">
                        <a href="#">CUSTOMER <i class="fa-solid fa-user-tag"></i></a>
                        <ul class="submenu">
                            <li><a href="<?= BASE_URL ?>read_orders.php">Orders</a></li>
                            <li><a href="<?= BASE_URL ?>read_doc.php">Quotations</a></li>
                            <li><a href="<?= BASE_URL ?>read_inv.php">Invoices</a></li>
                        </ul>
                    </li>

                    <!-- TRAINING -->
                    <li class="has-submenu">
                        <a href="#">TRAINING <i class="fa-solid fa-graduation-cap"></i></a>
                        <ul class="submenu">
                            <li><a href="<?= BASE_URL ?>view_instance.php">Groups <i class="fa-solid fa-users"></i></a></li>
                            <li><a href="<?= BASE_URL ?>quotation_participants.php">Attendance <i class="fa-solid fa-file"></i></a></li>
                            <li><a href="<?= BASE_URL ?>read_cert.php">Certificates <i class="fa-solid fa-graduation-cap"></i></a></li>
                        </ul>
                    </li>

                    <!-- INSTRUCTORS -->
                    <li class="has-submenu">
                        <a href="#">INSTRUCTORS <i class="fa-solid fa-chalkboard-user"></i></a>
                        <ul class="submenu">
                            <li><a href="<?= BASE_URL ?>search/">Profiles <i class="fa-solid fa-user"></i></a></li>
                              <li class="has-submenu">
                                <a href="#">Contracts <i class="fa-solid fa-handshake"></i></a>
                                <ul class="submenu">
                                    <li><a href="<?= BASE_URL ?>contracts/contract_form.php">
                                    <img src="<?= BASE_URL ?>resources/avenuelogo-white.png" alt="Avenue Logo" 
                                    style="width:100px; vertical-align:middle; margin-right:5px;">Avenue International</a>
                                    </li>
                                    
                                    <li><a href="<?= BASE_URL ?>contracts/contract_form_harvest.php">  <img src="<?= BASE_URL ?>resources/harvest_logo.png" alt="Avenue Logo" 
                                    style="width:100px; vertical-align:middle; margin-right:5px;">Harvest (Total Rate)</a></li>
                                    
                                     <li><a href="<?= BASE_URL ?>contracts/contract_form_harvest_monthly.php">  <img src="<?= BASE_URL ?>resources/harvest_logo.png" alt="Avenue Logo" 
                                    style="width:100px; vertical-align:middle; margin-right:5px;">Harvest (Monthly Rate)</a></li>
                                </ul>
                            </li>
                            <li><a href="<?= BASE_URL ?>instructor_due_list.php">Instructor Dues <i class="fa-solid fa-dollar-sign"></i></a></li>
                        </ul>
                    </li>

                    <!-- Login/Logout Logic -->
                    <?php if (!isset($_SESSION['user_name'])): ?>
                        <li><a href="<?= BASE_URL ?>login.php">Login <i class="fa-solid fa-right-to-bracket"></i></a></li>
                    <?php else: ?>
                        <li><a style="color: yellow;" href="<?= BASE_URL ?>logout.php">Logout <i class="fa-solid fa-right-from-bracket"></i></a></li>
                    <?php endif; ?>

                    <!-- MASTER DATA -->
                    <li class="has-submenu">
                        <a href="<?= BASE_URL ?>admin/index.php">MASTER DATA <i class="fa-solid fa-database"></i></a>
                        <ul class="submenu">
                            <li><a href="<?= BASE_URL ?>admin/courses.php">Courses <i class="fa-solid fa-graduation-cap"></i></a></li>
                            <li><a href="<?= BASE_URL ?>admin/customers.php">Customers <i class="fa-solid fa-building"></i></a></li>
                            <li><a href="<?= BASE_URL ?>admin/venues.php">Venues <i class="fa-solid fa-map"></i></a></li>
                            <li><a href="<?= BASE_URL ?>admin/countries.php">Countries <i class="fa-solid fa-globe"></i></a></li>
                            <li><a href="<?= BASE_URL ?>admin/users.php">Users <i class="fa-solid fa-users"></i></a></li>
                        </ul>
                    </li>
                  
                    <li class="white-text right" style="display: flex; align-items: center; gap: 8px;">
                        <img src="<?= htmlspecialchars($avatar_path) ?>" alt="avatar" class="header-avatar">
                        <span>
                            User: <b><?= htmlspecialchars($_SESSION['user_name']) ?></b> ||
                            Role: <b><?= htmlspecialchars($_SESSION['user_role']) ?></b>
                        </span>
                    </li>
                         <li><a style="color:yellow;font-weight:bold;" href="<?= BASE_URL ?>admin/profile.php" class="nav-link"> <i class="fa fa-user-circle" style="font-size: 1.5rem;"></i> My Profile [edit]</a></li>
                                </ul>
                          </nav>
        </header>
        <div><a href="index.php">
                <img src="<?= BASE_URL ?>resources/logo.png" alt="AVENUE INTERNATIONAL"></a></div>