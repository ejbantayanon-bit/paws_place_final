<?php
header('Content-Type: application/json; charset=utf-8');

// Parse JSON payload
$input = json_decode(file_get_contents('php://input'), true);
$studentId = $input['student_id'] ?? $_GET['student_id'] ?? null;

if (!$studentId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'student_id required']);
    exit;
}

// Mock student data - return different data based on student ID
$mockStudents = [
    '001' => [
        'student_id' => '001',
        'full_name' => 'Ahmed Hassan',
        'email' => 'ahmed.hassan@foundation.edu',
        'department' => 'Engineering',
        'balance' => 500.00,
        'status' => 'active'
    ],
    '002' => [
        'student_id' => '002',
        'full_name' => 'Fatima Mansour',
        'email' => 'fatima.mansour@foundation.edu',
        'department' => 'Business',
        'balance' => 750.00,
        'status' => 'active'
    ],
    '003' => [
        'student_id' => '003',
        'full_name' => 'Mohamed Karim',
        'email' => 'mohamed.karim@foundation.edu',
        'department' => 'Engineering',
        'balance' => 300.00,
        'status' => 'active'
    ],
];

// Return mock student or generic student object
if (isset($mockStudents[$studentId])) {
    $student = $mockStudents[$studentId];
} else {
    // Return a generic student object for any ID
    $student = [
        'student_id' => $studentId,
        'full_name' => 'Test Student',
        'email' => 'test@foundation.edu',
        'department' => 'General',
        'balance' => 500.00,
        'status' => 'active'
    ];
}

echo json_encode([
    'success' => true,
    'student' => $student
]);
?>
