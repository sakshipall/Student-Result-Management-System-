<?php
// Start session for flash messages (used across all pages)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---- Database configuration ----
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'student_result_db';

// ---- Create connection ----
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}
