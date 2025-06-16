<?php include 'functions.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Report Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .reports-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            background: rgba(0, 0, 0, 0.8);
            color: white;
        }
        .reports-table th, .reports-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #444;
        }
        .reports-table th {
            background-color: #00AFF0;
            color: white;
        }
        .status-pending { color: #ff9800; }
        .status-in_progress { color: #2196f3; }
        .status-resolved { color: #4caf50; }
        .screenshot-link {
            color: #00AFF0;
            text-decoration: none;
            transition: opacity 0.3s;
        }
        .screenshot-link:hover {
            opacity: 0.8;
        }
        .screenshot-preview {
            max-width: 100px;
            max-height: 100px;
            border-radius: 4px;
        }
        .status-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .status-select {
            padding: 5px;
            background: #333;
            color: white;
            border: 1px solid #555;
            border-radius: 4px;
        }
        .update-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .update-btn:hover {
            background: #45a049;
        }
        .admin-nav {
            background: #222;
            padding: 15px 40px;
            margin-bottom: 20px;
        }
        .admin-nav a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
        }
        .admin-nav a:hover {
            color: #00AFF0;
        }
    </style>
</head>
<body>
    <div class="admin-nav">
        <a href="admin_report.php">Reports Dashboard</a>
        <a href="control_panel.php">Machines Control</a>
        <a href="runner_management.php">Runner management</a>
    </div>

    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="success-message" style="color:#4CAF50; padding:10px; margin-bottom:20px;">
                ✅ Status updated successfully!
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="error-message" style="color:#ff4444; padding:10px; margin-bottom:20px;">
                ❌ Error updating status. Please try again.
            </div>
        <?php endif; ?>

        <h1>Problem Reports</h1>
        
        <table class="reports-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Machine</th>
                    <th>Urgency</th>
                    <th>Status</th>
                    <th>Screenshot</th>
                    <th>Collection Proof</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT r.*, m.name as machine_name 
                        FROM reports r
                        LEFT JOIN machines m ON r.machine_id = m.id
                        ORDER BY 
                            CASE WHEN r.status = 'resolved' THEN 1 ELSE 0 END,
                            CASE r.urgency
                                WHEN 'high' THEN 1
                                WHEN 'medium' THEN 2
                                WHEN 'low' THEN 3
                            END,
                            r.report_date DESC";
                
                $reports = $db->query($sql);
                while($report = $reports->fetch_assoc()):
                ?>
                <tr>
                    <td><?= date('M j, Y H:i', strtotime($report['report_date'])) ?></td>
                    <td><?= htmlspecialchars($report['problem_type']) ?></td>
                    <td><?= htmlspecialchars($report['description']) ?></td>
                    <td><?= !empty($report['machine_name']) ? htmlspecialchars($report['machine_name']) : '-' ?></td>
                    <td><?= ucfirst($report['urgency']) ?></td>
                    <td class="status-<?= $report['status'] ?>">
                        <?= ucfirst(str_replace('_', ' ', $report['status'])) ?>
                    </td>
                    <td>
                        <?php if(!empty($report['screenshot_path'])): ?>
                            <a href="<?= htmlspecialchars($report['screenshot_path']) ?>" 
                               class="screenshot-link" 
                               target="_blank"
                               title="View full image">
                                <img src="<?= htmlspecialchars($report['screenshot_path']) ?>" 
                                     class="screenshot-preview"
                                     alt="Report screenshot">
                            </a>
                        <?php else: ?>
                            <span class="no-screenshot">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if(!empty($report['collection_photo'])): ?>
                            <a href="<?= htmlspecialchars($report['collection_photo']) ?>" 
                               class="screenshot-link" 
                               target="_blank"
                               title="View collection proof">
                                <img src="<?= htmlspecialchars($report['collection_photo']) ?>" 
                                     class="screenshot-preview"
                                     alt="Collection proof">
                            </a>
                        <?php else: ?>
                            <span class="no-screenshot">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form class="status-form" method="POST" action="update_report_status.php">
                            <input type="hidden" name="report_id" value="<?= $report['id'] ?>">
                            <select name="new_status" class="status-select">
                                <option value="pending" <?= $report['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="in_progress" <?= $report['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="resolved" <?= $report['status'] == 'resolved' ? 'selected' : '' ?>>Resolved</option>
                            </select>
                            <button type="submit" class="update-btn">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>