<?php

namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        helper('auth'); // Load auth helper
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $session = session();

        // Get user name for the header
        $userName = trim(
            ($session->get('first_name') ?? '') . ' ' .
            ($session->get('last_name') ?? '')
        );

        return view('users', [
            'userName' => $userName,
            'users' => $this->userModel->findAll()
        ]);
    }

    public function store()
    {
        // Validation rules
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name'  => 'required|min_length[2]|max_length[50]',
            'email'      => 'required|valid_email|is_unique[users.email]',
            'password'   => 'required|min_length[8]',
            'role'       => 'required|in_list[user,admin]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Please check your input fields')->withInput();
        }

        $this->userModel->insert([
            'first_name'     => $this->request->getPost('first_name'),
            'last_name'      => $this->request->getPost('last_name'),
            'email'          => $this->request->getPost('email'),
            'password'       => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),
            'contact_number' => $this->request->getPost('contact_number'),
            'user_type_id'   => 1, // Default to regular user
            'registered_at'  => date('Y-m-d H:i:s'),
            'role'           => $this->request->getPost('role'),
            'status'         => 'active'
        ]);

        return redirect()->to('/users')->with('success', 'User added successfully!');
    }

    public function update()
    {
        $id = $this->request->getPost('id');

        if (!$id) {
            return redirect()->back()->with('error', 'User ID missing');
        }

        // Validation rules
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name'  => 'required|min_length[2]|max_length[50]',
            'email'      => "required|valid_email|is_unique[users.email,id,{$id}]",
            'status'     => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Please check your input fields')->withInput();
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name'  => $this->request->getPost('last_name'),
            'email'      => $this->request->getPost('email'),
            'status'     => $this->request->getPost('status'),
        ];

        $this->userModel->update($id, $data);

        return redirect()->to('/users')->with('success', 'User updated successfully!');
    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        
        if (!$id) {
            return redirect()->back()->with('error', 'User ID missing');
        }

        // Prevent deleting yourself
        $session = session();
        if ($id == $session->get('user_id')) {  // ✅ Correct
            return redirect()->back()->with('error', 'You cannot delete your own account!');
        }

        $this->userModel->delete($id);

        return redirect()->to('/users')->with('success', 'User deleted successfully!');
    }
}