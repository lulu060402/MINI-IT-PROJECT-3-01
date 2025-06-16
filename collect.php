<?php
include 'functions.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$machine_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$current_hb = isset($_POST['hb']) ? intval($_POST['hb']) : (isset($_GET['hb']) ? intval($_GET['hb']) : 1);
$redirect_page = isset($_POST['redirect']) ? $_POST['redirect'] : 'control_panel';

if ($machine_id > 0) {
    $machine = getMachine($machine_id);
    
    if ($machine['status'] == 'in_use') {
        $end_time = strtotime($machine['timer_end']);
        $current_time = time();
        
        $late_minutes = max(0, ($current_time - $end_time) / 60);
        
        $penalty = 0;
        if ($late_minute > 0) {
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
    
    // Handle photo upload
    if (isset($_FILES['collection_photo']) && $_FILES['collection_photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/collection_photos/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename
        $file_ext = pathinfo($_FILES['collection_photo']['name'], PATHINFO_EXTENSION);
        $file_name = 'collection_' . $machine_id . '_' . time() . '.' . $file_ext;
        $target_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['collection_photo']['tmp_name'], $target_path)) {
            // Save photo path to database
            $db->query("UPDATE machines SET collection_photo = '$target_path' WHERE id = $machine_id");
            
            // Create a report entry for the collection
            $report_data = [
                'machine_id' => $machine_id,
                'problem_type' => 'Collection Proof',
                'description' => 'Collection photo uploaded for machine ' . $machine['name'],
                'urgency' => 'low',
                'status' => 'resolved',
                'screenshot_path' => $target_path
            ];
            
            // Call the createReport function
            createReport($report_data);
        }
    }
    
    collectMachine($machine_id);
}

// Determine redirect destination
$redirect_url = match($redirect_page) {
    'control' => "control.php?id=$machine_id&hb=$current_hb",
    default => "control_panel.php?id=$machine_id&hb=$current_hb"
};

header("Location: $redirect_url");
exit;
?>