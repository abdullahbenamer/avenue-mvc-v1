<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session and check if user is logged in
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

// Check if the user has the required role
if ($_SESSION['user_role'] != 'ACCOUNTANT') {
    header("Location: unauthorized.php");
    exit();
}

// Include database configuration and header
include("resources/db_config.php");
include("resources/header_inv.php");

// Get the invoice ID from URL parameter
$inv_id = isset($_GET['inv_id']) ? intval($_GET['inv_id']) : 0;


//GROUP_CONCAT function has a default limit on the maximum allowed length.
//increase the group_concat_max_len setting in MySQL:
$conn->query("SET SESSION group_concat_max_len = 1000000");

// SQL query to fetch invoice details and participant names
$sql = "
SELECT 
    i.*, 
    c.cust_name, 
    c.cust_address, 
    c.cust_telephone, 
    c.cust_contact, 
    c.cust_email, 
    c.cust_mobile,
    r.course_title, 
    q.quot_ref,
    q.duration,
    qi.instance_id,
    p.part_id,
    p.start_date,
    GROUP_CONCAT(CONCAT(p.payroll_no, ' - ', p.full_name) SEPARATOR ', ') AS full_name 
FROM invoices i 
INNER JOIN customers c ON i.cust_id = c.cust_id 
INNER JOIN courses r ON i.course_id = r.course_id
INNER JOIN quotations q ON i.quot_id = q.quot_id 
INNER JOIN quotation_instances qi ON i.quot_instance_id = qi.instance_id 
LEFT JOIN quotation_participants p ON qi.instance_id = p.instance_id 
WHERE i.inv_id = $inv_id
GROUP BY i.inv_id
";

// Execute the query
$result = $conn->query($sql);

// Check if the invoice exists
if ($result->num_rows > 0) {
    $invoice = $result->fetch_assoc();
} else {
    echo "No results found";
    exit;
}

// Close the database connection
$conn->close();

// Function to convert numbers to words for invoice total
function convert_number_to_words($number) {
    $words = [
        '0' => 'zero', '1' => 'one', '2' => 'two', '3' => 'three',
        '4' => 'four', '5' => 'five', '6' => 'six', '7' => 'seven',
        '8' => 'eight', '9' => 'nine', '10' => 'ten', '11' => 'eleven',
        '12' => 'twelve', '13' => 'thirteen', '14' => 'fourteen', '15' => 'fifteen',
        '16' => 'sixteen', '17' => 'seventeen', '18' => 'eighteen', '19' => 'nineteen',
        '20' => 'twenty', '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
        '60' => 'sixty', '70' => 'seventy', '80' => 'eighty', '90' => 'ninety'
    ];

    if ($number < 20) {
        return $words[$number];
    }

    if ($number < 100) {
        return $words[($number - $number % 10)] . ($number % 10 ? '-' . $words[$number % 10] : '');
    }

    if ($number < 1000) {
        return $words[intval($number / 100)] . ' hundred' . ($number % 100 ? ' and ' . convert_number_to_words($number % 100) : '');
    }

    if ($number < 1000000) {
        return convert_number_to_words(intval($number / 1000)) . ' thousand' . ($number % 1000 ? ' ' . convert_number_to_words($number % 1000) : '');
    }

    if ($number < 1000000000) {
        return convert_number_to_words(intval($number / 1000000)) . ' million' . ($number % 1000000 ? ' ' . convert_number_to_words($number % 1000000) : '');
    }

    return convert_number_to_words(intval($number / 1000000000)) . ' billion' . ($number % 1000000000 ? ' ' . convert_number_to_words($number % 1000000000) : '');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Set the dynamic page title using the invoice data -->
    <title>INVOICE-AVENUE-<?php echo isset($invoice['quot_id']) && isset($invoice['inv_id']) ? date('y', strtotime($invoice['inv_date'])) . "-" . htmlspecialchars($invoice['quot_id'] . $invoice['inv_id']) : 'N/A'; ?></title>
       
    <style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        /* border: 1px solid #ddd; /* Light borders */
        padding: 8px;
    }
    th {
        /* background-color: #f0f0f0; /* Header gray background */
         background-color: #DDD; /* Header gray background */
        text-align: center;
    }
    .subtotal-row, .grand-total-row {
        background-color: #f0f0f0; /* Same gray as headers */
    }
    .amount {
        text-align: right;
    }
    /* Remove adjacent borders for Subtotal and Grand Total rows */
    .subtotal-row td:nth-child(1),
    .subtotal-row td:nth-child(2),
    .grand-total-row td:nth-child(1),
    .grand-total-row td:nth-child(2) {
        border-left: none;
        border-right: none;
    }
        .invoice-breakdown td:nth-child(1) {
            width: 50%;
        }
        .invoice-breakdown th, .invoice-breakdown td {
            padding: 8px;
        }

        /* Word-wrap for specific headers */
        .invoice-breakdown th.wrap {
            white-space: normal;
            word-wrap: break-word;
            width: 100px; /* Adjust width to limit it to content */
        }

        /* Control width of specific columns */
        .invoice-breakdown td.cost, .invoice-breakdown td.candidates {
            width: 100px; /* Adjust width for fitting content */
        }

        /* Right-align 'Subtotal' and 'Grand Total' */
        .invoice-breakdown td.right-align {
            text-align: right;
            padding-right: 10px; /* Add some padding for better alignment */
        }

        .invoice-breakdown td.amount {
            text-align: right;
            font-weight: bold;
        }

        /* Center align headers and table cells */
        .invoice-breakdown th, .invoice-breakdown td {
            text-align: center;
        }

        /* Set left alignment for the first column (Participants) */
        .invoice-breakdown td:nth-child(1) {
            text-align: left;
        }

        /* Style for Customer Name and Course Title */
        .customer-name, .course-title {
            color: #CC0A0B;
            font-weight: bold;
        }

        /* Style for the number in words */
        .amount-in-words {
            color: #CC0A0B;
            font-weight: bold;
        }
        
        /* Sigature */
