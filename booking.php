<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Laundry Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .booking-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
            cursor: pointer;
            border: 2px solid transparent;
        }
        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .booking-card.selected {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }
        .time-slot {
            cursor: pointer;
            transition: all 0.2s;
            border-radius: 5px;
        }
        .time-slot:hover {
            background-color: #e9ecef;
        }
        .time-slot.selected {
            background-color: #0d6efd;
            color: white;
        }
        .time-slot.booked {
            background-color: #f8d7da;
            color: #842029;
            cursor: not-allowed;
        }
        .hostel-block {
            position: relative;
            overflow: hidden;
        }
        .hostel-block::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 100%);
        }
        .hostel-block.selected::after {
            background: linear-gradient(135deg, rgba(13,110,253,0.2) 0%, rgba(13,110,253,0) 100%);
        }
        #bookingSummary {
            position: sticky;
            top: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h1>Hostel Laundry Booking</h1>
                    <p class="lead">Book your washer or dryer slot in advance</p>
                </div>
                
                <!-- Progress Steps -->
                <ul class="nav nav-pills nav-justified mb-5">
                    <li class="nav-item">
                        <a class="nav-link active" id="step1-tab" data-bs-toggle="pill" href="#step1">1. Hostel Block</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" id="step2-tab" data-bs-toggle="pill" href="#step2">2. Date & Time</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" id="step3-tab" data-bs-toggle="pill" href="#step3">3. Service Type</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" id="step4-tab" data-bs-toggle="pill" href="#step4">4. Confirm</a>
                    </li>
                </ul>
                
                <!-- Booking Form -->
                <form id="hostelLaundryForm">
                    <!-- Step 1: Hostel Block Selection -->
                    <div class="tab-pane fade show active" id="step1">
                        <h3 class="mb-4">Select Your Hostel Block</h3>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card hostel-block h-100" data-block="1">
                                    <div class="card-body text-center">
                                        <h2>Block 1</h2>
                                        
                                        <div class="d-flex justify-content-center">
                                            <span class="badge bg-primary me-2">Washers</span>
                                            <span class="badge bg-warning text-dark"> Dryers</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card hostel-block h-100" data-block="2">
                                    <div class="card-body text-center">
                                        <h2>Block 2</h2>
                                       
                                        <div class="d-flex justify-content-center">
                                            <span class="badge bg-primary me-2"> Washers</span>
                                            <span class="badge bg-warning text-dark"> Dryers</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card hostel-block h-100" data-block="3">
                                    <div class="card-body text-center">
                                        <h2>Block 3</h2>
                                        
                                        <div class="d-flex justify-content-center">
                                            <span class="badge bg-primary me-2">Washer</span>
                                            <span class="badge bg-warning text-dark">Dryer</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card hostel-block h-100" data-block="4">
                                    <div class="card-body text-center">
                                        <h2>Block 4</h2>
                                        
                                        <div class="d-flex justify-content-center">
                                            <span class="badge bg-primary me-2"> Washers</span>
                                            <span class="badge bg-warning text-dark">Dryers</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="hostel_block" id="hostelBlock">
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-primary next-step" data-next="step2">Continue</button>
                        </div>
                    </div>
                    
                    <!-- Step 2: Date & Time Selection -->
                    <div class="tab-pane fade" id="step2">
                        <h3 class="mb-4">Select Date & Time</h3>
                        <!-- Date Picker -->
                         <div class="mb-4">
                            <label for="bookingDate" class="form-label">Select Date</label>
                            <input type="text" class="form-control" id="bookingDate" placeholder="Select date">
                        </div>
                        <!-- Time Slots -->
                        <div class="mb-4">
                            <label class="form-label">Available Time Slots (1 hour slots)</label>
                            <div class="row g-4" id="timeSlotsContainer">
                                <!-- Time slots will be dynamically inserted here -->
                            </div>
                        </div>
                        
                        <input type="hidden" name="booking_time" id="bookingTime">
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary prev-step" data-prev="step1">Back</button>
                            <button type="button" class="btn btn-primary next-step" data-next="step3">Continue</button>
                        </div>
                    </div>
                    
                    <!-- Step 3: Service Type Selection -->
