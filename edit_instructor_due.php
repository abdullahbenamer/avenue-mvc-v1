<?php
ob_start();
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}
include 'resources/header.php';
include 'resources/db_config.php';

if (isset($_GET['due_id'])) {
    $due_id = $_GET['due_id'];

    // Fetch the existing values for the selected instructor due
    $query = "SELECT id.*, i.full_name, c.course_title 
              FROM instructor_dues id
              JOIN instructors i ON id.instructor_id = i.inst_id
              JOIN quotations q ON id.quot_id = q.quot_id
              JOIN courses c ON q.course_id = c.course_id
              WHERE due_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $due_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $due = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle the update
    $due_id = $_POST['due_id'];
    $course_date = $_POST['course_date'];
    $num_participants = $_POST['num_participants'];
    $days = $_POST['days'];

    // Calculate the due amount based on the number of participants and days
    if ($num_participants == 1) {
        $due_amount_per_day = 100;
    } elseif ($num_participants == 2) {
        $due_amount_per_day = 150;
    } elseif ($num_participants == 3) {
        $due_amount_per_day = 200;
    } elseif ($num_participants == 4) {
        $due_amount_per_day = 250;
    } else {
        $due_amount_per_day = 300;
    }

    // Total due amount = due amount per day * number of days
    $due_amount = $due_amount_per_day * $days;

    // Update the instructor due with the new values
    $update_query = "UPDATE instructor_dues SET course_date = ?, num_participants = ?, days = ?, due_amount = ? WHERE due_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("siidi", $course_date, $num_participants, $days, $due_amount, $due_id);

    if ($update_stmt->execute()) {
        header("Location: instructor_due_list.php?success=1");
        exit(); // Add exit after header to stop further code execution
    } else {
        echo "Error: " . $update_stmt->error;
    }
}
?>

<form action="edit_instructor_due.php" method="POST">
    <input type="hidden" name="due_id" value="<?php echo $due['due_id']; ?>">

     <div class="input-group">
    <label for="instructor_name">Instructor:</label>
    <input type="text" name="instructor_name" value="<?php echo $due['full_name']; ?>" disabled><br>
   </div>

    <div class="input-group">
    <label for="course_title">Course Title:</label>
    <input type="text" name="course_title" value="<?php echo $due['course_title']; ?>" disabled><br>
   </div>

    <div class="input-group">
    <label for="course_date">Course Date:</label>
    <input type="date" name="course_date" value="<?php echo $due['course_date']; ?>" required><br>
   </div>

    <div class="input-group">
    <label for="num_participants">Number of Participants:</label>
    <input type="number" name="num_participants" value="<?php echo $due['num_participants']; ?>" required><br>
   </div>

    <div class="input-group">
    <label for="days">Days:</label>
    <input type="number" name="days" value="<?php echo $due['days']; ?>" required><br>
   </div>

    <div class="input-group">
    <label for="due_amount">Due Amount ($):</label>
    <input type="number" step="0.01" name="due_amount" value="<?php echo $due['due_amount']; ?>" readonly><br>
   </div>
<br>
    <button type="submit" class="btn btn-primary">Update Instructor Dues</button>
</form>

<script>
function calculateDue() {
    const numParticipants = document.querySelector('input[name="num_participants"]').value;
    const days = document.querySelector('input[name="days"]').value;

    let dueAmountPerDay;

    if (numParticipants == 1) {
        dueAmountPerDay = 100;
    } else if (numParticipants == 2) {
        dueAmountPerDay = 150;
    } else if (numParticipants == 3) {
        dueAmountPerDay = 200;
    } else if (numParticipants == 4) {
        dueAmountPerDay = 250;
    } else {
        dueAmountPerDay = 300;
    }

    const totalDueAmount = dueAmountPerDay * days;
    document.querySelector('input[name="due_amount"]').value = totalDueAmount.toFixed(2);
}

// Attach the calculation function to input changes
document.querySelector('input[name="num_participants"]').addEventListener('input', calculateDue);
document.querySelector('input[name="days"]').addEventListener('input', calculateDue);
</script>

<?php include 'resources/footer.php'; ?>