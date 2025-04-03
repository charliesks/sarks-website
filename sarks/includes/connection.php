<?php
// Load credentials from environment variables with fallback defaults
$server = getenv('DB_HOST') ?: 'sarks_mysql';
$user   = getenv('DB_USER') ?: 'root';
$pass   = getenv('DB_PASSWORD') ?: 'root';
$db     = getenv('DB_NAME') ?: 'sarksdb';

// Connect to MySQL
$conn = mysqli_connect($server, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
