<?php
include 'functions.php';
$machine_id = intval($_POST['id']);

if (resumeMachine($machine_id)) {
    header("Location: control_panel.php");
} else {
    // Handle error case - maybe the remaining time was invalid
    header("Location: control_panel.php?error=resume_failed");
}
exit;
?>