.signature-container {
    display: flex;
    justify-content: flex-start; /* Aligns the image to the left */
    align-items: center;
    height: 100px; /* Adjust the height of the container as needed */
    position: relative;
}

.signature {
    width: 100px; 
    transform: rotate(-15deg); /* Tilt effect */
    position: absolute;
    right: 10px; 
    top: -5px; 
    transform: translateY(-50%) rotate(-15deg); /* Vertical center and tilt */
}

    </style>
</head>
<body>
  <div class="container">

    <!-- Invoice Header -->
    <div class="container">
        <!-- Invoice Header -->
        <table class="header-table">
            <tr>
                <th rowspan="2"><h2>Invoice</h2></th>
                <!-- Safely output invoice data -->
                <td>Invoice Ref#: <b>INV-<?php echo isset($invoice['quot_id']) && isset($invoice['inv_id']) ? date('y', strtotime($invoice['inv_date'])) . "-" . htmlspecialchars($invoice['quot_id'] . $invoice['inv_id']) : 'N/A'; ?></b>
                <br><br><span style="color: red;">Group ID: <strong><?php echo htmlspecialchars($invoice['instance_id']); ?></strong></span></td>
                <td>Invoice Date: <b><?php echo isset($invoice['inv_date']) ? date('d-m-Y', strtotime($invoice['inv_date'])) : 'N/A'; ?></b></td>
            </tr>
        </table>
 <br>
    <!-- Company Info -->
    <table class="company-info">
        <tr>
            <th colspan="2"><h3>AVENUE INTERNATIONAL</h3></th>
            <td>
                 <i class="fa-solid fa-location-pin"></i> Cumhuriyet Mah., 10 Ergenekon Cad., Ahmetbey Plaza k4, Pangalti, Şişli 34360 Istanbul, Turkey<br>
                <i class="fa-solid fa-phone"></i> +90 (212) 246 2080 <br> <i class="fa-solid fa-mobile"></i> +90 (542) 816 3500<br>
                <i class="fa-solid fa-envelope"></i> info@avenueinternational.net <br> <i class="fa-solid fa-globe"></i> www.avenueinternational.net
            </td>
        </tr>
    </table>
    <br>
    <!-- Customer Info and Course Details -->
   <table class="customer-info">
    <tr>
        <th colspan="2">Bill To: <h4 class="customer-name"><?php echo strtoupper(htmlspecialchars($invoice['cust_name'])); ?></h4></th>
        <td><i class="fa-solid fa-location-pin"></i> <?php echo htmlspecialchars($invoice['cust_address']); ?><br>
            <i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($invoice['cust_telephone']); ?><br>
             <i class="fa-solid fa-mobile"></i> <?php echo htmlspecialchars($invoice['cust_mobile']); ?><br>
            <i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($invoice['cust_email']); ?>
            </td>
    </tr>
    <tr>
        <!-- Set colspan="3" to span across all columns in this row -->
        <td colspan="3">
              Program Title: <strong class="course-title"><?php echo htmlspecialchars($invoice['course_title']); ?></strong>
              <br>Duration: <strong><?php echo htmlspecialchars($invoice['duration']); ?></strong>
               <br>Start Date: <strong><?php echo htmlspecialchars($invoice['start_date']); ?></strong>
              <!--<br>Proposal Ref#: <strong><?php //echo htmlspecialchars($invoice['quot_ref']); ?></strong>-->
              </td>
        </td>
    </tr>
