<?php
session_start();

// Set kiosk session variables
$_SESSION['role'] = 'KIOSK';
$_SESSION['full_name'] = $_POST['full_name'] ?? 'Customer';
$_SESSION['user_id'] = $_POST['user_id'] ?? null;
$_SESSION['department'] = $_POST['department'] ?? null;

// Redirect to kiosk ordering page
header('Location: ../../client/2_kiosk_ordering.php');
exit;
?>
