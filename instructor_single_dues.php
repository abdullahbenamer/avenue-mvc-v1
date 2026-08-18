<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

//Display the success message if
if (isset($_GET['success'])): ?>
    <div style="color: green; margin: 1rem 25%;">Instructor due successfully created.</div>
<?php endif; ?>

<?php if (isset($_GET['duplicate'])): ?>
    <div style="color: orange; margin: 1rem 25%;">This instructor due already exists for the selected date.</div>
<?php endif; ?>

<?php
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

// Check if the user has the required role
if ($_SESSION['user_role'] != 'ACCOUNTANT') {
    header("Location: unauthorized.php");
    exit();
}

include 'resources/header.php';
include 'resources/db_config.php';

// Validate and get instructor_id from the URL
$instructor_id = isset($_GET['instructor_id']) ? intval($_GET['instructor_id']) : 0;

// Fetch instructor name for the title
$name_query = "SELECT full_name, inst_portrait, bank_details FROM instructors WHERE inst_id = ?";
$name_stmt = $conn->prepare($name_query);
$name_stmt->bind_param("i", $instructor_id);
$name_stmt->execute();
$name_result = $name_stmt->get_result();
$instructor = $name_result->fetch_assoc();
$instructor_name = $instructor['full_name'] ?? 'Unknown Instructor';
$inst_portrait = $instructor['inst_portrait'] ?? '';
$portrait_file = $inst_portrait && file_exists('search/photo_uploads/' . $inst_portrait)
    ? $inst_portrait
    : 'instructor_male.jpg';
$portrait_path = 'search/photo_uploads/' . $portrait_file;

// Fetch instructor financial history records
$query = "SELECT 
    d.due_id,
    d.course_date,
    d.num_participants,
    d.days,
    d.due_amount,
    d.paid_amount,
    d.payment_status,

    GROUP_CONCAT(
        DISTINCT CONCAT(
            UPPER(c.course_title),
            ' (G',
            idi.instance_id,
            ')'
        )
        ORDER BY idi.instance_id
        SEPARATOR ', '
    ) AS courses,

    GROUP_CONCAT(
        DISTINCT cst.cust_code
        SEPARATOR ', '
    ) AS cust_codes

FROM instructor_dues d
JOIN instructor_due_instances idi ON d.due_id = idi.due_id
JOIN quotations q ON idi.quot_id = q.quot_id
JOIN customers cst ON cst.cust_id = q.cust_id
JOIN courses c ON idi.course_id = c.course_id

WHERE d.instructor_id = ?
GROUP BY d.due_id
ORDER BY d.course_date DESC";
		  
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();

$total_due = 0; // Initialize the total due amount
$total_remaining = 0;

$bank_details = trim($instructor['bank_details'] ?? '');

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Grid style layout -->
    <style>
.grid-list-container {
    width: 97%;
    margin: 1rem auto;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    font-family: Arial, sans-serif;
}

.grid-header, .grid-row {
    display: grid;
 grid-template-columns: 26% 10% 11% 7% 6% 12% 9% 9% 10%;
    gap: 0.5rem;
    align-items: center;
    padding: 0.5rem 0.6rem;
    font-size: 0.8rem;
    border-bottom: 1px solid #ccc;
}

.grid-header {
    font-weight: bold;
    background-color: #e2e2e2;
}

.grid-row {
    background-color: #fff;
    transition: background 0.2s ease;
}

.grid-row:hover {
    background-color: #f5f5f5;
}

.total-row {
    font-weight: bold;
    color: #C70607;
    border-top: 2px solid #999;
}
</style>

    <title>Single due</title>
</head>
<body>
<div class="about_title">
    <h3>Instructor Financial Statement</h3><br>
       <div class='input-group'>
        <a href='instructor_due_list.php'><button type='submit' class='btn btn-primary'>Back to INSTRUCTORS DUES</button></a>
    </div>
    <div style="display: flex; align-items: center; gap: 12px; margin: 0 0 1rem 0;">
    <img src="<?php echo $portrait_path; ?>" alt="Instructor Portrait"
         style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid #aaa;">
    <span style="font-size: 1rem; color: black;">
        INSTRUCTOR: 
        <b>
            <a href="search/view_single.php?id=<?php echo $instructor_id; ?>" target="_blank">
                <?php echo strtoupper(htmlspecialchars($instructor_name)); ?>
            </a>
        </b>
    </span>
</div>
<div style="margin-top: 0.5rem; font-size: 0.85rem; color: #333; background: #fafafa; padding: 0.6rem 1rem; border-left: 4px solid #777; max-width: 600px;">
    <strong>Bank Details:</strong><br>
    <?php echo $bank_details ? nl2br(htmlspecialchars($bank_details)) : "<em>No bank details provided.</em>"; ?>
</div>
</div>

<div class="grid-list-container">
    <div class="grid-header">
        <div>Course</div>
        <div>Cust(Quote)</div>
        <div>Start Date</div>
        <div>Trainees</div>
        <div>Days</div>
        <div>Due Amount</div>
		<div>Paid</div>
        <div>Status</div>
		<div>Action</div>
    </div>

    <?php
 while ($row = $result->fetch_assoc()) {
 $total_due += $row['due_amount'];
$total_remaining += ($row['due_amount'] - $row['paid_amount']);
    echo "<div class='grid-row'>";
    echo "<div>" . htmlspecialchars($row['courses']) . "</div>";
    echo "<div>" . htmlspecialchars($row['cust_codes']) . "</div>";
    echo "<div>" . htmlspecialchars($row['course_date']) . "</div>";
    echo "<div>" . htmlspecialchars($row['num_participants']) . "</div>";
    echo "<div>" . htmlspecialchars($row['days']) . "</div>";
    echo "<div>$" . number_format($row['due_amount'], 2) . "</div>";
    echo "<div>$" . number_format($row['paid_amount'], 2) . "</div>";
    echo "<div>" . ucfirst($row['payment_status']) . "</div>";

    if (strtolower($row['payment_status']) === 'paid') {
        echo "<div style='color: gray; font-style: italic;'>Paid</div>";
    } else {
        echo "<div><a href='update_due_payment_form.php?due_id=" . $row['due_id'] . "'>Pay</a></div>";
    }
    echo "</div>";
}
    ?>

    <div class="grid-row total-row" style="background-color: #f9f9f9;">
    <div style="grid-column: span 4; text-align: right;">Total Due ($):</div>
    <div style="font-weight: bold;color: #444;">$<?php echo number_format($total_due, 2); ?></div>
    <div style="grid-column: span 2; text-align: right;">Remaining ($):</div>
    <div style="font-weight: bold;">$<?php echo number_format($total_remaining, 2); ?></div>
    <div></div>
</div>

</div>

<?php
include 'resources/footer.php';
?>
