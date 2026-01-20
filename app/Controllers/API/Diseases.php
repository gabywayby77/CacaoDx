<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

class Diseases extends BaseController
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

    // GET all diseases
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
                    d.id,
                    d.name,
                    d.type,
                    pp.part as plant_part
                FROM diseases d
                LEFT JOIN plant_part pp ON d.plant_part_id = pp.id
                ORDER BY d.name
            ");
            
            $diseases = $query->getResultArray();
            
            return $this->respond([
                'status' => 'success',
                'data' => $diseases
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch diseases: ' . $e->getMessage());
            return $this->fail('Failed to load diseases', 500);
        }
    }

    // GET single disease with treatment details
    public function show($id = null)
    {
        $tokenData = $this->getUserFromToken();
        
        if (!$tokenData) {
            return $this->failUnauthorized('Invalid or missing token');
        }

        if (!$id) {
            return $this->fail('Disease ID is required', 400);
        }

        $db = \Config\Database::connect();
        
        try {
            // Get disease with treatment info and pest info
            $query = $db->query("
                SELECT 
                    d.id,
                    d.name,
                    d.type,
                    d.cause,
                    d.pest_id,
                    pp.part as plant_part,
                    t.treatment,
                    t.prevention,
                    t.recommended_action,
                    p.name as pest_name,
                    p.scientific_name as pest_scientific_name
                FROM diseases d
                LEFT JOIN plant_part pp ON d.plant_part_id = pp.id
                LEFT JOIN treatments t ON d.id = t.disease_id
                LEFT JOIN pests p ON d.pest_id = p.id
                WHERE d.id = ?
                LIMIT 1
            ", [$id]);
            
            $disease = $query->getRowArray();
            
            if (!$disease) {
                return $this->failNotFound('Disease not found');
            }
            
            return $this->respond($disease);
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch disease details: ' . $e->getMessage());
            return $this->fail('Failed to load disease details', 500);
        }
    }
}