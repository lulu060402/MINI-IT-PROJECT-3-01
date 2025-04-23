<?php include 'functions.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Laundry Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Machine Status Overview</h1>
    <div class="machines">
        <?php 
        $result = getMachines();
        while ($machine = $result->fetch_assoc()): 
            // Determine current status
            $status = $machine['status'];
            $is_ready = ($status == 'in_use' && time() > strtotime($machine['timer_end']));
            $current_status = $is_ready ? 'ready_to_collect' : $status;
        ?>
            <div class="machine <?= $current_status ?>">
                <h3><?= $machine['name'] ?></h3>
                <p>Status: 
                    <?= match($current_status) {
                        'ready_to_collect' => 'Ready to Collect',
                        'in_use' => 'In Use ('.max(0, round((strtotime($machine['timer_end']) - time()) / 60)).' mins left)',
                        default => 'Available'
                    } ?>
                </p>
                
                <?php if ($current_status == 'ready_to_collect'): ?>
                    <form method="POST" action="collect.php?id=<?= $machine['id'] ?>">
                        <button type="submit" class="collect-btn">Collect Clothes</button>
                    </form>
                <?php elseif ($status == 'available'): ?>
                    <a href="control.php?id=<?= $machine['id'] ?>" class="control-link">
                        Start Wash
                    </a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
    <!-- auto refresh every 20 second -->
    <script>setTimeout(() => location.reload(), 20000);</script>
</body>
</html>