<?php
require_once __DIR__ . '/includes/session_ready.php';
session_destroy();
header("Location: index.html");
exit();
