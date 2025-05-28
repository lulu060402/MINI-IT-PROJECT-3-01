<?php
$db = new mysqli('localhost', 'root', '', 'server_db');

if ($db->connect_error) {
    die("Database error: " . $db->connect_error);
}

function getMachines($hostel_block = 1) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM machines WHERE hostel_block = ?");
    $stmt->bind_param('i', $hostel_block);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $machines = [];
    while ($machine = $result->fetch_assoc()) {
        // calculate display status
        if ($machine['status'] == 'in_use') {
            if (strtotime($machine['timer_end']) <= time()) {
                $machine['display_status'] = 'ready_to_collect';
            } else {
                $machine['display_status'] = 'in_use';
            }
        } else {
            $machine['display_status'] = $machine['status'];
        }
        $machines[] = $machine;
    }
    return $machines;
}

function getMachine($id) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM machines WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $machine = $result->fetch_assoc();

    // calculate display status
    if ($machine['status'] == 'in_use') {
        if (strtotime($machine['timer_end']) <= time()) {
            $machine['display_status'] = 'ready_to_collect';
        } else {
            $machine['display_status'] = 'in_use';
        }
    } else {
        $machine['display_status'] = $machine['status'];
    }
    
    // ensure hostel_block exists
    $machine['hostel_block'] = $machine['hostel_block'] ?? 1;
    
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
    
    $stmt = $db->prepare("UPDATE machines SET 
                        status='available', 
                        timer_end=NULL, 
                        paused_time=NULL, 
                        remaining_time=NULL 
                        WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    
    // return true if successful
    return ($stmt->affected_rows > 0);
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
    
    try {
        // get machine data using prepared statement
        $stmt = $db->prepare("SELECT * FROM machines WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $machine = $stmt->get_result()->fetch_assoc();
        
        if ($machine['status'] == 'paused' && $machine['remaining_time'] > 0) {
            // calculate new end time using remaining seconds
            $end_time = date('Y-m-d H:i:s', time() + $machine['remaining_time']);
            
            $update = $db->prepare("UPDATE machines SET 
                                  status = 'in_use',
                                  timer_end = ?,
                                  paused_time = NULL,
                                  remaining_time = NULL
                                  WHERE id = ?");
            $update->bind_param('si', $end_time, $id);
            $update->execute();
            
            return ($update->affected_rows > 0);
        }
        return false;
    } catch (Exception $e) {
        error_log("Resume failed: " . $e->getMessage());
        return false;
    }
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

function addUserPoints($user_id, $points) {
    global $db;
    $stmt = $db->prepare("UPDATE users SET points = points + ? WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param('ii', $points, $user_id);
        $stmt->execute();
        $stmt->close();
    } else {
        error_log("Failed to prepare statement in addUserPoints: " . $db->error);
    }
}

function getCurrentPoints() {
    global $db;
    if (isset($_SESSION['user_id'])) {
        $stmt = $db->prepare("SELECT points FROM users WHERE user_id = ?");
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['points'] : 0;
    }
    return "Not logged in";
}
?>