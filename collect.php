<?php
include 'functions.php';
$machine_id = intval($_GET['id']);
$db->query("UPDATE machines SET status='available', timer_end=NULL WHERE id=$machine_id");
header("Location: dashboard.php");  // Redirect back
?>