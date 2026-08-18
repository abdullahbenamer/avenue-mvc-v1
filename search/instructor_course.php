<?php 

if ($query_run) {
    $inst_id = mysqli_insert_id($conn); // Get the last inserted instructor ID
    
    if (!empty($_POST['course_id'])) {
        foreach ($_POST['course_id'] as $course_id) {
            $course_query = "INSERT INTO instructor_course (inst_id, course_id) VALUES ('$inst_id', '$course_id')";
            mysqli_query($conn, $course_query);
        }
    }

    $_SESSION['message'] = "Instructor Created Successfully :)";
    header("Location: create.php");
    exit(0);
}

?>