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
        
        <?php if ($machine['status'] != 'available'): ?>
            <!-- show timer -->
            <div class="timer">
                <?php if ($machine['status'] == 'ready_to_collect'): ?>
                    <p>Status: <span class="ready-text">Ready to Collect!</span></p>
                <?php else: ?>
                    <p>⏳ Time left: <?= max(0, round((strtotime($machine['timer_end']) - time()) / 60)) ?> mins</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- select duration -->
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
        
        <!-- collect button -->
        <?php if ($machine['status'] == 'ready_to_collect'): ?>
            <form method="POST" action="collect.php?id=<?= $machine['id'] ?>">
                <button type="submit" class="collect-btn">Collect Clothes</button>
            </form>
        <?php endif; ?>
        
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>