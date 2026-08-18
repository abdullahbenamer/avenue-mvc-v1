<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}
include 'resources/db_config.php';
include 'resources/header.php';
?>
<br>
<div class="about" style="font-family: Tajawal, Verdana, Geneva, Tahoma, sans-serif; font-size: 1rem;">
    <h1><b style="color:#C70607;">WELCOME <i class="fa-solid fa-handshake"></i></b></h1>
    <p>
        This <b style="color:#C70607;"><i class="fa-solid fa-star"></i> NEWER VERSION</b> of our application offers a fully integrated workflow tailored for professional training centers. It supports the generation and tracking of various key documents, including 
        <b style="color:#C70607;"><i class="fa-solid fa-file-contract"></i> Orders</b>, 
        <b style="color:#C70607;"><i class="fa-solid fa-file-signature"></i> Quotations</b>, 
        <b style="color:#C70607;"><i class="fa-solid fa-clipboard-list"></i> Attendance Records</b>, 
        <b style="color:#C70607;"><i class="fa-solid fa-award"></i> Certificates</b>, and 
        <b style="color:#C70607;"><i class="fa-solid fa-file-invoice-dollar"></i> Invoices</b>.
        Added recently, <b style="color:#C70607;"><i class="fa-solid fa-user-pen"></i> User Profile</b> Editing.
    </p>
    <br>
    <p>
        The system also provides complete management of <b style="color:#C70607;"><i class="fa-solid fa-chalkboard-teacher"></i> Instructors</b> — covering 
        <b style="color:#C70607;"><i class="fa-solid fa-handshake"></i> Contracts</b>, 
        <b style="color:#C70607;"><i class="fa-solid fa-coins"></i> Financial Accounts (Dues)</b>, 
        <b style="color:#C70607;"><i class="fa-solid fa-id-card"></i> Personal</b> and 
        <b style="color:#C70607;"><i class="fa-solid fa-briefcase"></i> Professional Profile</b>. 
        <br>An advanced <b style="color:#C70607;"><i class="fa-solid fa-search"></i> Search Mechanism</b> is included to help identify instructors based on 
        <b style="color:#C70607;"><i class="fa-solid fa-key"></i> Keywords</b>, 
        <b style="color:#C70607;"><i class="fa-solid fa-lightbulb"></i> Interests</b>, and 
        <b style="color:#C70607;"><i class="fa-solid fa-map-marker-alt"></i> Areas of Expertise</b>.
        <b style="color:#C70607;"><i class="fa-solid fa-user-edit"></i> Instructor</b> can access and edit profile</b>
    </p>
    <br>
    <p>For assistance, contact us at  <br><i class="fa-solid fa-mobile"></i> +90 (545) 508 6099  - <i class="fa-solid fa-mobile"></i> +90 545 908 16 79 or <br>
        <i class="fa-solid fa-envelope"></i> <a href="mailto:contact@avenueinternational.net">cto@avenueinternational.net</a>.
    </p>
</div>

</div>



<br>

<?php
include 'resources/footer.php';
?>