<?php
session_start();

// Get the top-up amount and payment method
$amount = floatval($_POST['amount']);
$payment_method = $_POST['payment_method'];

// Check if the amount is valid
if ($amount > 0) {
    // Process based on payment method
    switch ($payment_method) {
        case 'bank_transfer':
            $_SESSION['message'] = "Successfully topped up RM " . number_format($amount, 2) . " via Bank Transfer!";
            break;
        case 'grabpay':
            $_SESSION['message'] = "Successfully topped up RM " . number_format($amount, 2) . " via GrabPay!";
            break;
        case 'paypal':
            $_SESSION['message'] = "Successfully topped up RM " . number_format($amount, 2) . " via PayPal!";
            break;
        default:
            $_SESSION['message'] = "Invalid payment method.";
            break;
    }

    // Update balance
    $_SESSION['balance'] += $amount;
} else {
    $_SESSION['message'] = "Invalid top-up amount.";
}

// Redirect back to the top-up page
header("Location: index.php");
exit();
