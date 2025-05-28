<?php
include 'functions.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$machine_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$hb = isset($_POST['hb']) ? intval($_POST['hb']) : 1;

if ($machine_id > 0) {
    collectMachine($machine_id);
    
    $points_to_add = 10; 
    if (isset($_SESSION['user_id'])) {
        addUserPoints($_SESSION['user_id'], $points_to_add);
    }

}

header("Location: control_panel.php?hb=$hb");
exit;
?>