</table>

 <br>
   <!-- Invoice Breakdown -->
<table class="invoice-breakdown">
    <tr>
        <th style="width: 50%;">Payroll No - Participant Name(s)</th>
        <th class="wrap"></th>
        <th class="wrap">Number of<br>Candidates</th>
        <th>Total Amount</th>
    </tr>
    <tr>
        <td style="width: 50%;">
    <?php
    if (!empty($invoice['full_name'])) {
        $full_names = explode(',', $invoice['full_name']);
        foreach ($full_names as $index => $name) {
            echo ($index + 1) . ". " . strtoupper(trim($name)) . "<br>";
        }
    } else {
        echo "No participants listed";
    }
    ?>
</td>

       
        <td class="cost"><strong></strong></td>
        <td class="candidates"><strong><?php echo $invoice['trainees']; ?></strong></td>
        <!-- Calculate the total Manually -->
        <td class="amount"><strong>US$<?php echo "147,620"; ?></strong></td>
    </tr>

    <!-- Subtotal Row -->
    <tr class="subtotal-row">
        <td colspan="2" style="border-right: none; background-color: #f0f0f0;"></td>
        <td style="text-align: right; border-left: none; background-color: #f0f0f0;"><b>SUBTOTAL</b></td>
        <td class="amount" style="text-align: right; background-color: #f0f0f0;"><h4>US$<?php echo "147,620"; ?></h4></td>
    </tr>

    <tr>
        <td colspan="2"></td>
        <td style="text-align: right;"><b>GRAND TOTAL</b></td>
        <td class="amount" style="text-align: right;"><h3>US$<?php echo "147,620"; ?></h3></td>
    </tr>

    <!-- Amount in Words Section -->
    <tr>
        <td colspan="4" style="text-align: center;">
            <i>Amount in words: <span style="color: #CC0A0B; font-weight: 600;">ONE HUNDRED FORTY-SEVEN THOUSAND SIX HUNDRED TWENTY US DOLLARS ONLY</span></i>
        </td>
    </tr>
</table>


 <br>
    <!-- Bank Information -->
    <table class="bank-info">
        <tr>
            <th colspan="3"><i class="fa-solid fa-bank"></i> Bank Information</th>
        </tr>
          <tr>
            <th>Bank Name</th>
            <td colspan="2">ABU DHABI ISLAMIC BANK</td>
        </tr>
        <tr>
            <th>Account Name</th>
            <td colspan="2">AVENUE INTERNATIONAL L.L.C-FZ</td>
        </tr>
         <tr>
            <th>Bank Branch</th>
            <td colspan="2">BBD VARIABLE CHANNEL</td>
        </tr>
        <tr>
            <th>Account Number</th>
            <td colspan="2">29192984</td>
        </tr>
               <tr>
            <th>IBAN</th>
            <td colspan="2">AE600500000000029192984</td>
        </tr>
        <tr>
            <th>SWIFT Code</th>
            <td colspan="2">ABDIAEAD</td>
        </tr>
         <tr>
            <th>Account Type</th>
            <td colspan="2">USD</td>
        </tr>
               </table>
    
    <div class="signature-container">
        <img src="resources/osama_signature_light.png" alt="Signature" class="signature">
    </div>

<!--</div>-->
</body>
</html>
