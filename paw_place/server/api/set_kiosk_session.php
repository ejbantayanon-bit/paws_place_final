<?php
session_start();

// Set kiosk session variables
$_SESSION['role'] = 'KIOSK';
$_SESSION['full_name'] = $_POST['full_name'] ?? 'Customer';
$_SESSION['user_id'] = $_POST['user_id'] ?? null;
$_SESSION['department'] = $_POST['department'] ?? null;

// Redirect to store selection page
header('Location: ../../client/store_selection.php');
exit;
?>
