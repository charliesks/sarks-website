<?php
// Centralized session handling for Sarks Website
// Use a consistent writable path for session storage
session_save_path('/tmp');
session_set_cookie_params(0, '/');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
