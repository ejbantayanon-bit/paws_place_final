<?php
// Simple authentication endpoint for GrabHound
header('Content-Type: application/json; charset=utf-8');
session_start();

// DB config - adjust as needed for your environment
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'paws_place_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_errno) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$role = isset($_POST['role']) ? $_POST['role'] : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($role) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

// Helper to verify password: supports both password_hash() and plain text fallback
function verify_pass($input, $stored) {
    if (password_get_info($stored)['algo'] !== 0) {
        return password_verify($input, $stored);
    }
    return hash_equals($stored, $input);
}

// Kiosk: username hidden, allow Admin or Cashier to unlock with their own password
if (strtoupper($role) === 'KIOSK') {
    $stmt = $conn->prepare("SELECT user_id, username, password_hash, full_name, role FROM users WHERE role IN ('Admin','Cashier')");
    $stmt->execute();
    $res = $stmt->get_result();
    $found = false;
    while ($row = $res->fetch_assoc()) {
        if (verify_pass($password, $row['password_hash'])) {
            // success
            $_SESSION['user_id'] = (int)$row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['full_name'] = $row['full_name'];
            $found = true;
            $userRow = $row;

            // Log Kiosk Login (Only for Admin/Cashier)
            if (in_array($row['role'], ['Admin', 'Cashier'])) {
                $logSql = "INSERT INTO activity_logs (user_id, user_role, activity_type, description) VALUES (?, ?, 'LOGIN', 'Kiosk unlocked')";
                $logStmt = $conn->prepare($logSql);
                $logStmt->bind_param('is', $row['user_id'], $row['role']);
                $logStmt->execute();
                $logStmt->close();
            }

            break;
        }
    }
    if ($found) {
        echo json_encode(['success' => true, 'role' => $userRow['role'], 'full_name' => $userRow['full_name'], 'user_id' => $userRow['user_id'], 'redirect' => '../client/2_kiosk_ordering.php']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid kiosk password']);
    }
    $stmt->close();
    $conn->close();
    exit;
}

// For Admin/Cashier: username + password required
if (empty($username)) {
    echo json_encode(['success' => false, 'message' => 'Username required']);
    $conn->close();
    exit;
}

$stmt = $conn->prepare("SELECT user_id, username, password_hash, full_name, role FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param('s', $username);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    // If user is not in local DB, try looking up employee via Grubhound (server token)
    try {
        require_once __DIR__ . '/GrubhoundAPI.php';
        $api = new GrubhoundAPI();
        $searchResult = $api->searchEmployees($username);
        if (is_array($searchResult) && count($searchResult) > 0) {
            // Use first matching employee to create a session (token-based lookup)
            $emp = $searchResult[0];
            $_SESSION['user_id'] = isset($emp['id']) ? (int)$emp['id'] : 0;
            $_SESSION['username'] = $emp['username'] ?? $username;
            // If MIS provides a role, use it; otherwise default to Cashier
            $_SESSION['role'] = $emp['role'] ?? 'Cashier';
            $_SESSION['full_name'] = $emp['full_name'] ?? ($emp['name'] ?? $username);

            $redirect = ($_SESSION['role'] === 'Admin') ? '../client/5_adminDashboard.php' : '../client/3_index.php';

            // Log API Login (Only for Admin/Cashier)
            if (in_array($_SESSION['role'], ['Admin', 'Cashier'])) {
                $logSql = "INSERT INTO activity_logs (user_id, user_role, activity_type, description) VALUES (?, ?, 'LOGIN', 'User logged in via GrabHound API')";
                $logStmt = $conn->prepare($logSql);
                $logStmt->bind_param('is', $_SESSION['user_id'], $_SESSION['role']);
                $logStmt->execute();
                $logStmt->close();
            }

            echo json_encode(['success' => true, 'role' => $_SESSION['role'], 'full_name' => $_SESSION['full_name'], 'user_id' => $_SESSION['user_id'], 'redirect' => $redirect]);
            $stmt->close();
            $conn->close();
            exit;
        }
    } catch (Exception $e) {
        // Fall through to error response below
    }

    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    $stmt->close();
    $conn->close();
    exit;
}
$row = $res->fetch_assoc();

// check role matches (allow Admin/Cashier only)
if (!in_array($row['role'], ['Admin','Cashier'])) {
    echo json_encode(['success' => false, 'message' => 'User role not allowed']);
    $stmt->close();
    $conn->close();
    exit;
}

// If the client specified a desired role (e.g. ADMIN or CASHIER), enforce access rules:
// - ADMIN request: only users with DB role 'Admin' may proceed.
// - CASHIER request: users with DB role 'Cashier' or 'Admin' may proceed (Admin allowed anywhere).
// - Other/empty: fallback to DB role behavior.
if (!empty($role) && strtoupper($role) !== 'KIOSK') {
    $requested = strtoupper(trim($role));
    $actual = strtoupper(trim($row['role']));

    if ($requested === 'ADMIN' && $actual !== 'ADMIN') {
        echo json_encode(['success' => false, 'message' => 'Selected role does not allow this user']);
        $stmt->close();
        $conn->close();
        exit;
    }

    if ($requested === 'CASHIER' && !in_array($actual, ['CASHIER','ADMIN'])) {
        echo json_encode(['success' => false, 'message' => 'Selected role does not allow this user']);
        $stmt->close();
        $conn->close();
        exit;
    }
    // If requested is something else, fall back to DB role checks below
}

if (!verify_pass($password, $row['password_hash'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    $stmt->close();
    $conn->close();
    exit;
}

// success: start session and return user info
$_SESSION['user_id'] = (int)$row['user_id'];
$_SESSION['username'] = $row['username'];
$_SESSION['role'] = $row['role'];
$_SESSION['full_name'] = $row['full_name'];

// Log Activity (Only for Admin/Cashier)
if (in_array($row['role'], ['Admin', 'Cashier'])) {
    $logSql = "INSERT INTO activity_logs (user_id, user_role, activity_type, description) VALUES (?, ?, 'LOGIN', 'User logged in')";
    $logStmt = $conn->prepare($logSql);
    $logStmt->bind_param('is', $row['user_id'], $row['role']);
    $logStmt->execute();
    $logStmt->close();
}

$redirect = ($row['role'] === 'Admin') ? '../client/5_adminDashboard.php' : '../client/3_index.php';

echo json_encode(['success' => true, 'role' => $row['role'], 'full_name' => $row['full_name'], 'user_id' => $row['user_id'], 'redirect' => $redirect]);

$stmt->close();
$conn->close();
exit;

?>
