<?php
session_start();
// DB Config
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'paws_place_db';

if (isset($_SESSION['user_id'])) {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if (!$conn->connect_errno) {
        $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Unknown';
        
        // Only log Admin or Cashier activity
        if (in_array($role, ['Admin', 'Cashier'])) {
            $logSql = "INSERT INTO activity_logs (user_id, user_role, activity_type, description) VALUES (?, ?, 'LOGOUT', 'User logged out')";
            $stmt = $conn->prepare($logSql);
            $stmt->bind_param('is', $_SESSION['user_id'], $role);
            $stmt->execute();
            $stmt->close();
        }
        $conn->close();
    }
}

// Clear session
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
// Redirect back to login
// Redirect back to appropriate login page
if (isset($role) && $role === 'KIOSK') {
    header('Location: ../client/customer_login.php');
} else {
    header('Location: ../client/staff_login.php');
}
exit;
?>