<?php
session_start();
require_once '../resources/db_config.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT') {
    header("Location: ../unauthorized.php");
    exit();
}

$cust_id = $_GET['id'] ?? null;

if (!$cust_id) {
    echo "Customer ID is required.";
    exit();
}

// Fetch existing customer data
$stmt = $conn->prepare("SELECT * FROM customers WHERE cust_id = ?");
$stmt->bind_param("i", $cust_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if (!$customer) {
    echo "Customer not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare("UPDATE customers SET cust_name=?, cust_code=?, cust_contact=?, cust_address=?, cust_telephone=?, cust_mobile=?, cust_email=? WHERE cust_id=?");
    $stmt->bind_param(
        "sssssssi",
        $_POST['cust_name'],
        $_POST['cust_code'],
        $_POST['cust_contact'],
        $_POST['cust_address'],
        $_POST['cust_telephone'],
        $_POST['cust_mobile'],
        $_POST['cust_email'],
        $cust_id
    );

    if ($stmt->execute()) {
        header("Location: customers.php?success=Customer updated");
        exit();
    } else {
        $error = $stmt->error;
    }
}
?>
<?php include 'header.php'; ?>

<h3>Edit Customer <i class="fa-solid fa-building fa-2x"></i></h3>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST" style="max-width: 600px;">
    <div class="input-group">
        <label for="cust_name">Customer Name</label>
        <input type="text" name="cust_name" id="cust_name" value="<?= htmlspecialchars($customer['cust_name']) ?>" required>
    </div>

    <div class="input-group">
        <label for="cust_code">Customer Code</label>
        <input type="text" name="cust_code" id="cust_code" value="<?= htmlspecialchars($customer['cust_code']) ?>" required>
    </div>

    <div class="input-group">
        <label for="cust_contact">Contact Person</label>
        <input type="text" name="cust_contact" id="cust_contact" value="<?= htmlspecialchars($customer['cust_contact']) ?>">
    </div>

    <div class="input-group">
        <label for="cust_address">Address</label>
        <textarea name="cust_address" id="cust_address" rows="3"><?= htmlspecialchars($customer['cust_address']) ?></textarea>
    </div>
    <div class="input-group">
        <label for="cust_telephone">Telephone</label>
        <input type="text" name="cust_telephone" id="cust_telephone" value="<?= htmlspecialchars($customer['cust_telephone']) ?>">
    </div>
    <div class="input-group">
        <label for="cust_mobile">Mobile</label>
        <input type="text" name="cust_mobile" id="cust_mobile" value="<?= htmlspecialchars($customer['cust_mobile']) ?>">
    </div>
    <div class="input-group">
        <label for="cust_email">Email</label>
        <input type="email" name="cust_email" id="cust_email" value="<?= htmlspecialchars($customer['cust_email']) ?>">
    </div>
    <div class="input-group">
        <button type="submit" class="btn btn-primary">Update Customer</button>
    </div>
</form>

<br>
<?php include 'footer.php'; ?>
