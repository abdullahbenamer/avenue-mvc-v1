<?php 
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

include 'resources/db_config.php';

// Fetch instructors
$instructor_result = $conn->query("
    SELECT DISTINCT i.inst_id, i.full_name
    FROM instructors i
    JOIN quotation_instructors qi ON i.inst_id = qi.instructor_id
    ORDER BY i.full_name
");
include 'resources/header.php';
?>
<div class="about" style="margin: 1rem 25%;">
    <p>
        You are logged in as <b><?= $_SESSION['user_role'] ?></b> | User: <b><?= $_SESSION['user_name'] ?></b>
    </p>
</div>

<div class="about_title" style="margin: 1rem 25%;">
    <h3>Create Instructor Due <i class="fa-solid fa-dollar fa-2xl"></i></h3>
</div>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div style="color: green;"><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div style="color: red;">There was an error submitting the form. Please try again.</div>
<?php endif; ?>

<form action="create_instructor_due.php" method="POST">
    <!-- Instructor Dropdown -->
    <div class="input-group">
        <label for="instructor_id">Instructor:</label>
        <select id="instructor_id" name="instructor_id" required>
            <option value="">Select Instructor</option>
            <?php while ($row = $instructor_result->fetch_assoc()): ?>
                <option value="<?= $row['inst_id'] ?>"><?= strtoupper($row['full_name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <!-- Training Program Dropdown -->
    <div class="input-group">
        <label for="instance_id">Training Program:</label>
        <select id="instance_id" name="instance_id" required>
            <option value="">Select Training Program</option>
        </select>
    </div>

    <!-- Hidden field for quotation ID -->
    <input type="hidden" id="quot_id" name="quot_id" value="">

    <!-- Date -->
    <div class="input-group">
        <label for="course_date">Start Date:</label>
        <input type="date" id="course_date" name="course_date" required>
    </div>

    <!-- Participants -->
    <div class="input-group">
        <label for="num_participants">Number of Participants:</label>
        <input type="number" id="num_participants" name="num_participants" required onchange="calculateDue()">
    </div>

    <!-- Days -->
    <div class="input-group">
        <label for="num_days">Number of Days:</label>
        <input type="number" id="num_days" name="num_days" required onchange="calculateDue()">
    </div>

    <!-- Due Amount -->
    <div class="input-group">
        <label for="instructor_due">Instructor Due Amount ($):</label>
        <input type="text" id="instructor_due" name="instructor_due" readonly>
    </div>

    <div class="input-group">
        <input type="submit" value="Submit">
    </div>
</form>

<script>
    function calculateDue() {
        const participants = parseInt(document.getElementById('num_participants').value || 0);
        const days = parseInt(document.getElementById('num_days').value || 0);
        let dailyRate = 0;

        if (participants === 1) dailyRate = 100;
        else if (participants === 2) dailyRate = 150;
        else if (participants === 3) dailyRate = 175;
        else if (participants === 4) dailyRate = 200;
        else if (participants >= 5) dailyRate = 250;

        const dueAmount = dailyRate * days;
        document.getElementById('instructor_due').value = dueAmount.toFixed(2);
    }

    // Populate training programs and set hidden quot_id
    document.getElementById('instructor_id').addEventListener('change', function () {
        const instructorId = this.value;
        const instanceSelect = document.getElementById('instance_id');
        const quotInput = document.getElementById('quot_id');
        instanceSelect.innerHTML = '<option value="">Select Training Program</option>';
        quotInput.value = '';

        if (instructorId) {
            fetch(`get_courses_by_instructor.php?instructor_id=${instructorId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.instance_id;
                        option.textContent = `${item.course_title} (G-ID -${item.instance_id})`;
                        option.dataset.quotId = item.quot_id;
                        instanceSelect.appendChild(option);
                    });
                });
        }
    });

    document.getElementById('instance_id').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const quotId = selected.dataset.quotId;
        document.getElementById('quot_id').value = quotId;
    });
</script>

<?php include 'resources/footer.php'; ?>
