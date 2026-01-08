<?php
// Centralized session handling for Sarks Website
// Use a consistent writable path for session storage (mapped to a Docker named volume)
$session_path = '/tmp';
if (!is_writable($session_path)) {
    // Fallback if /tmp is not writable for some reason
    $session_path = sys_get_temp_dir();
}
session_save_path($session_path);

// Set session lifetime to 2 weeks (1209600 seconds)
$lifetime = 1209600;
ini_set('session.gc_maxlifetime', $lifetime);
session_set_cookie_params($lifetime, '/');

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}
