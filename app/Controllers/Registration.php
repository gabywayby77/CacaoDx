<?php

namespace App\Controllers;

use App\Models\UserModel;

class Registration extends BaseController
{
    public function index()
    {
        return view('registration');
    }

    public function store()
    {
        $userModel = new UserModel();

        // Get the plain password
        $plainPassword = $this->request->getPost('password');
        
        // Hash it
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        // 🔍 TEMPORARY DEBUG - Remove after testing
        log_message('info', '=== REGISTRATION DEBUG ===');
        log_message('info', 'Plain password: ' . $plainPassword);
        log_message('info', 'Hashed password: ' . $hashedPassword);
        log_message('info', 'Hash length: ' . strlen($hashedPassword));

        $data = [
            'first_name'     => $this->request->getPost('first_name'),
            'last_name'      => $this->request->getPost('last_name'),
            'email'          => $this->request->getPost('email'),
            'password'       => $hashedPassword,
            'user_type_id'   => 1,
            'contact_number' => $this->request->getPost('contact_number'),
            'registered_at'  => date('Y-m-d H:i:s')
        ];

        $userModel->insert($data);

        return redirect()->to('/login')->with('success', 'Account created! Please login.');
    }
}