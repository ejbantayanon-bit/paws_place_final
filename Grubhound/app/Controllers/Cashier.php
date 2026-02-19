<?php

namespace App\Controllers;

class Cashier extends BaseController
{
    public function index()
    {
        // Ensure user is logged in
        $session = session();
        if (!$session->get('isLoggedIn')) {
             return redirect()->to('auth/login');
        }
        
        // Optional: Check role if needed
        // if (!in_array($session->get('role'), ['Admin', 'Cashier'])) { ... }

        $data = [
            'user_name' => $session->get('full_name') // Pass user name to view
        ];

        return view('cashier/pos', $data);
    }
}
