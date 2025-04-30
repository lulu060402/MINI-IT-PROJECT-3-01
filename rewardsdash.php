<?php
   ;
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Rewards Program</title>
    <link rel="stylesheet" href="rewards.css">
    
</head>
<body>
    <!-- Nav Bar -->
    <header class="nav">
        <div class="logo">DRYFANS</div>
        <nav>
          <ul>
            <li><a href="rewards.html">Home</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="support.php">Support</a></li>
          </ul>
        </nav>
    </header>
    
    <div class="container">
        <header>
            
            <h1>DRYFANS Rewards</h1>
            <p>Earn points with every wash and redeem exciting rewards!</p>
        </header>
        
        <div class="user-profile">
            <div class="user-avatar">USER</div>
            <div>
                <h2>Welcome, USER!</h2>
                <div class="points-display">Your Points: 1260</div>
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
                    <p class="reward-points">5000 points</p>
                    <button class="claim-btn">REDEEM NOW</button>
                </div>
                
                <div class="reward-card">
                    <h3>GARDENIA BREAD</h3>
                    <p>1x CHOCOLATE</p>
                    <p>1x CORN</p>
                    <p class="reward-points">500 points</p>
                    <button class="claim-btn">REDEEM NOW</button>
                </div>
                
                <div class="reward-card">
                    <h3> FREE TOWEL</h3>
                    <p>1x TOWEL (random colour)</p>
                    <p class="reward-points">1000 points</p>
                    <button class="claim-btn">REDEEM NOW</button>
                </div>
                
                <div class="reward-card">
                    <h3>Free 100 PLUS </h3>
                    <p>1x 100 PLUS (can choose any flavour)</p>
                    <p class="reward-points">500 points</p>
                    <button class="claim-btn">REDEEM NOW</button>
                </div>
                
                <div class="reward-card">
                    <h3>FREE ICE CREAM</h3>
                    <p>1x CUP ICE CREAM</p>
                    <p class="reward-points">350 points</p>
                    <button class="claim-btn">REDEEM NOW</button>
                </div>
                
                <div class="reward-card">
                    <h3>DIY MILO/COFFEE PACKETS</h3>
                    <p>1x MILO PACKET</p>
                    <p>1x COFFEE PACKET</p>
                    <p class="reward-points">800 points</p>
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
                    <tr>
                        <td>March 15, 2023</td>
                        <td>10% Discount</td>
                        <td>300</td>
                        <td>Redeemed</td>
                    </tr>
                    <tr>
                        <td>February 28, 2023</td>
                        <td>Free Detergent</td>
                        <td>500</td>
                        <td>Redeemed</td>
                    </tr>
                    <tr>
                        <td>January 10, 2023</td>
                        <td>Free Delivery</td>
                        <td>400</td>
                        <td>Expired</td>
                    </tr>
                </tbody>
            </table>
        </div><br><br>
        
        
        <footer>
            
            <div class="ka">
            <p>Copyright &copy; 2025 DryFans. Mini It Project Group 3-01. All rights reserved. <a href="termscondition.html">Terms And Conditions</a></p>
            </div>
        </footer>
    </div>
    
    <script>
        // Simple JavaScript to handle reward claiming
        document.querySelectorAll('.claim-btn').forEach(button => {
            button.addEventListener('click', function() {
                const rewardCard = this.closest('.reward-card');
                const rewardName = rewardCard.querySelector('h3').textContent;
                const pointsNeeded = rewardCard.querySelector('.reward-points').textContent;
                
                if(confirm(`Are you sure you want to claim "${rewardName}" for ${pointsNeeded}?`)) {
                    alert(`Congratulations! You've successfully claimed "${rewardName}".`);
                    // In a real application, you would send this to your backend
                }
            });
        });
    </script>
</body>
</html>