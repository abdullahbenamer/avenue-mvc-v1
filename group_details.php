<?php
session_start();
include 'resources/db_config.php';
include 'resources/header.php';

if (!isset($_GET['id'])) {
    echo "<p>Invalid group ID.</p>";
    exit();
}

$instance_id = intval($_GET['id']);

$group_sql = "SELECT qi.*, q.quot_ref, c.cust_code, c.cust_name, crs.course_title,
       GROUP_CONCAT(DISTINCT i.full_name SEPARATOR ', ') AS instructors,
       (
         SELECT MIN(start_date) 
         FROM quotation_participants 
         WHERE instance_id = qi.instance_id
       ) AS start_date
FROM quotation_instances qi
JOIN quotations q ON qi.quot_id = q.quot_id
JOIN customers c ON qi.cust_id = c.cust_id
JOIN courses crs ON q.course_id = crs.course_id
LEFT JOIN quotation_instructors qinst ON qi.instance_id = qinst.instance_id
LEFT JOIN instructors i ON qinst.instructor_id = i.inst_id
WHERE qi.instance_id = $instance_id
GROUP BY qi.instance_id";

$group = $conn->query($group_sql)->fetch_assoc();

if (!$group) {
    echo "<p>Group not found.</p>";
    exit();
}

// Fetch participants
$participants_sql = "
SELECT full_name, full_name_a, payroll_no, mobile
FROM quotation_participants
WHERE instance_id = $instance_id
ORDER BY full_name ASC
";
$participants = $conn->query($participants_sql);
?>
<br>
<div style="margin: 2rem auto; width: 90%; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <h3>Group Details:</h3> <br>
    <h4><?php echo htmlspecialchars($group['instance_ref']); ?> </h4><br><h4 style="color: red;">(G-<?php echo $group['instance_id']; ?>)</h4>
    <br>
    <p><strong>Course Title:</strong> <?php echo htmlspecialchars($group['course_title']); ?></p>
    <!--<p><strong>Customer:</strong> <?php //echo htmlspecialchars($group['cust_code']); ?></p>-->
     <p><strong>Customer:</strong> <?php echo htmlspecialchars($group['cust_name']); ?></p>
    <p><strong>Quotation Ref:</strong> <?php echo htmlspecialchars($group['quot_ref']); ?></p>
    <p><strong>Start Date:</strong> <?php echo htmlspecialchars($group['start_date']); ?></p>
    <p><strong>Instructor(s):</strong> <?php echo htmlspecialchars($group['instructors']); ?></p>

   <h3>Participants</h3><br>
<div class="participants-grid">
    <!-- Grid Header Row -->
    <div class="participant-row header-row">
        <div class="grid-header">#</div>
        <div class="grid-header">Full Name</div>
        <div class="grid-header arabic-header">Arabic Name</div>
        <div class="grid-header">Payroll No.</div>
        <div class="grid-header">Mobile</div>
    </div>
    <!-- Grid Rows -->
    <?php
    $count = 1;
    while ($row = $participants->fetch_assoc()) {
        echo '<div class="participant-row">';
        echo '<div class="grid-cell">' . $count++ . '</div>';
        echo '<div class="grid-cell">' . htmlspecialchars($row['full_name'] ?? '') . '</div>';
        echo '<div class="grid-cell arabic-cell" dir="rtl" lang="ar">' . htmlspecialchars($row['full_name_a'] ?? '') . '</div>';
        echo '<div class="grid-cell">' . htmlspecialchars($row['payroll_no'] ?? '') . '</div>';
        echo '<div class="grid-cell">' . htmlspecialchars($row['mobile'] ?? '') . '</div>';
        echo '</div>';
    }
    ?>
</div>
</div> 

<style>
.participants-grid {
    display: flex;
    flex-direction: column;
    font-size: 0.95rem;
    border-radius: 6px;
    background-color: #fafafa;
}

.participant-row {
    display: grid;
    grid-template-columns: 40px 2fr 2fr 1fr 1.5fr;
    align-items: center;
}

.participant-row:nth-child(odd):not(.header-row) {
    background-color: #f2f2f2;
}

.participant-row:nth-child(even):not(.header-row) {
    background-color: #ffffff;
}

.grid-header {
    font-weight: 600;
    background-color: #ddd;
    padding: 0.5rem 0.75rem;
}

.grid-cell {
    padding: 0.4rem 0.75rem;
}

.arabic-header,
.arabic-cell {
    font-family: 'Tajawal', sans-serif;
    direction: rtl;
    text-align: right;
}

</style>

<?php include 'resources/footer.php'; ?>
