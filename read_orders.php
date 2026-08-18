<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

// Check if the user has the required role
if ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT' && $_SESSION['user_role'] != 'USER') {
    header("Location: unauthorized.php");
    exit();
}

include 'resources/header.php';
include 'resources/db_config.php'; // Assuming this file contains the database connection

// Function to get status color
function getStatusColor($status)
{
    switch ($status) {
        case 'new':
            return '#ff9999'; // Red color 
        case 'under-process':
            return '#9999ff'; // Blue color 
        case 'completed':
            return '#99ff99'; // Green color 
        default:
            return ''; // Default color if 
    }
}

// Retrieve data from the 'orders' table
$conditions = [];

if (!empty($_GET['customer'])) {
    $customer = mysqli_real_escape_string($conn, $_GET['customer']);
    $conditions[] = "c.cust_name LIKE '%$customer%'";
}

if (!empty($_GET['assignee'])) {
    $assignee = mysqli_real_escape_string($conn, $_GET['assignee']);
    $conditions[] = "e.user_name LIKE '%$assignee%'";
}

if (!empty($_GET['type'])) {
    $type = mysqli_real_escape_string($conn, $_GET['type']);
    $conditions[] = "t.type_name LIKE '%$type%'";
}

$whereClause = '';
if (count($conditions) > 0) {
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
}

$sql = "SELECT o.*, c.cust_name, t.type_name, e.user_name 
        FROM orders o
        JOIN customers c ON o.cust_id = c.cust_id
        JOIN ord_types t ON o.ord_type_id = t.ord_type_id
        JOIN users e ON o.user_id = e.user_id
        $whereClause
        ORDER BY o.ord_id DESC";

