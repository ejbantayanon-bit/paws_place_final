<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../GrubhoundAPI.php';

// Parse JSON payload
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;
$password = $input['password'] ?? null;

if (!$id || !$password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID and password are required']);
    exit;
}

try {
    $api = new GrubhoundAPI();
    $result = null;
    $type = null;

    // 1. Try Student Login
    try {
        $student = $api->studentLogin($id, $password);
        if ($student) {
            $result = $student;
            $type = 'STUDENT';
        }
    } catch (Exception $e) {
        // Not a student or wrong password, try employee
    }

    // 2. Try Employee Login if student failed
    if (!$result) {
        try {
            $employee = $api->employeeLogin($id, $password);
            if ($employee) {
                $result = $employee;
                $type = 'EMPLOYEE';
            }
        } catch (Exception $e) {
            // Both API calls failed or API is down
            // 3. Fallback to local DB for Admin/Cashier (Staff Unlocking Kiosk)
            $DB_HOST = 'localhost';
            $DB_USER = 'root';
            $DB_PASS = '';
            $DB_NAME = 'paws_place_db';

            $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
            if (!$conn->connect_error) {
                // Check if it's a local staff account
                $stmt = $conn->prepare("SELECT user_id, username, password_hash, full_name, role FROM users WHERE username = ? AND role IN ('Admin', 'Cashier') LIMIT 1");
                $stmt->bind_param('s', $id);
                $stmt->execute();
                $dbRes = $stmt->get_result();
                
                if ($dbRes->num_rows > 0) {
                    $row = $dbRes->fetch_assoc();
                    
                    // Verify password (supporting both hash and plain text fallback like auth_login.php)
                    $valid = false;
                    if (password_get_info($row['password_hash'])['algo'] !== 0) {
                        $valid = password_verify($password, $row['password_hash']);
                    } else {
                        $valid = hash_equals($row['password_hash'], $password);
                    }

                    if ($valid) {
                        $result = [
                            'id' => $row['user_id'],
                            'full_name' => $row['full_name'],
                            'username' => $row['username'],
                            'role' => $row['role']
                        ];
                        $type = 'STAFF_FALLBACK';
                    }
                }
                $stmt->close();
                $conn->close();
            }

            if (!$result) {
                throw new Exception("Invalid ID or Password. Please try again.");
            }
        }
    }

    if ($result) {
        // Normalize response
        if (is_array($result)) {
            if (isset($result['data'])) $result = $result['data'];
            if (isset($result['student'])) $result = $result['student'];
            if (isset($result['employee'])) $result = $result['employee'];

            // Build full_name
            if (!isset($result['full_name'])) {
                $nameParts = [];
                if (!empty($result['first_name'])) $nameParts[] = trim($result['first_name']);
                if (!empty($result['middle_name'])) $nameParts[] = trim($result['middle_name']);
                if (!empty($result['last_name'])) $nameParts[] = trim($result['last_name']);
                if ($nameParts) $result['full_name'] = implode(' ', $nameParts);
            }
            if (!isset($result['full_name']) && isset($result['name'])) {
                $result['full_name'] = $result['name'];
            }
        }

        echo json_encode([
            'success' => true,
            'user' => $result,
            'type' => $type
        ]);
    } else {
        throw new Exception("Invalid ID or Password. Please try again.");
    }

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
