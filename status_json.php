<?php
include 'functions.php';
header('Content-Type: application/json');

// gget requested hostel block
$current_hb = isset($_GET['hb']) ? intval($_GET['hb']) : 1;
$machines = getMachines($current_hb);

$data = [];
foreach ($machines as $machine) {
    $item = [
        'id' => $machine['id'],
        'name' => $machine['name'],
        'status' => $machine['display_status'],
        'mins_left' => 0
    ];
    
    // to calculate time remaining
    switch($machine['display_status']) {
        case 'in_use':
            $item['mins_left'] = max(0, round((strtotime($machine['timer_end']) - time()) / 60));
            break;
            
        case 'paused':
            $item['mins_left'] = round($machine['remaining_time'] / 60);
            break;
    }
    
    $data[] = $item;
}

echo json_encode($data);
?>