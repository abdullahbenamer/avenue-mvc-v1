<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'resources/db_config.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

$instance_id = isset($_GET['instance_id']) ? intval($_GET['instance_id']) : null;

if (!$instance_id) {
    echo "No GROUP ID provided.";
    exit();
}

// Fetch instructor name(s) for the group
$inst_stmt = $conn->prepare("
    SELECT GROUP_CONCAT(i.full_name SEPARATOR ', ') AS instructors
    FROM quotation_instances qi
    LEFT JOIN quotation_instructors qi_inst ON qi.instance_id = qi_inst.instance_id
    LEFT JOIN instructors i ON qi_inst.instructor_id = i.inst_id
    WHERE qi.instance_id = ?
");
$inst_stmt->bind_param("i", $instance_id);
$inst_stmt->execute();
$inst_stmt->bind_result($instructor_names);
$inst_stmt->fetch();
$inst_stmt->close();


$query = "
    SELECT qp.full_name, qp.full_name_a, qp.payroll_no, qp.start_date, 
           c.course_title, c.course_title_a, c.week, c.month, cu.cust_name, qi.instance_ref, qi.cust_id, 
           qi.duration, v.ven_name, v.ven_address,
           GROUP_CONCAT(i.full_name SEPARATOR ', & ') AS instructors
    FROM quotation_participants qp
    JOIN courses c ON qp.course_id = c.course_id
    JOIN customers cu ON qp.cust_id = cu.cust_id
    JOIN quotation_instances qi ON qp.instance_id = qi.instance_id
    JOIN venues v ON qi.ven_id = v.ven_id
    LEFT JOIN quotation_instructors qi_inst ON qi.instance_id = qi_inst.instance_id
    LEFT JOIN instructors i ON qi_inst.instructor_id = i.inst_id
    WHERE qp.instance_id = ?
    GROUP BY qp.full_name, qp.full_name_a, qp.payroll_no
    ORDER BY qp.full_name
";


//  --  v.ven_name, v.ven_address
// JOIN venues v ON qi.ven_id = v.ven_id

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $instance_id);
$stmt->execute();
$result = $stmt->get_result();

$participants = [];
$instance_details = [];
while ($row = $result->fetch_assoc()) {
    $participants[] = $row;
    if (!$instance_details) {
        $instance_details = $row;  // Get instance-level details from the first row
    }
}

if (!$participants) {
    echo "No participants found for this GROUP ID: " . $instance_id;
    exit();
}

$course_title_a = $instance_details['course_title_a'];
$course_title = $instance_details['course_title'];
$customer_name = $instance_details['cust_name'];
$start_date = $instance_details['start_date'];
$venue_name = $instance_details['ven_name'];
$venue_address = $instance_details['ven_address'];
$instructor_names = $instance_details['instructors'] ?: 'N/A';

// Extract numeric value from duration (e.g., "10 DAYS" -> 10)
preg_match('/\d+/', $instance_details['duration'], $matches);
$program_duration = $matches[0];

// Generate a list of valid weekdays starting from the program start date
$weekdays = [];
$current_date = $start_date; // Start from the program start date
$total_weekdays = 0; // To track total weekdays

// Loop to collect weekdays until we reach the desired program duration
while ($total_weekdays < $program_duration) {
    $day_of_week = date('N', strtotime($current_date)); // 1 for Monday, 7 for Sunday

    // Only consider weekdays (Monday to Friday)
    if ($day_of_week < 6) {
        $weekdays[] = $current_date;
        $total_weekdays++;
    }

    // Move to the next day
    $current_date = date('Y-m-d', strtotime("+1 day", strtotime($current_date)));
}

// Now we have weekdays ready
$end_date = end($weekdays);

include("resources/header_inv.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Tajawal' rel='stylesheet'>
    <title><?php echo $course_title . " - Group No. " . $instance_id; ?></title>
    <style>
        .table-container {
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
        }

        .table-container table {
            width: 100%;
            border: 1px solid #AAA;
        }

        .table-container th,
        .table-container td {
            border: 1px solid #AAA;
            padding: 8px;
            text-align: center;
        }

        .table-container tbody tr:nth-child(even) {
            background-color: #E2E2E2;
        }

        .table-container tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }
    </style>
</head>

