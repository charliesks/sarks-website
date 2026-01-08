<?php
require_once __DIR__ . '/../includes/session_ready.php';
require_once __DIR__ . '/../includes/connection.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/stripe_handler.php';
require_once __DIR__ . '/confirm_fulfillment.php';

$session_id = $_GET['session_id'] ?? '';

if (!$session_id) {
    header("Location: ../index.php");
    exit();
}

try {
    $stripe = new StripeHandler(STRIPE_SECRET_KEY);
    $session = $stripe->retrieveSession($session_id);

    if ($session['payment_status'] === 'paid') {
        $order_group_id = $session['client_reference_id'];

        // 1. Update Database Status
        $stmt = $conn->prepare("UPDATE productsorder SET payment_status = 'paid' WHERE stripe_session_id = ?");
        $stmt->bind_param("s", $session_id);
        $stmt->execute();
        $stmt->close();

        // 2. Clear Cart
        unset($_SESSION['cart']);

        // 3. Trigger Fulfillment (if not already handled by webhook)
        fulfill_order($order_group_id);
    }
} catch (Exception $e) {
    error_log("Payment Success Error: " . $e->getMessage());
}

// Redirect back home with success message
echo "<script>alert('Payment Successful! Your guides are on their way to your email.'); window.location.href='../index.php';</script>";
exit();
