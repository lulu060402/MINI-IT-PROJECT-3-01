<?php
// Start session for messages
session_start();

// Clear messages on page load
if(isset($_GET['success'])) unset($_GET['success']);
if(isset($_GET['error'])) unset($_GET['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report a Problem</title>
    <link rel="stylesheet" href="report.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="bg-img">
        <header class="nav">
            <div class="logo">DRYFANS</div>
            <nav>
              <ul>
                <li><a href="intro.html">Home</a></li>
                <li><a href="aboutus.html">About Us</a></li>
                <li><a href="report.php">Support</a></li>
              </ul>
            </nav>
        </header>

        <div class="report-container">
            <?php if(isset($_GET['success'])): ?>
                <div class="success-message">✅ Report submitted successfully!</div>
            <?php endif; ?>
            
            <?php if(isset($_GET['error'])): ?>
                <div class="error-message">❌ Error submitting report. Please try again.</div>
            <?php endif; ?>
            
            <h1>Report a problem</h1>
            <p>Please fill out the form below to report any issues you're experiencing.</p>
            
            <form action="submit_report.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Username (optional):</label>
                    <input type="text" id="name" name="name" placeholder="Anonymous">
                </div>
                
                <div class="form-group">
                    <label for="email">Email (optional):</label>
                    <input type="email" id="email" name="email" placeholder="example@email.com">
                </div>
                
                <div class="form-group">
                    <label for="problem-type">Problem Type:</label>
                    <select id="problem-type" name="problem_type" required>
                        <option value="">Select a problem type</option>
                        <option value="Washing Machine Issues">Washing Machine Issues</option>
                        <option value="Account Problem">Account Problem</option>
                        <option value="Web Application Bugs">Web Application Bugs</option>
                        <option value="General Feedback">General Feedback</option>
                        <option value="Other">Others</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Problem Description:</label>
                    <textarea id="description" name="description" rows="5" placeholder="Describe the issue in detail..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="urgency">Urgency:</label>
                    <div class="radio-group">
                        <label><input type="radio" name="urgency" value="low" checked> Low</label>
                        <label><input type="radio" name="urgency" value="medium"> Medium</label>
                        <label><input type="radio" name="urgency" value="high"> High</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="screenshot">Upload Screenshot (optional, images only):</label>
                    <input type="file" id="screenshot" name="screenshot" accept="image/*">
                </div>
                
                <button type="submit" class="submit-btn">Submit Report</button>
            </form>
        </div>
    </div>

    <footer>
        <div class="ka">
            <p>Copyright &copy; 2025 DryFans. Mini It Project Group 3-01. All rights reserved. 
            <a href="termscondition.html">Terms And Conditions</a></p>
        </div>
    </footer>
</body>
</html>