<?php
include 'functions.php';

// Get machine ID and hostel block
$machine_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$current_hb = isset($_GET['hb']) ? intval($_GET['hb']) : 1;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['duration']) && $machine_id > 0) {
        // Start machine
        startMachine($machine_id, intval($_POST['duration']));
        // Redirect back to same machine control page
        header("Location: control.php?id=$machine_id&hb=$current_hb");
        exit;
    }
}

// Get machine details
$machine = $machine_id > 0 ? getMachine($machine_id) : null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>
        <?= $machine ? 'Control ' . htmlspecialchars($machine['name']) : 'Machine Control' ?>
    </title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }
        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            color: white;
            flex-wrap: wrap;
            background: linear-gradient(90deg, #27272B, #2b2b31, #3a3a3e, #27272B);
        }
        .nav .logo {
            font-weight: 900;
            font-size: 20px;
        }
        .nav ul {
            list-style: none;
            display: flex;
            gap: 40px;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }
        .nav a {
            text-decoration: none;
            color: white;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav a:hover {
            color: #878686;
        }
        .bar {
            background-color: rgba(41, 41, 48, 0.8);
            padding: 5px 40px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .bar ul {
            list-style: none;
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 0;
            gap: 10px;
        }
        .bar li {
            color: rgb(221, 216, 216);
            line-height: 1.2;
            font-weight: 100;
        }
        .bar a {
            color: #0377ca;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s;
        }
        .bar a:hover {
            color: #025fa2;
        }
        .bar h4 {
            margin: 0;
            font-size: 13px;
        }
        .control-panel {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .hb-indicator {
            font-size: 18px;
            color: #fffff;
            margin-bottom: 20px;
        }
        .status-display {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            padding: 10px;
            border-radius: 5px;
            background-color: #f8f9fa;
        }
        .ready-text {
            color: #28a745;
        }
        .time-display {
            font-size: 20px;
            margin-bottom: 20px;
            color: #6c757d;
        }
        .machine-controls {
            margin-top: 30px;
        }
        form {
            margin: 15px 0;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        button {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }
        .start-btn {
            background-color: #007bff;
            color: white;
            width: 100%;
        }
        .start-btn:hover {
            background-color: #0069d9;
        }
        .collect-btn {
            background-color: #28a745;
            color: white;
            width: 100%;
        }
        .collect-btn:hover {
            background-color: #218838;
        }
        .cancel-btn {
            background-color: #dc3545;
            color: white;
            width: 100%;
        }
        .cancel-btn:hover {
            background-color: #c82333;
        }
        .back-container {
            margin-top: 20px;
            text-align: center;
        }
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
        .photo-upload {
            margin-bottom: 15px;
        }
        .photo-upload label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .photo-upload input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        /* Responsive rules */
        @media screen and (max-width: 600px) {
            .nav ul {
                gap: 25px;
                font-size: 13px;
            }
            .nav .logo {
                font-size: 19px;
            }
            .bar {
                padding: 3px 20px;
            }
            .bar li {
                font-size: 10px;
            }
        }
        @media screen and (max-width: 450px) {
            .nav {
                padding: 15px;
            }
            .nav ul {
                gap: 15px;
                font-size: 12px;
            }
            .nav .logo {
                font-size: 17px;
            }
            .bar {
                padding: 1px 15px;
            }
            .bar li {
                font-size: 7px;
            }
        }
    </style>
</head>
<body>
    <header class="nav">
        <div class="logo">DRYFANS</div>
        <nav>
            <ul>
                <li><a href="aboutus.html">About Us</a></li>
                <li><a href="report.php">Support</a></li>
                <li><a href="#" id="play-game-btn">Play Game</a></li>
            </ul>
        </nav>
    </header>

    <div class="bar">
        <ul>
            <li><h4>Click <a href="signup.php">Here</a> to Sign Up and get Free Rewards!</h4></li>
        </ul>
    </div>

    <div class="control-panel">
        <?php if ($machine): ?>
            <h1><?= htmlspecialchars($machine['name']) ?></h1>
            <div class="hb-indicator">Hostel Block <?= $current_hb ?></div>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message" style="color: #ff4444; padding: 10px; margin-bottom: 20px;">
                    Error: <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>
            
            <div class="machine <?= $machine['display_status'] ?>" data-machine-id="<?= $machine['id'] ?>">
                <p class="status-text">Status: <?= getStatusText($machine) ?></p>
                <div class="machine-controls">
                    <?= getControlButtons($machine, $current_hb) ?>
                </div>
                <div class="text">
                    <h3> Collect washed clothes earlier for more bonus points! </h3>
            </div>
            </div>
        <?php else: ?>
            <h1>Machine Not Found</h1>
            <p>The requested machine could not be found.</p>
        <?php endif; ?>
    </div>

    <script>
    // Game functionality
    document.getElementById('play-game-btn').addEventListener('click', function(e) {
        e.preventDefault();
        const gameWindow = window.open('', '_blank', 'width=800,height=1000');
        gameWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>DryCatch Game</title>
                <style>
                    body { margin: 0; background: #000; }
                    canvas { display: block; }
                </style>
            </head>
            <body>
                <canvas id="gameCanvas" width="800" height="1000"></canvas>
                <script>
                    const canvas = document.getElementById('gameCanvas');
                    const ctx = canvas.getContext('2d');
                    const BASKET_WIDTH = 120;
                    const BASKET_HEIGHT = 60;
                    const CLOTHES_SIZE = 80;
                    let basketX = canvas.width / 2 - BASKET_WIDTH / 2;
                    let basketY = canvas.height - BASKET_HEIGHT - 40;
                    let score = 0;
                    let clothes = [];
                    let clothesSpeed = 5;
                    let spawnRate = 30;
                    let normalSpeed = 10;
                    let boostSpeed = 50;
                    let currentSpeed = normalSpeed;
                    
                    const basketImg = new Image();
                    basketImg.src = 'basket.png';
                    
                    const clothesImgs = {
                        'shirt': new Image(),
                        'pants': new Image(),
                        'sock': new Image(),
                        'skirt': new Image(),
                        'underwear': new Image()
                    };
                    
                    clothesImgs.shirt.src = 'mmushirt.png';
                    clothesImgs.pants.src = 'pants.png';
                    clothesImgs.sock.src = 'socks.png';
                    clothesImgs.skirt.src = 'skirt.png';
                    clothesImgs.underwear.src = 'underwear.png';
                    
                    function gameLoop() {
                        ctx.fillStyle = '#000';
                        ctx.fillRect(0, 0, canvas.width, canvas.height);
                        
                        if (keys['a'] && basketX > 0) {
                            basketX -= currentSpeed;
                        }
                        if (keys['d'] && basketX < canvas.width - BASKET_WIDTH) {
                            basketX += currentSpeed;
                        }
                        if (keys[' ']) {
                            currentSpeed = boostSpeed;
                        } else {
                            currentSpeed = normalSpeed;
                        }
                        
                        if (Math.random() < 1/spawnRate) {
                            clothes.push({
                                x: Math.random() * (canvas.width - CLOTHES_SIZE),
                                y: -CLOTHES_SIZE,
                                type: ['shirt', 'pants', 'sock', 'skirt', 'underwear'][Math.floor(Math.random() * 5)]
                            });
                        }
                        
                        for (let i = clothes.length - 1; i >= 0; i--) {
                            const item = clothes[i];
                            item.y += clothesSpeed;
                            
                            ctx.drawImage(clothesImgs[item.type], item.x, item.y, CLOTHES_SIZE, CLOTHES_SIZE);
                            
                            if (basketX < item.x + CLOTHES_SIZE &&
                                basketX + BASKET_WIDTH > item.x &&
                                basketY < item.y + CLOTHES_SIZE &&
                                basketY + BASKET_HEIGHT > item.y) {
                                clothes.splice(i, 1);
                                score++;
                            }
                            else if (item.y > canvas.height) {
                                clothes.splice(i, 1);
                                if (score > 0) score--;
                            }
                        }
                        
                        ctx.drawImage(basketImg, basketX, basketY, BASKET_WIDTH, BASKET_HEIGHT);
                        
                        ctx.fillStyle = '#FFF';
                        ctx.font = '36px Arial';
                        ctx.fillText('Score: ' + score, 20, 40);
                        
                        if (keys[' ']) {
                            ctx.fillStyle = '#F00';
                            ctx.fillText('BOOST!', canvas.width - 150, 40);
                        }
                        
                        requestAnimationFrame(gameLoop);
                    }
                    
                    const keys = {};
                    window.addEventListener('keydown', e => keys[e.key] = true);
                    window.addEventListener('keyup', e => keys[e.key] = false);
                    
                    gameLoop();
                <\/script>
            </body>
            </html>
        `);
        gameWindow.document.close();
    });

    function updateMachineStatus() {
        fetch(`status_json.php?hb=<?= $current_hb ?>`)
            .then(response => response.json())
            .then(machines => {
                // Find our specific machine
                const machine = machines.find(m => m.id == <?= $machine_id ?>);
                if (!machine) return;
                
                const element = document.querySelector(`[data-machine-id="<?= $machine_id ?>"]`);
                if (!element) return;

                // Update status text
                const statusEl = element.querySelector('.status-text');
                statusEl.textContent = `Status: ${getStatusTextJS(machine)}`;

                // Update machine class
                element.className = `machine ${machine.status}`;

                // Update controls
                const controlsEl = element.querySelector('.machine-controls');
                controlsEl.innerHTML = generateControls(machine);
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
                    <form method="POST" action="collect.php" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="${machine.id}">
                        <input type="hidden" name="hb" value="${hb}">
                        <input type="hidden" name="redirect" value="control">
                        <div class="photo-upload">
                            <label for="collection_photo">Upload Collection Proof:</label>
                            <input type="file" name="collection_photo" id="collection_photo" required accept="image/*">
                        </div>
                        <button class="collect-btn">Collect Clothes</button>
                    </form>
                `;
                break;
                
            case 'available':
                html = `
                    <form method="POST">
                        <label for="duration">Select Wash Duration (minutes):</label>
                        <select name="duration" id="duration" required>
                            <option value="2">2 mins (Quick Wash)</option>
                            <option value="5" selected>5 mins (Standard)</option>
                            <option value="45">45 mins (Heavy Duty)</option>
                            <option value="60">60 mins (Delicate)</option>
                        </select>
                        <input type="hidden" name="hb" value="${hb}">
                        <button type="submit" class="start-btn">Start Wash</button>
                    </form>
                `;
                break;
                
            case 'in_use':
            case 'paused': // Combined handling for both states
                html = `
                    <form method="POST" action="cancel_machine.php">
                        <input type="hidden" name="id" value="${machine.id}">
                        <input type="hidden" name="hb" value="${hb}">
                        <input type="hidden" name="redirect" value="control">
                        <button class="cancel-btn">Cancel Wash</button>
                    </form>
                `;
                break;
        }
        return html;
    }

    // Update every 10 seconds
    setInterval(updateMachineStatus, 10000);
    updateMachineStatus(); // Initial update
    
    // Handle form submissions
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.method.toUpperCase() === 'POST') {
            const button = form.querySelector('button');
            if (button) {
                button.disabled = true;
                button.textContent = 'Processing...';
            }
        }
    });
    </script>
</body>
</html>

<?php
// Helper functions for initial render
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
        <form method="POST" action="collect.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="hb" value="<?= $hb ?>">
            <input type="hidden" name="redirect" value="control">
            <div class="photo-upload">
                <label for="collection_photo">Upload Collection Proof:</label>
                <input type="file" name="collection_photo" id="collection_photo" required accept="image/*">
            </div>
            <button class="collect-btn">Collect Clothes</button>
        </form>
    <?php elseif ($status == 'available'): ?>
        <form method="POST">
            <label for="duration">Select Wash Duration (minutes):</label>
            <select name="duration" id="duration" required>
                <option value="2">2 mins (Quick Wash)</option>
                <option value="5" selected>5 mins (Standard)</option>
                <option value="45">45 mins (Heavy Duty)</option>
                <option value="60">60 mins (Delicate)</option>
            </select>
            <input type="hidden" name="hb" value="<?= $hb ?>">
            <button type="submit" class="start-btn">Start Wash</button>
        </form>
    <?php elseif ($status == 'in_use' || $status == 'paused'): ?>
        <form method="POST" action="cancel_machine.php">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="hb" value="<?= $hb ?>">
            <input type="hidden" name="redirect" value="control">
            <button class="cancel-btn">Cancel Wash</button>
        </form>
    <?php endif; ?>
    <?php
    return ob_get_clean();
}
?>