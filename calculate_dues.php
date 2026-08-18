<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

//Check if the user has the required role
 if ($_SESSION['user_role'] != 'ACCOUNTANT' && $_SESSION['user_role'] != 'ADMIN') {
    header("Location: unauthorized.php");
    exit();
 }
include 'resources/header.php';
include 'resources/db_config.php';

// Fetch instructors and their courses
$query = "SELECT ic.inst_course_id, i.full_name, c.course_title, c.course_duration, c.course_uod 
          FROM instructor_courses ic
          JOIN instructors i ON ic.inst_id = i.inst_id
          JOIN courses c ON ic.course_id = c.course_id";
$result = mysqli_query($conn, $query);


if (isset($_POST['calculate'])) {
    // Check if the form fields exist and are arrays
    if (isset($_POST['inst_course_id']) && is_array($_POST['inst_course_id']) &&
        isset($_POST['rate_type']) && is_array($_POST['rate_type']) &&
        isset($_POST['rate_value']) && is_array($_POST['rate_value']) &&
        isset($_POST['actual_duration']) && is_array($_POST['actual_duration']) &&
        isset($_POST['number_of_attendees']) && is_array($_POST['number_of_attendees'])) {

        $inst_course_ids = $_POST['inst_course_id'];
        $rate_types = $_POST['rate_type'];
        $rate_values = $_POST['rate_value'];
        $actual_durations = $_POST['actual_duration'];
        $number_of_attendees = $_POST['number_of_attendees'];

        for ($i = 0; $i < count($inst_course_ids); $i++) {
            $inst_course_id = $inst_course_ids[$i];
            $rate_type = $rate_types[$i];
            $rate_value = $rate_values[$i];
            $actual_duration = $actual_durations[$i];
            $attendees = $number_of_attendees[$i];

            // Calculate due amount
            $due_amount = 0;
            if ($rate_type == 'Daily') {
                $due_amount = $rate_value * $actual_duration;
            } elseif ($rate_type == 'Hourly') {
                $due_amount = $rate_value * $actual_duration;
            } elseif ($rate_type == 'Monthly') {
                $due_amount = $rate_value * ($actual_duration / 30); // Assuming 30 days in a month
            }

            // Insert into trainer_dues table
            $query = "INSERT INTO trainer_dues (inst_course_id, rate_type, rate_value, actual_duration, due_amount, number_of_attendees) 
                      VALUES ('$inst_course_id', '$rate_type', '$rate_value', '$actual_duration', '$due_amount', '$attendees')";
            mysqli_query($conn, $query);
        }

        echo "<p>Dues calculated and saved successfully!</p>";
    } else {
        echo "<p>Error: Form data is incomplete or missing. Please fill out all fields.</p>";
    }
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculate Trainers' Dues</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f2f2f2; }
        input[type="text"], select { width: 100%; padding: 5px; margin: 5px 0; }
        input[type="submit"] { padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        input[type="submit"]:hover { background-color: #45a049; }
    </style>
</head>
<body>

<h2>Calculate Trainers' Dues</h2>

<form method="POST" action="">
    <table>
        <thead>
            <tr>
                <th>Instructor</th>
                <th>Course</th>
                <th>Rate Type</th>
                <th>Rate Value</th>
                <th>Actual Duration</th>
                <th>Number of Attendees</th>
                <th>Calculate</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['full_name']; ?></td>
                    <td><?= $row['course_title']; ?> (<?= $row['course_duration'] . ' ' . $row['course_uod']; ?>)</td>
                    <td>
                        <select name="rate_type[]">
                            <option value="Daily">Daily</option>
                            <option value="Hourly">Hourly</option>
                            <option value="Monthly">Monthly</option>
                        </select>
                    </td>
                    <td><input type="text" name="rate_value[]" required></td>
                    <td><input type="text" name="actual_duration[]" required></td>
                    <td><input type="text" name="number_of_attendees[]" required></td>
                    <td>
                        <input type="hidden" name="inst_course_id[]" value="<?= $row['inst_course_id']; ?>">
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <input type="submit" name="calculate" value="Calculate Dues">
</form>

<?php

include 'resources/footer.php';
?>


