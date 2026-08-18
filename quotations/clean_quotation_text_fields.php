<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../resources/db_config.php'; 

if (!isset($_GET['quot_id'])) {
    exit("❌ No quotation ID provided.");
}

$quot_id = intval($_GET['quot_id']);

// Step 1: Fetch the quotation
$sql = "SELECT quot_id, introduction, objectives, audiences, outlines FROM quotations WHERE quot_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $quot_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    exit("❌ Quotation not found.");
}

$row = $result->fetch_assoc();
$fields = ['introduction', 'objectives', 'audiences', 'outlines'];
$updates = [];

foreach ($fields as $field) {
    $original = $row[$field];

    // Step 2: PHP-side deep cleaning
    $cleaned = stripslashes($original); // Remove slashes
    $cleaned = str_replace(['\\r\\n', '\r\n', '\r', '\n'], "\n", $cleaned); // normalize breaks
    $cleaned = str_replace(['\\\\r\\\\n', '\\\\n', '\\\\r', '\\\\'], "\n", $cleaned); // deeply escaped
    $cleaned = preg_replace('/\\\\+/', '', $cleaned); // final slash sweep
    $cleaned = preg_replace("/(\r\n|\n|\r){2,}/", "\n", $cleaned); // normalize excessive breaks

    $updates[$field] = $conn->real_escape_string($cleaned);
}

// Step 3: PHP-level update
$update_sql = "
    UPDATE quotations SET 
        introduction = '{$updates['introduction']}',
        objectives   = '{$updates['objectives']}',
        audiences    = '{$updates['audiences']}',
        outlines     = '{$updates['outlines']}'
    WHERE quot_id = $quot_id
";

$php_clean_success = $conn->query($update_sql);

// Step 4: Optional additional SQL-level bulk sanitize for deeper escaping
$extra_sql = "
UPDATE quotations 
SET 
  introduction = REPLACE(REPLACE(REPLACE(REPLACE(introduction, '\\\\r\\\\n', '\n'), '\\\\n', '\n'), '\\\\r', '\n'), '\\\\', ''),
  objectives   = REPLACE(REPLACE(REPLACE(REPLACE(objectives,   '\\\\r\\\\n', '\n'), '\\\\n', '\n'), '\\\\r', '\n'), '\\\\', ''),
  audiences    = REPLACE(REPLACE(REPLACE(REPLACE(audiences,    '\\\\r\\\\n', '\n'), '\\\\n', '\n'), '\\\\r', '\n'), '\\\\', ''),
  outlines     = REPLACE(REPLACE(REPLACE(REPLACE(outlines,     '\\\\r\\\\n', '\n'), '\\\\n', '\n'), '\\\\r', '\n'), '\\\\', '')
WHERE quot_id = $quot_id
";

$sql_clean_success = $conn->query($extra_sql);

// Final status
if ($php_clean_success && $sql_clean_success) {
    echo "✅ Quotation ID $quot_id cleaned successfully (PHP + SQL level).";
} elseif ($php_clean_success) {
    echo "⚠️ Quotation ID $quot_id cleaned (PHP level), but SQL-level fallback failed.";
} elseif ($sql_clean_success) {
    echo "⚠️ PHP cleanup failed, but SQL-level fallback succeeded.";
} else {
    echo "❌ Error cleaning quotation ID $quot_id.";
}

$conn->close();
?>
