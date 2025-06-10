
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Runner Applications Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success-message {
            color: #4CAF50;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #e8f5e9;
            border-radius: 4px;
        }
        .error-message {
            color: #ff4444;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #ffebee;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: 600;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .status-pending {
            color: #FF9800;
            font-weight: bold;
        }
        .status-approved {
            color: #4CAF50;
            font-weight: bold;
        }
        .status-rejected {
            color: #f44336;
            font-weight: bold;
        }
        select, button {
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        button {
            background-color: #2196F3;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0b7dda;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="success-message">
                ✅ Runner status updated successfully!
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="error-message">
                ❌ Error updating runner status. Please try again.
            </div>
        <?php endif; ?>

        <h1>Runner Applications Management</h1>
        
        <table>
            <thead>
                <tr>
                    <th>Application Date</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Database connection (should be in a separate config file)
                
                $db = new mysqli('localhost', 'root', '', 'server_db');

if ($db->connect_error) {
    die("Database error: " . $db->connect_error);
}
                // Prepare and execute query
                $sql = "SELECT id, name, email, phone, status, created_at 
                        FROM runners
                        ORDER BY 
                            CASE status 
                                WHEN 'pending' THEN 1
                                WHEN 'rejected' THEN 2
                                WHEN 'approved' THEN 3
                                ELSE 4
                            END,
                            created_at DESC";
                
                $result = $db->query($sql);
                
                if ($result->num_rows > 0):
                    while($runner = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= htmlspecialchars(date('M j, Y H:i', strtotime($runner['created_at']))) ?></td>
                    <td><?= htmlspecialchars($runner['name']) ?></td>
                    <td><?= htmlspecialchars($runner['email']) ?></td>
                    <td><?= htmlspecialchars($runner['phone']) ?></td>
                    <td class="status-<?= htmlspecialchars($runner['status']) ?>">
                        <?= ucfirst(htmlspecialchars($runner['status'])) ?>
                    </td>
                    
                    <td>
                        <form method="POST" action="update_runner_status.php">
                            <input type="hidden" name="runner_id" value="<?= htmlspecialchars($runner['id']) ?>">
                            <select name="new_status" class="status-select">
                                <option value="pending" <?= $runner['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="approved" <?= $runner['status'] == 'approved' ? 'selected' : '' ?>>Approve</option>
                                <option value="rejected" <?= $runner['status'] == 'rejected' ? 'selected' : '' ?>>Reject</option>
                            </select>
                            <button type="submit" class="update-btn">Update</button>
                        </form>
                    </td>
                </tr>
                <?php 
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No runner applications found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>