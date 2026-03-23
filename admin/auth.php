<?php
/**
 * Admin Authentication Guard
 * Include this at the top of every admin page (except login.php)
 */
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
