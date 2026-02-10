<?php
header('Content-Type: application/json; charset=utf-8');


require_once __DIR__ . '/../GrubhoundAPI.php';

// Parse JSON payload
$input = json_decode(file_get_contents('php://input'), true);
$employeeId = $input['employee_id'] ?? $_GET['employee_id'] ?? null;

// Simple debug logging (local only)
$debugDir = __DIR__ . '/../logs';
if (!is_dir($debugDir)) @mkdir($debugDir, 0755, true);
$debugFile = $debugDir . '/get_employee_debug.log';
file_put_contents($debugFile, date('c') . " REQUEST: " . json_encode(['ip' => $_SERVER['REMOTE_ADDR'] ?? '', 'payload' => $input]) . PHP_EOL, FILE_APPEND);

if (!$employeeId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'employee_id required']);
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
    
    $employee = $api->getEmployee($employeeId);
    
    // Log full response
    file_put_contents($debugFile, date('c') . " API_RESPONSE_RAW: " . json_encode($employee, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND);
    
    // Normalize common API response wrappers and field names
    if (is_array($employee)) {
        if (isset($employee['data'])) {
            $employee = $employee['data'];
        }
        if (isset($employee['employee'])) {
            $employee = $employee['employee'];
        }
        
        // Map common field variants to expected keys
        if (isset($employee['id']) && !isset($employee['employee_id'])) {
            $employee['employee_id'] = $employee['id'];
        }
        
        // Build full_name from first/last/middle name fields if not already present
        if (!isset($employee['full_name'])) {
            $nameParts = [];
            if (!empty($employee['first_name'])) $nameParts[] = trim($employee['first_name']);
            if (!empty($employee['middle_name'])) $nameParts[] = trim($employee['middle_name']);
            if (!empty($employee['last_name'])) $nameParts[] = trim($employee['last_name']);
            if ($nameParts) {
                $employee['full_name'] = implode(' ', $nameParts);
            }
        }
        
        // Fallback: map single name field if full_name still missing
        if (!isset($employee['full_name']) && isset($employee['name'])) {
            $employee['full_name'] = $employee['name'];
        }
        if (!isset($employee['full_name']) && isset($employee['fullName'])) {
            $employee['full_name'] = $employee['fullName'];
        }

        // Map department_name to department if needed
        if (!isset($employee['department']) && isset($employee['department_name'])) {
            $employee['department'] = $employee['department_name'];
        }
    }
    file_put_contents($debugFile, date('c') . " NORMALIZED_EMPLOYEE: " . json_encode($employee) . PHP_EOL, FILE_APPEND);
    
    echo json_encode([
        'success' => true,
        'employee' => $employee
    ]);
} catch (Exception $e) {
    file_put_contents($debugFile, date('c') . " ERROR: " . $e->getMessage() . PHP_EOL . PHP_EOL, FILE_APPEND);
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
