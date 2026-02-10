<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../GrubhoundAPI.php';

// Parse JSON payload
$input = json_decode(file_get_contents('php://input'), true);
$studentId = $input['student_id'] ?? $_GET['student_id'] ?? null;

// Simple debug logging (local only) to help diagnose 400 vs Postman differences
$debugDir = __DIR__ . '/../logs';
if (!is_dir($debugDir)) @mkdir($debugDir, 0755, true);
$debugFile = $debugDir . '/get_student_debug.log';
file_put_contents($debugFile, date('c') . " REQUEST: " . json_encode(['ip' => $_SERVER['REMOTE_ADDR'] ?? '', 'payload' => $input]) . PHP_EOL, FILE_APPEND);

if (!$studentId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'student_id required']);
    exit;
}

try {
    $api = new GrubhoundAPI();
    // Log which token is being used (masked)
    $cfgPath = __DIR__ . '/../config/grubhound_config.json';
    if (file_exists($cfgPath)) {
        $cfg = json_decode(file_get_contents($cfgPath), true);
        $tok = $cfg['access_token'] ?? '';
        $masked = $tok ? (substr($tok,0,8) . '...' . substr($tok,-8)) : '(none)';
        file_put_contents($debugFile, date('c') . " TOKEN: " . $masked . PHP_EOL, FILE_APPEND);
    }
    $student = $api->getStudent($studentId);
    // Log full response with increased visibility
    file_put_contents($debugFile, date('c') . " API_RESPONSE_RAW: " . json_encode($student, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND);
    file_put_contents($debugFile, "AVAILABLE_FIELDS: " . implode(', ', array_keys((array)$student)) . PHP_EOL . PHP_EOL, FILE_APPEND);
    
    // Normalize common API response wrappers and field names so client can read expected keys
    if (is_array($student)) {
        if (isset($student['data'])) {
            $student = $student['data'];
        }
        if (isset($student['student'])) {
            $student = $student['student'];
        }
        
        // Map common field variants to expected keys
        if (isset($student['id']) && !isset($student['student_id'])) {
            $student['student_id'] = $student['id'];
        }
        
        // Build full_name from first/last/middle name fields if not already present
        if (!isset($student['full_name'])) {
            $nameParts = [];
            if (!empty($student['first_name'])) $nameParts[] = trim($student['first_name']);
            if (!empty($student['middle_name'])) $nameParts[] = trim($student['middle_name']);
            if (!empty($student['last_name'])) $nameParts[] = trim($student['last_name']);
            if ($nameParts) {
                $student['full_name'] = implode(' ', $nameParts);
            }
        }
        
        // Fallback: map single name field if full_name still missing
        if (!isset($student['full_name']) && isset($student['name'])) {
            $student['full_name'] = $student['name'];
        }
        if (!isset($student['full_name']) && isset($student['fullName'])) {
            $student['full_name'] = $student['fullName'];
        }
    }
    file_put_contents($debugFile, date('c') . " NORMALIZED_STUDENT: " . json_encode($student) . PHP_EOL, FILE_APPEND);

    echo json_encode([
        'success' => true,
        'student' => $student
    ]);
} catch (Exception $e) {
    file_put_contents($debugFile, date('c') . " ERROR: " . $e->getMessage() . PHP_EOL . PHP_EOL, FILE_APPEND);
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
