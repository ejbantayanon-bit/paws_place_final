<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../GrubhoundAPI.php';

// Parse JSON payload
$input = json_decode(file_get_contents('php://input'), true);
$employeeId = $input['employee_id'] ?? null;
$password = $input['password'] ?? null;

if (!$employeeId || !$password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'employee_id and password are required']);
    exit;
}

try {
    $api = new GrubhoundAPI();
    $employee = $api->employeeLogin($employeeId, $password);

    // Normalize response
    if (is_array($employee)) {
        if (isset($employee['data'])) $employee = $employee['data'];
        if (isset($employee['employee'])) $employee = $employee['employee'];

        // Build full_name
        if (!isset($employee['full_name'])) {
            $nameParts = [];
            if (!empty($employee['first_name'])) $nameParts[] = trim($employee['first_name']);
            if (!empty($employee['middle_name'])) $nameParts[] = trim($employee['middle_name']);
            if (!empty($employee['last_name'])) $nameParts[] = trim($employee['last_name']);
            if ($nameParts) $employee['full_name'] = implode(' ', $nameParts);
        }
        if (!isset($employee['full_name']) && isset($employee['name'])) {
            $employee['full_name'] = $employee['name'];
        }
        if (!isset($employee['full_name']) && isset($employee['fullName'])) {
            $employee['full_name'] = $employee['fullName'];
        }

        // Map department
        if (!isset($employee['department']) && isset($employee['department_name'])) {
            $employee['department'] = $employee['department_name'];
        }
    }

    echo json_encode([
        'success' => true,
        'employee' => $employee
    ]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
