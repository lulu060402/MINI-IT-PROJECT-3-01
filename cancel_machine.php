<?php
include 'functions.php';

$machine_id = intval($_POST['id']);
$hb = intval($_POST['hb']);

cancelMachine($machine_id);
header("Location: control_panel.php?hb=$hb");
exit;
?> 