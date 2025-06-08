<?php
include 'functions.php';

$machine_id = intval($_POST['id']);
$hb = intval($_POST['hb']);

if (resumeMachine($machine_id)) {
    header("Location: control_panel.php?hb=$hb");
} else {
    header("Location: control_panel.php?hb=$hb&error=resume_failed");
}
exit;
?>