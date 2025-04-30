<?php
$db = new mysqli('localhost', 'root', '', 'laundry_db');

if ($db->connect_error) {
    die("Database error: " . $db->connect_error);
}

function getMachines() {
    global $db;
    $result = $db->query("SELECT * FROM machines");
    $machines = [];
    while ($machine = $result->fetch_assoc()) {
        // Calculate display status for each machine
        if ($machine['status'] == 'in_use' && strtotime($machine['timer_end']) <= time()) {
            $machine['display_status'] = 'ready_to_collect';
        } elseif ($machine['status'] == 'in_use') {
            $machine['display_status'] = 'in_use';
        } else {
            $machine['display_status'] = $machine['status'];
        }
        $machines[] = $machine;
    }
    return $machines;
}

function getMachine($id) {
    global $db;
    $result = $db->query("SELECT * FROM machines WHERE id=" . intval($id));
    $machine = $result->fetch_assoc();
    
    // Calculate display status
    if ($machine['status'] == 'in_use' && strtotime($machine['timer_end']) <= time()) {
        $machine['display_status'] = 'ready_to_collect';
    } elseif ($machine['status'] == 'in_use') {
        $machine['display_status'] = 'in_use';
    } else {
        $machine['display_status'] = $machine['status'];
    }
    
    return $machine;
}

function startMachine($id, $minutes) {
    global $db;
    $end_time = date('Y-m-d H:i:s', strtotime("+$minutes minutes"));
    $db->query("UPDATE machines SET 
               status='in_use', 
               timer_end='$end_time', 
               duration=$minutes,
               paused_time=NULL, 
               remaining_time=NULL 
               WHERE id=" . intval($id));
}

function collectMachine($id) {
    global $db;
    $db->query("UPDATE machines SET 
               status='available', 
               timer_end=NULL, 
               paused_time=NULL, 
               remaining_time=NULL 
               WHERE id=" . intval($id));
}

function pauseMachine($id) {
    global $db;
    $machine = $db->query("SELECT * FROM machines WHERE id=" . intval($id))->fetch_assoc();
    
    if ($machine['status'] == 'in_use') {
        $remaining = strtotime($machine['timer_end']) - time();
        if ($remaining > 0) {
            $db->query("UPDATE machines SET 
                       status='paused', 
                       paused_time=NOW(), 
                       remaining_time=$remaining, 
                       timer_end=NULL 
                       WHERE id=" . intval($id));
            return true;
        }
    }
    return false;
}

function resumeMachine($id) {
    global $db;
    $machine = $db->query("SELECT * FROM machines WHERE id=" . intval($id))->fetch_assoc();
    
    if ($machine['status'] == 'paused' && $machine['remaining_time'] > 0) {
        $end_time = date('Y-m-d H:i:s', time() + $machine['remaining_time']);
        $db->query("UPDATE machines SET 
                   status='in_use', 
                   timer_end='$end_time', 
                   paused_time=NULL, 
                   remaining_time=NULL 
                   WHERE id=" . intval($id));
        return true;
    }
    return false;
}

function cancelMachine($id) {
    global $db;
    $db->query("UPDATE machines SET 
               status='available', 
               timer_end=NULL, 
               paused_time=NULL, 
               remaining_time=NULL 
               WHERE id=" . intval($id));
}
?>