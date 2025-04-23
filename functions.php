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
    return $db->query("SELECT * FROM machines WHERE id=" . intval($id))->fetch_assoc();
}

function startMachine($id, $minutes) {
    global $db;
    $end_time = date('Y-m-d H:i:s', strtotime("+$minutes minutes"));
    $db->query("UPDATE machines SET status='in_use', timer_end='$end_time', duration=$minutes WHERE id=" . intval($id));
}
// collectt button to reset
function collectMachine($id) {
    global $db;
    $db->query("UPDATE machines SET status='available', timer_end=NULL WHERE id=" . intval($id));
}
?>