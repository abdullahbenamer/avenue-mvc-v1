<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: ../login.php");
    exit();
}

// Check if the user has the required role
if ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT') {
    header("Location: ../unauthorized.php");
    exit();
}

include 'header.php';
include '../resources/db_config.php'; // Include the database configuration

// Fetch categories from the database
$sql = "SELECT cat_id, cat_name FROM categories";
$result = $conn->query($sql);

if ($result === false) {
    echo "Error: " . $conn->error;
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
   .button-links {
  display: flex;
  flex-direction: column;
  gap: 10px;
  max-width: 250px;
  margin: 1rem auto;
}

.btn-link {
  display: inline-block;
  padding: 10px 20px;
  background-color: #CC0A0B;
  color: white;
  font-weight: 500;
  text-align: center;
  border-radius: 6px;
  text-decoration: none;
  transition: background-color 0.3s ease;
}

.btn-link:hover {
  background-color: #E89B9C;
  color: #444;
}

    </style>
   
    <title>MASTER DATA MANAGEMENT</title>
</head>

<body> 
<div style="text-align: center;">
<p class="form_title">MASTER  DATA  MANAGEMENT</p>
<div class="button-links">
  <a href="users.php" class="btn-link">USERS <i class="fa-solid fa-users"></i></a>
  <a href="courses.php" class="btn-link">COURSES <i class="fa-solid fa-book"></i></a>
  <a href="customers.php" class="btn-link">CUSTOMERS <i class="fa-solid fa-building"></i></a>
  <a href="countries.php" class="btn-link">COUNTRIES <i class="fa-solid fa-globe"></i></a>
  <a href="venues.php" class="btn-link">Training VENUES <i class="fa-solid fa-location-dot"></i></a>
</div>
</div>

<?php
include 'footer.php';
?>