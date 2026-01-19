<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        return view('login'); // Shows login form
    }public function authenticate()
    {
        $userModel = new UserModel();
    
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');
    
        $user = $userModel->where('email', $email)->first();
    
        if (!$user) {
            return redirect()->back()->with('error', 'Invalid email or password.');
        }
    
        $storedPassword = $user['password'];
    
        // ✅ Case 1: Proper hashed password
        if (str_starts_with($storedPassword, '$2y$')) {
            if (!password_verify($password, $storedPassword)) {
                return redirect()->back()->with('error', 'Invalid email or password.');
            }
        }
        // ⚠ Case 2: Plain-text password → auto-upgrade
        elseif ($password === $storedPassword) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $userModel->update($user['id'], ['password' => $newHash]);
        }
        // ❌ Case 3: Fake hash (hashedpassword1, etc.)
        else {
            return redirect()->back()->with('error', 'Invalid email or password.');
        }
    
        // ✅ Login success
        session()->set([
            'user_id'    => $user['id'],
            'user_type'  => $user['user_type_id'],
            'first_name' => $user['first_name'],
            'last_name'  => $user['last_name'],
            'email'      => $user['email'],
            'isLoggedIn' => true
        ]);
    
        return redirect()->to('/dashboard');
    }
    

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }
}