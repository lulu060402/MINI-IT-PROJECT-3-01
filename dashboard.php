<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <link rel="stylesheet" href="dashboard.css" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
</head>

<body>

<header class="nav">
        <div class="logo">DRYFANS</div>

        <nav>
          <ul>
            <li><a href="dashboard.html">Home</a></li>
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
            <div class="user-avatar"><?php echo $_SESSION['username']; ?></div>
            <div>
                <h2>Welcome,<?php echo $_SESSION['username']; ?>!</h2>
                <div class="points-display">Your Points: <?php echo $_SESSION['points']; ?></div>
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
            <?php foreach ($redemption_history as $history): ?>
            <tr>
                <td><?php echo $history['redemption_date']; ?></td>
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
        
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get user's current points from PHP session
        const currentPoints = <?php echo $_SESSION['points_balance'] ?? 6000; ?>;
        document.querySelector('.points-display').textContent = `Your Points: ${currentPoints}`;
        
        document.querySelectorAll('.claim-btn').forEach(button => {
            button.addEventListener('click', function() {
                const rewardCard = this.closest('.reward-card');
                const rewardName = rewardCard.querySelector('h3').textContent;
                const pointsNeeded = parseInt(rewardCard.querySelector('.points_required').textContent);
                
                // Check if user has enough points
                if (currentPoints < pointsNeeded) {
                    alert(`You don't have enough points to redeem ${rewardName}. You need ${pointsNeeded} points but only have ${currentPoints}.`);
                    return;
                }
                
                if(confirm(`Are you sure you want to claim "${rewardName}" for ${pointsNeeded} points?`)) {
                    // Show loading state
                    const originalText = this.textContent;
                    this.textContent = 'Processing...';
                    this.disabled = true;
                    
                    // Send AJAX request
                    fetch('process_redemption.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `reward_name=${encodeURIComponent(rewardName)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            alert(`Success! You've claimed "${rewardName}". Your new balance is ${data.new_balance} points.`);
                            // Update points display
                            document.querySelector('.points-display').textContent = `Your Points: ${data.new_balance}`;
                            // Add new redemption to history table
                            addToHistoryTable(rewardName, pointsNeeded, 'Redeemed');
                        } else {
                            alert(`Error: ${data.message}`);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while processing your request.');
                    })
                    .finally(() => {
                        this.textContent = originalText;
                        this.disabled = false;
                    });
                }
            });
        });
        
        // Function to add new redemption to history table
        function addToHistoryTable(rewardName, pointsUsed, status) {
            const now = new Date();
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            const redemptionDate = now.toLocaleDateString('en-US', options);
            
            const historyTable = document.querySelector('.history-section tbody');
            const newRow = document.createElement('tr');
            
            newRow.innerHTML = `
                <td>${redemptionDate}</td>
                <td>${rewardName}</td>
                <td>${pointsUsed}</td>
                <td class="status-${status.toLowerCase()}">${status}</td>
            `;
            
            // Insert at the top of the table
            historyTable.insertBefore(newRow, historyTable.firstChild);
        }
    });
</script>
</body>
<footer>
            
            <div class="ka">
            <p>Copyright &copy; 2025 DryFans. Mini It Project Group 3-01. All rights reserved. <a href="termscondition.html">Terms And Conditions</a></p>
            </div>
        </footer>
</html>
