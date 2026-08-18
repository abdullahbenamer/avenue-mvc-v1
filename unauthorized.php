<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php"); 
    exit();
}

include 'resources/header.php';
include 'resources/db_config.php';

?>

<div class="about_title" style="margin: 1rem 25% 1rem 25%;">
        <p>
            <h4>UNAUTHORIZED ACCESS <i class="fa-solid fa-hand fa-2xl"></i></h4>
            <br>
            <p>THIS PAGE CAN BE ACCSSED ONLY BY <b>ADMIN</b> or <b>ACCOUNTANT</b> user role</p>
        </p>
        
            </div>
            
<?php include 'resources/footer.php'; ?>
