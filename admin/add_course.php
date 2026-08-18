<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
    <!--<link rel="stylesheet" href="../resources/styles.css">-->

    <!-- <title>MASTER DATA ADMINISTRATION</title>-->
</head>

<body> 
<br>
<!-- Form to add a course -->
<form method="POST" action="save_course.php">
<p class="form_title"><i class="fa-solid fa-book fa-2x"></i></p>
<p class="form_title">Add Course</p>

<div class="input-group">
    <label for="course_title">Course Title (English):</label>
    <input type="text" id="course_title" name="course_title" required>
</div>

<div class="input-group">
    <label for="course_title_a">Course Title (Arabic):</label>
    <input type="text" id="course_title_a" name="course_title_a" required>
</div>


<div class="input-group">
    <label for="course_duration">Course Duration:</label>
    <input type="number" id="course_duration" name="course_duration" min="1" required>
</div>

<div class="input-group">
    <label for="course_uod">Unit of Duration:</label>
    <select id="course_uod" name="course_uod" required>
        <option value="HOURS">HOURS</option>
        <option value="DAYS">DAYS</option>
        <option value="WEEKS">WEEKS</option>
        <option value="MONTH(S)">MONTHS</option>
    </select>
</div>

<div class="input-group">
    <label for="cat_id">Category:</label>
    <select id="cat_id" name="cat_id" required>
        <?php 
        while ($row = $result->fetch_assoc()) { 
            echo "<option value='" . $row['cat_id'] . "'>" . $row['cat_name'] . "</option>";
        } 
        ?>
    </select>
</div>

<div class="input-group">
    <button type="submit" class="btn btn-primary">Save Course</button>
</div>

</form>

<?php
include 'footer.php';
?>