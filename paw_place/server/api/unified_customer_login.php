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
            $employee = $api->employeeLogin($id, $password); // Map to internal call
            if ($employee) {
                $result = $employee;
                $type = 'EMPLOYEE';
            }
        } catch (Exception $e) {
            // Both failed
            throw new Exception("Invalid ID or Password. Please try again.");
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
