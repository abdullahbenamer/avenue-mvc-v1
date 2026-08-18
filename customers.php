<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['user_name'])) {
    header("Location: ../login.php");
    exit();
}

//Check if the user has the required role
 if ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT') {
    header("Location: ../unauthorized.php");
    exit();
 }
 
 include 'header.php';

// Query to fetch all fields from the courses table and cat_name from categories table
$sql = "SELECT courses.course_id, courses.course_title, courses.course_duration, courses.course_uod, categories.cat_name, categories.cat_code  
        FROM courses 
        INNER JOIN categories ON courses.cat_id = categories.cat_id ORDER BY courses.course_title";

$result = $conn->query($sql);

// Check for SQL errors or no results
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
    <link rel="stylesheet" href="../resources/styles.css">

    <!-- <title>MASTER DATA ADMINISTRATION</title>-->
</head>

<body> 
<!-- HTML code with session information and course table display -->
<div class="about" style="margin: 1rem 25% 1rem 25%;">
    <p>
        <?php
        echo "You are logged as <b>";
        print_r($_SESSION['user_role']);
        echo "</b>";
        echo " | User: ";
        echo "<b>";
        print_r($_SESSION['user_name']);
        echo "</b>";
        ?>
    </p>
</div>

  
<div class="about_title" style="margin: 1rem 25% 1rem 25%;">
    <div class="input-group">
       <p> <a href="index.php"><button type="submit" class="btn">Add a New Course</button><i class="fa-solid fa-plus fa-2xl"></i></a></p>
    </div>
    <p>
    <h3>List of Courses <i class="fa-solid fa-file-contract fa-2xl"></i></h3>
    </p>
 
</div>
    
  
  <!------------------------------->
  
<table class="form-group">
    <thead>
        <tr>
            <th>Course ID</th>
            <th>Course Title</th>
            <th>Duration</th>
            <th>UOD</th>
            <th>Category</th>
            <th colspan="2">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo "CRS-" . $row['cat_code'] . '-' .  $row['course_id']; ?></td>
                <td><?php echo strtoupper($row['course_title']); ?></td>
                <td><?php echo $row['course_duration']; ?></td>
                <td><?php echo $row['course_uod']; ?></td>
                <td><?php echo $row['cat_name']; ?></td>
               
                <!--ACTION BUTTONS-->
                
              <td>
              <!--      <a href="edit_course.php?edit=<?php //echo $row['course_id']; ?>" class="edit_btn">Edit</a>-->
             
             Edit & Delete, -soon إنشاء الله</td>

              <!--  <?php //if ($_SESSION['user_role'] === 'ADMIN' || $_SESSION['user_role'] === 'ACCOUNTANT') : ?>-->
                    <td>
              <!--          <a href="read_courses.php?del=<?php //echo $row['course_id']; ?>" class="del_btn">Delete</a>-->- </td>
              <!--  <?php //endif; ?>-->
              
                    
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php
include 'footer.php';
?>
