<?php include 'functions.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Laundry Machine Status</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Machine Status</h1>
    
    <div class="back-container">
        <a href="hostel_map.html" class="back-button">← Back to Hostel Map</a>
    </div>
    
    <div class="machines" id="machine-container">
        <?php 
        $machines = getMachines();
        foreach ($machines as $machine):
            $status = $machine['status'];
            $is_ready = ($status == 'in_use' && time() > strtotime($machine['timer_end']));
            $current_status = $is_ready ? 'ready_to_collect' : $status;
        ?>
            <div class="machine <?= $current_status ?>">
                <h3><?= htmlspecialchars($machine['name']) ?></h3>
                <p>Status: 
                    <?= match($current_status) {
                        'ready_to_collect' => 'Ready to Collect',
                        'in_use' => 'In Use ('.max(0, round((strtotime($machine['timer_end']) - time()) / 60)).' mins left)',
                        'paused' => 'Paused ('.round($machine['remaining_time'] / 60).' mins remaining)',
                        default => 'Available'
                    } ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="control-link-container">
        <a href="control_panel.php" class="control-link">Go to Control Panel</a>
    </div>
    
    <script>setTimeout(() => location.reload(), 20000);</script>
</body>
</html>