<div class="tab-pane fade" id="step3">
    <h3 class="mb-4">Select Service Type</h3> <!-- Reduced mb-4 to mb-3 -->
    
    <div class="row g-3"> <!-- Reduced g-4 to g-3 -->
        <div class="col-md-6">
            <div class="card booking-card" data-service="washer"> 
                <div class="card-body text-center p-3"> <!-- Added p-3 for consistent padding -->
                    <div class="mb-2"> <!-- Reduced mb-3 to mb-2 -->
                    </div>
                    <h4>Washing Machine</h4>
                    <p class="text-muted mb-2">1 hour cycle</p> <!-- Added mb-2 -->
                    <div class="d-flex justify-content-center mb-2"> <!-- Reduced mb-3 to mb-2 -->
                        <span class="badge bg-success">RM3.00</span>
                    </div>
                    <ul class="text-start ps-3 mb-0"> <!-- Reduced ps-4 to ps-3, added mb-0 -->
                        <li>Standard wash cycle</li>
                        <li>Cold water setting</li>
                        <li>Detergent included</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card booking-card" data-service="dryer"> <!-- Removed h-100 -->
                <div class="card-body text-center p-3"> <!-- Added p-3 -->
                    <div class="mb-2">

                    </div>
                    <h4>Dryer</h4>
                    <p class="text-muted mb-2">45 minute cycle</p>
                    <div class="d-flex justify-content-center mb-2">
                        <span class="badge bg-success">RM2.50</span>
                    </div>
                    <ul class="text-start ps-3 mb-0">
                        <li>Standard drying cycle</li>
                        <li>Medium heat setting</li>
                        <li>Automatic shutoff</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <input type="hidden" name="service_type" id="serviceType">
    
    <div class="d-flex justify-content-between mt-3"> <!-- Reduced mt-4 to mt-3 -->
        <button type="button" class="btn btn-outline-secondary prev-step" data-prev="step2">Back</button>
        <button type="button" class="btn btn-primary next-step" data-next="step4">Continue</button>
    </div>
