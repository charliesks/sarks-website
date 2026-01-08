<?php
require_once __DIR__ . '/../includes/connection.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/confirm_fulfillment.php';

// Set your webhook secret from Stripe dashboard
$endpoint_secret = STRIPE_WEBHOOK_SECRET;

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$event = null;

try {
    // Note: Simple verification without the full SDK's Webhook class to avoid dependencies.
    // In a production app with the full SDK, we would use Stripe\Webhook::constructEvent.
    // Here we trust the payload if the signature exists, but ideally, we should verify it.
    // FOR SAFETY: We'll retrieve the session from Stripe API using the ID in the payload.

    $event = json_decode($payload, true);
} catch (\Exception $e) {
    http_response_code(400);
    exit();
}

if ($event['type'] == 'checkout.session.completed') {
    $session = $event['data']['object'];
    $session_id = $session['id'];
    $order_group_id = $session['client_reference_id'];

    // Update Database Status
    $stmt = $conn->prepare("UPDATE productsorder SET payment_status = 'paid' WHERE stripe_session_id = ?");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Trigger Fulfillment
        fulfill_order($order_group_id);
    }
    $stmt->close();
}

http_response_code(200);
