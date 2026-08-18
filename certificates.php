<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

include 'resources/header.php';
include 'resources/db_config.php';

// Fetch quotation instances data for initial dropdowns
$quotation_instances = $conn->query("SELECT * FROM quotation_instances");
if (!$quotation_instances) {
    die("Error fetching quotation instances: " . $conn->error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'resources/db_config.php';

    $quot_instance_id = $_POST['quot_instance_id'];
    $cust_id = $_POST['cust_id'];
    $course_id = $_POST['course_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];  // New field
    $ven_id = $_POST['ven_id'];  // New field

    // Fetch participants from the hidden JSON input
    $participants = json_decode($_POST['full_name_json'], true); // Decode JSON list of participants

        var_dump($_POST['end_date']); // Check if this outputs the correct value

    // Insert each participant into the 'certificates' table
foreach ($participants as $participant) {
    $part_id = $participant['part_id'];
    $full_name = $participant['full_name'];

    if (!empty($part_id) && !empty($full_name)) {
        // Step 1: Insert certificate record
        $insert_query = "INSERT INTO certificates (part_id, full_name, cust_id, course_id, start_date, end_date, ven_id)
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("isisssi", $part_id, $full_name, $cust_id, $course_id, $start_date, $end_date, $ven_id);

        if (!$insert_stmt->execute()) {
            die("Error executing insert statement: " . $insert_stmt->error);
        }

        // Step 2: Generate cert_no
        $cert_id = $conn->insert_id;
        $year = date('y'); // Get last two digits of the current year
        $cert_no = "CERT-{$year}-{$cust_id}-{$quot_instance_id}-{$cert_id}";

        // Step 3: Save cert_no into that row
        $update = $conn->prepare("UPDATE certificates SET cert_no = ? WHERE cert_id = ?");
        $update->bind_param("si", $cert_no, $cert_id);
        $update->execute();
    }
}

    // Redirect or display success message
    header("Location: read_cert.php");
    exit();
}


ob_end_flush();
?>

<!-- HTML form with hidden input for participant JSON data -->
<div class="about" style="margin: 1rem 25% 1rem 25%;">
    <p>
        <?php
        echo "You are logged in as <b>";
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
    <h3>Create New CERTIFICATE <i class="fa-solid fa-award fa-2xl"></i></h3>
</div>

<form action="" method="POST">
    <div class="input-group">
        <label for="quot_instance_id">Quotation Instance REF#:</label>
        <select id="quot_instance_id" name="quot_instance_id" required>
            <option value="">Select Group</option>
            <?php while ($row = $quotation_instances->fetch_assoc()): ?>
                <option value="<?= $row['instance_id'] ?>"><?= $row['instance_ref'] . " - (G.ID " . $row['instance_id'] . ")" ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="input-group">
        <label for="customer">Customer:</label>
        <select id="customer" name="cust_id" disabled required>
            <option value="">Select Customer</option>
        </select>
    </div>

    <div class="input-group">
        <label for="course">Course Title:</label>
        <select id="course" name="course_id" disabled required>
            <option value="">Select Course</option>
        </select>
    </div>

    <div class="input-group">
        <label for="start_date">Start Date:</label>
     <select id="date" name="start_date" disabled required>
    <option value="">Select Date</option>
</select>
    </div>

    <div class="input-group">
    <label for="end_date">End Date:</label>
    <input type="date" id="end_date" name="end_date" required>
</div>

    <div class="input-group">
        <label for="ven_id">Venue:</label>
        <select id="ven_id" name="ven_id" required>
            <option value="">Select Venue</option>
            <?php 
            // Fetch venues from database and populate dropdown
            $venues = $conn->query("SELECT ven_id, ven_name FROM venues");
            while ($venue_row = $venues->fetch_assoc()): ?>
                <option value="<?= $venue_row['ven_id'] ?>"><?= $venue_row['ven_name'] ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="input-group">
        <label for="full_name">Full Name(s)</label>
        <textarea id="full_name" name="full_name" style="height: 150px; width: 100%;" disabled required></textarea>
    </div>

    <input type="hidden" id="full_name_json" name="full_name_json"> <!-- Hidden input to store JSON -->

    <button type="submit" class="btn btn-primary">Save CERTIFICATE(S)</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const instanceIdSelect = document.getElementById('quot_instance_id');
    const customerSelect = document.getElementById('customer');
    const courseSelect = document.getElementById('course');
    const dateSelect = document.getElementById('date');
    const fullNameTextarea = document.getElementById('full_name');
    const fullNameJsonInput = document.getElementById('full_name_json'); // Hidden input for JSON

    // Quotation instance selection change
    instanceIdSelect.addEventListener('change', function() {
        const instanceId = this.value;
        if (instanceId) {
            fetch(`get_cert_customers.php?instance_id=${instanceId}`)
                .then(response => response.json())
                .then(data => {
                    customerSelect.innerHTML = '<option value="">Select Customer</option>';
                    data.forEach(item => {
                        customerSelect.innerHTML += `<option value="${item.cust_id}">${item.cust_name}</option>`;
                    });
                    customerSelect.disabled = false;
                });
            courseSelect.innerHTML = '<option value="">Select Course</option>';
            dateSelect.innerHTML = '<option value="">Select Date</option>';
            courseSelect.disabled = true;
            dateSelect.disabled = true;
        } else {
            resetDropdowns();
        }
    });

    // Customer selection change
    customerSelect.addEventListener('change', function() {
        const custId = this.value;
        const instanceId = instanceIdSelect.value;
        if (custId && instanceId) {
            fetch(`get_cert_courses.php?instance_id=${instanceId}&cust_id=${custId}`)
                .then(response => response.json())
                .then(data => {
                    courseSelect.innerHTML = '<option value="">Select Course</option>';
                    data.forEach(item => {
                        courseSelect.innerHTML += `<option value="${item.course_id}">${item.course_title}</option>`;
                    });
                    courseSelect.disabled = false;
                });
            dateSelect.innerHTML = '<option value="">Select Date</option>';
            dateSelect.disabled = true;
        } else {
            resetDropdowns();
        }
    });

// Course selection change
courseSelect.addEventListener('change', function() {
    const courseId = this.value;
    const custId = customerSelect.value;
    const instanceId = instanceIdSelect.value;

    if (courseId && custId && instanceId) {
        // Fetch the start dates
        fetch(`get_cert_dates.php?instance_id=${instanceId}&cust_id=${custId}&course_id=${courseId}`)
            .then(response => response.json())
            .then(data => {
                // Populate the start date dropdown
                dateSelect.innerHTML = '<option value="">Select Date</option>';
                data.forEach(item => {
                    dateSelect.innerHTML += `<option value="${item}">${item}</option>`;
                });
                dateSelect.disabled = false; // Enable dropdown after loading dates
            })
            .catch(error => console.error('Error fetching dates:', error));

        // Fetch the participants (unchanged)
        fetch(`get_cert_participants.php?instance_id=${instanceId}&cust_id=${custId}&course_id=${courseId}`)
            .then(response => response.json())
            .then(data => {
                fullNameTextarea.value = ''; // Clear previous participants
                if (data.length > 0) {
                    fullNameTextarea.value = data.map(p => p.full_name).join("\n"); // Display in textarea
                    fullNameJsonInput.value = JSON.stringify(data); // Store JSON data in hidden input
                } else {
                    fullNameTextarea.value = 'No participants found.';
                    fullNameJsonInput.value = ''; // Clear JSON if no participants
                }
                fullNameTextarea.disabled = false; // Enable textarea
            })
            .catch(error => console.error('Error fetching participants:', error));
    }
});


    // Function to reset all dropdowns
    function resetDropdowns() {
        customerSelect.innerHTML = '<option value="">Select Customer</option>';
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        dateSelect.innerHTML = '<option value="">Select Date</option>';
        fullNameTextarea.value = '';
        fullNameTextarea.disabled = true;
        customerSelect.disabled = true;
        courseSelect.disabled = true;
        dateSelect.disabled = true;
    }
});
</script>

<?php
include 'resources/footer.php';
?>
