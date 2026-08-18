<?php
include 'resources/db_config.php';

if (isset($_GET['cust_id'])) {
    $cust_id = $_GET['cust_id'];

    $query = "SELECT course_id, course_title FROM quotations WHERE cust_id = '$cust_id'";
    $result = mysqli_query($conn, $query);

    $courses = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $courses[] = $row;
    }

    foreach ($courses as $course) {
        echo "<option value='" . $course['course_id'] . "'>" . $course['course_title'] . "</option>";
    }
}
?>
