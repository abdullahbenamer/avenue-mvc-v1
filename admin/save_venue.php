<?php
// Include database connection
include '../resources/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect POST data
    $ven_name = $_POST['ven_name'];
    $ven_address = $_POST['ven_address'];
    $count_id = $_POST['count_id'];
    $city_id = $_POST['city_id'];
    
    // Insert data into database
    $sql = "INSERT INTO venues (ven_name, ven_address, count_id, city_id) 
            VALUES ('$ven_name', '$ven_address', '$count_id', '$city_id')";
    
    if (mysqli_query($conn, $sql)) {
        echo "New venue added successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
    
    // Close connection
    mysqli_close($conn);
}
?>
