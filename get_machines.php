<?php
include 'functions.php';
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