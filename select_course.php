<?php
session_start();
include 'resources/db_config.php';
include 'resources/header.php';

// Fetch all courses that have participants
$course_query = "SELECT DISTINCT c.course_id, c.course_title FROM courses c
                 JOIN quotation_participants qp ON c.course_id = qp.course_id";
$course_result = $conn->query($course_query);
?>

<div class="about_title">
    <h3>Select Course to Generate Attendance Sheet</h3>
</div>

<form method="POST" action="attendance_sheet.php">
    <label for="course_id">Select Course:</label>
    <select name="course_id" id="course_id" required>
        <option value="">-- Select Course --</option>
        <?php while ($row = $course_result->fetch_assoc()) { ?>
            <option value="<?php echo $row['course_id']; ?>">
                <?php echo $row['course_title']; ?>
            </option>
        <?php } ?>
    </select>
    <br><br>
    <button type="submit" class="btn btn-primary">Generate Attendance Sheet</button>
</form>

<?php include 'resources/footer.php'; ?>
