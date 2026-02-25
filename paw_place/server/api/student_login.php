<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../GrubhoundAPI.php';

// Parse JSON payload
$input = json_decode(file_get_contents('php://input'), true);
$studentId = $input['student_id'] ?? null;
$password = $input['password'] ?? null;

if (!$studentId || !$password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'student_id and password are required']);
    exit;
}

try {
    $api = new GrubhoundAPI();
    $student = $api->studentLogin($studentId, $password);

    // Normalize response
    if (is_array($student)) {
        if (isset($student['data'])) $student = $student['data'];
        if (isset($student['student'])) $student = $student['student'];

        // Build full_name
        if (!isset($student['full_name'])) {
            $nameParts = [];
            if (!empty($student['first_name'])) $nameParts[] = trim($student['first_name']);
            if (!empty($student['middle_name'])) $nameParts[] = trim($student['middle_name']);
            if (!empty($student['last_name'])) $nameParts[] = trim($student['last_name']);
            if ($nameParts) $student['full_name'] = implode(' ', $nameParts);
        }
        if (!isset($student['full_name']) && isset($student['name'])) {
            $student['full_name'] = $student['name'];
        }
        if (!isset($student['full_name']) && isset($student['fullName'])) {
            $student['full_name'] = $student['fullName'];
        }
    }

    echo json_encode([
        'success' => true,
        'student' => $student
    ]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
