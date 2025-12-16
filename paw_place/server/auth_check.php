<?php
// Include to protect pages. Usage: include '../server/auth_check.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../client/1_login.php');
    exit;
}
// Provide convenient globals
$current_user_id = $_SESSION['user_id'];
$current_user_role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
$current_user_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : null;
?>