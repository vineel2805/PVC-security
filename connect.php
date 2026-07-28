<?php
/**
 * connect.php
 * Shared database connection used by all pages (header.php, all-products.php, etc.)
 * Uses mysqli ($con) since all-products.php calls mysqli_query() / mysqli_real_escape_string()
 * directly against this variable.
 */
$DB_HOST = 'localhost';
$DB_USER = 'root';   // <-- change to your MySQL username
$DB_PASS = '';       // <-- change to your MySQL password
$DB_NAME = 'pvc';   // matches "-- Database: `pvc1`" in pvc.sql

$con = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if (!$con) {
    // In production, log this instead of echoing it.
    die('Database connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($con, 'utf8mb4');