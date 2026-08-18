<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['user_name'])) {
    header("Location: ../login.php");
    exit();
}

include 'harvest_header.php';
include('../resources/db_config.php');

// Fetch instructors for dropdown (using quotation_instructors instead of quotation_instances)
$query = "SELECT DISTINCT
    inst_id,
    full_name
FROM instructors
ORDER BY full_name";

$result = mysqli_query($conn, $query);
$options = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $options[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>TRAINER CONTRACT FORM</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f7f9fc;
      color: #333;
      padding: 40px;
    }

    h2 {
      text-align: center;
      color: grey;
    }

    form {
      max-width: 600px;
      margin: auto;
      background-color: #ffffff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }

    input, select, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    button {
      margin-top: 20px;
      padding: 12px;
      background-color: navy;
      color: white;
      border: none;
      border-radius: 4px;
      width: 100%;
      font-size: 16px;
      cursor: pointer;
    }

    button:hover {
      background-color: blue;
    }
  </style>
</head>
<body>

<h2>TRAINER AGREEMENT CONTRACT - INPUT FORM</h2>

<form id="contractForm" action="view_contract_harvest_monthly.php" method="post" target="view_contract_tab" enctype="multipart/form-data">

    <input type="hidden" name="instructor_full_name" id="instructor_full_name">

    <label for="instructor_select">Instructor Name (as per bank account name):
    <select name="instructor_id" id="instructor_select" required onchange="updateInstructorName()">
      <option value="">-- SELECT INSTRUCTOR --</option>
      <?php foreach ($options as $row): ?>
        <option value="<?= htmlspecialchars($row['inst_id']) ?>">
          <?= strtoupper(htmlspecialchars($row['full_name'])) ?>
        </option>
      <?php endforeach; ?>
    </select>
    </label>

    <label for="location_input">Location (address):
      <input type="text" name="location" id="location_input" value="ISTANBUL, TURKEY" required>
    </label>
    <p style="color: red;">[update the Instructor Address as required]</p>

    <label for="course_select">Training Course Title:
      <input name="course_title" id="course_select" required>
    </label>
    
    <label for="rate_input">Rate (Monthly):
      <input type="number" name="rate" id="rate_input" min="50" required>
    </label>

    <!--<label for="rate_input">Rate (USD per day):-->
    <!--  <input type="number" name="rate" id="rate_input" min="50" required>-->
    <!--</label>-->
    
    <!--  <label for="rate_input">For a Total of (US$):-->
    <!--  <input type="number" name="rate" id="rate_input" min="50" required>-->
    <!--</label>-->

    <label for="start_date">Start Date of Contract:
      <input type="date" name="contract_date" id="start_date" required>
    </label>

    <label for="end_date">End Date of Training:
      <input type="date" name="end_date" id="end_date" required>
    </label>

    <label for="bank_details">Bank Account Details (for bulk copy & paste):
      <textarea name="bank_details" id="bank_details" rows="6"
        style="white-space: pre; font-family: monospace; width: 100%; border: 1px solid #ccc; border-radius: 4px; padding: 10px;"
        required minlength="30"
        placeholder="Enter at least 30 characters of bank details..."></textarea>
    </label>

    <label for="signature_upload">Upload Instructor Signature (PNG only) Optional:
      <input type="file" name="signature_image" id="signature_upload" accept="image/png">
    </label>

    <button type="submit" formtarget="view_contract_tab">VIEW CONTRACT</button>
    <p style="color: red;">[This will open in a New tab for your convenience]</p>
</form>

<br><br>
<script>
function updateInstructorName() {
    const select = document.getElementById('instructor_select');
    const fullName = select.options[select.selectedIndex].text;
    document.getElementById('instructor_full_name').value = fullName;
}

document.getElementById('instructor_select').addEventListener('change', function () {
    const instructorId = this.value;
    const courseSelect = document.getElementById('course_select');
    const bankTextarea = document.getElementById('bank_details');

    // Clear previous course options
    courseSelect.innerHTML = '<option value="">-- SELECT COURSE --</option>';
    bankTextarea.value = ''; // Clear previous bank details

    if (instructorId) {
        // Fetch courses
        fetch(`get_courses_by_instructor.php?instructor_id=${instructorId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.course_title.toUpperCase();
                    option.textContent = course.course_title;
                    courseSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error fetching courses:', error);
            });

        // Fetch bank details
        fetch(`get_bank_details.php?instructor_id=${instructorId}`)
            .then(response => response.json())
            .then(data => {
                bankTextarea.value = data.bank_details || '';
            })
            .catch(error => {
                console.error('Error fetching bank details:', error);
            });
    }
});

</script>

<!-- LOAD THE FIRST COURSE OF THE INSTRUCTOR -->
 <script>
window.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('instructor_select');
    if (select.value) {
        const event = new Event('change');
        select.dispatchEvent(event);  // Triggers course loading for first instructor
    }
});
</script> 

</body>
</html>
