<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;

class Auth extends BaseController
{
    use ResponseTrait;

    private $key = "MY_SECRET_KEY"; // change this!

    public function register()
    {
        $data = json_decode($this->request->getBody(), true);
        $userModel = new UserModel();

        if ($userModel->where('email', $data['email'])->first()) {
            return $this->fail('Email already registered', 400);
        }

        $userModel->insert([
            'first_name'     => $data['first_name'],
            'last_name'      => $data['last_name'],
            'email'          => $data['email'],
            'password'       => password_hash($data['password'], PASSWORD_DEFAULT),
            'contact_number' => $data['contact_number'],
            'user_type_id'   => $data['user_type_id']
        ]);

        return $this->respondCreated(['message' => 'Account created successfully']);
    }

    public function login()
    {
        $data = json_decode($this->request->getBody(), true);
        $userModel = new UserModel();
        $user = $userModel->where('email', $data['email'])->first();

        // if (!$user || !password_verify($data['password'], $user['password'])) {
        if (!$user || $data['password'] !== $user['password']) { // Testing purpose only, remove later
            return $this->failUnauthorized('Invalid credentials');
        }

        // Generate JWT
        $issuedAt   = time();
        $expiration = $issuedAt + 3600; // 1 hour
        $payload = [
            'iat'   => $issuedAt,
            'exp'   => $expiration,
            'uid'   => $user['id'],
            'email' => $user['email']
        ];

        $token = JWT::encode($payload, $this->key, 'HS256');

        return $this->respond([
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => [
                'id'    => $user['id'],
                'email' => $user['email'],
                'name'  => $user['first_name'] . ' ' . $user['last_name']
            ]
        ]);
    }
}
