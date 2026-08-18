<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

// Check if the user has the required role
if ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT' && $_SESSION['user_role'] != 'USER') {
    header("Location: unauthorized.php");
    exit();
}

include 'resources/db_config.php';
include 'resources/header.php';

// find the total number of participants
// Fetch the total number of participants
$count_query = "SELECT COUNT(part_id) AS total_participants FROM quotation_participants";
$count_result = $conn->query($count_query);
$total_participants = $count_result->fetch_assoc()['total_participants'];

// Check if a specific participant is being edited
$edit_part_id = isset($_GET['edit']) ? intval($_GET['edit']) : null;

// Fetch all participants with their course title and customer name
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$query = "
    SELECT qp.*, c.course_title, cu.cust_code 
    FROM quotation_participants qp
    JOIN courses c ON qp.course_id = c.course_id
    JOIN customers cu ON qp.cust_id = cu.cust_id
";
if ($search !== '') {
    $escaped = $conn->real_escape_string($search);
    $query .= " WHERE 
        LOWER(qp.full_name) LIKE LOWER('%$escaped%') OR 
        LOWER(qp.full_name_a) LIKE LOWER('%$escaped%') OR 
        LOWER(qp.payroll_no) LIKE LOWER('%$escaped%') OR 
        LOWER(c.course_title) LIKE LOWER('%$escaped%')";
}
$query .= " ORDER BY qp.instance_id DESC LIMIT 500";

$result = $conn->query($query);

