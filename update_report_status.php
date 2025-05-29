<?php
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_id = intval($_POST['report_id']);
    $new_status = $_POST['new_status'];
    
    // Validate status
    $allowed_statuses = ['pending', 'in_progress', 'resolved'];
    if (!in_array($new_status, $allowed_statuses)) {
        header("Location: admin_report.php?error=invalid_status");
        exit;
    }

    $stmt = $db->prepare("UPDATE reports SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $new_status, $report_id);
    
    if ($stmt->execute()) {
        header("Location: admin_report.php?success=1");
    } else {
        header("Location: admin_report.php?error=update_failed");
    }
    exit;
}

header("Location: admin_report.php");
exit;
?>