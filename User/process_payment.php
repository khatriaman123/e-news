<?php
// Assuming the use of a payment gateway like Stripe or PayPal API

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan = $_POST['plan'];
    $payment_method = $_POST['payment_method'];

    // Perform payment logic based on the selected method

    if ($payment_method === 'credit_card') {
        // Handle credit card payment (using payment gateway API)
    } elseif ($payment_method === 'paypal') {
        // Handle PayPal payment
    } elseif ($payment_method === 'bank_transfer') {
        // Handle bank transfer
    }

    // After payment success, redirect or show success message
    echo "Thank you for subscribing to the $plan plan!";
}
?>
