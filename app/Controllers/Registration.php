<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Registration extends Controller
{
    public function index()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }
        
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
            'role'           => 'required|in_list[user,admin]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->validator->listErrors());
        }

        $selectedRole = $this->request->getPost('role');

        // ✅ Map role to user_type_id
        // 1 = Admin, 2 = Farmer (we'll use this for regular users)
        $userTypeId = ($selectedRole === 'admin') ? 1 : 2;

        // Get form data
        $data = [
            'first_name'     => $this->request->getPost('first_name'),
            'last_name'      => $this->request->getPost('last_name'),
            'email'          => $this->request->getPost('email'),
            'contact_number' => $this->request->getPost('contact_number'),
            'password'       => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'           => $selectedRole,      // ✅ 'admin' or 'user'
            'user_type_id'   => $userTypeId,        // ✅ 1 for admin, 2 for user
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