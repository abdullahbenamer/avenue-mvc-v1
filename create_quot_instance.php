<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

include 'resources/header.php';
include 'resources/db_config.php';

$duration = ""; // Initialize the duration variable

// Fetch quotations for the dropdown menu
$query = "SELECT q.quot_id, q.quot_ref, q.duration, c.course_title, c.course_id , c.course_title_a, m.cust_code
          FROM quotations q 
          JOIN courses c ON q.course_id = c.course_id
          JOIN customers m ON q.cust_id = m.cust_id
          ORDER BY c.course_title;";
$result = $conn->query($query);

// Fetch customers
$customer_query = "SELECT * FROM `customers` ORDER BY `cust_name`";
$customer_result = $conn->query($customer_query);

// Fetch instructors
$instructor_query = "SELECT inst_id, full_name, inst_portrait FROM `instructors` ORDER BY `full_name`";
$instructor_result = $conn->query($instructor_query);

// Fetch venues for the dropdown
$venue_query = "SELECT ven_id, ven_name FROM venues ORDER BY ven_name";
$venue_result = $conn->query($venue_query);

if (!$venue_result) {
    echo "Error fetching venues: " . $conn->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $quot_id = $_POST['quot_id'];
    $customer = $_POST['cust_id'];
    $instance_ref = $_POST['instance_ref'];
    $participant_names = mysqli_real_escape_string($conn, $_POST['participant_names']);
    $course_id = $_POST['course_id'];
    $start_date = $_POST['start_date'];
    $instructor_ids = $_POST['instructor_id']; // now an array
    $ven_id = $_POST['ven_id'];  // Get the selected venue

    // Fetch the duration for the selected quotation
    $duration_query = "SELECT duration FROM quotations WHERE quot_id = ?";
    $duration_stmt = $conn->prepare($duration_query);
    $duration_stmt->bind_param("i", $quot_id);
    $duration_stmt->execute();
    $duration_stmt->bind_result($duration);
    $duration_stmt->fetch();
    $duration_stmt->close();

    // Insert into quotation_instances table (include ven_id)
    $stmt = $conn->prepare("INSERT INTO quotation_instances (quot_id, cust_id, instance_ref, duration, ven_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissi", $quot_id, $customer, $instance_ref, $duration, $ven_id);

    if ($stmt->execute()) {
        $instance_id = $stmt->insert_id;

        foreach ($instructor_ids as $inst_id) {
    $stmt_instructor = $conn->prepare("INSERT INTO quotation_instructors (instance_id, instructor_id) VALUES (?, ?)");
    $stmt_instructor->bind_param("ii", $instance_id, $inst_id);
    $stmt_instructor->execute();
}
        // Insert participants into quotation_participants table
        $names = explode(',', $participant_names);
        foreach ($names as $name) {
            $stmt_participant = $conn->prepare("INSERT INTO quotation_participants (instance_id, full_name, course_id, cust_id, start_date) VALUES (?, ?, ?, ?, ?)");
            $stmt_participant->bind_param("isiis", $instance_id, trim($name), $course_id, $customer, $start_date);
            $stmt_participant->execute();
        }

        echo "<script>alert('Quotation Instance created successfully'); window.location.href='view_instance.php';</script>";
    } else {
        echo "<script>alert('Error creating Quotation Instance'); window.location.href='create_quot_instance.php';</script>";
    }
}

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
.custom-multi-select {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #ccc;
    padding: 0.5rem;
    border-radius: 5px;
    background: #fafafa;
}

.instructor-option {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

.instructor-option input[type="checkbox"] {
    transform: scale(1.2);
}

.instructor-option img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid #999;
}

.instructor-option span {
    font-weight: 500;
    font-size: 0.9rem;
}
</style>

    <title>CREATE GROUP</title>
</head>
 <body>   

<div class="about_title" style="margin: 1rem 25% 1rem 25%;">
    <h3>Create a New GROUP <i class="fa-solid fa-users fa-2xl"></i></h3>
