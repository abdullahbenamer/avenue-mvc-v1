<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); 

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['user_role'] != 'ACCOUNTANT') {
    header("Location: unauthorized.php");
    exit();
}

include 'resources/db_config.php';

// Calculate total due and total Paid for all instructors
$total_query = "SELECT SUM(due_amount) AS total_due_amount, SUM(paid_amount) AS total_paid_amount FROM instructor_dues";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_due_amount = $total_row['total_due_amount'] ?? 0.00;
$total_paid_amount = $total_row['total_paid_amount'] ?? 0.00;

$query = "SELECT 
    d.due_id,
    i.inst_id,
    i.full_name,
    i.inst_portrait,
    d.course_date,
    d.num_participants,
    d.days,
    d.due_amount,
    d.paid_amount,
    d.payment_status,
COALESCE(
    GROUP_CONCAT(
        DISTINCT c.course_title
        ORDER BY c.course_title
        SEPARATOR ', '
    ),
    ''
) AS course_titles,

GROUP_CONCAT(
        DISTINCT IFNULL(cst.cust_code, 'N/A')
        SEPARATOR ', '
    ) AS cust_codes

FROM instructor_dues d
JOIN instructors i ON d.instructor_id = i.inst_id

LEFT JOIN instructor_due_instances idi 
       ON d.due_id = idi.due_id

LEFT JOIN courses c 
       ON idi.course_id = c.course_id

LEFT JOIN quotations q 
       ON idi.quot_id = q.quot_id

LEFT JOIN customers cst 
       ON q.cust_id = cst.cust_id

GROUP BY d.due_id
ORDER BY d.course_date DESC";

$result = $conn->query($query);
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .grid-list-container {
            width: 97%;
            margin: 1rem auto;
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
            font-family: sans-serif;
        }

        .grid-header,
        .grid-row {
            display: grid;
            grid-template-columns: 3% 18% 5% 22% 6% 6% 3% 4% 8% 5% 4% 5% 6%;
            gap: 0.3rem;
            align-items: center;
            padding: 0.3rem;
            border-bottom: 1px solid #BBB;
            font-size: 0.8rem;
        }

        .grid-header {
            font-weight: bold;
            background-color: #D1D1D1;
        }

        .grid-row {
            background: #fff;
            transition: background 0.2s ease;
        }

        .grid-row:hover {
            background: #D9D9D9;
        }

        .grid-row a {
            color: #0055aa;
            text-decoration: none;
        }

        .grid-row a:hover {
            text-decoration: underline;
        }

        .grid-list-container {
            overflow-x: auto;
        }
    </style>
    <title>INSTRUCTOR DUES</title>
</head>
<body>
<?php include 'resources/header.php'; ?>
<!--show succes message when inserting a new instructor dues-->
<?php
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success" style="margin: 1rem 25%; font-weight: bold; color: green;">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']);
}
?>
    <div class="about_title" style="margin: 1rem 25% 1rem 25%;">

        <h3>Instructor Dues List <i class="fa-solid fa-list fa-1xl"></i></h3>
        <div class='input-group'>
            <a href='instructor_dues.php'><button type='submit' class='btn'>New INSTRUCTOR Due</button></a>
        </div>
    </div>
        <!--Export to Excel-->
    <div><a href='export_instructor_dues.php'><button type='submit' class='btn' style="text-decoration: none; background-color: #28a745; color: #fff;">Export Instructor Dues List to Excel</button></a>
    </div>
    <!-- Grid layout container -->
    <div class="grid-list-container">
        <!-- Header row -->
        <div class="grid-header">
            <div>ID</div>
            <div>Instructor</div>
            <div>Inst_ID</div>
           <div>Training Programs</div>
            <div>Customer(s)</div>
            <div>Start Date</div>
            <div>Trainee</div>
            <div>Days</div>
            <div>Amount ($)</div>
            <div>paid</div>
            <div>status</div>
            <div>More..</div>
            <div>Action</div>
        </div>

        <!-- Totals at the top -->
        <div class="grid-row" style="font-weight:bold;color:#C70607;">
            <div colspan="9" style="grid-column: 1 / span 8; text-align:right;">Total Due Amount:</div>
            <div>$<?php echo number_format($total_due_amount, 2); ?></div>
            <div>$<?php echo number_format($total_paid_amount, 2); ?></div>
            <div></div>
        </div>

        <!-- Instructor dues rows -->
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="grid-row">
                <div><?php echo $row['due_id']; ?></div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <!-- Instructor's name and photo -->
                    <?php
                    $portrait = htmlspecialchars($row['inst_portrait']) ?: 'instructor_male.jpg';
                    $portrait_path = 'search/photo_uploads/' . $portrait;
                    ?>
                    <img src="<?php echo $portrait_path; ?>" alt="Instructor Portrait" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover;">
                    <span><?php echo strtoupper(htmlspecialchars($row['full_name'])); ?></span>
                </div>

                <div><?php echo "[{$row['inst_id']}]"; ?></div>
           <div><?php echo htmlspecialchars($row['course_titles'] ?? ''); ?></div>
              <div><?php echo htmlspecialchars($row['cust_codes']); ?></div>
                <div><?php echo $row['course_date']; ?></div>
                <div><?php echo $row['num_participants']; ?></div>
                <div><?php echo $row['days']; ?></div>
                <div>$<?php echo number_format($row['due_amount'], 2); ?></div>
                <div>$<?php echo number_format($row['paid_amount'], 2); ?></div>
                <div><?php echo ucfirst($row['payment_status']); ?></div>
                <div>

                <a href='instructor_single_dues.php?instructor_id=<?php echo $row['inst_id']; ?>'
   style="padding: 3px 8px; background: green; color: white; border-radius: 4px; text-decoration: none;">
   Details
</a>
                </div>
                <div style="display: flex; flex-direction: column; gap: 6px; width: max-content;">
                    <a href='edit_due_form.php?due_id=<?php echo $row['due_id']; ?>' title="Edit Due" style="
       display: inline-block;
       padding: 3px 8px;
       background-color: #007bff;
       color: white;
       text-decoration: none;
       border-radius: 4px;
       text-align: center;
       font-weight: 500;
       transition: background-color 0.3s;
     " onmouseover="this.style.backgroundColor='#0056b3';" onmouseout="this.style.backgroundColor='#007bff';">Edit</a>

                    <a href='delete_due.php?due_id=<?php echo $row['due_id']; ?>' title="Delete Payment" onclick="return confirm('Are you sure you want to delete this due record?');" style="
       display: inline-block;
       padding: 3px 8px;
       background-color: #dc3545;
       color: white;
       text-decoration: none;
       border-radius: 4px;
       text-align: center;
       font-weight: 500;
       transition: background-color 0.3s;
     " onmouseover="this.style.backgroundColor='#a71d2a';" onmouseout="this.style.backgroundColor='#dc3545';">Delete</a>
                </div>

            </div>
        <?php } ?>

        <!-- Total at the bottom -->
        <div class="grid-row" style="font-weight:bold;color:#C70607;">
            <div colspan="9" style="grid-column: 1 / span 8; text-align:right;">Total Due Amount:</div>
            <div>$<?php echo number_format($total_due_amount, 2); ?></div>
            <div>$<?php echo number_format($total_paid_amount, 2); ?></div>
            <div></div>
        </div>
    </div>

    <script>
        // remove the notification message after refreshing
        if (window.location.search.includes("deleted") || window.location.search.includes("error")) {
            // Use history.replaceState to remove query string without reloading
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    </script>

    <?php
    include 'resources/footer.php';
    ?>