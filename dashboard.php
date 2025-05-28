<?php
// Enhanced session security
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("PHP Error [$errno]: $errstr in $errfile on line $errline");
    return true;
});

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Session regeneration
if (!isset($_SESSION['created'])) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// CSRF token generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Secure output function
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
// Database connection (update credentials as needed)
$mysqli = new mysqli('localhost', 'root', '', 'server_db');
if ($mysqli->connect_errno) {
    die('Database connection failed: ' . $mysqli->connect_error);
}
// User data
$username = $_SESSION['username'] ?? '';
$points = $_SESSION['points'] ?? 0;
$redemptions = $_SESSION['redemptions'] ?? [];

// Fetch user points from database
$user_id = $_SESSION['user_id'];
$stmt = $mysqli->prepare("SELECT points FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($points);
if (!$stmt->fetch()) {
    $points = 0; // Default if user not found
}
$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DRYFANS Rewards</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .alert {
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .error {
            background-color: #ffdddd;
            color: #d8000c;
        }
        .success {
            background-color: #ddffdd;
            color: #4F8A10;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.8em;
        }
        .pending {
            background-color: #FFF3CD;
            color: #856404;
        }
        .claimed {
            background-color: #D4EDDA;
            color: #155724;
        }
    </style>
</head>
<body>
    <header class="nav">
        <div class="logo">DRYFANS</div>
        <nav>
            <ul>
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="aboutus.html">About Us</a></li>
                <li><a href="report.html">Support</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="head">
            <h1>DRYFANS Rewards</h1>
            <p>Earn points with every wash and redeem exciting rewards!</p>
        </div>
        
        <div class="user-profile">
            <div class="user-avatar">
                <?php 
                $initials = '';
                if (!empty($username)) {
                    $parts = preg_split('/\s+/', trim($username));
                    $initials = strtoupper(substr($parts[0], 0, 1));
                    if (count($parts) > 1) {
                        $initials .= strtoupper(substr(end($parts), 0, 1));
                    }
                }
                echo e($initials);
                ?>
            </div>
            <div>
                <h2>Welcome, <?php echo e($username); ?>!</h2>
                <div class="points-display">Your Points: <span id="points-balance"><?php echo($points); ?></span></div>
                 <br><br><button class="status-btn" onclick="window.location.href='control_panel.php'">Status</button>
                 <br><br><button class="booking" onclick="window.location.href='booking.php'">Book Now</button>
                
            </div>
        </div>
        
        <div class="rewards-section">
            <h2>Redeem your points</h2>
            
            <div class="how-it-works">
                <h3>How It Works</h3>
                <ol>
                    <li><strong>Earn Points:</strong> Get 10 points for every RM1 spent</li>
                    <li><strong>Redeem Rewards:</strong> Exchange points for rewards</li>
                    <li><strong>Collect Rewards:</strong> Pick up at Tekun Mart</li>
                </ol>
            </div>
            
            <div class="rewards-grid">
                <?php
                $rewards = [
                    ['name' => 'Free maggie hot cup', 'description' => '1x MAGGIE KARI<br>1x MAGGIE TOMYAM', 'points' => 5000],
                    ['name' => 'GARDENIA BREAD', 'description' => '1x CHOCOLATE<br>1x CORN', 'points' => 500],
                    ['name' => 'FREE TOWEL', 'description' => '1x TOWEL (random colour)', 'points' => 1000],
                    ['name' => 'Free 100 PLUS', 'description' => '1x 100 PLUS (any flavour)', 'points' => 500],
                    ['name' => 'FREE ICE CREAM', 'description' => '1x CUP ICE CREAM', 'points' => 350],
                    ['name' => 'DIY MILO/COFFEE PACKETS', 'description' => '1x MILO PACKET<br>1x COFFEE PACKET', 'points' => 800]
                ];
                
                foreach ($rewards as $reward) {
                    echo '<div class="reward-card">
                        <h3>' . e($reward['name']) . '</h3>
                        <div class="reward-desc">' . $reward['description'] . '</div>
                        <div class="points-required">' . e($reward['points']) . ' points</div>
                        <button class="claim-btn" 
                                data-reward="' . e($reward['name']) . '" 
                                data-points="' . e($reward['points']) . '"
                                ' . ($points < $reward['points'] ? 'disabled' : '') . '>
                            ' . ($points < $reward['points'] ? 'Not Enough Points' : 'REDEEM NOW') . '
                        </button>
                    </div>';
                }
                ?>
            </div>
        </div>
        
        <div class="history-section">
            <h2>Your Reward History</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reward</th>
                            <th>Points</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($redemptions)): ?>
                            <?php foreach ($redemptions as $redemption): ?>
                                <tr>
                                    <td><?= e(date('M j, Y g:i A', strtotime($redemption['redemption_date']))) ?></td>
                                    <td><?= e($redemption['reward_name']) ?></td>
                                    <td><?= e($redemption['points_used']) ?></td>
                                    <td><span class="status-badge <?= e($redemption['status']) ?>"><?= e(ucfirst($redemption['status'])) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No redemption history yet</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <footer>
        <p>Copyright &copy; 2025 DryFans. All rights reserved.  <a href="termscondition.html">Terms And Conditions</a></p>
            
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = '<?php echo e($_SESSION['csrf_token']); ?>';
        
        // Handle reward redemption
        document.querySelectorAll('.claim-btn').forEach(button => {
            button.addEventListener('click', async function() {
                const rewardName = this.dataset.reward;
                const pointsRequired = parseInt(this.dataset.points);
                const currentPoints = parseInt(document.getElementById('points-balance').textContent);
                
                // Client-side validation
                if (currentPoints < pointsRequired) {
                    showAlert(`You need ${pointsRequired - currentPoints} more points for this reward!`, 'error');
                    return;
                }
                
                if (!confirm(`Redeem ${rewardName} for ${pointsRequired} points?`)) {
                    return;
                }
                
                // Disable button during processing
                const originalText = this.textContent;
                this.disabled = true;
                this.textContent = 'Processing...';
                
                    try {
                        const response = await fetch('rewards.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                reward_name: rewardName,
                                points: pointsRequired,
                                csrf_token: csrfToken
                            })
                        });
    
                        const data = await response.json();
    
                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Redemption failed');
                        }
    
                        // Update UI
                        document.getElementById('points-balance').textContent = data.new_balance;
                        addRedemptionToHistory({
                            reward_name: rewardName,
                            points_used: pointsRequired,
                            status: 'pending',
                            redemption_date: new Date().toISOString()
                        });
    
                        showAlert(data.message, 'success');
    
                        // Update button states
                        updateRewardButtons(data.new_balance);
    
                    } catch (error) {
                        console.error('Redemption error:', error);
                        showAlert(error.message, 'error');
                    } finally {
                        this.disabled = false;
                        this.textContent = originalText;
                    }
                });
            });
        
        // Update all reward buttons based on new balance
        function updateRewardButtons(newBalance) {
            document.querySelectorAll('.claim-btn').forEach(button => {
                const pointsNeeded = parseInt(button.dataset.points);
                button.disabled = newBalance < pointsNeeded;
                button.textContent = newBalance < pointsNeeded ? 'Not Enough Points' : 'REDEEM NOW';
            });
        }
        
        // Add new redemption to history table
        function addRedemptionToHistory(redemption) {
            const tbody = document.querySelector('.history-section tbody');
            const newRow = document.createElement('tr');
            
            newRow.innerHTML = `
                <td>${new Date(redemption.redemption_date).toLocaleString()}</td>
                <td>${escapeHtml(redemption.reward_name)}</td>
                <td>${redemption.points_used}</td>
                <td><span class="status-badge success">Success</span></td>
            `;
            
            // Add to top of table
            if (tbody.firstChild) {
                tbody.insertBefore(newRow, tbody.firstChild);
            } else {
                tbody.appendChild(newRow);
            }
        }
        
        // Show alert message
        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert ${type}`;
            alertDiv.textContent = message;
            
            const container = document.querySelector('.container');
            container.prepend(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
        
        // Basic HTML escaping
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    });
    </script>
</body>
</html>