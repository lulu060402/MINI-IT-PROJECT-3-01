<?php
include 'functions.php';
$machine_id = intval($_GET['id']);
$machine = getMachine($machine_id);

// start with selected duration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['duration'])) {
    startMachine($machine_id, intval($_POST['duration']));
    header("Location: control.php?id=$machine_id");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Control <?= htmlspecialchars($machine['name']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="control-panel">
        <h1><?= htmlspecialchars($machine['name']) ?></h1>
        
        <?php if ($machine['display_status'] == 'ready_to_collect'): ?>
            <p>Status: <span class="ready-text">Ready to Collect!</span></p>
            <form method="POST" action="collect.php?id=<?= $machine['id'] ?>">
                <button type="submit" class="collect-btn">Collect Clothes</button>
            </form>
            
        <?php elseif ($machine['display_status'] == 'in_use'): ?>
            <?php 
            $time_left = strtotime($machine['timer_end']) - time();
            $minutes_left = max(0, round($time_left / 60));
            ?>
            <p>⏳ Time left: <?= $minutes_left ?> mins</p>
            <form method="POST" action="pause_machine.php">
                <input type="hidden" name="id" value="<?= $machine['id'] ?>">
                <button type="submit" class="pause-btn">Pause</button>
            </form>
            <form method="POST" action="cancel_machine.php" style="margin-top: 10px;">
                <input type="hidden" name="id" value="<?= $machine['id'] ?>">
                <button type="submit" class="cancel-btn">Cancel Wash</button>
            </form>
            
        <?php elseif ($machine['display_status'] == 'paused'): ?>
            <p>⏸ Paused: <?= round($machine['remaining_time'] / 60) ?> mins remaining</p>
            <form method="POST" action="resume_machine.php">
                <input type="hidden" name="id" value="<?= $machine['id'] ?>">
                <button type="submit" class="resume-btn">Resume</button>
            </form>
            <form method="POST" action="cancel_machine.php" style="margin-top: 10px;">
                <input type="hidden" name="id" value="<?= $machine['id'] ?>">
                <button type="submit" class="cancel-btn">Cancel Wash</button>
            </form>
            
        <?php else: ?>
            <form method="POST">
                <label for="duration">Select Wash Duration (minutes):</label>
                <select name="duration" id="duration" required>
                    <option value="2">2 mins (Quick Wash)</option>
                    <option value="5" selected>5 mins (Standard)</option>
                    <option value="45">45 mins (Heavy Duty)</option>
                    <option value="60">60 mins (Delicate)</option>
                </select>
                <button type="submit" class="start-btn">Start Wash</button>
            </form>
        <?php endif; ?>
        
        <a href="control_panel.php" class="back-link">← Back to Control Panel</a>
    </div>
</body>
</html>