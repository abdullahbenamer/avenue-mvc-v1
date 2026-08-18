<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../resources/db_config.php';
include 'header.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect POST data
    $course_title = $_POST['course_title'];
    $course_title_a = $_POST['course_title_a'];
    $course_duration = $_POST['course_duration'];
    $course_uod = $_POST['course_uod'];
    $cat_id = $_POST['cat_id'];

    // Correct prepared statement
    $sql = "INSERT INTO courses (course_title, course_title_a, course_duration, course_uod, cat_id) 
            VALUES (?, ?, ?, ?, ?)";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisi", $course_title, $course_title_a, $course_duration, $course_uod, $cat_id);
        $stmt->execute();

        echo "<div class='success'>New course added successfully.</div>";
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            echo "<div class='error'>Course Title '" . htmlspecialchars($course_title) . "' already exists...!</div>";
        } else {
            echo "<div class='error'>There is a problem saving the course: " . htmlspecialchars($course_title) . "<br>Error: " . $e->getMessage() . "</div>";
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> 
    <!--<link rel="stylesheet" href="../resources/styles.css">-->
     <link rel="stylesheet" href="styles.css">
</head>

<body>

<?php include 'footer.php'; ?>


