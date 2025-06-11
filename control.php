<?php
include 'functions.php';
$machine_id = intval($_GET['id']);
$current_hb = intval($_GET['hb'] ?? 1);
$machine = getMachine($machine_id);

// Handle all machine actions directly on this page
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['duration'])) {
        // Start machine
        startMachine($machine_id, intval($_POST['duration']));
    } elseif (isset($_POST['pause'])) {
        // Pause machine
        pauseMachine($machine_id);
    } elseif (isset($_POST['resume'])) {
        // Resume machine
        resumeMachine($machine_id);
    } elseif (isset($_POST['cancel'])) {
        // Cancel machine
        cancelMachine($machine_id);
    } elseif (isset($_POST['collect'])) {
        // Collect clothes
        collectMachine($machine_id);
    }
    
    // Refresh the machine status after action
    $machine = getMachine($machine_id);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Control <?= htmlspecialchars($machine['name']) ?></title>
    <link rel="stylesheet" href="style.css">
    <style>

body {
    margin:0;
    padding:0;
}
.nav {

    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 40px;
    color: white;
    flex-wrap: wrap;
    background: linear-gradient( 90deg,
    #27272B,
    #2b2b31,
    #3a3a3e,
    #27272B
      );

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

  }
  

  .nav a {

    text-decoration: none;
    color: white;
    font-weight: 500;

  }

  .nav a:hover {
    
    color:#878686;

  }

                                 /* Responsive rules for Navigating bar*/

    @media screen and (max-width:600px) {

        .nav ul {
            gap : 25px;
            font-size: 13px;
        }

        .nav .logo {

            font-weight: 900;
            font-size: 19px;
        
          }

    }



    @media screen and (max-width:450px) {

    .nav ul {
        gap : 15px;
        font-size: 12px;
    }

    .nav .logo {

        font-weight: 900;
        font-size: 17px;
    
      }   
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
    line-height:1.2;
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

@media screen and (max-width: 600px) {
    .bar {
        padding: 3px 20px;
    }
    .bar li {
        font-size: 10px;
    }
}

@media screen and (max-width: 450px) {
    .bar {
        padding: 1px 15px;
    }
    .bar li {
        font-size: 7px;
    }
}
    </style>
    <script>
    function updateMachineStatus() {
        fetch(`status_json.php?machine_id=<?= $machine_id ?>`)
            .then(response => response.json())
            .then(machine => {
                // Update status display
                const statusDisplay = document.querySelector('.status-display');
                const timeDisplay = document.querySelector('.time-display');
                const controlsContainer = document.querySelector('.machine-controls');
                
                // Update status text
                if (machine.display_status === 'ready_to_collect') {
                    statusDisplay.innerHTML = 'Status: <span class="ready-text">Ready to Collect!</span>';
                } else if (machine.display_status === 'in_use') {
                    const minutesLeft = Math.max(0, Math.round((new Date(machine.timer_end) - new Date()) / 60000);
                    statusDisplay.textContent = 'Status: In Use';
                    timeDisplay.innerHTML = `⏳ Time left: ${minutesLeft} mins`;
                } else if (machine.display_status === 'paused') {
                    const minutesLeft = Math.round(machine.remaining_time / 60);
                    statusDisplay.textContent = 'Status: Paused';
                    timeDisplay.innerHTML = `⏸ Paused: ${minutesLeft} mins remaining`;
                } else {
                    statusDisplay.textContent = 'Status: Available';
                    timeDisplay.textContent = '';
                }
                
                // Update controls
                controlsContainer.innerHTML = generateControlsHTML(machine);
            });
    }

    function generateControlsHTML(machine) {
        const hb = <?= $current_hb ?>;
        let html = '';
        
        if (machine.display_status === 'ready_to_collect') {
            html = `
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $machine_id ?>">
                    <input type="hidden" name="hb" value="${hb}">
                    <button type="submit" name="collect" class="collect-btn">Collect Clothes</button>
                </form>
            `;
        } else if (machine.display_status === 'in_use') {
            html = `
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $machine_id ?>">
                    <input type="hidden" name="hb" value="${hb}">
                    <button type="submit" name="pause" class="pause-btn">Pause</button>
                </form>
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $machine_id ?>">
                    <input type="hidden" name="hb" value="${hb}">
                    <button type="submit" name="cancel" class="cancel-btn">Cancel Wash</button>
                </form>
            `;
        } else if (machine.display_status === 'paused') {
            html = `
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $machine_id ?>">
                    <input type="hidden" name="hb" value="${hb}">
                    <button type="submit" name="resume" class="resume-btn">Resume</button>
                </form>
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $machine_id ?>">
                    <input type="hidden" name="hb" value="${hb}">
                    <button type="submit" name="cancel" class="cancel-btn">Cancel Wash</button>
                </form>
            `;
        } else {
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
        }
        return html;
    }

    // Update status every 10 seconds
    setInterval(updateMachineStatus, 10000);
    updateMachineStatus(); // Initial update
    </script>
</head>
<body>
<!-- Add this in the navigation bar section -->
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

<script>
document.getElementById('play-game-btn').addEventListener('click', function(e) {
    e.preventDefault();
    
    // Create a new window/tab for the game
    const gameWindow = window.open('', '_blank', 'width=800,height=1000');
    
    // Write the game HTML content directly to the new window
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
                
                // Game variables
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
                
                // Game assets
                const basketImg = new Image();
                basketImg.src = 'basket.png';
                
                const clothesImgs = {
                    'shirt': new Image(),
                    'pants': new Image(),
                    'sock': new Image(),
                    'skirt': new Image(),
                    'underwear': new Image()
                };
                
                // Load images
                clothesImgs.shirt.src = 'mmushirt.png';
                clothesImgs.pants.src = 'pants.png';
                clothesImgs.sock.src = 'socks.png';
                clothesImgs.skirt.src = 'skirt.png';
                clothesImgs.underwear.src = 'underwear.png';
                
                // Game loop
                function gameLoop() {
                    // Clear canvas
                    ctx.fillStyle = '#000';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    
                    // Handle keyboard input
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
                    
                    // Spawn new clothes
                    if (Math.random() < 1/spawnRate) {
                        clothes.push({
                            x: Math.random() * (canvas.width - CLOTHES_SIZE),
                            y: -CLOTHES_SIZE,
                            type: ['shirt', 'pants', 'sock', 'skirt', 'underwear'][Math.floor(Math.random() * 5)]
                        });
                    }
                    
                    // Update and draw clothes
                    for (let i = clothes.length - 1; i >= 0; i--) {
                        const item = clothes[i];
                        item.y += clothesSpeed;
                        
                        // Draw clothing item
                        ctx.drawImage(clothesImgs[item.type], item.x, item.y, CLOTHES_SIZE, CLOTHES_SIZE);
                        
                        // Check for collision with basket
                        if (basketX < item.x + CLOTHES_SIZE &&
                            basketX + BASKET_WIDTH > item.x &&
                            basketY < item.y + CLOTHES_SIZE &&
                            basketY + BASKET_HEIGHT > item.y) {
                            clothes.splice(i, 1);
                            score++;
                        }
                        // Remove if out of bounds
                        else if (item.y > canvas.height) {
                            clothes.splice(i, 1);
                            if (score > 0) score--;
                        }
                    }
                    
                    // Draw basket
                    ctx.drawImage(basketImg, basketX, basketY, BASKET_WIDTH, BASKET_HEIGHT);
                    
                    // Draw score
                    ctx.fillStyle = '#FFF';
                    ctx.font = '36px Arial';
                    ctx.fillText('Score: ' + score, 20, 40);
                    
                    // Draw boost indicator
                    if (keys[' ']) {
                        ctx.fillStyle = '#F00';
                        ctx.fillText('BOOST!', canvas.width - 150, 40);
                    }
                    
                    requestAnimationFrame(gameLoop);
                }
                
                // Keyboard input handling
                const keys = {};
                window.addEventListener('keydown', e => keys[e.key] = true);
                window.addEventListener('keyup', e => keys[e.key] = false);
                
                // Start the game
                gameLoop();
            <\/script>
        </body>
        </html>
    `);
    gameWindow.document.close();
});
</script>
        <div class="bar">
      <ul>
        <li><h4>Click <a href="signup.php">Here</a> to Sign Up and get Free Rewards!</h4></li>
      </ul>
    </div>

    <div class="control-panel">
        <h1><?= htmlspecialchars($machine['name']) ?></h1>
        <div class="hb-indicator">Hostel Block <?= $current_hb ?></div>
        
        <div class="status-display">
            <?php if ($machine['display_status'] == 'ready_to_collect'): ?>
                Status: <span class="ready-text">Ready to Collect!</span>
            <?php elseif ($machine['display_status'] == 'in_use'): ?>
                Status: In Use
            <?php elseif ($machine['display_status'] == 'paused'): ?>
                Status: Paused
            <?php else: ?>
                Status: Available
            <?php endif; ?>
        </div>
        
        <div class="time-display">
            <?php if ($machine['display_status'] == 'in_use'): 
                $time_left = strtotime($machine['timer_end']) - time();
                $minutes_left = max(0, round($time_left / 60));
            ?>
                ⏳ Time left: <?= $minutes_left ?> mins
            <?php elseif ($machine['display_status'] == 'paused'): ?>
                ⏸ Paused: <?= round($machine['remaining_time'] / 60) ?> mins remaining
            <?php endif; ?>
        </div>

        <div class="machine-controls">
            <?php if ($machine['display_status'] == 'ready_to_collect'): ?>
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $machine_id ?>">
                    <input type="hidden" name="hb" value="<?= $current_hb ?>">
                    <button type="submit" name="collect" class="collect-btn">Collect Clothes</button>
                </form>
                
            <?php elseif ($machine['display_status'] == 'in_use'): ?>
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $machine_id ?>">
                    <input type="hidden" name="hb" value="<?= $current_hb ?>">
                    <button type="submit" name="pause" class="pause-btn">Pause</button>
                </form>
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $machine_id ?>">
                    <input type="hidden" name="hb" value="<?= $current_hb ?>">
                    <button type="submit" name="cancel" class="cancel-btn">Cancel Wash</button>
                </form>
                
            <?php elseif ($machine['display_status'] == 'paused'): ?>
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $machine_id ?>">
                    <input type="hidden" name="hb" value="<?= $current_hb ?>">
                    <button type="submit" name="resume" class="resume-btn">Resume</button>
                </form>
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $machine_id ?>">
                    <input type="hidden" name="hb" value="<?= $current_hb ?>">
                    <button type="submit" name="cancel" class="cancel-btn">Cancel Wash</button>
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
                    <input type="hidden" name="hb" value="<?= $current_hb ?>">
                    <button type="submit" class="start-btn">Start Wash</button>
                </form>
            <?php endif; ?>
        </div>
        
    </div>
</body>
</html>