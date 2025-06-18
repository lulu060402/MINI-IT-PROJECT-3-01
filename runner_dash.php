<?php
session_start(); // Make sure session is started
// Redirect to login if runner is not logged in
if (!isset($_SESSION['runner_id'])) {
header("Location: runner_login.php");
exit();
}
// Check for session messages
if (isset($_SESSION['error'])) {
$error = $_SESSION['error'];
unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
$success = $_SESSION['success'];
unset($_SESSION['success']);
}
// Database connection
$DB_CONFIG = [
'host' => 'localhost',
'user' => 'root',
'password' => '',
'database' => 'server_db',
'charset' => 'utf8mb4'
];

try {

$conn = new mysqli(

$DB_CONFIG['host'],

$DB_CONFIG['user'],

$DB_CONFIG['password'],

$DB_CONFIG['database']

);

if ($conn->connect_error) {

throw new Exception("Database connection failed: {$conn->connect_error}");

}
// Fetch runner's name for display

if (isset($_SESSION['runner_id'])) {

$stmt = $conn->prepare("SELECT name FROM runners WHERE id = ?");

$stmt->bind_param("i", $_SESSION['runner_id']);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

$runner = $result->fetch_assoc();

$runner_name = $runner['name'];

} else {

$runner_name = "Runner"; // default

}

$stmt->close();

} else {

// Should not happen because we redirect if not logged in, but set default

$runner_name = "Guest";

}

// Set charset
$conn->set_charset($DB_CONFIG['charset']);

// Get bookings based on current tab

$active_tab = $_GET['tab'] ?? 'available';

$bookings = [];

if ($active_tab === 'available') {

// Get available bookings (pending and not assigned to anyone)

$stmt = $conn->prepare("
    SELECT b.id, b.username AS customer_name, b.room, b.phone, 
           b.service_type, b.payment_method, b.status, b.created_at,
           r.name AS runner_name
    FROM bookings b
    LEFT JOIN runners r ON b.runner_id = r.id
    WHERE b.status = 'pending' AND b.runner_id IS NULL
    ORDER BY b.created_at ASC
");

$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

$bookings[] = $row;

}

$stmt->close();

}

elseif ($active_tab === 'assigned') {

// Get bookings assigned to current runner (confirmed or completed status)

if (isset($_SESSION['runner_id'])) {

$stmt = $conn->prepare("
    SELECT b.id, b.username AS customer_name, b.room, b.phone, 
           b.service_type, b.payment_method, b.status, b.created_at,
           b.runner_id,
           r.name AS runner_name
    FROM bookings b
    LEFT JOIN runners r ON b.runner_id = r.id
    WHERE b.runner_id = ? AND b.status = 'confirmed'
    ORDER BY b.created_at ASC
");

$stmt->bind_param("i", $_SESSION['runner_id']);

$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

$bookings[] = $row;

}

$stmt->close();

}

}

elseif ($active_tab === 'completed') {

	// Get completed bookings by current runner

	if (isset($_SESSION['runner_id'])) {

		$stmt = $conn->prepare("
    SELECT b.id, b.username AS customer_name, b.room, b.phone, 
           b.service_type, b.payment_method, b.status, b.created_at,
           b.updated_at, r.name AS runner_name
    FROM bookings b
    LEFT JOIN runners r ON b.runner_id = r.id
    WHERE b.runner_id = ? AND b.status = 'completed'
    ORDER BY b.updated_at DESC
");

		$stmt->bind_param("i", $_SESSION['runner_id']);

		$stmt->execute();

		$result = $stmt->get_result();

		while ($row = $result->fetch_assoc()) {
			$bookings[] = $row;
		}

		$stmt->close();

	}

}
} catch (Exception $e) {

	$error = "Database error: " . $e->getMessage();

}

// Handle form submissions

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

// Start output buffering to prevent headers already sent error

ob_start();