$result = mysqli_query($conn, $sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../resources/styles.css">
<style>
    /* Grid Container */
.grid-container {
    display: grid;
    grid-template-columns: 6% 11% 25% 25% 8% 5% 6% 4% 6%; /* Adjust column widths as needed */
    gap: 1px; /* Spacing between grid cells */
    margin: 0 auto; /* Center the grid horizontally */
    max-width: 98%; /* Adjust the width of the grid container */
    font-family: tajawal;
}

/* Header Cells */
.grid-header {
    background-color: #CCC;
    padding: 10px;
    font-weight: bold;
    text-align: center;
     font-size: 0.8rem;
}

/* Data Cells */
.grid-item {
    background-color: #fff;
    padding: 10px;
    text-align: center;
    font-size: 0.8rem;
    text-align: left;
}

/* Apply alternating row colors */
.grid-item:nth-child(9n + 1),
.grid-item:nth-child(9n + 2),
.grid-item:nth-child(9n + 3),
.grid-item:nth-child(9n + 4),
.grid-item:nth-child(9n + 5),
.grid-item:nth-child(9n + 6),
.grid-item:nth-child(9n + 7),
.grid-item:nth-child(9n + 8),
.grid-item:nth-child(9n + 9) {
    background-color: #f1f1f1;
}

.grid-item:nth-child(18n + 1),
.grid-item:nth-child(18n + 2),
.grid-item:nth-child(18n + 3),
.grid-item:nth-child(18n + 4),
.grid-item:nth-child(18n + 5),
.grid-item:nth-child(18n + 6),
.grid-item:nth-child(18n + 7),
.grid-item:nth-child(18n + 8),
.grid-item:nth-child(18n + 9) {
    background-color: #fff;
}

/* Responsive Design */
@media (max-width: 768px) {
    .grid-container {
        grid-template-columns: 1fr; /* Stack all columns on smaller screens */
    }
    
    .grid-header,
    .grid-item {
        text-align: center;
    }
}

</style>

</head>
<body>

<div class="about_title" style="margin: 1rem 25% 1rem 25%;">
    <p>
    <h3>List of Orders <i class="fa-solid fa-person-circle-question fa-2xl"></i></h3> 
    </p>
   
    <div class="input-group">
        <a href="orders.php"><button type="submit" class="btn">Create a New Order</button></a>
    </div>
</div>

<!-- Search -->
    <form method="GET" action="read_orders.php" style="width: 100%; margin: 1rem auto; display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; justify-content: flex-start; padding: 1rem; border: none;">
    <input type="text" name="customer" placeholder="Search by Customer"
        value="<?php echo htmlspecialchars($_GET['customer'] ?? ''); ?>"
        style="padding: 0.5rem 1rem; border: 1px solid #BBB; border-radius: 5px; flex: 1 1 200px;" />
        
    <input type="text" name="assignee" placeholder="Search by Assignee"
        value="<?php echo htmlspecialchars($_GET['assignee'] ?? ''); ?>"
        style="padding: 0.5rem 1rem; border: 1px solid #BBB; border-radius: 5px; flex: 1 1 200px;" />
        
    <input type="text" name="type" placeholder="Search by Order Type"
        value="<?php echo htmlspecialchars($_GET['type'] ?? ''); ?>"
        style="padding: 0.5rem 1rem; border: 1px solid #BBB; border-radius: 5px; flex: 1 1 200px;" />
        
    <button type="submit"
        style="padding: 0.5rem 1.2rem; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
        Search
    </button>
    
    <a href="read_orders.php" style="text-decoration: none;">
        <button type="button"
            style="padding: 0.5rem 1.2rem; background-color: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Reset
        </button>
    </a>
    <p style="color: red;font-size: 0.75rem;">Filter by one or more fields. Filter by Assignee to show Tasks</p>
</form>



<!-- Grid Container -->
<div class="grid-container">
    <!-- Grid Header -->
    <div class="grid-header">Order ID</div>
    <div class="grid-header">Order Type</div>
    <div class="grid-header">Customer</div>
    <div class="grid-header">Description</div>
    <div class="grid-header">Date</div>
    <div class="grid-header">More ..</div>
    <div class="grid-header">Assignee</div>
    <div class="grid-header">File</div>
    <div class="grid-header">Status</div>

    <!-- Grid Data Rows -->
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <div class="grid-item"><?php echo "ord-" . $row['ord_id']; ?></div>
        <div class="grid-item"><?php echo $row['type_name']; ?></div>
        <div class="grid-item"><?php echo $row['cust_name']; ?></div>
        <div class="grid-item"><?php echo $row['ord_subject']; ?></div>
        <div class="grid-item"><?php echo $row['ord_date']; ?></div>
        <div class="grid-item"><a href="order_details.php?id=<?php echo $row['ord_id']; ?>">Details</a></div>
        <div class="grid-item" style="font-weight: bold;"><?php echo strtoupper($row['user_name']); ?></div>
        <div class="grid-item">
            <?php if ($row['ord_file']) { ?>
                <a href="<?php echo $row['ord_file']; ?>" target="_blank">View</a>
            <?php } else {
                echo "N/A";
            } ?>
        </div>
       <div class="grid-item" id="status-<?php echo $row['ord_id']; ?>" style="background-color: <?php echo getStatusColor($row['status']); ?>;">
    <select name="status" onchange="updateStatus(this.value, <?php echo $row['ord_id']; ?>)">
        <option value="new" <?php if ($row['status'] == 'new') echo 'selected'; ?>>New</option>
        <option value="under-process" <?php if ($row['status'] == 'under-process') echo 'selected'; ?>>under</option>
        <option value="completed" <?php if ($row['status'] == 'completed') echo 'selected'; ?>>Done</option>
    </select>
</div>

            <?php } ?>
</div>

<script>
function updateStatus(status, ord_id) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "update_status.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Update the status color dynamically
            var statusCell = document.getElementById("status-" + ord_id);
            if (status == 'new') {
                statusCell.style.backgroundColor = '#ff9999'; // Red
            } else if (status == 'under-process') {
                statusCell.style.backgroundColor = '#9999ff'; // Blue
            } else if (status == 'completed') {
                statusCell.style.backgroundColor = '#99ff99'; // Green
            }
        }
    };
    xhr.send("ord_id=" + ord_id + "&status=" + status);
}

</script>

<?php include 'resources/footer.php'; ?>
