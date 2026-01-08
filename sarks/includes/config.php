<?php
// Sarks Configuration

// Stripe Keys (REPLACE WITH YOUR ACTUAL KEYS FROM STRIPE DASHBOARD)
define('STRIPE_SECRET_KEY', 'sk_test_51P...placeholder...'); // Your Secret Key
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_51P...placeholder...'); // Your Publishable Key
define('STRIPE_WEBHOOK_SECRET', 'whsec_...placeholder...'); // Your Webhook Signing Secret

// Base URL for redirects (e.g., https://sarks.org or http://localhost:8085)
define('BASE_URL', (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/sarks");
