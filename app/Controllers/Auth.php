<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function login()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }
        
        return view('login');
    }

    public function authenticate()
    {
        $userModel = new UserModel();
        
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Validation
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please check your email and password.');
        }

        // Find user by email
        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            
            // Check if user is active
            if ($user['status'] !== 'active') {
                return redirect()->back()
                    ->with('error', 'Your account is not active. Please contact support.');
            }

            // ✅ SET SESSION DATA INCLUDING ROLE
            $sessionData = [
                'user_id'       => $user['id'],
                'first_name'    => $user['first_name'],
                'last_name'     => $user['last_name'],
                'email'         => $user['email'],
                'role'          => $user['role'],           // ✅ CRITICAL: Set role from database
                'user_type_id'  => $user['user_type_id'],
                'contact_number'=> $user['contact_number'] ?? null,
                'isLoggedIn'    => true
            ];

            session()->set($sessionData);

            // Log the login activity
            $this->logActivity($user['id'], 'User logged in');

            // Redirect to dashboard
            return redirect()->to(base_url('dashboard'))
                ->with('success', 'Welcome back, ' . $user['first_name'] . '!');
                
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid email or password.');
        }
    }

    public function logout()
    {
        // Log the logout activity
        if (session()->get('user_id')) {
            $this->logActivity(session()->get('user_id'), 'User logged out');
        }

        // Destroy session
        session()->destroy();
        
        return redirect()->to(base_url('login'))
            ->with('success', 'You have been logged out successfully.');
    }

    // Helper method to log activities
    private function logActivity($userId, $activity)
    {
        $logModel = new \App\Models\ActivityLogModel();
        $logModel->insert([
            'user_id'  => $userId,
            'activity' => $activity,
            'log_date' => date('Y-m-d H:i:s')
        ]);
    }
}