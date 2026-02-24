<?php
/**
 * GrabHound API Client
 * Handles token management and API requests to Foundation University MIS
 */

class GrubhoundAPI {
    private $configPath = __DIR__ . '/config/grubhound_config.json';
    private $config;
    private $baseUrl = 'https://mis.foundationu.com/api/grubhound';

    public function __construct() {
        $this->loadConfig();
    }

    /**
     * Load configuration from JSON file
     */
    private function loadConfig() {
        if (!file_exists($this->configPath)) {
            throw new Exception('GrabHound config file not found: ' . $this->configPath);
        }
        $this->config = json_decode(file_get_contents($this->configPath), true);
        if (!$this->config) {
            throw new Exception('Failed to parse grubhound config JSON');
        }
    }

    /**
     * Check if access token is expired and refresh if needed
     */
    private function ensureValidToken() {
        // Default to a past time if missing
        $expiresAt = isset($this->config['expires_at']) ? strtotime($this->config['expires_at']) : 0;
        $now = time();

        // Refresh if expired or will expire in the next 5 minutes (300 seconds)
        if ($expiresAt <= ($now + 300)) { 
            try {
                $this->refreshToken();
            } catch (Exception $e) {
                // Log error but attempt to proceed (maybe token is still valid for a few seconds)
                error_log("Grubhound Token Refresh Failed: " . $e->getMessage());
            }
        }
    }

    /**
     * Refresh the access token using refresh token
     */
    private function refreshToken() {
        $refreshUrl = $this->config['refresh_url'];
        $refreshToken = $this->config['refresh_token'];

        $ch = curl_init();
        
        // Try sending refresh token in body (standard OAuth2 pattern)
        $postData = json_encode([
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token'
        ]);

        curl_setopt_array($ch, [
            CURLOPT_URL => $refreshUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new Exception('cURL error during token refresh: ' . $curlError);
        }

        if ($httpCode === 200) {
            $newTokenData = json_decode($response, true);
            if (!$newTokenData || !isset($newTokenData['access_token'])) {
                throw new Exception('Invalid token refresh response: ' . $response);
            }
            
            $this->config['access_token'] = $newTokenData['access_token'];
            $this->config['refresh_token'] = $newTokenData['refresh_token'] ?? $this->config['refresh_token'];
            
            // Handle expiration
            if (isset($newTokenData['expires_at'])) {
                // If API returns explicit date string
                $this->config['expires_at'] = $newTokenData['expires_at'];
            } elseif (isset($newTokenData['expires_in'])) {
                // If API returns seconds duration (OAuth2 standard)
                // Calculate future date: now + seconds
                $this->config['expires_at'] = date('Y-m-d H:i:s', time() + (int)$newTokenData['expires_in']);
            } else {
                // Default fallback: 2 hours from now
                $this->config['expires_at'] = date('Y-m-d H:i:s', time() + 7200);
            }

            $this->saveConfig();
        } else {
            throw new Exception('Token refresh failed (HTTP ' . $httpCode . '): ' . $response);
        }
    }

    /**
     * Save updated config back to JSON file
     */
    private function saveConfig() {
        file_put_contents($this->configPath, json_encode($this->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Make API request to GrabHound
     */
    public function request($endpoint, $method = 'GET', $data = null) {
        $this->ensureValidToken();

        $response = $this->executeRequest($endpoint, $method, $data);
        
        // If 401 Unauthorized, try refreshing token once and retry
        if ($response['http_code'] === 401) {
            try {
                $this->refreshToken();
                $response = $this->executeRequest($endpoint, $method, $data);
            } catch (Exception $e) {
                // If refresh fails or retry fails, throw the original error or new error
                throw new Exception('Token expired and refresh failed: ' . $e->getMessage());
            }
        }

        if ($response['http_code'] !== 200) {
            $errorMsg = is_array($response['body']) && isset($response['body']['message']) 
                ? $response['body']['message'] 
                : 'Unknown error';
            throw new Exception('API error (HTTP ' . $response['http_code'] . '): ' . $errorMsg);
        }

        return $response['body'];
    }

    /**
     * Internal method to execute cURL request
     */
    private function executeRequest($endpoint, $method, $data) {
        $url = $this->baseUrl . $endpoint;
        $headers = [
            'Authorization: Bearer ' . $this->config['access_token'],
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        if ($method === 'POST' && $data) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
             throw new Exception('cURL error: ' . $curlError);
        }

        return [
            'http_code' => $httpCode,
            'body' => json_decode($rawResponse, true) ?? $rawResponse
        ];
    }

    /**
     * Get student information by ID
     */
    public function getStudent($studentId) {
        return $this->request('/student/' . $studentId);
    }

    /**
     * Search students
     */
    public function searchStudents($search) {
        return $this->request('/student-search/?search=' . urlencode($search));
    }

    /**
     * Get employee information by ID
     */
    public function getEmployee($employeeId) {
        return $this->request('/employee/' . $employeeId);
    }

    /**
     * Search employees
     */
    public function searchEmployees($search) {
        return $this->request('/employee-search/?search=' . urlencode($search));
    }

    /**
     * Get all departments
     */
    public function getDepartments() {
        return $this->request('/departments');
    }

    /**
     * Search departments
     */
    public function searchDepartments($search) {
        return $this->request('/department-search/?search=' . urlencode($search));
    }

    /**
     * Get department information
     */
    public function getDepartment($department) {
        return $this->request('/department/' . urlencode($department));
    }
}
?>
