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
if ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT' && $_SESSION['user_role'] != 'USER') {
    header("Location: unauthorized.php");
    exit();
}

include 'resources/db_config.php';
include 'resources/header.php';

// Fetch all certificates with venue and instance information without duplication
$sql = "SELECT DISTINCT t.cert_id, t.full_name, qp.full_name_a, t.cust_id, c.cust_code, r.course_title, t.cert_date,
               v.ven_id, v.ven_name, qp.instance_id
        FROM certificates t
        INNER JOIN customers c ON t.cust_id = c.cust_id
        INNER JOIN courses r ON t.course_id = r.course_id
        INNER JOIN venues v ON t.ven_id = v.ven_id
        INNER JOIN quotation_participants qp ON t.part_id = qp.part_id ORDER BY qp.instance_id DESC";  // Fetch instance_id from quotation_participants

$result = mysqli_query($conn, $sql);

// Check if query failed
if (!$result) {
    die("Error: " . $sql . "<br>" . mysqli_error($conn));
}



// Delete certificate
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $sql = "DELETE FROM certificates WHERE cert_id=$id";
    mysqli_query($conn, $sql);
    header('location: read_cert.php');
}
ob_end_flush();
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .cert-grid-container {
            width: 97%;
            margin: 0.6rem auto;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            font-family: Arial, sans-serif;
        }

        .cert-grid-header,
        .cert-grid-row {
            display: grid;
            grid-template-columns: 10% 25% 4% 6% 30% 8% 8% 5% 5% 5%;
            gap: 0.2rem;
            padding: 0.3rem;
            font-size: 0.8rem;
            align-items: start;
            border-bottom: 1px solid #ccc;
        }

        .cert-grid-header {
            background: #f0f0f0;
            font-weight: bold;
            border-top: 2px solid #888;
            border-bottom: 2px solid #888;
        }

        .cert-grid-row a {
            display: inline-block;
            margin: 0.1rem 0;
            font-size: 0.7rem;
            color: #fff;
            font-weight: bold;

        }

        .cert-grid-row:hover {
    background-color: #E9E9E9;
}

        .highlight-red {
            color: red;
            font-weight: bold;
        }

        .btn-cert {
            display: inline-block;
            padding: 3px 6px;
            margin: 3px 2px;
            font-size: 0.7rem;
            font-family: Arial, sans-serif;
            text-align: center;
            text-decoration: none;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            color: #fff;
        }

        /* Specific colors */
        .btn-normal {
            background-color: #007bff;
        }

        /* Blue */
        .btn-stamp {
            background-color: #6c757d;
        }

        /* Gray */
        /*.btn-pmp {*/
        /*    background-color: #28a745;*/
        /*}*/

        /* Green */
        /*.btn-pmp-stamp {*/
        /*    background-color: #17a2b8;*/
        /*}*/

        /* Teal */
        .btn-edit {
            background-color: blue;
        }

        /* Yellow */
        .btn-danger {
            background-color: #dc3545;
        }/* Red */

        /* Hover: no underline, same color */
        .btn-cert:hover {
            /* text-decoration: none; */
            color: #000;
            opacity: 0.9;
            /* subtle feedback */
        }
        .arabic-name {
            font-size: 0.85rem;
            font-family: 'Tajawal', 'Arial', sans-serif;
            /*direction: rtl;*/
            /*text-align: right;*/
        }
        
    </style>
    <title>CERTIFICATES</title>
</head>

<body>

<div class="about_title" style="margin: 1rem 25% 1rem 25%;">
    <p>
    <h3>List of CERTIFICATES <i class="fa-solid fa-award fa-2xl"></i></h3>
    </p>

    <div class="input-group">
        <a href="certificates.php"><button type="submit" class="btn">Issue New Group CERTIFICATE(S)</button></a>
    </div>
</div>
<!-- --------------------------- -->
<div class="cert-grid-container">
    <!-- Header -->
    <div class="cert-grid-header">
        <div>Reference #</div>
        <div>Full Name</div>
        <div>G.ID</div>
        <div>COMPANY</div>
        <div>Course Title</div>
        <div>Issued</div>
        <div>Venue</div>
        <div>Certificate</div>
        <!--<div>PMP<br>Cert</div>-->
        <div>Action</div>

    </div>

    <!-- Rows -->
    <?php while ($row = mysqli_fetch_array($result)) { ?>
        <div class="cert-grid-row">
            <div><?php echo "CERT-" . date('y', strtotime($row['cert_date'])) . '-' . $row['cust_id'] . '-' . $row['cert_id']; ?></div>
           <div class="arabic-name"><?php echo $row['full_name_a']; ?><br>
            <?php echo strtoupper($row['full_name']); ?></div>
            <div class="highlight-red"><?php echo $row['instance_id']; ?></div>
            <!-- <div><?php //echo strtoupper($row['cust_name']); 
                        ?></div> -->
            <div><?php echo strtoupper($row['cust_code']); ?></div>
            <div><?php echo strtoupper($row['course_title']); ?></div>
            <div><?php echo date('d-m-Y', strtotime($row['cert_date'])); ?></div>
            <div><?php echo strtoupper($row['ven_name']); ?></div>
            <!-- ------------------------ -->
            <div>
                <a href="cert_template.php?cert_id=<?php echo $row['cert_id']; ?>" class="btn-cert btn-normal" target="_blank">Normal</a><br>
                <a href="cert_template_w-stamp.php?cert_id=<?php echo $row['cert_id']; ?>" class="btn-cert btn-stamp" target="_blank">With Seal</a>
            </div>

            <!--<div>-->
            <!--    <a href="cert_template_pmp.php?cert_id=<?php //echo $row['cert_id']; ?>" class="btn-cert btn-pmp" target="_blank">PMP</a><br>-->
            <!--    <a href="cert_template_w-stamp_pmp.php?cert_id=<?php //echo $row['cert_id']; ?>" class="btn-cert btn-pmp-stamp" target="_blank">PMP-Seal</a>-->
            <!--</div>-->

            <div>
                <a href="edit_cert.php?edit=<?php echo $row['cert_id']; ?>" class="btn-cert btn-edit">Edit</a>
                <!--<?php //if ($_SESSION['user_role'] === 'ADMIN' || $_SESSION['user_role'] === 'ACCOUNTANT') : ?>-->
                <!--    <a href="read_cert.php?del=<?php //echo $row['cert_id']; ?>" class="btn-cert btn-danger">Delete</a>-->
                <!--<?php //endif; ?>-->
            </div>
        </div>
    <?php } ?>
</div>

<?php
include 'resources/footer.php';
?>