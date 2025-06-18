 <?php
session_start();  // Add this at the very top
?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>LaundryRunner - Hostel Laundry Services</title>

<style>

:root {

--primary: #00AFF0;

--secondary:  #018CF1;

--accent: #4fc3f7;

--light: #f8f9fa;

--dark: #212529;

}

body {

font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

line-height: 1.6;

color: var(--dark);

background-color: #f5f5f5;

margin: 0;

padding: 0;

}

.container {

max-width: 1200px;

margin: 0 auto;

padding: 20px;

}

header {

background-color: #018CF1;

color: white;

padding: 20px 0;

text-align: center;

border-radius: 0 0 10px 10px;

box-shadow: 0 4px 6px rgba(0,0,0,0.1);

}

h1 {

margin: 0;

font-size: 2.5rem;

}

.tagline {

font-style: italic;

margin-top: 10px;

}

.service-cards {

display: flex;

flex-wrap: wrap;

justify-content: center;

gap: 30px;

margin: 40px 0;

}

.card {

background: white;

border-radius: 10px;

box-shadow: 0 4px 8px rgba(0,0,0,0.1);

width: 350px;

padding: 25px;

transition: transform 0.3s ease;

}

.card:hover {

transform: translateY(-10px);

}

.card h2 {

color: var(--secondary);

border-bottom: 2px solid var(--accent);

padding-bottom: 10px;

margin-top: 0;

}

.price {

font-size: 1.8rem;

font-weight: bold;

color: var(--primary);

margin: 15px 0;

}

.btn {

display: inline-block;

background-color: var(--secondary);

color: white;

padding: 12px 25px;

border: none;

border-radius: 5px;

cursor: pointer;

text-decoration: none;

font-weight: bold;

transition: background-color 0.3s;

width: 100%;

text-align: center;

}

.btn:hover {

background-color: var(--primary);

}

.btn-runner {

background-color: #28a745;

}

.btn-runner:hover {

background-color: #218838;

}

.features {

margin: 20px 0;

}

.feature-item {

margin-bottom: 10px;

display: flex;

align-items: center;

}

.feature-item:before {

content: "✓";

color: #28a745;

font-weight: bold;

margin-right: 10px;

}

.modal {

display: none;

position: fixed;

z-index: 1;

left: 0;

top: 0;

width: 100%;

height: 100%;

background-color: rgba(0,0,0,0.5);

}

.modal-content {

background-color: white;

margin: 10% auto;

padding: 30px;

border-radius: 10px;

width: 80%;

max-width: 600px;

box-shadow: 0 5px 15px rgba(0,0,0,0.3);

position: relative;

}

.close {

position: absolute;

top: 15px;

right: 25px;

font-size: 28px;

font-weight: bold;

color: #aaa;

cursor: pointer;

}

.close:hover {

color: var(--dark);

}

form {

display: flex;

flex-direction: column;

gap: 15px;

}

.form-group {

display: flex;

flex-direction: column;

}

label {

margin-bottom: 5px;

font-weight: 600;

}

input, select, textarea {

padding: 10px;

border: 1px solid #ddd;

border-radius: 5px;

font-size: 16px;

}

.form-actions {

display: flex;

justify-content: flex-end;

gap: 10px;

margin-top: 20px;

}

.return-container {

text-align: center;

}

.return-btn {

display: inline-block;

padding: 10px 20px;

background-color: #00AFF0;

color: white;

text-decoration: none;

border-radius: 5px;

transition: background-color 0.3s;

}

.return-btn:hover {

background-color: #018CF1;

}

.error {

color: #dc3545;

font-size: 0.875em;

margin-top: 0.25rem;

height: 18px;

}

input:invalid, select:invalid {

border-color: #dc3545;

}

input:valid, select:valid {

border-color: #28a745;

}

@media (max-width: 768px) {

.service-cards {

flex-direction: column;

align-items: center;

}

.card {

width: 90%;

}

}

