<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

include 'resources/db_config.php';
include 'resources/header.php';

// Fetch courses from quotation_participants table to populate the dropdown
$course_query = "SELECT DISTINCT qp.course_id, c.course_title 
                 FROM quotation_participants qp
                 JOIN courses c ON qp.course_id = c.course_id
                 ORDER BY c.course_title";
$course_result = $conn->query($course_query);

// Check if a course is selected
$participants = [];
if (isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];

    // Fetch participants related to the selected course
    $participant_query = "SELECT qp.*, c.course_title, cu.cust_name
                          FROM quotation_participants qp
                          JOIN courses c ON qp.course_id = c.course_id
                          JOIN customers cu ON qp.cust_id = cu.cust_id
                          WHERE qp.course_id = ?";
    $stmt = $conn->prepare($participant_query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $participants[] = $row;
    }
}
?>

<div class="about" style="margin: 1rem 25% 1rem 25%;">
    <p>
        <?php
        echo "You are logged as <b>" . $_SESSION['user_role'] . "</b>";
        echo " | User: <b>" . $_SESSION['user_name'] . "</b>";
        ?>
    </p>
</div>

<div class="about_title" style="margin: 1rem 25% 1rem 25%;">
    <h3>Filter Participants by Course <i class="fa-solid fa-filter fa-2xl"></i></h3>
</div>

<form method="POST" action="filter_participants.php">
    <div class="input-group">
        <label for="course_id">Select Course:</label>
        <select name="course_id" id="course_id" required>
            <option value="">Select Course</option>
            <?php while ($row = $course_result->fetch_assoc()) { ?>
                <option value="<?php echo $row['course_id']; ?>"><?php echo $row['course_title']; ?></option>
            <?php } ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Show Participants</button>
</form>

<?php if (!empty($participants)) { ?>
    <h3>Participants for Course: <?php echo $participants[0]['course_title']; ?></h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Full Name (Arabic)</th>
                <th>Payroll No</th>
                <th>Start Date</th>
                <th>Mobile</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($participants as $participant) { ?>
            <tr>
                <td><?php echo $participant['part_id']; ?></td>
                <td><?php echo $participant['full_name']; ?></td>
                <td><?php echo $participant['full_name_a']; ?></td>
                <td><?php echo $participant['payroll_no']; ?></td>
                <td><?php echo $participant['start_date']; ?></td>
                <td><?php echo $participant['mobile']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>

<?php include 'resources/footer.php'; ?>
