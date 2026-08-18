<?php
session_start();
require_once '../resources/db_config.php';

// Check permissions
if (!isset($_SESSION['user_name']) || ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT')) {
    header("Location: ../login.php");
    exit();
}

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=customers_export_" . date('Ymd_His') . ".csv");

// Open output stream
$output = fopen('php://output', 'w');

// Output column headings
fputcsv($output, ['ID', 'Name', 'Short Code', 'Contact', 'Address', 'Telephone', 'Mobile', 'Email']);

// Fetch customers data
$sql = "SELECT cust_id, cust_name, cust_code, cust_contact, cust_address, cust_telephone, cust_mobile, cust_email FROM customers ORDER BY cust_name";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Format telephone numbers (remove new lines, join with commas)
        $telephone = str_replace(["\n", "\r"], [' ', ' '], $row['cust_telephone']);
        fputcsv($output, [
            $row['cust_id'],
            $row['cust_name'],
            $row['cust_code'],
            $row['cust_contact'],
            $row['cust_address'],
            $telephone,
            $row['cust_mobile'],
            $row['cust_email'],
        ]);
    }
}

fclose($output);
exit();
