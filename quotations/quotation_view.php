<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include '../resources/db_config.php';

if (!isset($_GET['quot_id'])) {
    echo "No quotation ID provided.";
    exit();
}

$quot_id = intval($_GET['quot_id']);

$sql = "SELECT q.*, o.ord_subject, c.cust_name, c.cust_code, 
               cs.course_title, cs.course_title_a, cs.course_duration, cs.course_uod, cs.week, 
               cat.cat_name, cat.cat_code, v.ven_name
        FROM quotations q
        JOIN orders o ON q.ord_id = o.ord_id
        JOIN customers c ON q.cust_id = c.cust_id
        JOIN courses cs ON q.course_id = cs.course_id
        JOIN categories cat ON q.cat_id = cat.cat_id
        JOIN venues v ON q.ven_id = v.ven_id
        WHERE q.quot_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $quot_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Quotation not found.";
    exit();
}

$data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Training Quotation</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400;500;600;700&family=Roboto:wght@300;400;500&family=Tajawal:wght@200;300;400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../resources/fa/css/all.min.css" rel="stylesheet">

  <style>
* { box-sizing: border-box; }
html, body {
  margin: 0;
  padding: 0;
  min-height: 100vh;
  font-family: Arial, sans-serif;
}
main { flex: 1; }

/* Footer */
.footer {
  text-align: center;
  width: 100%;
  font-size: 12px;
  color: #444;
  border-top: 1px solid #BBB;
  padding: 10px 0;
  background: #eee;
}
@media print {
  /* Cover page: full A4, no margins */
  @page:first {
    margin: 0;
  }
  .cover {
    width: 210mm;
    height: 297mm;
    margin: 0;
    padding: 0;
    background: url("avenuebuilding.jpg") no-repeat center center;
    background-size: cover;
    page-break-after: always;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
  }
  /* Other pages use margins */
  @page {
    margin: 15mm 10mm 15mm 10mm;
  }

  main {
    padding-bottom: 35mm; /* reserve space for footer */
  }
}

/* Screen version of cover */
.cover {
  width: 100%;
  height: 100vh;
  /*background: url("avenuebuilding.jpg") no-repeat center center;*/
    background: url("training-background.jpg") no-repeat center center;
  background-size: cover;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  position: relative;
  page-break-after: always;
}

/* Pages */
.page { margin: 20mm; }
.page-finance { margin: 10mm; }

.section-title {
  font-weight: bold;
  border-bottom: 1px solid #ccc;
  margin-bottom: 10px;
  padding-bottom: 15px;
  padding-top: 30px;
  font-size: 1.2rem;
  text-transform: uppercase;
  color: #222;
}

.title {
  font-weight: bold;
  margin-bottom: 10px;
  padding-bottom: 15px;
    padding-top: 20px;
  font-size: 1rem;
  text-transform: uppercase;
  color: #CC0A0B;
}

.section { margin: 20px 0; }

.arabic-title {
  font-family: 'Tajawal', sans-serif;
  font-size: 1.2rem;
  font-weight: bold;
  direction: rtl;
}
.english-title {
  font-family: 'Tajawal', sans-serif;
  font-size: 1.5rem;
  color: #CC0A0B;
  font-weight: bold;
  text-transform: uppercase;
}
.arabic-title-small {
  font-family: 'Tajawal', sans-serif;
  direction: rtl;
  font-size: 0.9rem;
  font-weight: bold;
}
.english-title-small {
  font-family: 'Tajawal', sans-serif;
  font-size: 0.9rem;
  font-weight: bold;
  text-transform: uppercase;
}

