<?php
include 'functions.php';
$machine_id = intval($_POST['id']);
cancelMachine($machine_id);
header("Location: control_panel.php");
exit;
?>