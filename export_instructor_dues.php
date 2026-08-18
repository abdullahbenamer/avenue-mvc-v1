<?php
session_start();
include 'resources/db_config.php';

// Restrict access to ACCOUNTANT role
if (!isset($_SESSION['user_name']) || $_SESSION['user_role'] !== 'ACCOUNTANT') {
    header("Location: unauthorized.php");
    exit();
}

// Set headers to prompt download as Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=instructor_dues_" . date("Ymd_His") . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Output column headers
echo "Due ID\tInstructor Name\tInstructor ID\tCourse Title\tCustomer Code\tStart Date\tTrainees\tDays\tAmount ($)\tPaid ($)\tStatus\n";

// Fetch data
$query = "
SELECT id.due_id, i.inst_id, i.full_name, c.course_title, id.course_date, 
       id.num_participants, id.days, id.due_amount, id.paid_amount, id.payment_status, cst.cust_code 
FROM instructor_dues id
JOIN instructors i ON id.instructor_id = i.inst_id
JOIN quotations q ON id.quot_id = q.quot_id
JOIN customers cst ON cst.cust_id = q.cust_id
JOIN courses c ON q.course_id = c.course_id
ORDER BY id.due_id DESC
";

$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    echo "{$row['due_id']}\t";
    echo strtoupper($row['full_name']) . "\t";
    echo "{$row['inst_id']}\t";
    echo "{$row['course_title']}\t";
    echo "{$row['cust_code']}\t";
    echo "{$row['course_date']}\t";
    echo "{$row['num_participants']}\t";
    echo "{$row['days']}\t";
    echo number_format($row['due_amount'], 2) . "\t";
    echo number_format($row['paid_amount'], 2) . "\t";
    echo ucfirst($row['payment_status']) . "\n";
}
?>