footer {

background: linear-gradient(90deg,#2ec7ff 10%, #09b5f4 50%, #00bbff 70%,#018CF1 100%);

color: #ffff;

text-align: center;

padding: 20px 40px;

font-size: 15px;

}

</style>

</head>

<body>

<header>

<div class="container">

<h1>DRYFANSRunner</h1>

<p class="tagline">We run so you don't have to!</p>

</div>

</header>

<div class="container">

<div class="service-cards">

<div class="card">

<h2>Lazy Laundry Service</h2>

<p>Too busy (or lazy) to deal with your laundry? Our runners will handle everything from pickup to delivery!</p>

<div class="price">RM3 per load</div>

<div class="features">

<div class="feature-item">Pickup from your room</div>

<div class="feature-item">Wash, dry, and fold</div>

<div class="feature-item">Delivery back to you</div>

</div><br>

<button id="lazyBtn" class="btn">Book Runner Service</button>

</div>

<div class="card">

<h2>Become a Runner</h2>

<p>Want to earn some extra cash? Sign up to be a laundry runner and help your fellow hostel mates!</p>

<div class="price">Earn RM3 per delivery</div>

<div class="features">

<div class="feature-item">Flexible hours</div>

<div class="feature-item">Work around your schedule</div>

<div class="feature-item">Easy payments</div>

<div class="feature-item">Help your community</div>

</div>

<button id="runnerBtn" class="btn btn-runner">Sign Up as Runner</button>

</div>

</div>

</div>

<!-- Lazy Service Modal -->

<div id="lazyModal" class="modal">

<div class="modal-content">

<span class="close">&times;</span>

<h2>Book Lazy Laundry Service</h2>

<form id="lazyForm" method="POST" action="booking_table.php" novalidate>

<div class="form-group">

<label for="name">Full Name</label>
                <input type="text" id="name" name="name" required minlength="2" maxlength="50"
                    value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>"
                    <?php echo isset($_SESSION['username']) ? 'readonly' : ''; ?>>
                <div class="error" id="name-error"></div>
            </div>

<div class="form-group">

<label for="room">Hostel Room Number</label>

<input type="text" id="room" name="room" required pattern="[A-Za-z0-9\-]+" title="Alphanumeric characters and hyphens only">

<div class="error" id="room-error"></div>

</div>

<div class="form-group">

<label for="phone">Phone Number</label>

<input type="tel" id="phone" name="phone" required

pattern="^(\+?6?01)[0-46-9]-*[0-9]{7,8}$"

title="Malaysian phone number format (e.g., 0123456789 or +60123456789)">

<div class="error" id="phone-error"></div>

</div>

<div class="form-group">

<label for="service_type">Service type</label>

<select id="service_type" name="service_type" required>

<option value="">Select service type</option>

<option value="washer">washer</option>

<option value="dryer">dryer</option>

</select>

<div class="error" id="service_type-error"></div>

</div>

<div class="form-group">

<label for="payment_method">Preferred Payment Method</label>

<select id="payment_method" name="payment_method" required>

<option value="">Select payment method</option>

<option value="Cash">Cash</option>

</select>

<div class="error" id="payment_method-error"></div>

</div>

<div class="form-actions">

<button type="button" class="btn" onclick="document.getElementById('lazyModal').style.display='none'">Cancel</button>

<button type="submit" class="btn">Confirm Booking</button>

</div>

</form>

</div>

</div>

<!-- Runner Signup Modal -->

<div id="runnerModal" class="modal">

<div class="modal-content">

<span class="close">&times;</span>

<h2>Sign Up as Laundry Runner</h2>

<form id="runnerForm" method="post" action="process_runner.php">

<div class="form-group">

<label for="runner-name">Full Name</label>

 <input type="text" id="name" name="name" required minlength="2" maxlength="50"
                    value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>"
                    <?php echo isset($_SESSION['username']) ? 'readonly' : ''; ?>>

</div>

<div class="form-group">

<label for="runner-email">Email</label>

<input type="email" id="runner-email" name="runner-email"required>

</div>

<div class="form-group">

<label for="runner-phone">Phone Number</label>

<input type="tel" id="phone" name="phone" required

pattern="^(\+?6?01)[0-46-9]-*[0-9]{7,8}$"

title="Malaysian phone number format (e.g., 0123456789 or +60123456789)">

<div class="error" id="phone-error"></div>

</div>

<div class="form-group">

<label for="runner-room">Hostel Room Number</label>

<input type="text" id="runner-room" name="runner-room"required>

</div>

<div class="form-group">

<label for="runner-availability">Availability</label>

<select id="runner-availability" name="runner-availability[]" multiple>

<option value="morning">Morning (8am-12pm)</option>

<option value="afternoon">Afternoon (12pm-4pm)</option>

<option value="evening">Evening (4pm-8pm)</option>

<option value="late">Late Night (8pm-12am)</option>

</select>

</div>

<div class="form-actions">

<button type="button" class="btn" onclick="document.getElementById('runnerModal').style.display='none'">Cancel</button>

<button type="submit" class="btn btn-runner">Submit Application</button>

</div>

</form>

</div>

</div>

<div class="button-container" style="display: flex; justify-content: center; gap: 20px; margin: 20px 0;">

<a href="dashboard.php" class="return-btn">Return</a>

<a href="runner_login.php" class="return-btn" style="background-color: #28a745;">View Task</a>

</div>

<footer>

<p>Copyright &copy; 2025 DryFans. All rights reserved.  <a href="termscondition.html">Terms And Conditions</a></p>

</footer>

<script>

// Modal handling

const lazyModal = document.getElementById('lazyModal');

const runnerModal = document.getElementById('runnerModal');

const lazyBtn = document.getElementById('lazyBtn');

const runnerBtn = document.getElementById('runnerBtn');

const spans = document.getElementsByClassName('close');

lazyBtn.onclick = () => lazyModal.style.display = "block";

runnerBtn.onclick = () => runnerModal.style.display = "block";

spans[0].onclick = () => lazyModal.style.display = "none";

spans[1].onclick = () => runnerModal.style.display = "none";

window.onclick = (event) => {

if (event.target == lazyModal) lazyModal.style.display = "none";

if (event.target == runnerModal) runnerModal.style.display = "none";

};

// Form validation and submission

document.getElementById('lazyForm').addEventListener('submit', async (e) => {

e.preventDefault();

// Clear previous errors

document.querySelectorAll('.error').forEach(el => el.textContent = '');

// Validate fields

const fields = [

{ id: 'name', validate: validateName },

{ id: 'room', validate: validateRoom },

{ id: 'phone', validate: validatePhone },

{ id: 'service_type', validate: validateSelect },

{ id: 'payment_method', validate: validateSelect }

];

let isValid = true;

fields.forEach(field => {

const element = document.getElementById(field.id);

const errorElement = document.getElementById(`${field.id}-error`);

const { valid, message } = field.validate(element);

if (!valid) {

errorElement.textContent = message;

isValid = false;

}

});

if (!isValid) return;

// Submit form

try {

const response = await fetch('booking_table.php', {

method: 'POST',

body: new FormData(e.target)

});

const data = await response.json();

if (!response.ok) {

throw new Error(data.message || 'Server error');

}

alert(data.message);

lazyModal.style.display = "none";

e.target.reset();

} catch (error) {

console.error('Submission error:', error);

alert(error.message || 'Failed to submit. Please try again.');

}

});

// Validation functions

function validateName(element) {

const value = element.value.trim();

if (!value) return { valid: false, message: 'Name is required' };

if (value.length < 2) return { valid: false, message: 'Name must be at least 2 characters' };

return { valid: true };

}

function validateRoom(element) {

const value = element.value.trim();

if (!value) return { valid: false, message: 'Room number is required' };

if (!/^[A-Za-z0-9-]+$/.test(value)) {

return { valid: false, message: 'Only alphanumeric characters and hyphens allowed' };

}

return { valid: true };

}

function validatePhone(element) {

const value = element.value.trim();

const phoneRegex = /^(\+?6?01)[0-46-9]-*[0-9]{7,8}$/;

if (!value) return { valid: false, message: 'Phone number is required' };

if (!phoneRegex.test(value)) {

return { valid: false, message: 'Please enter a valid Malaysian phone number' };

}

return { valid: true };

}

function validateSelect(element) {

if (!element.value) return { valid: false, message: 'This field is required' };

return { valid: true };

}

// Real-time phone validation

document.getElementById('phone').addEventListener('input', function() {

const errorElement = document.getElementById('phone-error');

const { valid, message } = validatePhone(this);

errorElement.textContent = valid ? '' : message;

});

// Runner form handling

// Replace your current runner form handler with this:

document.getElementById('runnerForm').addEventListener('submit', async (e) => {

e.preventDefault();

try {

const formData = new FormData(e.target);

// Add validation if needed here

const response = await fetch('booking_table.php', {

method: 'POST',

body: formData

});

const data = await response.json();

if (data.success) {

alert(data.message);

runnerModal.style.display = "none";

e.target.reset();

} else {

throw new Error(data.message);

}

} catch (error) {

console.error('Error:', error);

alert(error.message || 'Failed to submit application');

}

});

</script>

</body>

</html>