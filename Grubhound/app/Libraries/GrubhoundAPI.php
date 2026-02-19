<?php

namespace App\Libraries;

use Exception;

/**
 * Grubhound API Client
 * Handles token management and API requests to Foundation University MIS
 */
class GrubhoundAPI {
    private $configPath = WRITEPATH . 'grubhound_config.json';
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
            throw new Exception('Grubhound config file not found: ' . $this->configPath);
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
        // If expires_at is not set, assume token is valid or let it fail
        if (!isset($this->config['expires_at'])) {
            return; 
        }

        $expiresAt = strtotime($this->config['expires_at']);
        $now = time();

        if ($expiresAt <= $now) { // Token is expired or expiring
            $this->refreshToken();
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
            if (isset($newTokenData['refresh_token'])) {
                $this->config['refresh_token'] = $newTokenData['refresh_token'];
            }
            if (isset($newTokenData['expires_at'])) {
                $this->config['expires_at'] = $newTokenData['expires_at'];
            }
            $this->saveConfig();
        } else {
            // Log error but maybe don't throw exception immediately to allow graceful failure?
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
     * Make API request to Grubhound
     */
    public function getStudent($id)
    {
        return $this->request('student/' . $id);
    }

    public function getEmployee($id)
    {
        return $this->request('employee/' . $id);
    }

    /**
     * Make API request to Grubhound
     */
    public function request($endpoint, $method = 'GET', $data = null) {
        $this->ensureValidToken();

        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        
        log_message('info', "GrubhoundAPI Request: $method $url");
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
            CURLOPT_SSL_VERIFYPEER => false, // For testing - allow self-signed certs
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        if ($method === 'POST' && $data) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception('cURL error: ' . $error);
        }

        $decodedResponse = json_decode($response, true);
        
        if ($httpCode !== 200) {
            log_message('error', "GrubhoundAPI Error ($httpCode): " . $response);
        } else {
            // log_message('info', "GrubhoundAPI Response: " . substr($response, 0, 100) . "...");
        }
        
        return $decodedResponse;
    }

    public function searchEmployees($search) {
        return $this->request('/employee-search/?search=' . urlencode($search));
    }
}
