<?php 
include 'functions.php';
$current_hb = isset($_GET['hb']) ? intval($_GET['hb']) : 1;
?>
<!DOCTYPE html>
<html>
<head>
    <title>HB <?= $current_hb ?> Control</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>HB <?= $current_hb ?> Control Panel</h1>
    <div class="hb-indicator">Hostel Block <?= $current_hb ?></div>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="error-message">
            Error: <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

        <div class="points-display">
        <?php if (isset($_SESSION['user_id'])): ?>
            Your Points: <?= getCurrentPoints() ?>
        <?php else: ?>
            Admin Control Panel
        <?php endif; ?>
    </div>

    <div class="machines">
        <?php 
        $machines = getMachines($current_hb);
        foreach ($machines as $machine): 
            $display_status = $machine['display_status'];
            $status_text = getStatusText($machine);
        ?>
            <div class="machine <?= $display_status ?>" data-machine-id="<?= $machine['id'] ?>">
                <h3><?= htmlspecialchars($machine['name']) ?></h3>
                <p class="status-text">Status: <?= $status_text ?></p>
                <div class="machine-controls">
                    <?= getControlButtons($machine, $current_hb) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
    function updateControlPanel() {
        fetch(`status_json.php?hb=<?= $current_hb ?>`)
            .then(response => response.json())
            .then(machines => {
                machines.forEach(machine => {
                    const element = document.querySelector(`[data-machine-id="${machine.id}"]`);
                    if (!element) return;

                    // update status text
                    const statusEl = element.querySelector('.status-text');
                    statusEl.textContent = `Status: ${getStatusTextJS(machine)}`;

                    // update machine class
                    element.className = `machine ${machine.status}`;

                    // update controls
                    const controlsEl = element.querySelector('.machine-controls');
                    controlsEl.innerHTML = generateControls(machine);
                });
            });
    }

    function getStatusTextJS(machine) {
        switch(machine.status) {
            case 'ready_to_collect': return 'Ready to Collect';
            case 'in_use': return `In Use (${machine.mins_left} mins left)`;
            case 'paused': return `Paused (${machine.mins_left} mins remaining)`;
            default: return 'Available';
        }
    }

    function generateControls(machine) {
        const hb = <?= $current_hb ?>;
        let html = '';
        
        switch(machine.status) {
            case 'ready_to_collect':
                html = `
                    <form method="POST" action="collect.php">
                        <input type="hidden" name="id" value="${machine.id}">
                        <input type="hidden" name="hb" value="${hb}">
                        <button class="collect-btn">Collect Clothes</button>
                    </form>
                `;
                break;
                
            case 'available':
                html = `
                    <a href="control.php?id=${machine.id}&hb=${hb}" class="control-link">
                        Start Wash
                    </a>
                `;
                break;
                
            case 'in_use':
                html = `
                    <form method="POST" action="pause_machine.php">
                        <input type="hidden" name="id" value="${machine.id}">
                        <input type="hidden" name="hb" value="${hb}">
                        <button class="pause-btn">Pause</button>
                    </form>
                    <form method="POST" action="cancel_machine.php">
                        <input type="hidden" name="id" value="${machine.id}">
                        <input type="hidden" name="hb" value="${hb}">
                        <button class="cancel-btn">Cancel</button>
                    </form>
                `;
                break;
                
            case 'paused':
                html = `
                    <form method="POST" action="resume_machine.php">
                        <input type="hidden" name="id" value="${machine.id}">
                        <input type="hidden" name="hb" value="${hb}">
                        <button class="resume-btn">Resume</button>
                    </form>
                    <form method="POST" action="cancel_machine.php">
                        <input type="hidden" name="id" value="${machine.id}">
                        <input type="hidden" name="hb" value="${hb}">
                        <button class="cancel-btn">Cancel</button>
                    </form>
                `;
                break;
        }
        return html;
    }

    // Update every 10 seconds
    setInterval(updateControlPanel, 10000);
    updateControlPanel(); // Initial update
    </script>
</body>
</html>

<?php
// helper functions for initial render
function getStatusText($machine) {
    switch($machine['display_status']) {
        case 'ready_to_collect':
            return 'Ready to Collect';
            
        case 'in_use':
            $mins = max(0, round((strtotime($machine['timer_end']) - time()) / 60));
            return "In Use ($mins mins left)";
            
        case 'paused':
            $mins = round($machine['remaining_time'] / 60);
            return "Paused ($mins mins remaining)";
            
        default:
            return 'Available';
    }
}

function getControlButtons($machine, $hb) {
    ob_start();
    $id = $machine['id'];
    $status = $machine['display_status'];
    ?>
    <?php if ($status == 'ready_to_collect'): ?>
        <form method="POST" action="collect.php">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="hb" value="<?= $hb ?>">
            <button class="collect-btn">Collect Clothes</button>
        </form>
    <?php elseif ($status == 'available'): ?>
        <a href="control.php?id=<?= $id ?>&hb=<?= $hb ?>" class="control-link">
            Start Wash
        </a>
    <?php elseif ($status == 'in_use'): ?>
        <form method="POST" action="pause_machine.php">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="hb" value="<?= $hb ?>">
            <button class="pause-btn">Pause</button>
        </form>
        <form method="POST" action="cancel_machine.php">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="hb" value="<?= $hb ?>">
            <button class="cancel-btn">Cancel</button>
        </form>
    <?php elseif ($status == 'paused'): ?>
        <form method="POST" action="resume_machine.php">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="hb" value="<?= $hb ?>">
            <button class="resume-btn">Resume</button>
        </form>
        <form method="POST" action="cancel_machine.php">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="hb" value="<?= $hb ?>">
            <button class="cancel-btn">Cancel</button>
        </form>
    <?php endif; ?>
    <?php
    return ob_get_clean();
}
?>
