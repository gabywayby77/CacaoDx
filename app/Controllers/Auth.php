<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function authenticate()
    {
        $session = session();
        $userModel = new UserModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Invalid email or password.');
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Invalid email or password.');
        }

        // ✅ Store user data in session INCLUDING ROLE
        $sessionData = [
            'id'            => $user['id'],
            'first_name'    => $user['first_name'],
            'last_name'     => $user['last_name'],
            'email'         => $user['email'],
            'role'          => $user['role'], // ✅ THIS IS CRITICAL
            'user_type_id'  => $user['user_type_id'],
            'isLoggedIn'    => true,
        ];

        $session->set($sessionData);

        // Optional: Log the login activity
        // $this->logActivity($user['id'], 'User logged in');

        return redirect()->to(base_url('dashboard'));
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to(base_url('login'));
    }
}