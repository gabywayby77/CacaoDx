<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

class Profile extends BaseController
{
    use ResponseTrait;

    private $key = "cacaodx1234567890"; // Same key as Auth controller

    // Get authenticated user from JWT token
    private function getUserFromToken()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        
        if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return null;
        }

        try {
            $token = $matches[1];
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            log_message('error', 'Token decode error: ' . $e->getMessage());
            return null;
        }
    }

    public function index()
    {
        $tokenData = $this->getUserFromToken();
        
        if (!$tokenData) {
            return $this->failUnauthorized('Invalid or missing token');
        }

        $userModel = new UserModel();
        $user = $userModel->find($tokenData->uid);

        if (!$user) {
            return $this->failNotFound('User not found');
        }

        // Remove password from response
        unset($user['password']);

        return $this->respond($user);
    }

    public function changePassword()
    {
        $tokenData = $this->getUserFromToken();
        
        if (!$tokenData) {
            return $this->failUnauthorized('Invalid or missing token');
        }

        $data = json_decode($this->request->getBody(), true);

        // Validate input
        if (empty($data['current_password']) || empty($data['new_password'])) {
            return $this->fail('Current password and new password are required', 400);
        }

        if (strlen($data['new_password']) < 6) {
            return $this->fail('New password must be at least 6 characters', 400);
        }

        $userModel = new UserModel();
        $user = $userModel->find($tokenData->uid);

        if (!$user) {
            return $this->failNotFound('User not found');
        }

        // Verify current password (plain text comparison for testing)
        if ($data['current_password'] !== $user['password']) {
            return $this->fail('Current password is incorrect', 400);
        }

        // Check if new password is same as current
        if ($data['current_password'] === $data['new_password']) {
            return $this->fail('New password must be different from current password', 400);
        }

        // Update password (plain text for testing - USE HASHING IN PRODUCTION!)
        $updateData = [
            'password' => $data['new_password']
            // In production, use: 'password' => password_hash($data['new_password'], PASSWORD_DEFAULT)
        ];

        try {
            $result = $userModel->update($tokenData->uid, $updateData);
            
            if ($result === false) {
                $errors = $userModel->errors();
                log_message('error', 'Password update failed: ' . json_encode($errors));
                return $this->fail('Failed to update password', 500);
            }

            return $this->respond([
                'message' => 'Password changed successfully',
                'success' => true
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Change password exception: ' . $e->getMessage());
            return $this->fail('Failed to change password: ' . $e->getMessage(), 500);
        }
    }
}