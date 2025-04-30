<?php include 'functions.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Laundry Machine Status</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Machine Status</h1>
    <div class="machines" id="machine-container">
        <!-- Machines will be loaded here by JavaScript -->
    </div>
    <div class="control-link-container">
        <a href="control_panel.php" class="control-link">Go to Control Panel</a>
    </div>

    <script>
    function updateMachines() {
        fetch('get_machines.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('machine-container').innerHTML = data;
            });
    }

    // Update immediately and every 10 seconds
    updateMachines();
    setInterval(updateMachines, 10000);
    </script>
</body>
</html>