<body>

    <div class="table-container">
        <h3>PROGRAM ATTENDANCE REPORT - Group ID [<?php echo $instance_id; ?>]</h3>
        <h3>
            <p style="color: blue;">Instructor(s): <?php echo strtoupper(htmlspecialchars($instructor_names)); ?></p>
        </h3>
        <table>
            <tr>
                <th>Provider:</th>
                <td colspan="2">AVENUE INTERNATIONAL</td>
                <th>Number of candidates:</th>
                <td><?php echo count($participants); ?></td>
                <th>Course Duration:</th>
                <td><?php echo $instance_details['week']; ?> WEEKS - (
                <?php echo $program_duration; ?> DAYS)</td>
            </tr>
            <tr>
                <th>Course Title:</th>
                <td colspan="6"><span style="color: #333; font-size: 0.8rem; font-weight: 600; font-family: 'Tajawal';"><?php echo $course_title_a; ?></span><br>
                    <span style="color: #c70607; font-size: 0.7rem; font-weight: 500;"><?php echo $course_title; ?></span>
                </td>
            </tr>
            <tr>
                <th>Customer:</th>
                <td colspan="6"><span style="color: #444; font-size: 0.7rem; font-weight: 600;"><?php echo $customer_name; ?></span></td>
            <!-- <tr>
                <th>Instructor(s):</th>
                <td colspan="6"><?php //echo $instructor_names ? htmlspecialchars($instructor_names) : 'N/A'; ?></td>
            </tr> -->

            </tr>
            <tr>
                <th>Start Date</th>
                <td><?php echo date('d-m-Y', strtotime($start_date)); ?></td>
                <th>End Date</th>
                <!-- Calculating the end Date-->
               
                <td><?php echo date('d-m-Y', strtotime($end_date)); ?></td>

                <th>Venue</th>
                <td colspan="2"><?php echo $instance_details['ven_name'] . ' / ' . $instance_details['ven_address']; ?></td>
            </tr>
            </tr>
        </table>
        <?php
        // Generate a list of valid weekdays starting from the program start date
        $weekdays = [];
        $current_date = $start_date; // Start from the program start date
        $total_weekdays = 0; // To track total weekdays

        // Loop to collect weekdays until we reach the desired program duration
        while ($total_weekdays < $program_duration) {
            $day_of_week = date('N', strtotime($current_date)); // 1 for Monday, 7 for Sunday

            // Only consider weekdays (Monday to Friday)
            if ($day_of_week < 6) {
                $weekdays[] = $current_date;
                $total_weekdays++;
            }

            // Move to the next day
            $current_date = date('Y-m-d', strtotime("+1 day", strtotime($current_date)));
        }

        // Now chunk the weekdays for the attendance table
        $chunks = ceil(count($weekdays) / 5);
        for ($chunk = 0; $chunk < $chunks; $chunk++) {
            $start_index = $chunk * 5;
            $end_index = min(($chunk + 1) * 5, count($weekdays));
        ?>
            <!--<br><img src="resources/logo.png" width="300" alt="">-->
            <br>
            <h4>Attendance (Days <?php echo $start_index + 1 . ' - ' . $end_index; ?>)</h4>
            <table>
                <thead>
                    <tr>
                        <th>S</th>
                        <th>Trainee Name(s) <br><span style="font-size: 0.6rem; font-weight: 600; font-family: 'Tajawal';"><?php echo $course_title_a; ?></span><br>
                            <span style="font-size: 0.6rem; font-weight: 500;"><?php echo $course_title; ?></span><br>
                            <span style="color: #c70706; font-size: 0.7rem; font-weight: 500; font-family: 'Tajawal';"><?php echo $customer_name; ?></span>
                        </th>
                        <th>P. No</th>
                        <?php for ($i = $start_index; $i < $end_index; $i++) { ?>
                            <th>Day <?php echo $i + 1; ?> <br>
                                <?php echo date('l', strtotime($weekdays[$i])); // This gets the full name of the day (e.g., Monday) 
                                ?> - <?php echo date('d-m-Y', strtotime($weekdays[$i])); ?>
                            </th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($participants as $index => $participant) { ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><span style="color: #222; font-size: 0.8rem; font-weight: 600; font-family: 'Tajawal';"><?php echo $participant['full_name_a']; ?></span><br><span style="color: #222; font-size: 0.7rem; font-weight: 600; font-family: 'Tajawal';"><?php echo strtoupper($participant['full_name']); ?></span></td>
                            <td style="color: #222; font-size: 0.7rem;"><?php echo $participant['payroll_no']; ?></td>

                            <?php
                            // For each participant, create a cell for each weekday
                            for ($i = $start_index; $i < $end_index; $i++) {
                            ?>
                                <td>In: .......| .................<br><br>Out: .......| .................</td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

        <?php } ?>
       
        <!--<p>Trainer Signature: ........................................... - Center Signature: ........................................</p>-->
    </div>
<br><br>
</body>
</html>