// Fetch data for the participant being edited (if any)
$edit_participant = null;
if ($edit_part_id) {
    $edit_query = "
        SELECT qp.*, c.course_title, cu.cust_code 
        FROM quotation_participants qp
        JOIN courses c ON qp.course_id = c.course_id
        JOIN customers cu ON qp.cust_id = cu.cust_id
        WHERE qp.part_id = ?
    ";
    $stmt = $conn->prepare($edit_query);
    $stmt->bind_param("i", $edit_part_id);
    $stmt->execute();
    $edit_result = $stmt->get_result();
    $edit_participant = $edit_result->fetch_assoc();
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $part_id = $_POST['part_id'];
    $full_name = $_POST['full_name'];
    $full_name_a = $_POST['full_name_a'];
    $payroll_no = $_POST['payroll_no'];
    $start_date = $_POST['start_date'];
    $mobile = $_POST['mobile'];

    // Update query for the participant
    $stmt = $conn->prepare("UPDATE quotation_participants SET full_name = ?, full_name_a = ?, payroll_no = ?, start_date = ?, mobile = ? WHERE part_id = ?");
    $stmt->bind_param("sssssi", $full_name, $full_name_a, $payroll_no, $start_date, $mobile, $part_id);
    $stmt->execute();

    echo "<script>alert('Participant updated successfully'); window.location.href='quotation_participants.php';</script>";
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    .quote-grid-container {
        width: 97%;
        margin: 0.5rem auto;
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
        font-family: Arial, sans-serif;
    }

    .quote-grid-header, .quote-grid-row {
        display: grid;
        grid-template-columns: 3% 3% 23% 4% 14% 6% 7% 23% 5% 10%;
        gap: 0.2rem;
        align-items: center;
        padding: 0.5rem 0.5rem;
        font-size: 0.8rem;
        border-bottom: 1px solid #ccc;
    }

    .quote-grid-header {
        font-weight: bold;
        background-color: #f5f5f5;
        border-top: 2px solid #999;
        border-bottom: 2px solid #999;
    }
    
    .quote-grid-row:hover {
    background: #D9D9D9;
}

    .highlight-red {
        color: red;
        font-weight: bold;
    }

    .quote-grid-row a {
        color: blue;
        text-decoration: underline;
    }

    /* Arabic fonts only in the fifth column */
    .quote-grid-row > div:nth-child(5) {
    font-size: 1rem;
    font-family: 'Tajawal', 'Arial', 'Noto Naskh Arabic', sans-serif;
    direction: rtl;
    text-align: right;
    margin-right: 10px;
}

</style>
    <title>CANDIDATES</title>
</head>
<body>
 
<div class="about_title" style="margin: 1rem 25% 1rem 25%;">
    <h3>Manage Group Participants <i class="fa-solid fa-people-group"></i></h3>
    <div><a href="view_instance.php"><button type="submit" class="btn1">List GROUPS <i class="fa-solid fa-people-group"></i></button></a></div>
</div>

<form method="GET" action="" style="margin: 1rem 25% 1rem 25%; display: flex; gap: 0.5rem;border:none;">
    <input type="text" name="search" placeholder="Search by Eng or Arab عربي name, payroll no, or course"
           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
           style="flex: 1; padding: 0.5rem;">
    <button type="submit" style="padding: 0.5rem; border-radius: 4px">Search Trainee</button>
    <a href="quotation_participants.php" style="padding: 0.5rem; background: #ccc; text-decoration: none; border-radius: 4px">Reset</a>
</form>


<!-- Edit Form for a Single Participant -->
<?php if ($edit_participant) { ?>
    <form method="POST" action="quotation_participants.php?edit=<?php echo $edit_part_id; ?>">
        <h4>Edit Participant: <?php echo $edit_participant['full_name']; ?></h4>
        <input type="hidden" name="part_id" value="<?php echo $edit_participant['part_id']; ?>">
        
        <div class="input-group">
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" id="full_name" value="<?php echo $edit_participant['full_name']; ?>" required>
        </div>
        <div class="input-group">
            <label for="full_name_a">Full Name (Arabic):</label>
            <input type="text" name="full_name_a" id="full_name_a" value="<?php echo $edit_participant['full_name_a']; ?>">
        </div>
        <div class="input-group">
            <label for="payroll_no">Payroll No:</label>
            <input type="text" name="payroll_no" id="payroll_no" value="<?php echo $edit_participant['payroll_no']; ?>">
        </div>
        <div class="input-group">
            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" id="start_date" value="<?php echo $edit_participant['start_date']; ?>" required>
        </div>
        <div class="input-group">
            <label for="mobile">Mobile:</label>
            <input type="text" name="mobile" id="mobile" value="<?php echo $edit_participant['mobile']; ?>">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="quotation_participants.php" class="btn btn-secondary">Cancel</a>
    </form>
<?php } ?>

<!-- Display total count -->
<p style="font-family: Tajawal, Verdana, Geneva, Tahoma, sans-serif; font-size: 1.2rem; margin: 20px; text-align: center;">
    Total Participants to date: <strong><?php echo $total_participants; ?> Trainees.</strong>
</p>
<!--<p style="font-family: Tajawal, Verdana, Geneva, Tahoma, sans-serif; font-size: 1.2rem; margin: 20px; text-align: center;"> Total Participants: (<strong>Before October 2024</strong>): <strong> 1,030 Trainees.</strong></p>-->
<!-- Read-Only list for All Participants -->
<div class="quote-grid-container">
    <!-- Header -->
    <div class="quote-grid-header">
        <div>ID</div>
        <div>GRP<br>ID</div>
        <div>Full Name</div>
         <div>P. No</div>
        <div>Full Name (Arabic)</div>
         <div>Start On</div>
        <div>Mobile</div>
        <div>Course Title</div>
        <div>Customer</div>
        <div>Action</div>
    </div>

    <!-- Data Rows -->
    <?php while ($row = $result->fetch_assoc()) { ?>
    <div class="quote-grid-row">
        <div><?php echo $row['part_id']; ?></div>
        <div class="highlight-red"><?php echo $row['instance_id']; ?></div>
        <div><?php echo strtoupper($row['full_name']); ?></div>
         <div><?php echo $row['payroll_no']; ?></div>
         <div><?php echo $row['full_name_a']; ?></div>
         <div><?php echo $row['start_date']; ?></div>
        <div><?php echo $row['mobile']; ?></div>
        <div><?php echo $row['course_title']; ?></div>
        <div><?php echo $row['cust_code']; ?></div>
        <div>
           <div style="display: flex; gap: 5px;">
    <a href="attendance_template.php?instance_id=<?php echo $row['instance_id']; ?>" 
       target="_blank" 
       style="background-color: green; color: white; padding: 7px 6px; text-decoration: none; border-radius: 4px;">
        Print Sheet
    </a>
    
    <a href="quotation_participants.php?edit=<?php echo $row['part_id']; ?>" 
       style="background-color: blue; color: white; padding: 7px 6px; text-decoration: none; border-radius: 4px;">
        Edit
    </a>
</div>
        </div>
    </div>
    <?php } ?>
</div>

<?php include 'resources/footer.php'; ?>
