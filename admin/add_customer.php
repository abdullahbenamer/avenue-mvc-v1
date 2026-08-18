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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare("INSERT INTO customers (cust_name, cust_code, cust_contact, cust_address, cust_telephone, cust_mobile, cust_email) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sssssss",
        $_POST['cust_name'],
        $_POST['cust_code'],
        $_POST['cust_contact'],
        $_POST['cust_address'],
        $_POST['cust_telephone'],
        $_POST['cust_mobile'],
        $_POST['cust_email']
    );

    if ($stmt->execute()) {
        header("Location: customers.php?success=Customer added");
        exit();
    } else {
        $error = $stmt->error;
    }
}
?>
<?php include 'header.php'; ?>

<h3>Add New Customer <i class="fa-solid fa-building fa-2x"></i></h3>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST" style="max-width: 600px;">
    <div class="input-group">
    <label for="cust_name">Customer Name</label><br>
    <input type="text" name="cust_name" id="cust_name" required><br><br>
</div>

<div class="input-group">
    <label for="cust_code">Customer Code</label><br>
    <input type="text" name="cust_code" id="cust_code" required><br><br>
</div>

<div class="input-group">
    <label for="cust_contact">Contact Person</label><br>
    <input type="text" name="cust_contact" id="cust_contact"><br><br>
</div>

<div class="input-group">
  <label for="cust_address">Customer Address</label>
  <textarea name="cust_address" id="cust_address" rows="5" style="width: 100%; max-width: 500px;"></textarea>
  <p><small style="color: red;">💡 You can use <b>separate lines</b> to make the address more readable.</small></p>
</div>


<div class="input-group">
    <label for="cust_telephone">Telephone</label><br>
    <input type="text" name="cust_telephone" id="cust_telephone"><br><br>
</div>

<div class="input-group">
    <label for="cust_mobile">Mobile</label><br>
    <input type="text" name="cust_mobile" id="cust_mobile"><br><br>
</div>

<div class="input-group">
    <label for="cust_email">Email</label><br>
    <input type="email" name="cust_email" id="cust_email"><br><br>
</div>

<div class="input-group">
    <button type="submit" class="btn btn-success">Save Customer</button>
    </div>
</form>
<br>
<?php include 'footer.php'; ?>
