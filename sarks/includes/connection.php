<?php
// Load credentials from environment variables with fallback defaults
$server = getenv('MYSQL_HOST') ?: 'sarks_mysql';
$user   = getenv('MYSQL_USER') ?: 'PsychoDad007';
$pass   = getenv('MYSQL_PASSWORD') ?: 'Moonscape$Caddy$Overlap$Shorts$Enduring4$Sibling$Broker$Skewer$Exact$Euphemism$User';
$db     = getenv('MYSQL_DATABASE') ?: 'sarksdb';

// Connect to MySQL
$conn = mysqli_connect($server, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
