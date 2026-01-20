<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use App\Models\DiagnosisModel;
use App\Models\DiseaseModel;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Stats extends ResourceController
{
    private $key = 'cacaodx1234567890';

    public function index()
    {
        try {
            // Get user ID from JWT token
            $userId = $this->getUserId();

            $userModel = new UserModel();
            $diseaseModel = new DiseaseModel();
            $db = \Config\Database::connect();

            // Get counts
            $totalUsers = $userModel->countAll();
            $totalDiseases = $diseaseModel->countAll();
            
            // Get diagnoses only for current user
            $userDiagnoses = 0;
            if ($userId) {
                $userDiagnoses = $db->table('diagnosis')
                    ->where('user_id', $userId)
                    ->countAllResults();
            }

            $data = [
                'total_users'     => $totalUsers,
                'total_diagnoses' => $userDiagnoses,  // Only current user's diagnoses
                'total_diseases'  => $totalDiseases,
                'user_id'         => $userId  // Optional: for debugging
            ];

            return $this->respond($data);

        } catch (\Exception $e) {
            log_message('error', 'Stats error: ' . $e->getMessage());
            return $this->fail('Error fetching stats: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get authenticated user ID from JWT token
     */
    private function getUserId()
    {
        try {
            $authHeader = $this->request->getHeaderLine('Authorization');
            
            if (!$authHeader) {
                log_message('warning', 'Missing Authorization header in stats');
                return null;
            }

            $token = str_replace('Bearer ', '', $authHeader);

            try {
                $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            } catch (\Exception $e) {
                log_message('error', 'JWT Decode Error in stats: ' . $e->getMessage());
                return null;
            }

            if (!isset($decoded->uid)) {
                log_message('error', 'No uid in token');
                return null;
            }

            return $decoded->uid;

        } catch (\Exception $e) {
            log_message('error', 'Auth error in stats: ' . $e->getMessage());
            return null;
        }
    }
}