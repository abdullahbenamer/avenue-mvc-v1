<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

include 'resources/header.php';
include 'resources/db_config.php';

// Check if Order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='header'>ORDER ID not provided.</div>";
    exit();
}

$ord_id = intval($_GET['id']); // Ensure the order ID is an integer to prevent SQL injection

// Retrieve details of the Order with the provided ID
$sql = "SELECT o.*, c.cust_name, t.type_name, u.user_name 
        FROM orders o
        JOIN customers c ON o.cust_id = c.cust_id
        JOIN ord_types t ON o.ord_type_id = t.ord_type_id
        JOIN users u ON o.user_id = u.user_id
        WHERE o.ord_id = $ord_id";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error executing query: " . mysqli_error($conn);
    exit();
}

if (mysqli_num_rows($result) == 0) {
    echo "<div class='header'>Order not found.</div>";
    exit();
}

$row = mysqli_fetch_assoc($result);
?>

<br>
<div class="about_title" style="margin: 1rem 25% 1rem 25%;">
<p><i class="fa-solid fa-file fa-2xl"></i></p>  
<br>
    <p>
        <h3>Order Details</h3>
    </p>
    <br>
    
    <div class="input-group">
        <a href="read_orders.php" class="btn"><button type="submit" class="btn">Back to Orders List</button></a>
    </div>
    
</div>

<div class="rfq_container">
<div class="details">
    <p><strong>Order Type:</strong> <?php echo $row['type_name']; ?></p>
    <br>
    <p><strong>Customer:</strong> <?php echo $row['cust_name']; ?></p>
    <br>
    <p><strong>Assignee:</strong> <?php echo $row['user_name']; ?></p>
    <br>
    <p><strong>Subject:</strong> <?php echo $row['ord_subject']; ?></p>
    <br>
    <p><strong>Date:</strong> <?php echo $row['ord_date']; ?></p>
    <br>
    <p><strong>Details:</strong></p>
     <p><?php echo nl2br($row['ord_details']); ?></p>
    <p> <?php if ($row['ord_file']) { ?>
                        <a href="<?php echo $row['ord_file']; ?>" target="_blank">View Attachement</a> 
                    <?php } else {
                        echo "No Attachement.";
                    } ?></p>
 
    <div class="input-group">
        <a href="edit_order.php?id=<?php echo $row['ord_id']; ?>" class="btn"><button type="submit" class="btn">Edit Order</button></a>
    </div>
</div>
</div>

<?php include 'resources/footer.php'; ?>
