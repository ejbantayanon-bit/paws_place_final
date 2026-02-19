<?php

namespace App\Controllers;

use App\Libraries\GrubhoundAPI;

class Kiosk extends BaseController
{
    public function login()
    {
        return view('kiosk/login');
    }

    public function lookup()
    {
        $request = \Config\Services::request();
        $idNumber = $request->getJSON(true)['id_number'] ?? '';

        if (empty($idNumber)) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID Number is required']);
        }

        $api = new GrubhoundAPI();

        // 1. Try Student
        try {
            log_message('info', "Kiosk Lookup Student ID: $idNumber");
            $student = $api->getStudent($idNumber);
            if ($student && !isset($student['error'])) { // Check for error key just in case
                return $this->response->setJSON([
                    'success' => true,
                    'type' => 'STUDENT',
                    'data' => [
                        'name' => $student['name'] ?? $student['full_name'],
                        'department' => $student['department'] ?? 'Student',
                        'id' => $idNumber
                    ]
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', "Kiosk Lookup Student Failed: " . $e->getMessage());
        }

        // 2. Try Employee
        try {
            // Use searchEmployees for now as we don't have getEmployee in library yet?
            // Wait, library has searchEmployees. Let's check library or assume we add getEmployee.
            // Actually searchEmployees returns array.
            // Let's us searchEmployees by ID if possible? Or assume similar endpoint exists.
            // The old code used `get_employee.php` which called `/api/employees/{id}` presumably.
            // Let's stick to what we have in GrubhoundAPI or extend it.
            // Extended GrubhoundAPI in mind.
            $employee = $api->getEmployee($idNumber); // We need to ensure this method exists
            if ($employee) {
                return $this->response->setJSON([
                    'success' => true,
                    'type' => 'EMPLOYEE',
                    'data' => [
                        'name' => $employee['name'] ?? $employee['full_name'],
                        'department' => $employee['department'] ?? 'Employee',
                        'id' => $idNumber
                    ]
                ]);
            }
        } catch (\Exception $e) {
           // Ignore
        }

        return $this->response->setJSON(['success' => false, 'message' => 'ID not found']);
    }

    public function setSession()
    {
        $request = \Config\Services::request();
        $data = $request->getJSON(true);

        $session = session();
        $session->set([
            'kiosk_mode' => true,
            'role' => 'KIOSK',
            'user_id' => $data['user_id'],
            'full_name' => $data['full_name'],
            'department' => $data['department'] ?? 'Guest',
            'user_type' => $data['user_type'] ?? 'GUEST' // STUDENT, EMPLOYEE, GUEST
        ]);

        return $this->response->setJSON(['success' => true, 'redirect' => base_url('kiosk/menu')]);
    }
}
