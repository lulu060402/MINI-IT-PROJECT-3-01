<?php
include 'functions.php';

$machine_id = intval($_POST['id']);
$hb = intval($_POST['hb']);
$redirect_page = isset($_POST['redirect']) ? $_POST['redirect'] : 'control_panel';

cancelMachine($machine_id);

// Determine redirect destination
$redirect_url = match($redirect_page) {
    'control' => "control.php?id=$machine_id&hb=$hb",
    default => "control_panel.php?hb=$hb"
};

header("Location: $redirect_url");
exit;
?>