if (isset($_POST['accept_booking'])) {

try {

// Verify session first

if (!isset($_SESSION['runner_id'])) {

throw new Exception("Runner not logged in");

}

$booking_id = intval($_POST['booking_id']);

// Start transaction to prevent race conditions

$conn->begin_transaction();

// First check if booking is still available with FOR UPDATE lock

$check_stmt = $conn->prepare("SELECT status, runner_id FROM bookings WHERE id = ? FOR UPDATE");

$check_stmt->bind_param("i", $booking_id);

$check_stmt->execute();

$check_result = $check_stmt->get_result();

$booking_data = $check_result->fetch_assoc();

$check_stmt->close();

if ($booking_data && $booking_data['status'] === 'pending' && $booking_data['runner_id'] === null) {

// If still available, try to claim it

$stmt = $conn->prepare("

UPDATE bookings

SET runner_id = ?, status = 'confirmed', updated_at = NOW()

WHERE id = ? AND status = 'pending'

");

$stmt->bind_param("ii", $_SESSION['runner_id'], $booking_id);

$stmt->execute();

if ($stmt->affected_rows > 0) {

$_SESSION['success'] = "Booking accepted successfully!";

} else {

$_SESSION['error'] = "Booking was already claimed by another runner.";

}

$stmt->close();

} else {

$_SESSION['error'] = "Booking is no longer available.";

}

$conn->commit();

// Clear output buffer and redirect

ob_end_clean();

header("Location: runner_dash.php");

exit();

} catch (Exception $e) {

$conn->rollback();

$_SESSION['error'] = "Error accepting booking: " . $e->getMessage();

ob_end_clean();

header("Location: runner_dash.php");

exit();

}

}

if (isset($_POST['complete_booking'])) {

try {

// Verify session first

if (!isset($_SESSION['runner_id'])) {

throw new Exception("Runner not logged in");

}

$booking_id = intval($_POST['booking_id']);

$stmt = $conn->prepare("

UPDATE bookings

SET status = 'completed', updated_at = NOW()

WHERE id = ? AND runner_id = ? AND status = 'confirmed'

");

$stmt->bind_param("ii", $booking_id, $_SESSION['runner_id']);

$stmt->execute();

if ($stmt->affected_rows > 0) {

$_SESSION['success'] = "Booking marked as completed!";

} else {

$_SESSION['error'] = "Failed to complete booking. It may have been already completed or doesn't belong to you.";

}

$stmt->close();

ob_end_clean();

header("Location: runner_dash.php");

exit();

} catch (Exception $e) {

$_SESSION['error'] = "Error completing booking: " . $e->getMessage();

ob_end_clean();

header("Location: runner_dash.php");

exit();

}

}

}

$conn->close();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Runner Dashboard - DRYFANSRunner</title>

<style>

body {

font-family:'Montserrat', sans-serif;

line-height: 1.6;

margin: 0;

padding: 0;

background-color: #f5f5f5;

color: #333;

}

.container {

width: 90%;

max-width: 1200px;

margin: 0 auto;

padding: 20px;

}

header {

background-color: #2c3e50;

color: white;

padding: 20px 0;

margin-bottom: 30px;

position: relative;

}
.runner-info {
    position: absolute;
    top: 20px;
    right: 20px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    color: white;
}

.runner-info span {
    font-weight: bold;
    margin-bottom: 5px;
}

.runner-info .return-link {
    color: white;
    text-decoration: none;
    padding: 5px 10px;
    background-color: #3498db;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.runner-info .return-link:hover {
    background-color: #2980b9;
}

h1, h2 {

margin: 0;

}


.logout-link {

position: absolute;

top: 20px;

right: 20px;

color: white;

text-decoration: none;

}

.tabs {

display: flex;

margin-bottom: 20px;

border-bottom: 1px solid #ddd;

}

.tab {

padding: 10px 20px;

cursor: pointer;

background: #f1f1f1;

margin-right: 5px;

border-radius: 5px 5px 0 0;

transition: all 0.3s;

}

.tab.active {

background: #3498db;

color: white;

}

.tab:hover:not(.active) {

background: #ddd;

}

.booking-card {

background: white;

border-radius: 8px;

box-shadow: 0 2px 10px rgba(0,0,0,0.1);

padding: 20px;

margin-bottom: 20px;

}

.booking-header {

display: flex;

justify-content: space-between;

align-items: center;

margin-bottom: 15px;

padding-bottom: 10px;

border-bottom: 1px solid #eee;

}

.booking-details {

display: grid;

grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));

gap: 15px;

}

.detail-group label {

font-weight: bold;

display: block;

margin-bottom: 5px;

color: #7f8c8d;

}

.btn {

background-color: #3498db;

color: white;

border: none;

padding: 8px 15px;

border-radius: 4px;

cursor: pointer;

font-size: 14px;

transition: background-color 0.3s;

}

.btn:hover {

background-color: #2980b9;

}

.btn-complete {

background-color: #2ecc71;

}

.btn-complete:hover {

background-color: #27ae60;

}

.status-pending {

color: #e67e22;

font-weight: bold;

}