</div>
<div style="text-align: center; font-size: 0.75rem;"><b>Note: </b>A GROUP is a set of candidates enrolled in the <b>same course</b>
<br> and come from the <b>same company</b>
<br> A Course may include <b>more than one group</b></div>
<br>
<!-- Form starts -->
<form method="POST" action="create_quot_instance.php">
    <div class="input-group">
        <label for="quot_id">Quotation:</label>
        <select name="quot_id" id="quot_id" required onchange="fetchCourse(this.value)">
            <option value="">Select Quotation</option>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <option value="<?php echo $row['quot_id']; ?>"><?php echo $row['course_title'] . " (id: " . $row['course_id'] . ")" . " - [" . $row['cust_code'] . "] - " . $row['course_title_a']; ?></option>
            <?php } ?>
        </select><br>
    </div>

    <div class="input-group">
        <label for="course_id">Course Title:</label>
        <select name="course_id" id="course_id" required>
            <option value="">Select Course</option>
        </select><br><br>
    </div>

    <div class="input-group">
        <label for="duration">Duration:</label>
        <input type="text" name="duration" id="duration" value="<?php echo htmlspecialchars($duration); ?>" required><br>
    </div>
    
    <div class="input-group">
        <label>Customer Name</label>
        <select name="cust_id" id="cust_id" required>
            <option value=""></option>
            <?php while ($row = mysqli_fetch_array($customer_result)) { ?>
                <option value="<?php echo $row['cust_id']; ?>"><?php echo strtoupper($row['cust_name']); ?></option>
            <?php } ?>
        </select>
    </div>

    
    <!-- INSTRUCTOR custom-multi-select  -->
     <label>Instructor(s) Name</label>
    <div class="custom-multi-select">
    <?php while ($row = mysqli_fetch_array($instructor_result)) {
        $portrait = $row['inst_portrait'] && file_exists('search/photo_uploads/' . $row['inst_portrait'])
            ? $row['inst_portrait']
            : 'instructor_male.jpg';
        $img_src = 'search/photo_uploads/' . $portrait;
        $display_name = strtoupper($row['full_name']);
    ?>
    <label class="instructor-option">
        <input type="checkbox" name="instructor_id[]" value="<?php echo $row['inst_id']; ?>">
        <img src="<?php echo $img_src; ?>" alt="portrait">
        <span><?php echo $display_name; ?></span>
    </label>
    <?php } ?>
</div>

 <p style="color: red;font-size:0.75rem;">Use Checkbox To select one or more INSTRUCTORs.</p>
   <br>
       <!--Select venue-->
    <div class="input-group">
    <label for="ven_id">Venue:</label>
    <select name="ven_id" id="ven_id" required>
        <option value="">Select Venue</option>
        <?php while ($row = mysqli_fetch_array($venue_result)) { ?>
            <option value="<?php echo $row['ven_id']; ?>"><?php echo $row['ven_name']; ?></option>
        <?php } ?>
    </select>
</div>
    <div class="input-group">
        <label for="instance_ref">Group Description:</label>
        <input type="text" name="instance_ref" id="instance_ref" required><br>
    </div>

    <div class="input-group">
        <label for="participant_names">Participant Name(s)</label>
        <textarea name="participant_names" id="participant_names" style="width: 100%; height: 250px;" required></textarea>
    </div>
    <p style="color: red;">* Please use comma (,) to separate names, and remove any <b>spaces</b> before saving *</p>
    
    <div class="input-group">
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" id="start_date" required><br>
      
    </div>
    
    <br>
    <button type="submit" class="btn btn-primary">Save GROUP</button>
</form>

<script>
function fetchCourse(quot_id) {
    if (quot_id) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_courses_by_quotation.php?quot_id=' + quot_id, true);
        xhr.onload = function() {
            if (this.status == 200) {
                try {
                    var response = JSON.parse(this.responseText);
                    document.getElementById('course_id').innerHTML = response.courses || '';  // Populate course options
                    document.getElementById('duration').value = response.duration || '';      // Populate duration field
                } catch (e) {
                    console.error('Error parsing JSON:', e, this.responseText);
                }
            }
        };
        xhr.send();
    }
}

</script>

<?php
include 'resources/footer.php';
?>
    

