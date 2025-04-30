<?php
include 'functions.php';
session_start();

// Ensure machine ID is set
if (!isset($_GET['id'])) {
    die("Machine ID is required.");
}

$machine_id = intval($_GET['id']);
$machine = getMachine($machine_id);

// Debugging (optional)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['duration'])) {
        startMachine($machine_id, intval($_POST['duration']));
    } 
    elseif (isset($_POST['pause'])) {
        $remaining = max(1, round((strtotime($machine['timer_end']) - time()) / 60));
        pauseMachine($machine_id, $remaining); // ✅ pass remaining value
    } 
    elseif (isset($_POST['resume'])) {
        if (isset($machine['remaining_minutes']) && $machine['remaining_minutes'] > 0) {
            resumeMachine($machine_id);
        } else {
            $_SESSION['error'] = "No remaining time to resume";
        }
    } 
    elseif (isset($_POST['cancel'])) {
        cancelMachine($machine_id);
    }

    header("Location: control.php?id=$machine_id");
    exit;
}

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Control <?= htmlspecialchars($machine['name']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }
        .control-panel {
            max-width: 500px;
            margin: 0 auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .timer-display {
            font-size: 24px;
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
        button {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .start-btn { background: #28a745; color: white; }
        .pause-btn { background: #ffc107; color: #212529; }
        .resume-btn { background: #17a2b8; color: white; }
        .cancel-btn { background: #dc3545; color: white; }
        .collect-btn { background: #fd7e14; color: white; }
        .error-message {
            color: #dc3545;
            text-align: center;
            margin-top: 15px;
            padding: 10px;
            background: #f8d7da;
            border-radius: 5px;
        }
        select {
            padding: 10px;
            width: 100%;
            margin: 10px 0;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="control-panel">
        <h1 style="text-align: center;"><?= htmlspecialchars($machine['name']) ?></h1>
        
        <?php if ($error): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($machine['status'] == 'available'): ?>
            <form method="POST">
                <label for="duration"><strong>Wash Duration:</strong></label>
                <select name="duration" id="duration" required>
                    <option value="15">15 minutes</option>
                    <option value="30" selected>30 minutes</option>
                    <option value="45">45 minutes</option>
                    <option value="60">60 minutes</option>
                </select>
                <div class="button-group">
                    <button type="submit" class="start-btn">Start Wash</button>
                </div>
            </form>

        <?php elseif ($machine['status'] == 'in_use'): ?>
            <div class="timer-display">
                ⏳ <?= max(0, round((strtotime($machine['timer_end']) - time()) / 60)) ?> minutes remaining
            </div>
            <div class="button-group">
                <form method="POST">
                    <input type="hidden" name="pause" value="1">
                    <button type="submit" class="pause-btn">Pause</button>
                </form>
                <form method="POST">
                    <input type="hidden" name="cancel" value="1">
                    <button type="submit" class="cancel-btn">Cancel</button>
                </form>
            </div>
            <script>
                setTimeout(() => location.reload(), 30000);
            </script>

        <?php elseif ($machine['status'] == 'paused'): ?>
            <div class="timer-display">
                ⏸ Paused - <?= $machine['remaining_minutes'] ?> minutes remaining
                <div style="font-size: 16px; margin-top: 8px;">
                    (Original duration: <?= $machine['duration'] ?> minutes)
                </div>
            </div>
            <div class="button-group">
                <form method="POST">
                    <input type="hidden" name="resume" value="1">
                    <button type="submit" class="resume-btn">Resume</button>
                </form>
                <form method="POST">
                    <input type="hidden" name="cancel" value="1">
                    <button type="submit" class="cancel-btn">Cancel</button>
                </form>
            </div>

        <?php elseif ($machine['status'] == 'ready_to_collect'): ?>
            <div class="timer-display">
                ✅ Wash Complete!
            </div>
            <div class="button-group">
                <form method="POST" action="collect.php?id=<?= $machine['id'] ?>">
                    <button type="submit" class="collect-btn">Collect Clothes</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
