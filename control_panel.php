<?php include 'functions.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Laundry Control Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Machine Control Panel</h1>
    <?php if (isset($_GET['error']) && $_GET['error'] == 'resume_failed'): ?>
        <div class="error-message">Failed to resume machine. Please try again.</div>
    <?php endif; ?>
    
    <div class="machines">
        <?php 
        $machines = getMachines();
        foreach ($machines as $machine): 
            $display_status = $machine['display_status'];
        ?>
            <div class="machine <?= $display_status ?>">
                <h3><?= htmlspecialchars($machine['name']) ?></h3>
                <p>Status: 
                    <?= match($display_status) {
                        'ready_to_collect' => 'Ready to Collect',
                        'in_use' => 'In Use ('.max(0, round((strtotime($machine['timer_end']) - time()) / 60)).' mins left)',
                        'paused' => 'Paused ('.round($machine['remaining_time'] / 60).' mins remaining)',
                        default => 'Available'
                    } ?>
                </p>
                
                <div class="machine-controls">
                    <?php if ($display_status == 'ready_to_collect'): ?>
                        <form method="POST" action="collect.php?id=<?= $machine['id'] ?>">
                            <button type="submit" class="collect-btn">Collect Clothes</button>
                        </form>
                    <?php elseif ($display_status == 'available'): ?>
                        <a href="control.php?id=<?= $machine['id'] ?>" class="control-link">
                            Start Wash
                        </a>
                    <?php elseif ($display_status == 'in_use'): ?>
                        <form method="POST" action="pause_machine.php" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $machine['id'] ?>">
                            <button type="submit" class="pause-btn">Pause</button>
                        </form>
                        <form method="POST" action="cancel_machine.php" style="display: inline; margin-left: 5px;">
                            <input type="hidden" name="id" value="<?= $machine['id'] ?>">
                            <button type="submit" class="cancel-btn">Cancel</button>
                        </form>
                    <?php elseif ($display_status == 'paused'): ?>
                        <form method="POST" action="resume_machine.php" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $machine['id'] ?>">
                            <button type="submit" class="resume-btn">Resume</button>
                        </form>
                        <form method="POST" action="cancel_machine.php" style="display: inline; margin-left: 5px;">
                            <input type="hidden" name="id" value="<?= $machine['id'] ?>">
                            <button type="submit" class="cancel-btn">Cancel</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="control-link-container">
        <a href="status.php" class="control-link">View Status Only</a>
    </div>
</body>
</html>