/* Datasheet */
.custom-datasheet {
  width: 97%;
  border-collapse: collapse;
  margin-top: 15px;
  font-size: 15px;
}
.custom-datasheet th, .custom-datasheet td {
  padding: 5px 10px;
  text-align: left;
}
.custom-datasheet th {
  width: 28%;
  font-weight: bold;
}
.custom-datasheet td {
  width: 72%;
}
.custom-datasheet tr:not(:last-child) td { border-bottom: 1px solid #ddd; }
.custom-datasheet tr:nth-child(odd) { background-color: #EEE; }
.custom-datasheet tr:nth-child(even) { background-color: #FFF; }
.custom-datasheet th + td { border-left: 1px solid #ccc; }

.tight-text {
    font-family: 'Tajawal', sans-serif;
    font-size: 1.1rem; 
  padding: 5px 30px;
  line-height: 1.7;
 
  white-space: pre-wrap;
}

/* Finance table */
.financial-table {
  width: 97%;
  border-collapse: collapse;
  margin-top: 15px;
  font-size: 1rem;
}
.financial-table th,
.financial-table td {
  padding: 10px 15px;
  text-align: center;
  border-top: 1px solid #ccc;
  border-bottom: 1px solid #ccc;
}
.financial-table th {
  background-color: #CC0A0B;
  color: #fff;
}
.financial-table tbody tr:nth-child(odd) { background-color: #f2f2f2; }
.financial-table tbody tr:nth-child(even) { background-color: #e6e6e6; }
.financial-table .bottom-row td {
  background-color: #CCC;
  height: 8px;
  border: none;
}
  </style>
</head>
<body>
<main>
  <!-- Cover -->
  <section class="cover">
    <img src="../resources/logo.png" alt="Logo" 
         style="position:absolute;top:15px;left:15px;height:120px; background:rgba(255,255,255,0.5); padding: 10px; border-radius: 5px">

    <div style="text-align:center; background:rgba(255,255,255,0.5); padding: 15px; border-radius: 8px;">
      <div style="color:#555;font-size:1rem;">COURSE TITLE</div>
      <div class="arabic-title"><?= htmlspecialchars($data['course_title_a']) ?></div>
      <div class="english-title"><?= strtoupper(htmlspecialchars($data['course_title'])) ?></div>
    </div>

    <div style="position:absolute;bottom:80px;left:40px;text-align:left;color:#000; background:rgba(255,255,255,0.5); padding: 15px; border-radius: 8px">
      <p><strong>TRAINING PROPOSAL</strong></p>
      <p><strong>Ref#:</strong> <?= htmlspecialchars($data['quot_ref']) ?></p>
      <p><strong>Date:</strong> <?= htmlspecialchars($data['quot_date']) ?></p>
      <p><strong>For:</strong> <?= htmlspecialchars($data['cust_name']) ?></p>
    </div>
  </section>

  <!-- Datasheet -->
  <section class="page">
    <img src="../resources/logo.png" alt="Logo" style="height:100px;">
    <br><br>
    <div class="section-title">Program Datasheet</div>
    <table class="custom-datasheet">
      <tr><th>Quotation Ref#</th><td><?= htmlspecialchars($data['quot_ref']) ?></td></tr>
      <tr><th>Quotation Issue date</th><td><?= htmlspecialchars($data['quot_date']) ?></td></tr>
      <tr>
        <th>Program Title</th>
        <td>
          <div class="arabic-title-small"><?= htmlspecialchars($data['course_title_a']) ?></div>
          <div class="english-title-small"><?= htmlspecialchars($data['course_title']) ?></div>
        </td>
      </tr>
      <tr><th>Company</th><td><?= htmlspecialchars($data['cust_name']) ?></td></tr>
      <tr>
        <th>Duration</th>
        <td><?= htmlspecialchars($data['duration'] . " " . $data['course_uod'] . " - " . $data['week'] . " WEEK(s)") ?></td>
      </tr>
      <!--<tr><th>Start Date</th><td><?//= htmlspecialchars($data['quot_date']) ?></td></tr>-->
         <tr><th>Start Date</th><td>TBA</td></tr>
      <tr><th>Cost Per Candidate</th><td><?= number_format($data['cost']) ?> USD</td></tr>
      <tr><th>Number of Candidates</th><td><?= htmlspecialchars($data['trainees']) ?></td></tr>
      <tr><th>Payment Terms</th><td>On Confirmation</td></tr>
      <tr><th>Proposal Validity</th><td>60 Days</td></tr>
      <tr><th>Venue</th><td><?= htmlspecialchars($data['ven_name']) ?></td></tr>
      <tr><th>Certification</th><td>Avenue International Certificate, PEARSON BTEC APPROVED CENTER</td></tr>
      <tr><th>Email</th><td>info@avenueinternational.net</td></tr>
       <tr><th>Contact - Telephone</th><td>+90 (212) 246 2080</td></tr>
       <tr><th>Contact - Mobile</th><td>+90 (534) 921 6965 </td></tr>
    </table>
  </section>

  <!-- Overview -->
  <section class="page" style="page-break-before:always;">
    <img src="../resources/logo.png" alt="Logo" style="height:100px;">
    <br>
    <div class="section-title">COURSE OVERVIEW</div>
    <div class="title">Introduction</div>
    <pre class="tight-text"><?= htmlspecialchars(trim($data['introduction'])) ?></pre>
    <div class="title">Objectives</div>
    <pre class="tight-text"><?= htmlspecialchars(trim($data['objectives'])) ?></pre>
    <div class="title">Target Audience</div>
    <pre class="tight-text"><?= htmlspecialchars(trim($data['audiences'])) ?></pre>
    <div class="title">Course Outlines</div>
    <pre class="tight-text"><?= htmlspecialchars(trim($data['outlines'])) ?></pre>
  </section>

  <!-- Finance -- START IN A NEW PAGE -->
<section class="page-finance" style="page-break-before: always;">
  <img src="../resources/logo.png" alt="Logo" style="height:100px;">
  <p><strong>Date:</strong> <?= htmlspecialchars($data['quot_date']) ?></p>
  <div class="section-title">FINANCIAL PROPOSAL</div>
  <table class="financial-table">
    <thead>
      <tr>
        <th>TRAINING COURSE TITLE</th>
        <th>DURATION</th>
        <th>NO. OF CANDIDATES</th>
        <th>COST PER TRAINEE</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
    <div class="arabic-title"><?= htmlspecialchars($data['course_title_a']) ?></div>
    <div><?= htmlspecialchars($data['course_title']) ?></div>
</td>
        <td><?= htmlspecialchars($data['duration'] . " " . $data['course_uod'] . " - " . $data['week'] . " WEEK(s)") ?></td>
        <td><?= htmlspecialchars($data['trainees']) ?></td>
        <td>US$<?= number_format($data['cost']) ?></td>
      </tr>
      <tr class="bottom-row"><td colspan="4"></td></tr>
    </tbody>
  </table>
  <p><strong>This proposal includes:</strong></p>
  <ul>
    <li>Complete training course delivery</li>
    <li>Course Material</li>
    <li>Certificate of Attendance</li>
    <li>Coffee break</li>
    <li>Transportation from/to Airport</li>
    <!--<li>Residency cost and procedure</li>-->
  </ul>
  <p style="font-style:italic;">
    <!--<strong>Dr Osama Younis<br>CEO, Avenue International, Istanbul</strong>-->
    <strong>Avenue International, Istanbul</strong>
  </p>
</section>

  </main>
<div class="footer">
  Cumhuriyet Mahallasi., 10 Ergenekon Caddasi., Ahmetbey Plaza k4, Şişli 34360 Istanbul, Turkey<br>
  📞 +90 (212) 246 2080 | ✉️ info@avenueinternational.net | 🌐 www.avenueinternational.net
</div>
</body>
</html>
