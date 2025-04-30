<?php
include 'functions.php';
$machine_id = intval($_POST['id']);
$machine = getMachine($machine_id);

if ($machine['status'] == 'in_use') {
    $remaining = strtotime($machine['timer_end']) - time();
    if ($remaining > 0) {
        $db->query("UPDATE machines SET 
                   status='paused', 
                   paused_time=NOW(), 
                   remaining_time=$remaining, 
                   timer_end=NULL 
                   WHERE id=" . intval($machine_id));
    }
}
header("Location: control_panel.php");
exit;
?>