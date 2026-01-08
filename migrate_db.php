<?php
require_once __DIR__ . '/sarks/includes/connection.php';

$queries = [
    "ALTER TABLE productsorder ADD COLUMN IF NOT EXISTS orderemail VARCHAR(255)",
    "ALTER TABLE productsorder ADD COLUMN IF NOT EXISTS payment_status VARCHAR(20) DEFAULT 'pending'",
    "ALTER TABLE productsorder ADD COLUMN IF NOT EXISTS stripe_session_id VARCHAR(255)",
    "ALTER TABLE productsorder ADD COLUMN IF NOT EXISTS order_group_id VARCHAR(100)",
    "ALTER TABLE productsorder ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
];

foreach ($queries as $sql) {
    if (mysqli_query($conn, $sql)) {
        echo "Successfully executed: $sql\n";
    } else {
        echo "Error executing $sql: " . mysqli_error($conn) . "\n";
    }
}
