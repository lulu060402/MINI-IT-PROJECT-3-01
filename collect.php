<?php
include 'functions.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$machine_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$hb = isset($_POST['hb']) ? intval($_POST['hb']) : 1;

if ($machine_id > 0) {
    $machine = getMachine($machine_id);
    
    if ($machine['status'] == 'in_use') {
        $end_time = strtotime($machine['timer_end']);
        $current_time = time();
        
        $late_minutes = max(0, ($current_time - $end_time) / 60);
        
        $penalty = 0;
        if ($late_minutes > 0) {

            $penalty = 10;
            
            if ($late_minutes > 5) {
                $extra_minutes = $late_minutes - 5;
                $penalty_blocks = ceil($extra_minutes / 10);
                $penalty += $penalty_blocks * 20;
            }
        }
        

        $points_to_add = max(10, 100 - $penalty);
        
        if (isset($_SESSION['user_id'])) {
            addUserPoints($_SESSION['user_id'], $points_to_add);
        }
    }
    
    collectMachine($machine_id);
}

header("Location: control_panel.php?hb=$hb");
exit;
?>
