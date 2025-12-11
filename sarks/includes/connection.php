<?php
$server = getenv('MYSQL_HOST') ?: 'sarks_mysql';
$user   = getenv('MYSQL_USER') ?: 'PsychoDad007';
$pass   = getenv('MYSQL_PASSWORD') ?: 'Moonscape$Caddy$Overlap$Shorts$Enduring4$Sibling$Broker$Skewer$Exact$Euphemism$User';
$db     = getenv('MYSQL_DATABASE') ?: 'sarksdb';
$port   = getenv('MYSQL_PORT') ?: 3306;

$conn = mysqli_connect($server, $user, $pass, $db, (int)$port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
