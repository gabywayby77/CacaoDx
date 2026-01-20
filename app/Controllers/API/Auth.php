<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\FarmModel;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

class Auth extends BaseController
{
    use ResponseTrait;

    private $key = "cacaodx1234567890"; // change this!

    /**
     * Helper method to log user activities
     */
    private function logActivity($userId, $activity)
    {
        $db = \Config\Database::connect();
        $db->table('activity_log')->insert([
            'user_id' => $userId,
            'activity' => $activity,
            'log_date' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get authenticated user ID from JWT token
     */
    private function getUserIdFromToken()
    {
        try {
            $authHeader = $this->request->getHeaderLine('Authorization');
            
            if (!$authHeader) {
                return null;
            }

            $token = str_replace('Bearer ', '', $authHeader);

            try {
                $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            } catch (\Exception $e) {
                log_message('error', 'JWT Decode Error: ' . $e->getMessage());
                return null;
            }

            if (!isset($decoded->uid)) {
                return null;
            }

            return $decoded->uid;

        } catch (\Exception $e) {
            log_message('error', 'Auth error: ' . $e->getMessage());
            return null;
        }
    }

    public function register()
    {
        $data = json_decode($this->request->getBody(), true);
        
        // Validate required fields
        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || 
            empty($data['password']) || empty($data['contact_number']) || empty($data['user_type_id'])) {
            return $this->fail('All fields are required', 400);
        }
        
        // For farmers (user_type_id = 2), farm_location is required
        if ($data['user_type_id'] == 2 && empty($data['farm_location'])) {
            return $this->fail('Farm location is required for farmers', 400);
        }
        
        $userModel = new UserModel();

        if ($userModel->where('email', $data['email'])->first()) {
            return $this->fail('Email already registered', 400);
        }

        $insertData = [
            'first_name'     => $data['first_name'],
            'last_name'      => $data['last_name'],
            'email'          => $data['email'],
            'password'       => $data['password'], // NOT HASHED - FOR TESTING ONLY
            // 'password'       => password_hash($data['password'], PASSWORD_DEFAULT), // Use this in production!
            'contact_number' => $data['contact_number'],
            'user_type_id'   => $data['user_type_id']
        ];

        $db = \Config\Database::connect();
        $db->transStart(); // Start transaction

        try {
            // Insert user
            $result = $userModel->insert($insertData);
            
            if ($result === false) {
                $errors = $userModel->errors();
                log_message('error', 'Insert failed: ' . json_encode($errors));
                $db->transRollback();
                return $this->fail('Registration failed: ' . json_encode($errors), 500);
            }
            
            $userId = $userModel->getInsertID();
            
            // If user is a farmer and has farm_location, create farm entry
            if ($data['user_type_id'] == 2 && !empty($data['farm_location'])) {
                $farmModel = new FarmModel();
                
                // Parse farm location (assuming format like "Dumaguete City" or "Barangay, Municipality")
                $locationParts = explode(',', $data['farm_location']);
                $municipality = trim(end($locationParts));
                $barangay = count($locationParts) > 1 ? trim($locationParts[0]) : null;
                
                $farmData = [
                    'user_id'          => $userId,
                    'user_type_id'     => $data['user_type_id'],
                    'farm_name'        => $data['first_name'] . "'s Farm", // Default farm name
                    'barangay'         => $barangay,
                    'municipality'     => $municipality,
                    'size_in_hectares' => null, // Can be updated later
                    'created_date'     => date('Y-m-d')
                ];
                
                $farmResult = $farmModel->insert($farmData);
                
                if ($farmResult === false) {
                    log_message('error', 'Farm insert failed: ' . json_encode($farmModel->errors()));
                    $db->transRollback();
                    return $this->fail('Registration failed: Could not create farm entry', 500);
                }
            }
            
            // Log registration activity
            $this->logActivity($userId, 'Registered new account');
            
            $db->transComplete(); // Complete transaction
            
            if ($db->transStatus() === false) {
                return $this->fail('Registration failed due to database error', 500);
            }
            
            return $this->respondCreated(['message' => 'Account created successfully']);
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Registration exception: ' . $e->getMessage());
            return $this->fail('Registration failed: ' . $e->getMessage(), 500);
        }
    }

    public function login()
    {
        $data = json_decode($this->request->getBody(), true);
        $userModel = new UserModel();
        $user = $userModel->where('email', $data['email'])->first();

        // Testing with plain text password comparison
        if (!$user || $data['password'] !== $user['password']) {
            return $this->failUnauthorized('Invalid credentials');
        }

        // Generate JWT with long expiration (30 days)
        $issuedAt   = time();
        $expiration = $issuedAt + (30 * 24 * 60 * 60); // 30 days
        $payload = [
            'iat'   => $issuedAt,
            'exp'   => $expiration,
            'uid'   => $user['id'],
            'email' => $user['email']
        ];

        $token = JWT::encode($payload, $this->key, 'HS256');

        // Log login activity
        $this->logActivity($user['id'], 'Logged into the system');

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

    public function logout()
    {
        // Get user ID from token
        $userId = $this->getUserIdFromToken();
        
        if ($userId) {
            // Log logout activity
            $this->logActivity($userId, 'Logged out of the system');
        }

        return $this->respond([
            'message' => 'Logged out successfully'
        ]);
    }
}