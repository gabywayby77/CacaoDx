<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Registration extends Controller
{
    public function index()
    {
        return view('registration');
    }

    public function store()
    {
        $userModel = new UserModel();

        // Validation rules
        $rules = [
            'first_name'     => 'required|min_length[2]',
            'last_name'      => 'required|min_length[2]',
            'email'          => 'required|valid_email|is_unique[users.email]',
            'contact_number' => 'required|min_length[10]',
            'password'       => 'required|min_length[6]',
            'role'           => 'required|in_list[user,admin]', // ✅ Validate role
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->validator->listErrors());
        }

        // Get form data
        $data = [
            'first_name'     => $this->request->getPost('first_name'),
            'last_name'      => $this->request->getPost('last_name'),
            'email'          => $this->request->getPost('email'),
            'contact_number' => $this->request->getPost('contact_number'),
            'password'       => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'           => $this->request->getPost('role'), // ✅ Store selected role
            'user_type_id'   => $this->request->getPost('role') === 'admin' ? 1 : 2, // Optional: sync with user_type_id
            'registered_at'  => date('Y-m-d H:i:s'),
            'status'         => 'active',
        ];

        // Insert user
        if ($userModel->insert($data)) {
            return redirect()->to(base_url('login'))
                ->with('success', 'Registration successful! Please login.');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Registration failed. Please try again.');
        }
    }
}