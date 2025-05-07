<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
</head>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Rewards Program</title>
    <link rel="stylesheet" href="rewards.css">
    
</head>
<body>
    <!-- Navi -->
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
        <header>
            
            <h1>DRYFANS Rewards</h1>
            <p>Earn points with every wash and redeem exciting rewards!</p>
        </header>
        
        <div class="user-profile">
            <div class="user-avatar"><?php echo $_SESSION['username']; ?></div>
            <div>
            <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
            <p>you are back with DRYFANS</p>
            <a href="logout.php">Logout</a>
            
            </div>
        </div>
        
        <div class="rewards-section">
            <h2>Redeem your points</h2>
            <p>Claim your rewards using your earned points</p>

            <div class="how-it-works">
                <h2>How It Works</h2>
                <ol>
                    <li><strong>Earn Points:</strong> Get 10 points for every RM1 spent on laundry services</li>
                    <li><strong>Track Your Points:</strong> Your points balance updates after each service</li>
                    <li><strong>Redeem Rewards:</strong> Exchange your points for exciting rewards</li>
                    <li><strong>Collect Rewards:</strong> Get your rewards at Tekun Mart</li>
            </div><br><br>
            
            <div class="rewards-grid">
                <div class="reward-card">
                    <h3>Free maggie hot cup</h3>
                    <P>1x MAGGIE KARI</P>
                    <P>1x MAGGIE TOMYAM </P>
                    <p class="points_required">5000 points</p>
                    <button class="claim-btn">REDEEM NOW</button>
                </div>
                
                <div class="reward-card">
                    <h3>GARDENIA BREAD</h3>
                    <p>1x CHOCOLATE</p>
                    <p>1x CORN</p>
                    <p class="points_required">500 points</p>
                    <button class="claim-btn">REDEEM NOW</button>
                </div>
                
                <div class="reward-card">
                    <h3> FREE TOWEL</h3>
                    <p>1x TOWEL (random colour)</p>
                    <p class="points_required">1000 points</p>
                    <button class="claim-btn">REDEEM NOW</button>
                </div>
                
                <div class="reward-card">
                    <h3>Free 100 PLUS </h3>
                    <p>1x 100 PLUS (can choose any flavour)</p>
                    <p class="points_required">500 points</p>
                    <button class="claim-btn">REDEEM NOW</button>
                </div>
                
                <div class="reward-card">
                    <h3>FREE ICE CREAM</h3>
                    <p>1x CUP ICE CREAM</p>
                    <p class="points_required">350 points</p>
                    <button class="claim-btn">REDEEM NOW</button>
                </div>
                
                <div class="reward-card">
                    <h3>DIY MILO/COFFEE PACKETS</h3>
                    <p>1x MILO PACKET</p>
                    <p>1x COFFEE PACKET</p>
                    <p class="points_required">800 points</p>
                    <button class="claim-btn">REDEEM NOW</button>
                </div>
            </div>
        </div>
        
        <div class="history-section">
    <h2>Your Reward History</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Reward</th>
                <th>Points Used</th>
                <th>Status</th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($points_history as $history): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($history['redemption_date']); ?></td>
                        <td><?php echo htmlspecialchars($history['reward_name']); ?></td>
                        <td><?php echo $history['points_used']; ?></td>
                        <td class="status-<?php echo strtolower($history['status']); ?>">
                            <?php echo ucfirst($history['status']); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        
        <footer>
            
            <div class="ka">
            <p>Copyright &copy; 2025 DryFans. Mini It Project Group 3-01. All rights reserved. <a href="termscondition.html">Terms And Conditions</a></p>
            </div>
        </footer>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const currentPoints = <?php echo $_SESSION['points_balance'] ?? 0; ?>;
        
        document.querySelectorAll('.claim-btn').forEach(button => {
            button.addEventListener('click', function() {
                const rewardName = this.dataset.reward;
                const pointsNeeded = parseInt(this.dataset.points);
                
                if (currentPoints < pointsNeeded) {
                    alert(`You need ${pointsNeeded} points to redeem this reward. You currently have ${currentPoints} points.`);
                    return;
                }
                
                if (confirm(`Redeem ${rewardName} for ${pointsNeeded} points?`)) {
                    const originalText = this.textContent;
                    this.textContent = 'Processing...';
                    this.disabled = true;
                    
                    fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `reward_name=${encodeURIComponent(rewardName)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(`Success! ${data.message} New balance: ${data.new_balance} points`);
                            document.querySelector('.points-display').textContent = `Your Points: ${data.new_balance}`;
                            location.reload(); // Refresh to show updated history
                        } else {
                            alert(`Error: ${data.message}`);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred during redemption');
                    })
                    .finally(() => {
                        this.textContent = originalText;
                        this.disabled = false;
                    });
                }
            });
        });
    });
    </script>
</body>
</html>
