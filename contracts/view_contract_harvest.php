<?php
function formatDate($dateString) {
  return date("jS F Y", strtotime($dateString));
}

// Get form data
$instructor = htmlspecialchars($_POST['instructor_full_name']);
$location = htmlspecialchars($_POST['location']);
$course = htmlspecialchars($_POST['course_title']);
$rate = htmlspecialchars($_POST['rate']);
$end_date = formatDate($_POST['end_date']);
$contract_date = formatDate($_POST['contract_date']);
$bankDetails = nl2br(htmlspecialchars($_POST['bank_details']));

// Generate file name
$sanitizedInstructor = preg_replace("/[^a-zA-Z0-9]/", "_", strtolower($instructor));
$sanitizedDate = date("Ymd", strtotime($_POST['contract_date']));
$fileBaseName = $sanitizedInstructor . "_" . $sanitizedDate;
$contractDir = 'contracts/';
if (!is_dir($contractDir)) {
    mkdir($contractDir, 0775, true);
}
$contractFilePath = $contractDir . $fileBaseName . '_contract.html';

// Handle signature image
$signatureImgTag = '';
if (isset($_FILES['signature_image']) && $_FILES['signature_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/signatures/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $tmpName = $_FILES['signature_image']['tmp_name'];
    $fileName = basename($_FILES['signature_image']['name']);
    $targetFilePath = $uploadDir . uniqid() . '_' . $fileName;

    if (move_uploaded_file($tmpName, $targetFilePath)) {
        $signatureImgTag = '<img src="' . htmlspecialchars($targetFilePath) . '" alt="Signature Image" height="80">';
    }
}

// Prepare contract HTML content
ob_start();
?>

<!DOCTYPE html>
<html>
<head>
<title><?= $instructor ?>-<?= $contract_date ?>-<?= substr($course, 0, 30) ?></title>
  <style>
    body {
      font-family: Arial, Serif;
      background-color: #fff;
      color: #222;
      padding: 50px;
      max-width: 800px;
      margin: auto;
    }

    h2 {
      text-align: center;
      color: grey;
    }

    h3 {
      color: #444;
      margin-top: 30px;
    }

    p {
      text-align: justify;
      line-height: 1.6;
    }

    .signature-block {
      margin-top: 40px;
    }

    .signature-block p {
      margin-bottom: 40px;
    }

    .print-button {
      position: fixed;
      top: 10px;
      right: 10px;
      background-color: blue;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .print-button:hover {
      background-color: navy;
    }

    @media print {
      #printButton {
        display: none;
      }
    }
  </style>
</head>
<body>
  <div>
    <img src="../resources/harvest_logo.png" width="300px" alt="HARVEST">
  </div>
  <button class="print-button" id="printButton" onclick="window.print()">Print Contract</button>

 <br>
 <h2>INSTRUCTOR CONTRACT AGREEMENT</h2>

  <p>This Agreement is made and entered into on the <strong><?= $contract_date ?></strong> by and between <strong>HARVEST FOR MANAGEMENT AND TRAINING SERVICES LLC FZ</strong>, Dubai, United Arab Emirates, with its principal office located at DUBAI, UAE, and THE INSTRUCTOR <strong><?= $instructor ?></strong>, located in <strong><?= strtoupper($location) ?></strong>.</p>

  <h3>ARTICLE 1: SUBJECT OF THE CONTRACT</h3>
  <p>The Trainer agrees to provide the training course under the Title: <strong><?= strtoupper($course) ?></strong> in accordance with the curriculum, materials, and schedule specified by the Company.</p>

  <h3>ARTICLE 2: NON-SOLICITATION OF TRAINEES</h3>
  <p>The Trainer agrees not to engage directly with the trainees participating in the training courses organized by the Company for any commercial or personal purposes.</p>

  <h3>ARTICLE 3: TRAINEE EVALUATION</h3>
  <p>The Trainer agrees to conduct evaluations of the trainees both before and after the training course.</p>

  <!--<h3>ARTICLE 4: RATE</h3>-->
  <!--<p>The parties agree that the Trainer’s fee will be <strong>$<?//= $rate ?></strong> per day of training. The end date of training will be on <strong><?//= $end_date ?></strong>.</p>-->
  <h3>ARTICLE 4: RATE</h3>
  <p>The parties agree that the Trainer’s fee will be <strong>$<?= $rate ?></strong> for the full of training duration. The end date of training will be on <strong><?= $end_date ?></strong>.</p>

  <h3>ARTICLE 5: PAYMENT TERMS</h3>
  <p>Payments will be made promptly upon availability of cash and upon the customer's commitment to pay.</p>

  <h3>ARTICLE 6: INTELLECTUAL PROPERTY RIGHTS</h3>
  <p>If the Company provides more than one training session, all training materials... will become the property of the Company.</p>

  <h3>ARTICLE 7: BRANDING</h3>
  <p>The Trainer agrees to prepare all scientific and training materials in accordance with the Company’s branding identity.</p>

  <h3>ARTICLE 8: CONFIDENTIALITY</h3>
  <p>The Trainer agrees to maintain the confidentiality of all information obtained from the Company.</p>

  <h3>ARTICLE 9: COMPLIANCE WITH LAWS</h3>
  <p>The Trainer agrees to comply with all applicable laws and regulations.</p>

  <h3>ARTICLE 10: INSURANCE</h3>
  <p>The Trainer agrees to maintain adequate insurance coverage for any liabilities that may arise.</p>

  <h3>ARTICLE 11: INDEMNIFICATION</h3>
  <p>The Trainer agrees to indemnify and hold harmless the Company from any and all claims.</p>

  <h3>ARTICLE 12: INDEPENDENT CONTRACTOR</h3>
  <p>The Trainer acknowledges that they are an independent contractor and not an employee of the Company.</p>

  <h3>ARTICLE 13: TERMINATION</h3>
  <p>This contract begins on the date of signing and continues until the completion of the training course.</p>

  <h3>ARTICLE 14: GOVERNING LAW</h3>
  <p>This contract shall be governed by and construed in accordance with the laws of United Arab Emirates.</p>

  <h3>ARTICLE 15: DISPUTE RESOLUTION</h3>
  <p>In the event of any dispute arising out of or in connection with this contract, the parties agree to resolve the dispute through informal negotiations and mediation before legal action.</p>

  <h3>ARTICLE 16: ENTIRE AGREEMENT</h3>
  <p>This contract constitutes the entire agreement between the parties and supersedes all prior agreements and understandings.</p>

  <h3>ARTICLE 17: AMENDMENTS</h3>
  <p>This contract may only be amended or modified by a written agreement signed by both parties.</p>

  <!--<div class="signature-block">-->
  <!--       <p><strong>INSTRUCTOR</strong><br>-->
  <!--  Name: <?//= strtoupper($instructor) ?><br>-->
  <!--  <div style="display: inline-flex; align-items: top;">Signature: <?//= $signatureImgTag ?></div>-->
  <!--  <br><br>-->
  <!--  Date: <?//= $contract_date ?></p>-->
  <!--  <hr>-->
  <!--  <p><strong>HARVEST</strong><br>-->
  <!--  Name: OSAMA ELFEITORI<br>-->
  <!--  Title: CEO<br>-->
  <!--  <div style="display: inline-flex; align-items: top;">Signature: <img src="osama signature_black.png" width="150px"></div><br>-->
  <!--  Date: <?//= $contract_date ?></p>-->
  <!--</div>-->
  
  <hr>
  <div class="signature-block">
  <!-- Instructor block (top, full width) -->
  <div class="instructor-block">
    <p>
      <strong>INSTRUCTOR</strong><br>
      Name: <?= strtoupper($instructor) ?><br>
      <div style="display: inline-flex; align-items: top;">
        Signature: <?= $signatureImgTag ?>
      </div>
      <br><br>
      Date: <?= $contract_date ?>
    </p>
    <hr>
  </div>

  <!-- Training Manager & CEO blocks (same row) -->
  <div class="manager-ceo-row" style="display: flex; justify-content: space-between; gap: 40px;">
    <!-- Training Manager block -->
    <div class="training-manager" style="flex: 1;">
      <p>
        <strong>TRAINING MANAGER</strong><br>
        Name: AMMAR ABOSHANAB<br>
        <div style="display: inline-flex; align-items: top;">
          Signature: <!--<img src="training_manager_signature.png" width="150px">-->
        </div>
        <br><br>
        <!--Date: <?//= $contract_date ?>-->
      </p>
    </div>

    <!-- CEO block -->
    <div class="ceo" style="flex: 1; text-align: left;">
      <p>
        <strong>CEO, HARVEST</strong><br>
        <div style="display: inline-flex; align-items: top;">
          Signature:  <!--<img src="osama signature_black.png" width="150px">-->
        </div>
        <br><br><br><br>
        Date: <?= $contract_date ?>
      </p>
    </div>
  </div>
</div>

  <hr>
  
  <!--<?php //if (!empty($bankDetails)): ?>-->
  <!--<h3>BANK ACCOUNT DETAILS</h3>-->
  <!--<p style="color: blue;">The following bank account details were provided by the Instructor for payment processing:</p>-->
  <!--<div style="background:#f5f5f5; border:1px solid #ccc; padding:15px; white-space:pre-wrap; font-family: Arial, Serif;">-->
    <?//= $bankDetails ?>
  <!--</div>-->
<?php //endif; ?>

  <!--<h3>BANK ACCOUNT DETAILS</h3>-->
  <!--<p style="color: blue;">The following bank account details were provided by the Instructor for payment processing:</p>-->
  <!--<div style="background:#f5f5f5; border:1px solid #ccc; padding:15px; white-space:pre-wrap; font-family: Arial, Serif;">-->
  <!--  <?//= $bankDetails ?>-->
  <!--</div>-->

</body>
</html>

<?php
// Save the generated HTML to a file
$htmlContent = ob_get_clean();
file_put_contents($contractFilePath, $htmlContent);

// Optionally display the contract after saving
echo $htmlContent;
?>
