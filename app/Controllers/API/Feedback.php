<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

class Feedback extends BaseController
{
    use ResponseTrait;

    private $key = "cacaodx1234567890";

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

    // GET user's own feedbacks
    public function user()
    {
        $tokenData = $this->getUserFromToken();
        
        if (!$tokenData) {
            return $this->failUnauthorized('Invalid or missing token');
        }

        $db = \Config\Database::connect();
        
        try {
            $query = $db->query("
                SELECT 
                    id,
                    rating,
                    comments,
                    created_at
                FROM feedback
                WHERE user_id = ?
                ORDER BY created_at DESC
            ", [$tokenData->uid]);
            
            $feedbacks = $query->getResultArray();
            
            return $this->respond([
                'status' => 'success',
                'data' => $feedbacks
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch user feedbacks: ' . $e->getMessage());
            return $this->fail('Failed to load feedbacks', 500);
        }
    }

    // POST create feedback
    public function create()
    {
        $tokenData = $this->getUserFromToken();
        
        if (!$tokenData) {
            return $this->failUnauthorized('Invalid or missing token');
        }

        $data = json_decode($this->request->getBody(), true);

        // Validate input
        if (empty($data['rating'])) {
            return $this->fail('Rating is required', 400);
        }

        if ($data['rating'] < 1 || $data['rating'] > 5) {
            return $this->fail('Rating must be between 1 and 5', 400);
        }

        $db = \Config\Database::connect();
        
        try {
            $insertData = [
                'user_id' => $tokenData->uid,
                'rating' => $data['rating'],
                'comments' => $data['comments'] ?? null,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $query = $db->table('feedback')->insert($insertData);
            
            if ($query) {
                return $this->respondCreated([
                    'status' => 'success',
                    'message' => 'Feedback submitted successfully'
                ]);
            } else {
                return $this->fail('Failed to submit feedback', 500);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to create feedback: ' . $e->getMessage());
            return $this->fail('Failed to submit feedback', 500);
        }
    }

    // GET all feedbacks (admin only - optional)
    public function index()
    {
        $tokenData = $this->getUserFromToken();
        
        if (!$tokenData) {
            return $this->failUnauthorized('Invalid or missing token');
        }

        $db = \Config\Database::connect();
        
        try {
            $query = $db->query("
                SELECT 
                    f.id,
                    f.rating,
                    f.comments,
                    f.created_at,
                    CONCAT(u.first_name, ' ', u.last_name) as user_name,
                    u.email
                FROM feedback f
                JOIN users u ON f.user_id = u.id
                ORDER BY f.created_at DESC
            ");
            
            $feedbacks = $query->getResultArray();
            
            return $this->respond([
                'status' => 'success',
                'data' => $feedbacks
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch feedbacks: ' . $e->getMessage());
            return $this->fail('Failed to load feedbacks', 500);
        }
    }
}