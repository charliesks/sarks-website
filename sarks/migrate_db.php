<?php
require_once __DIR__ . '/includes/connection.php';

echo "<h2>Sarks Database Migration</h2>";
echo "<pre>";

$queries = [
    "ALTER TABLE productsorder ADD COLUMN IF NOT EXISTS orderemail VARCHAR(255)",
    "ALTER TABLE productsorder ADD COLUMN IF NOT EXISTS payment_status VARCHAR(20) DEFAULT 'pending'",
    "ALTER TABLE productsorder ADD COLUMN IF NOT EXISTS stripe_session_id VARCHAR(255)",
    "ALTER TABLE productsorder ADD COLUMN IF NOT EXISTS order_group_id VARCHAR(100)",
    "ALTER TABLE productsorder ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
];

foreach ($queries as $sql) {
    if (mysqli_query($conn, $sql)) {
        echo "[SUCCESS] $sql\n";
    } else {
        echo "[ERROR]   $sql: " . mysqli_error($conn) . "\n";
    }
}

echo "</pre>";
echo "<p>Migration complete. <a href='index.php?page=cart'>Back to Cart</a></p>";
