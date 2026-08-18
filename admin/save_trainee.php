<?php
// Include database connection
include '../resources/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect POST data
    $full_name = $_POST['full_name'];
    $payroll_no = $_POST['payroll_no'];
    $cust_id = $_POST['cust_id'];
    $course_id = $_POST['course_id'];
    $course_date = $_POST['course_date'];
    
    // Insert data into database
    $sql = "INSERT INTO trainees (full_name, payroll_no, cust_id, course_id, course_date) 
            VALUES ('$full_name', '$payroll_no', '$cust_id', '$course_id', '$course_date')";
    
    if (mysqli_query($conn, $sql)) {
        echo "New trainee added successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
    
    // Close connection
    mysqli_close($conn);
}
?>