</div>
                    
                    <!-- Step 4: Confirmation -->
                    <div class="tab-pane fade" id="step4">
                        <h3 class="mb-4">Confirm Your Booking</h3>
                        
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Booking Summary</h5>
                                <div id="bookingSummaryContent">
                                    <!-- Summary will be dynamically inserted here -->
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Student Information</h5>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="studentName" placeholder="Full Name" required>
                                <label for="studentName">Full Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="studentId" placeholder="Student ID" required>
                                <label for="studentId">Student ID</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="studentEmail" placeholder="Email" required>
                                <label for="studentEmail">Email</label>
                            </div>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="termsAgreement" required>
                            <label class="form-check-label" for="termsAgreement">
                                I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a>
                            </label>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary prev-step" data-prev="step3">Back</button>
                            <button type="submit" class="btn btn-success">Confirm Booking</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Booking Summary Sidebar -->
            <div class="col-lg-4">
                <div class="card" id="bookingSummary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Booking Summary</h5>
                    </div>
                    <div class="card-body">
                        <div id="dynamicSummary">
                            <p class="text-muted">Your booking details will appear here as you make selections</p>
                        </div>
                        <hr>
                        <div class="d-grid">
                            <button class="btn btn-outline-primary" id="saveForLater">
                                Save for Later
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Laundry Booking Terms</h6>
                    <ol>
                        <li>Bookings must be made at least 2 hours in advance</li>
                        <li>Each time slot is for 1 hour maximum</li>
                        <li>Please remove your laundry promptly when done</li>
                        <li>Late arrivals will lose their time slot</li>
                        <li>RM5 penalty for leaving laundry unattended more than 15 minutes</li>
                        <li>Report any machine issues immediately</li>
                    </ol>
                    <h6 class="mt-4">Cancellation Policy</h6>
                    <p>Cancellations must be made at least 1 hour before the booking time or a penalty of RM2 will be applied to your student account.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="bookingSuccessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Booking Confirmed!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                        <h4 class="mt-3">Your slot has been booked</h4>
                    </div>
                    <div class="alert alert-info">
                        <strong>Booking Reference:</strong> <span id="bookingRef">HLB-<span id="bookingId"></span></span>
                    </div>
                    <p>We've sent the details to your student email. Please arrive on time for your slot.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Print Receipt</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize date picker
            const datePicker = flatpickr("#bookingDate", {
                minDate: "today",
                maxDate: new Date().fp_incr(7), // 1 week from today
                disable: [
                    function(date) {
                        // Disable weekends
                        return (date.getDay() === 0 || date.getDay() === 6);
                    }
                ],
                onChange: function(selectedDates, dateStr, instance) {
                    updateTimeSlots();
                    updateSummary();
                }
            });

            // Sample booked slots data (in real app, this would come from server)
            const bookedSlots = {
                "2023-11-15": ["09:00:00", "11:00:00"],
                "2023-11-16": ["14:00:00", "15:00:00"]
            };

            // Hostel block selection
            const hostelBlocks = document.querySelectorAll('.hostel-block');
            hostelBlocks.forEach(block => {
                block.addEventListener('click', function() {
                    hostelBlocks.forEach(b => b.classList.remove('selected'));
                    this.classList.add('selected');
                    document.getElementById('hostelBlock').value = this.dataset.block;
                    updateSummary();
                });
            });

            // Service type selection
            const serviceCards = document.querySelectorAll('.booking-card[data-service]');
            serviceCards.forEach(card => {
                card.addEventListener('click', function() {
                    serviceCards.forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                    document.getElementById('serviceType').value = this.dataset.service;
                    updateSummary();
                });
            });

            // Step navigation
            document.querySelectorAll('.next-step').forEach(button => {
                button.addEventListener('click', function() {
                    const nextStep = this.dataset.next;
                    const currentTab = this.closest('.tab-pane').id;
                    
                    // Validate before proceeding
                    if (currentTab === 'step1' && !document.getElementById('hostelBlock').value) {
                        alert('Please select your hostel block');
                        return;
                    }
                    
                    if (currentTab === 'step2' && (!document.getElementById('bookingDate').value || !document.getElementById('bookingTime').value)) {
                        alert('Please select a date and time slot');
                        return;
                    }
                    
                    if (currentTab === 'step3' && !document.getElementById('serviceType').value) {
                        alert('Please select a service type');
                        return;
                    }
                    
                    // Switch tabs
                    document.getElementById(currentTab + '-tab').classList.remove('active');
                    document.getElementById(currentTab).classList.remove('show', 'active');
                    
                    document.getElementById(nextStep + '-tab').classList.remove('disabled');
                    document.getElementById(nextStep + '-tab').classList.add('active');
                    document.getElementById(nextStep).classList.add('show', 'active');
                });
            });

            document.querySelectorAll('.prev-step').forEach(button => {
                button.addEventListener('click', function() {
                    const prevStep = this.dataset.prev;
                    const currentTab = this.closest('.tab-pane').id;
                    
                    // Switch tabs
                    document.getElementById(currentTab + '-tab').classList.remove('active');
                    document.getElementById(currentTab).classList.remove('show', 'active');
                    
                    document.getElementById(prevStep + '-tab').classList.add('active');
                    document.getElementById(prevStep).classList.add('show', 'active');
                });
            });

            // Form submission
            document.getElementById('hostelLaundryForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Generate random booking ID
                const bookingId = 'HLB-' + Math.floor(1000 + Math.random() * 9000);
                document.getElementById('bookingId').textContent = bookingId;
                
                // Show success modal
                const successModal = new bootstrap.Modal(document.getElementById('bookingSuccessModal'));
                successModal.show();
                
                // In a real app, you would submit to server here
                console.log('Booking submitted', {
                    hostelBlock: document.getElementById('hostelBlock').value,
                    date: document.getElementById('bookingDate').value,
                    time: document.getElementById('bookingTime').value,
                    service: document.getElementById('serviceType').value,
                    studentInfo: {
                        name: document.getElementById('studentName').value,
                        id: document.getElementById('studentId').value,
                        email: document.getElementById('studentEmail').value
                    }
                });
            });

            // Update time slots based on selected date
            function updateTimeSlots() {
                const timeSlotsContainer = document.getElementById('timeSlotsContainer');
                timeSlotsContainer.innerHTML = '';
                
                const selectedDate = document.getElementById('bookingDate').value;
                if (!selectedDate) return;
                
                // Generate time slots (9AM to 5PM)
                const startHour = 9;
                const endHour = 17;
                
                for (let hour = startHour; hour < endHour; hour++) {
                    const timeString = hour.toString().padStart(2, '0') + ':00:00';
                    const displayTime = hour.toString().padStart(2, '0') + ':00 - ' + (hour + 1).toString().padStart(2, '0') + ':00';
                    
                    // Check if slot is booked
                    const isBooked = bookedSlots[selectedDate] && bookedSlots[selectedDate].includes(timeString);
                    
                    const slotCol = document.createElement('div');
                    slotCol.className = 'col-6 col-md-4 col-lg-3';
                    
                    const slotDiv = document.createElement('div');
                    slotDiv.className = `time-slot p-2 text-center rounded ${isBooked ? 'booked' : ''}`;
                    slotDiv.textContent = displayTime.slice(0, -3); // Remove seconds for display
                    
                    if (!isBooked) {
                        slotDiv.addEventListener('click', function() {
                            document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                            this.classList.add('selected');
                            document.getElementById('bookingTime').value = displayTime;
                            updateSummary();
                        });
                    }
                    
                    slotCol.appendChild(slotDiv);
                    timeSlotsContainer.appendChild(slotCol);
                }
            }

            // Update booking summary
            function updateSummary() {
                const summaryContent = document.getElementById('dynamicSummary');
                let html = '';
                
                const hostelBlock = document.getElementById('hostelBlock').value;
                const bookingDate = document.getElementById('bookingDate').value;
                const bookingTime = document.getElementById('bookingTime').value;
                const serviceType = document.getElementById('serviceType').value;
                
                if (hostelBlock) {
                    html += `<p><strong>Hostel Block:</strong> ${hostelBlock}</p>`;
                }
                
                if (bookingDate) {
                    html += `<p><strong>Date:</strong> ${new Date(bookingDate).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>`;
                }
                
                if (bookingTime) {
                    html += `<p><strong>Time:</strong> ${bookingTime}</p>`;
                }
                
                if (serviceType) {
                    const serviceName = serviceType === 'washer' ? 'Washing Machine' : 'Dryer';
                    const servicePrice = serviceType === 'washer' ? 'RM3.00' : 'RM2.50';
                    html += `<p><strong>Service:</strong> ${serviceName} (${servicePrice})</p>`;
                }
                
                summaryContent.innerHTML = html || '<p class="text-muted">Your booking details will appear here as you make selections</p>';
                
                // Also update the confirmation summary
                document.getElementById('bookingSummaryContent').innerHTML = html || '<p>No details selected yet</p>';
            }
            
            // Initialize summary
            updateSummary();
        });
    </script>
</body>
</html>