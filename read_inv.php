<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

// Check if the user has the required role
if ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT') {
    header("Location: unauthorized.php");
    exit();
}

include 'resources/db_config.php';

// Calculate the total amount of all invoices
$totalQuery = "SELECT SUM(total) AS total_invoices FROM invoices";
$totalResult = $conn->query($totalQuery);
$totalInvoices = $totalResult->fetch_assoc()['total_invoices'] ?? 0;

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['inv_id'])) {
    $inv_id = $_GET['inv_id'];
    $deleteSql = "DELETE FROM invoices WHERE inv_id = $inv_id";
    if ($conn->query($deleteSql) === TRUE) {
        echo "<script>alert('Invoice deleted successfully');</script>";
        header("Location: read_inv.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT i.inv_id, c.cust_code, i.quot_id, r.course_title,  
       i.inv_date, i.inv_file, i.trainees, i.cost, 
       (i.trainees * i.cost) AS total, 
       GROUP_CONCAT(DISTINCT q.instance_id ORDER BY q.instance_id) AS instance_ids, 
       i.status
FROM invoices i
INNER JOIN customers c ON i.cust_id = c.cust_id
INNER JOIN courses r ON i.course_id = r.course_id
LEFT JOIN quotation_instances q ON i.quot_instance_id = q.instance_id";

if ($search !== '') {
    $escaped = $conn->real_escape_string($search);
    $sql .= " WHERE c.cust_code LIKE '%$escaped%' OR r.course_title LIKE '%$escaped%'";
}

$sql .= " GROUP BY i.inv_id, i.quot_instance_id, c.cust_code, r.course_title, 
           i.inv_date, i.inv_file, i.trainees, i.cost, i.status
          ORDER BY MIN(q.instance_id) DESC";
$result = $conn->query($sql);

// ✅ EXPORT: run before sending any HTML
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=invoices_list_" . date('Y-m-d') . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "Invoice Ref\tCustomer\tCourse Title\tIssued Date\tTotal\tGroup IDs\tStatus\n";

    while ($row = mysqli_fetch_assoc($result)) {
        $invoiceRef = "INV-" . date('y', strtotime($row['inv_date'])) . '-' . $row['quot_id'] . $row['inv_id'];
        echo $invoiceRef . "\t" .
             strtoupper($row['cust_code']) . "\t" .
             strtoupper($row['course_title']) . "\t" .
             $row['inv_date'] . "\t" .
             number_format($row['total'], 0) . "\t" .
             $row['instance_ids'] . "\t" .
             $row['status'] . "\n";
    }
    exit;
}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVOICES</title>
    <style>
    .invoice-grid-container {
    width: 95%;
    margin: 1rem auto;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    font-family: Arial, sans-serif;
    font-size: 0.7rem;
}

.invoice-grid-header,
.invoice-grid-row {
    display: grid;
    grid-template-columns: 8% 8% 40% 10% 5% 5% 12% 10% 5%;/* for delete 4%*/
    align-items: center;
    padding: 0.3rem;
    gap: 0.3rem;
    border-bottom: 1px solid #ccc;
}

.invoice-grid-header {
    font-weight: bold;
    background-color: #f2f2f2;
}

.invoice-status-pending {
    background-color: #fff3cd;
}

.invoice-status-inprocess {
    background-color: #cce500; /* light Green */
}

.invoice-status-paid {
    background-color: #d4edda;
}

.invoice-status-cancelled {
    background-color: #f8d7da;
}

.invoice-grid-row select {
    padding: 0.2rem;
    font-size: 0.9rem;
}

.invoice-grid-row:hover {
    background-color: #D9D9D9;
}
</style>

</head>
<body>
<?php include 'resources/header.php'; ?>
<div class='about_title'>
    <h3>List of INVOICES <i class="fa-solid fa-file-invoice fa-2xl"></i></h3>
    
       <div class='input-group'>
        <a href='invoices.php'><button type='submit' class='btn btn-primary'>Issue a New INVOICE</button></a>
    </div>
</div>

<div style="margin: 1rem;">
    <a href="read_inv.php?<?php echo http_build_query(array_merge($_GET, ['export' => 'excel'])); ?>" 
       style="text-decoration: none; background-color: #28a745; color: #fff;" class="btn btn-success">
       Export Invoice List to Excel
    </a>
</div>


<form method="GET" action="" style="margin: 1rem 25% 1rem 25%; display: flex; gap: 0.5rem;border:none;">
    <input type="text" name="search" placeholder="Search by Customer or Course Title"
           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
           style="flex: 1; padding: 0.5rem;">
    <button type="submit" style="padding: 0.5rem;border-radius: 4px;">Search Invoices</button>
    <a href="read_inv.php" style="padding: 0.5rem; background: #ccc; text-decoration: none;border-radius: 4px;">Reset</a>
</form>
 <div>
       <p style="font-family: Tajawal, Verdana, Geneva, Tahoma, sans-serif; font-size: 1.2rem; color: #CC0A0B; margin: 20px; text-align: center;"><strong>Total Invoices to Date: $ <?php echo number_format($totalInvoices+2217950, 0); ?></strong></p>
       
    </div>
<!--  -->
<div class="invoice-grid-container">
    <div class="invoice-grid-header">
        <div>INVOICE Ref#</div>
        <div>CUSTOMER</div>
        <div>COURSE TITLE</div>
        <div>ISSUED</div>
        <div>TOTAL</div>
        <div style="color: red;">Grp.<br>ID</div>
        <div>STATUS</div> 
           <div>PRINT INVOICE</div>
        <div>ACTION</div>
        
    </div>

    <?php while ($row = mysqli_fetch_array($result)) {
        // Determine class by status
       $statusClass = '';
if ($row['status'] == 'Paid') {
    $statusClass = 'invoice-status-paid';
} elseif ($row['status'] == 'Cancelled') {
    $statusClass = 'invoice-status-cancelled';
} elseif ($row['status'] == 'Pending') {
    $statusClass = 'invoice-status-pending';
} elseif ($row['status'] == 'In_process') {
    $statusClass = 'invoice-status-inprocess';
}
    ?>
    <div class="invoice-grid-row <?php echo $statusClass; ?>">
        <div><?php echo "INV-" . date('y', strtotime($row['inv_date'])) . '-' . $row['quot_id'] . $row['inv_id']; ?></div>
        <div><?php echo strtoupper($row['cust_code']); ?></div>
        <div><?php echo strtoupper($row['course_title']); ?></div>
        <div><?php echo $row['inv_date']; ?></div>
        <div><?php echo number_format($row['total'], 0); ?></div>
        <div style="color: red;"><?php echo $row['instance_ids']; ?></div>

        <div>
            <form style="border: none; margin: 1px; padding: 1px;" method="post" action="update_inv_status.php">
                <input type="hidden" name="inv_id" value="<?php echo $row['inv_id']; ?>">
                <select name="status" onchange="this.form.submit()">
                   <option value="Pending" <?php if ($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                   <option value="In_process" <?php if ($row['status'] == 'In_process') echo 'selected'; ?>>In Process</option>
                   <option value="Paid" <?php if ($row['status'] == 'Paid') echo 'selected'; ?>>Paid</option>
                   <option value="Cancelled" <?php if ($row['status'] == 'Cancelled') echo 'selected'; ?>>Cancel</option>
                </select>
            </form>
        </div>

         <div>
             <a href="inv_template_adib.php?inv_id=<?php echo $row['inv_id']; ?>" target="_blank">ADIB</a> |
             <a href="inv_template_adib_stamp.php?inv_id=<?php echo $row['inv_id']; ?>" target="_blank">ADIB-STMP</a>  |
              <a href="inv_template_kt.php?inv_id=<?php echo $row['inv_id']; ?>" target="_blank">KT</a>
            
           
        </div>

        <div><a href="edit_inv.php?edit=<?php echo $row['inv_id']; ?>" class="edit_btn">Edit</a> 
             <!--<a href="inv_template_wio.php?inv_id=<?php //echo $row['inv_id']; ?>" target="_blank">WIO</a>-->
             <?php if ($_SESSION['user_role'] === 'ADMIN' || $_SESSION['user_role'] === 'ACCOUNTANT') : ?>
           
                <!--<a href="read_inv.php?action=delete&inv_id=<?php echo $row['inv_id']; ?>" class="del_btn">Del</a>-->
            <?php endif; ?>
        </div>
    </div>
    <?php } ?>
</div>


<?php
include 'resources/footer.php';
?>
