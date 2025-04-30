<?php
$db = new mysqli('localhost', 'root', '', 'laundry_db');

if ($db->connect_error) {
    die("Database error: Import database.sql first!");
}

function getMachines() {
    global $db;
    return $db->query("SELECT * FROM machines");
}

function getMachine($id) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM machines WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function startMachine($id, $minutes) {
    global $db;
    $end_time = date('Y-m-d H:i:s', strtotime("+$minutes minutes"));
    $stmt = $db->prepare("UPDATE machines SET status='in_use', timer_end=?, duration=?, remaining_minutes=NULL WHERE id=?");
    $stmt->bind_param("sii", $end_time, $minutes, $id);
    return $stmt->execute();
}

// ✅ FIXED pauseMachine to accept remaining time
function pauseMachine($id, $remaining) {
    global $db;
    $stmt = $db->prepare("UPDATE machines SET status='paused', remaining_minutes=?, timer_end=NULL WHERE id=?");
    $stmt->bind_param("ii", $remaining, $id);
    return $stmt->execute();
}

function resumeMachine($id) {
    global $db;
    $machine = getMachine($id);

    if ($machine['status'] == 'paused' && isset($machine['remaining_minutes']) && $machine['remaining_minutes'] > 0) {
        $end_time = date('Y-m-d H:i:s', strtotime("+{$machine['remaining_minutes']} minutes"));
        $duration = !empty($machine['duration']) ? $machine['duration'] : $machine['remaining_minutes'];
        $stmt = $db->prepare("UPDATE machines SET status='in_use', timer_end=?, remaining_minutes=NULL, duration=? WHERE id=?");
        $stmt->bind_param("sii", $end_time, $duration, $id);
        return $stmt->execute();
    }

    return false;
}

function cancelMachine($id) {
    global $db;
    $stmt = $db->prepare("UPDATE machines SET status='available', timer_end=NULL, remaining_minutes=NULL, duration=NULL WHERE id=?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function collectMachine($id) {
    global $db;
    $stmt = $db->prepare("UPDATE machines SET status='available', timer_end=NULL, remaining_minutes=NULL, duration=NULL WHERE id=?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
