<?php
// Include database connection
include '../resources/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect POST data
    $cust_name = $_POST['cust_name'];
    $cust_contact = $_POST['cust_contact'];
    $cust_address = $_POST['cust_address'];
    $cust_telephone = $_POST['cust_telephone'];
    $cust_mobile = $_POST['cust_mobile'];
    $cust_email = $_POST['cust_email'];
    
    // Insert data into database
    $sql = "INSERT INTO customers (cust_name, cust_contact, cust_address, cust_telephone, cust_mobile, cust_email) 
            VALUES ('$cust_name', '$cust_contact', '$cust_address', '$cust_telephone', '$cust_mobile', '$cust_email')";
    
    if (mysqli_query($conn, $sql)) {
        echo "New customer added successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
    
    // Close connection
    mysqli_close($conn);
}
?>
