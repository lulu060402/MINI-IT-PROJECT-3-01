<?php 
include 'functions.php';
$current_hb = isset($_GET['hb']) ? intval($_GET['hb']) : 1;
?>
<!DOCTYPE html>
<html>
<head>
    <title>HB <?= $current_hb ?> Status</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>HB <?= $current_hb ?> Machine Status</h1>
    <div class="hb-indicator">Hostel Block <?= $current_hb ?></div>
    
    <div class="back-container">
        <a href="hostel_map.html" class="back-button">← Back to Hostel Map</a>
    </div>
    
    <div class="machines" id="machine-container">
        <?php 
        $machines = getMachines($current_hb);
        foreach ($machines as $machine):
            $status = $machine['status'];
            $is_ready = ($status == 'in_use' && time() > strtotime($machine['timer_end']));
            $current_status = $is_ready ? 'ready_to_collect' : $status;
            
            $status_text = 'Available';
            switch($current_status) {
                case 'ready_to_collect':
                    $status_text = 'Ready to Collect';
                    break;
                case 'in_use':
                    $mins_left = max(0, round((strtotime($machine['timer_end']) - time()) / 60));
                    $status_text = "In Use ($mins_left mins left)";
                    break;
                case 'paused':
                    $mins_left = round($machine['remaining_time'] / 60);
                    $status_text = "Paused ($mins_left mins remaining)";
                    break;
            }
        ?>
            <div class="machine <?= $current_status ?>" data-machine-id="<?= $machine['id'] ?>">
                <h3><?= htmlspecialchars($machine['name']) ?></h3>
                <p class="status-text">Status: <?= $status_text ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
    function updateStatus() {
        fetch(`status_json.php?hb=<?= $current_hb ?>`)
            .then(response => response.json())
            .then(machines => {
                machines.forEach(machine => {
                    const element = document.querySelector(`[data-machine-id="${machine.id}"]`);
                    if (!element) return;

                    const statusEl = element.querySelector('.status-text');
                    let statusText = 'Available';
                    
                    switch(machine.status) {
                        case 'ready_to_collect':
                            statusText = 'Ready to Collect';
                            break;
                        case 'in_use':
                            statusText = `In Use (${machine.mins_left} mins left)`;
                            break;
                        case 'paused':
                            statusText = `Paused (${machine.mins_left} mins remaining)`;
                            break;
                    }
                    
                    statusEl.textContent = `Status: ${statusText}`;
                    element.className = `machine ${machine.status}`;
                });
            });
    }

    // update every 10 seconds
    setInterval(updateStatus, 10000);
    updateStatus();
    </script>
</body>
</html>