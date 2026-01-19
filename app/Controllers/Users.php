<?php

namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        // LOAD MODEL ONCE
        $this->userModel = new UserModel();
    }

    public function index()
    {
        return view('users', [
            'users' => $this->userModel->findAll()
        ]);
    }

    public function store()
    {
        $this->userModel->insert([
            'first_name'     => $this->request->getPost('first_name'),
            'last_name'      => $this->request->getPost('last_name'),
            'email'          => $this->request->getPost('email'),
            'password'       => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),
            'contact_number' => $this->request->getPost('contact_number'),
            'user_type_id'   => 2,
            'registered_at'  => date('Y-m-d H:i:s'),
            'role'           => 'user',
            'status'         => 'active'
        ]);

        return redirect()->to('/users')->with('success', 'User added');
    }

    public function update()
    {
        $id = $this->request->getPost('id');

        if (!$id) {
            return redirect()->back()->with('error', 'User ID missing');
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name'  => $this->request->getPost('last_name'),
            'email'      => $this->request->getPost('email'),
            'status'     => $this->request->getPost('status'),
        ];

        $this->userModel->update($id, $data);

        return redirect()->to('/users')->with('success', 'User updated');
    }

    public function delete()
    {
        $this->userModel->delete($this->request->getPost('id'));

        return redirect()->to('/users')->with('success', 'User deleted');
    }
}
