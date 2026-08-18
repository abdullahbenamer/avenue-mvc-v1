<?php
include("resources/db_config.php");

// Get cert_id from the URL
if (isset($_GET['cert_id'])) {
    $cert_id = $_GET['cert_id'];
} else {
    die("No certificate ID provided.");
}

// Query to get the certificate, course, and venue details
$query = "
    SELECT 
        cert.cert_id,
        cert.full_name,
        cert.start_date,
        cert.end_date,
        cert.cert_date,
        cert.part_id,
        cert.cust_id,
        courses.course_title,
        venues.ven_name
    FROM certificates cert
    JOIN courses ON cert.course_id = courses.course_id
    JOIN venues ON cert.ven_id = venues.ven_id
    WHERE cert.cert_id = ?";

// Prepare and execute the query
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $cert_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the certificate was found
if ($result->num_rows > 0) {
    // Fetch certificate data
    $row = $result->fetch_assoc();
    $participant_name = $row['full_name'];
    $course_title = $row['course_title'];
    $start_date = date("F j, Y", strtotime($row['start_date']));
    $end_date = date("F j, Y", strtotime($row['end_date']));
    $certificate_date = date("F j, Y", strtotime($row['cert_date']));
    $venue = $row['ven_name'];
    $certificate_id = "cert-" . date('y', strtotime($row['cert_date'])) . '-' . $row['cust_id'] . '-' . $row['part_id'] . '-' . $row['cert_id'];

    // Generate file name using the participant's name and certificate ID
    $safe_name = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', strtoupper($participant_name)); // Clean and format the participant's name for file system safety
    $file_name = "{$safe_name}_{$certificate_id}.pdf";
} else {
    die("Certificate not found.");
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo strtoupper($participant_name) . "-" . $certificate_id; ?></title>
    <style>
        /* Reset margin and padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

         body {
            font-family: 'Georgia', serif;
            background-image: url('resources/cert_background.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
        }

        .certificate-container {
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .certificate-content {
            width: 100%;
            height: 100%;
            padding: 0;
            /*background-color: rgba(255, 255, 255, 0.9);*/
            /*position: relative;*/
            /*box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);*/
            border: 25px solid;
            border-image: linear-gradient(to left, #C70607, #550000) 1;
        }

        /* Top-center logo (Organization Logo) */
        .certificate-logo {
            text-align: center;
            margin-bottom: 0.8rem;
            margin-top: 1rem;
        }

        .left-logo {
            position: absolute;
            top: 100px;
            left: 50px;
        }

        .left-logo img {
            width: 200px;
        }
        
           .ms-logo {
            position: absolute;
            top: 255px;
            left:80px;
            background-color: #ffffff;
            width: 150px;
        }
        
        /*.ms-logo img {*/
        /*    width: 60px;*/
        /*}*/

            .approved {
            font-size: 0.8rem;
            color: #FE6D00;
            margin-left: 60px;
        }

        .certificate-logo img {
            width: 350px;
        }

        .certificate-title {
            font-size: 3rem;
            color: #C70607;
            text-align: center;
            margin-top: 5px;
            margin-bottom: 5px;
            font-family: 'Cursive', serif;
        }

        .certificate-divider {
            width: 35%;
            height: 1px;
            background-color: #b59410;
            margin: 5px auto;
        }

        .certificate-name {
            font-size: 2.5rem;
            font-weight: bold;
            color: #B59410;
            text-align: center;
            margin-top: 30px;
            font-family: 'Cursive', serif;
        }

        .certificate-course {
            font-size: 1.5rem;
            color: #555;
            text-align: center;
            margin-top: 10px;
        }

        .certificate-course-date {
            font-size: 1.3rem;
            color: #555;
            text-align: center;
            margin-top: 10px;
            font-weight: 600;
        }

        .certificate-course-title {
            font-size: 2rem;
            font-weight: 700;
            color: #111;
            text-align: center;
            margin-top: 10px;
            font-family: 'Cursive', serif;
        }

        .certificate-date {
            font-size: 18px;
            color: #999;
            text-align: center;
            margin-top: 40px;
        }
  
        .certificate-bottom-arabesque-divider {
            max-width: 230px;
            display: block;
            margin: 15px auto;
            text-align: center;
        }

        .certificate-signature_right {
            position: absolute;
            bottom: 55px;
            right: 120px;
            text-align: center;
        }

        .certificate-signature_right img {
            width: 130px;
        }

        .certificate-signature_right .signature-name_right {
            font-size: 1rem;
            color: #555;
            margin-top: 10px;
        }

        .certificate-signature_left {
            position: absolute;
            bottom: 55px;
            left: 120px;
            text-align: center;
        }

        .certificate-signature_left img {
            width: 130px;
        }

        .certificate-signature_left .signature-name_left {
            font-size: 1rem;
            color: #555;
            margin-top: 10px;
        }

        .certificate-footer {
            font-size: 0.9rem;
            color: #666;
            position: absolute;
            bottom: 40px;
            left: 370px;
            text-align: center;
        }

        /* Add Arabesque designs to the corners */
        .corner-design {
            position: absolute;
            width: 75px;
        }

        .top-left-corner {
            top: 32px;
            left: 32px;
        }

        .top-right-corner {
            top: 32px;
            right:32px;
        }

        .bottom-left-corner {
           bottom: 32px;
            left: 32px;
        }

        .bottom-right-corner {
            bottom:32px;
            right:32px;
        }

        /* Vertical dividers */
        .vertical-divider {
            position: absolute;
            width: 20px;
            height: calc(100% - 170px); /* Adjust height to fit between the corners */
            background: url('resources/arabesque-divider-gold-vertical20.png') no-repeat center center;
            background-size: contain;
        }

        .left-divider {
            left: 26px; /* Adjust this to position 5px from the left corner */
            top: 85px;
        }

        .right-divider {
            right: 26px; /* Adjust this to position 5px from the right corner */
            top: 85px;
        }
   
        /* Top-right stamp (Apostille stamp) */
        .stamp {
            position: absolute;
            top: 90px;
            right: 100px;
        }

        .stamp img {
            width: 200px; /* Adjust size as needed */
        }
 </style>
   
         </head>
<body>
    <div class="certificate-container">
        <div class="certificate-content">
               <!-- Apostille Stamp in the top-right corner -->
            <div class="stamp">
                <img src="resources/cert-stamp400.png" alt="Apostille Stamp">
            </div>
            <!-- Left Logo -->
            <div class="left-logo">
                <img src="resources/pearson_btec_logo.png" alt="Pearson BTEC Approved">
                <p class="approved">Approved Centre</p>
                </div>
                <div><img class="ms-logo" width="100" src="resources/ms-partner-logo.png" alt="Pearson BTEC Approved"></div>

            <!-- Top-center Organization Logo -->
            <div class="certificate-logo">
                <img src="resources/logo.png" alt="Organization Logo">
            </div>

            <img src="resources/arabesque-divider-gold-up.png" class="certificate-bottom-arabesque-divider">
             
            <div class="certificate-title">CERTIFICATE</div>

            <div class="certificate-course">We certify that</div>

            <!-- Participant Name -->
            <div class="certificate-name"><?php echo strtoupper($participant_name); ?></div>
            <div class="certificate-course">Has successfully completed all prescribed requirements of the course entitled</div>
            <div class="certificate-course-title"><?php echo strtoupper($course_title); ?></div>
            
            <div class="certificate-course">From <span class="certificate-course-date"><?php echo $start_date; ?></span> To <span class="certificate-course-date"><?php echo $end_date; ?></span></div>
            <br>
            <div class="certificate-course">Venue: <?php echo $venue; ?></div>
            
            <img src="resources/arabesque-divider-gold.png" class="certificate-bottom-arabesque-divider">

            <!-- Right Signature Area -->
            <div class="certificate-signature_right">
                <img src="resources/osama_signature_light.png" alt="Signature">
                <div class="signature-name_right">Center Head</div>
            </div>

            <!-- Left Signature Area -->
            <div class="certificate-signature_left">
                <img src="resources/abdullah_signature.png" alt="Signature">
                <div class="signature-name_left">Quality Nominee</div>
            </div>

            <!-- Certificate Footer -->
            <div class="certificate-footer">Issue date: <?php echo $certificate_date; ?> | Certificate ID: <?php echo $certificate_id; ?></div>

            <!-- Corner Designs -->
            <img src="resources/arabesque-arch_lt_gold.png" alt="Top-left Corner" class="corner-design top-left-corner">
            <img src="resources/arabesque-arch_rt_gold.png" alt="Top-right Corner" class="corner-design top-right-corner">
            <img src="resources/arabesque-arch_lb_gold.png" alt="Bottom-left Corner" class="corner-design bottom-left-corner">
            <img src="resources/arabesque-arch_rb_gold.png" alt="Bottom-right Corner" class="corner-design bottom-right-corner">

            <!-- Vertical Dividers -->
            <div class="vertical-divider left-divider"></div>
            <div class="vertical-divider right-divider"></div>
        </div>
    </div>
</body>
</html>

          