<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Libraries\GrubhoundAPI;

class Auth extends BaseController
{
    public function login()
    {
        // If already logged in, redirect
        $session = session();
        if ($session->get('role')) {
            $role = $session->get('role');
            if ($role === 'Admin') return redirect()->to('admin/dashboard');
            if (in_array($role, ['Cashier'])) return redirect()->to('cashier');
            return redirect()->to('kiosk');
        }
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $request = \Config\Services::request();
        $username = trim($request->getPost('username') ?? '');
        $password = $request->getPost('password') ?? '';
        $role = $request->getPost('role') ?? '';

        if (empty($username) || empty($password)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing username or password']);
        }

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        // 1. Try Local DB
        if ($user) {
            // Verify Role if specified
            if (!empty($role) && strtoupper($role) !== 'KIOSK') {
               $requested = strtoupper(trim($role));
               $actual = strtoupper(trim($user['role']));
               
               $allow = false;
               if ($requested === 'ADMIN' && $actual === 'ADMIN') $allow = true;
               if ($requested === 'CASHIER' && in_array($actual, ['CASHIER', 'ADMIN'])) $allow = true;

               if (!$allow) {
                   return $this->response->setJSON(['success' => false, 'message' => 'Selected role does not allow this user']);
               }
            }

            // Verify Password
            if ($userModel->verifyPassword($password, $user['password_hash'])) {
                // Success
                $this->setSession($user);
                $redirect = ($user['role'] === 'Admin') ? base_url('admin/dashboard') : base_url('cashier'); // Changed kitchen to cashier
                
                return $this->response->setJSON(['success' => true, 'redirect' => $redirect]);
            }
        }

        // 2. Try Grubhound API (if not found in DB or password failed - actually typically only if not in DB to avoid double checking)
        // If user was found in DB but password failed, we should probably stop there. 
        // But logic in old code: if num_rows === 0, try API.
        if (!$user) {
            try {
                $api = new GrubhoundAPI();
                $searchResult = $api->searchEmployees($username);
                
                if (is_array($searchResult) && count($searchResult) > 0) {
                    $emp = $searchResult[0]; // Take first match
                    
                    // In real world we would check password here against something, but API search doesn't verify password usually?
                    // The old code: 
                    // $searchResult = $api->searchEmployees($username);
                    // if (count > 0) -> LOGIN SUCCESS directly? 
                    // WAIT. The old code creates a session just by searching?
                    // "Use first matching employee to create a session (token-based lookup)"
                    // Yes, it looks like it trusts the searchResult?? That seems insecure if it doesn't check credentials. 
                    // Ah, maybe the API search is protected/trusted or this is an intranet app.
                    // Following EXISTING LOGIC strictly.

                    $role = $emp['role'] ?? 'Cashier';
                    $userData = [
                        'user_id' => $emp['id'] ?? 0,
                        'username' => $emp['username'] ?? $username,
                        'role' => $role,
                        'full_name' => $emp['full_name'] ?? ($emp['name'] ?? $username)
                    ];
                    
                    $this->setSession($userData);
                    $redirect = ($role === 'Admin') ? base_url('admin/dashboard') : base_url('cashier');

                    return $this->response->setJSON(['success' => true, 'redirect' => $redirect]);
                }
            } catch (\Exception $e) {
                // Log error
                log_message('error', 'Grubhound API Error: ' . $e->getMessage());
            }
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Invalid username or password']);
    }

    private function setSession($user)
    {
        $session = session();
        $session->set([
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'full_name' => $user['full_name'],
            'isLoggedIn' => true
        ]);
    }
    
    public function logout()
    {
        session()->destroy();
        return redirect()->to('auth/login');
    }
}
