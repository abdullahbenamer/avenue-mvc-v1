<?php
session_start();
include '../resources/db_config.php';
//require '../resources/PHPMailer/PHPMailerAutoload.php'; // Adjust path if needed

if (!isset($_SESSION['inst_id'])) {
    header("Location: login_instructor.php");
    exit();
}

$inst_id = $_SESSION['inst_id'];
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (strlen($new) < 6) {
        $message = "New password must be at least 6 characters.";
        $message_type = "danger";
    } elseif ($new !== $confirm) {
        $message = "New password and confirmation do not match.";
        $message_type = "danger";
    } else {
        $stmt = $conn->prepare("SELECT password, email, full_name FROM instructors WHERE inst_id = ?");
        $stmt->bind_param("i", $inst_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (password_verify($current, $result['password'])) {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE instructors SET password = ? WHERE inst_id = ?");
            $update->bind_param("si", $hashed, $inst_id);
            $update->execute();

            // Send confirmation email
            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->Host = 'smtp.example.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'you@example.com';
            $mail->Password = 'your_email_password';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            
            //Notes:
            //Replace smtp.example.com, you@example.com, and your_email_password with your actual SMTP settings.
            //Make sure PHPMailer is included correctly (you can also use Composer).
            //You can adjust the password length rule by changing 6 to another value.
            
            $mail->setFrom('noreply@example.com', 'Training Center');
            $mail->addAddress($result['email'], $result['full_name']);
            $mail->Subject = 'Your password has been changed';
            $mail->Body = "Dear " . $result['full_name'] . ",\n\nYour password was successfully changed.\n\nIf this was not you, please contact the administrator immediately.";

            if ($mail->send()) {
                $message = "Password updated and confirmation email sent.";
                $message_type = "success";
            } else {
                $message = "Password updated, but email failed: " . $mail->ErrorInfo;
                $message_type = "warning";
            }
        } else {
            $message = "Current password is incorrect.";
            $message_type = "danger";
        }
    }
}
?>
<!-- Change Password-->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="col-md-6 mx-auto bg-white p-4 rounded shadow">
    <h4 class="mb-4 text-center">Change Password</h4>

    <form method="post" action="change_password.php" onsubmit="return validateForm()">
      <div class="mb-3">
        <label class="form-label">Current Password</label>
        <input type="password" name="current_password" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">New Password</label>
        <input type="password" name="new_password" id="new_password" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Confirm New Password</label>
        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
      </div>

      <button type="submit" name="update_password" class="btn btn-primary w-100">Update Password</button>
    </form>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-<?= $message_type ?>">
      <div class="modal-header bg-<?= $message_type ?> text-white">
        <h5 class="modal-title" id="feedbackModalLabel">
          <?= ucfirst($message_type) ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?= $message ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-<?= $message_type ?>" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function validateForm() {
  let newPwd = document.getElementById("new_password").value;
  let confirmPwd = document.getElementById("confirm_password").value;

  if (newPwd.length < 6) {
    showModal("Error", "New password must be at least 6 characters.");
    return false;
  }

  if (newPwd !== confirmPwd) {
    showModal("Error", "New password and confirmation do not match.");
    return false;
  }

  return true;
}

function showModal(title, body) {
  document.getElementById('feedbackModalLabel').textContent = title;
  document.querySelector('#feedbackModal .modal-body').textContent = body;
  new bootstrap.Modal(document.getElementById('feedbackModal')).show();
}

<?php if (!empty($message)): ?>
  window.addEventListener("DOMContentLoaded", () => {
    new bootstrap.Modal(document.getElementById('feedbackModal')).show();
  });
<?php endif; ?>
</script>
</body>
</html>