.status-confirmed {

color: #3498db;

font-weight: bold;

}

.status-completed {

color: #2ecc71;

font-weight: bold;

}

.alert {

padding: 15px;

margin-bottom: 20px;

border-radius: 4px;

}

.alert-success {

background-color: #d4edda;

color: #155724;

}

.alert-error {

background-color: #f8d7da;

color: #721c24;

}

.no-bookings {

text-align: center;

padding: 40px;

background: white;

border-radius: 8px;

box-shadow: 0 2px 10px rgba(0,0,0,0.1);

}

</style>

</head>

<body>

<header>
    <div class="container">
        <h1>DRYFANSRunner</h1>
        <p>Runner Dashboard</p>
        <div class="runner-info">
            <span>Welcome, <?php echo htmlspecialchars($runner_name); ?></span>
            <a href="booking.php" class="return-link">Return</a>
        </div>
    </div>
</header>

<div class="container">

<?php if (isset($error)): ?>

<div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>

<?php endif; ?>

<?php if (isset($success)): ?>

<div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>

<?php endif; ?>

<div class="tabs">

<a href="?tab=available" class="tab <?php echo $active_tab === 'available' ? 'active' : ''; ?>">Available Bookings</a>

<a href="?tab=assigned" class="tab <?php echo $active_tab === 'assigned' ? 'active' : ''; ?>">My Assignments (<?php echo $active_tab === 'assigned' ? count($bookings) : 0; ?>)</a>

<a href="?tab=completed" class="tab <?php echo $active_tab === 'completed' ? 'active' : ''; ?>">Completed Jobs</a>

</div>

<h2>

<?php

echo match($active_tab) {

'available' => 'Available Bookings',

'assigned' => 'Your Current Assignments',

'completed' => 'Completed Jobs History',

default => 'Bookings'

};

?>

</h2>

<?php if (empty($bookings)): ?>

<div class="no-bookings">

<p>

<?php

echo match($active_tab) {

'available' => 'No available bookings at the moment. Please check back later.',

'assigned' => 'You have no current assignments.',

'completed' => 'No completed jobs yet.',

default => 'No bookings found.'

};

?>

</p>

</div>

<?php else: ?>

<?php foreach ($bookings as $booking): ?>

<div class="booking-card">

<div class="booking-header">

<h3>Booking #<?php echo $booking['id']; ?></h3>

<span class="status-<?php echo $booking['status']; ?>">

<?php echo ucfirst($booking['status']); ?>

<?php if ($booking['runner_name'] && $active_tab !== 'completed'): ?>

(<?php echo htmlspecialchars($booking['runner_name']); ?>)

<?php endif; ?>

</span>

</div>

<div class="booking-details">

<div class="detail-group">
    <label>Customer Name</label>
    <div><?php echo htmlspecialchars($booking['customer_name']); ?></div>
</div>

<div class="detail-group">

<label>Room Number</label>

<div><?php echo htmlspecialchars($booking['room']); ?></div>

</div>

<div class="detail-group">

<label>Phone</label>

<div><?php echo htmlspecialchars($booking['phone']); ?></div>

</div>

<div class="detail-group">

<label>Service Type</label>

<div><?php echo ucfirst($booking['service_type']); ?></div>

</div>

<div class="detail-group">

<label>Payment Method</label>

<div><?php echo htmlspecialchars($booking['payment_method']); ?></div>

</div>

<div class="detail-group">

<label>Requested On</label>

<div><?php echo date('M j, Y g:i A', strtotime($booking['created_at'])); ?></div>

</div>

<?php if ($active_tab === 'completed'): ?>

<div class="detail-group">

<label>Completed On</label>

<div><?php echo date('M j, Y g:i A', strtotime($booking['updated_at'])); ?></div>

</div>

<?php endif; ?>

</div>

<div class="booking-actions">

<?php if ($active_tab === 'available' && $booking['status'] === 'pending'): ?>

<form method="post" style="display: inline;">

<input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">

<button type="submit" name="accept_booking" class="btn">Accept Booking</button>

</form>

<?php elseif ($active_tab === 'assigned' && $booking['status'] === 'confirmed' && $booking['runner_id'] == $_SESSION['runner_id']): ?>

<form method="post" style="display: inline;">

<input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">

<button type="submit" name="complete_booking" class="btn btn-complete">Mark as Completed</button>

</form>

<?php endif; ?>

</div>

</div>

<?php endforeach; ?>

<?php endif; ?>

</div>

</body>

</html>