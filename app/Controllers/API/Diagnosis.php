<?php

namespace App\Controllers\Api;

use App\Models\DiagnosisModel;
use App\Models\DiseaseModel;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Diagnosis extends ResourceController
{
    protected $format = 'json';
    private $key = 'cacaodx1234567890';
    private $ML_API_URL = 'http://172.16.48.119:8080/predict';

    // Map ML class names (snake_case from model) to database disease IDs
    private $diseaseMapping = [
        'black_pod_disease' => 1,
        'black_pod_rot' => 1,  // Alternative name
        'frosty_pod_rot' => 6,
        'mirid_bug' => 7,
        'healthy_pod' => 8,    ];
    
    private $confidenceThreshold = 60.0; // Minimum confidence to trust detection

    /**
     * Upload image for ML diagnosis
     * POST /api/diagnosis/upload
     */
    public function upload()
    {
        try {
            $userId = $this->getUserId();
            
            if (!$userId) {
                return $this->failUnauthorized('Missing or invalid token');
            }

            // Validate image
            $validationRule = [
                'image' => 'uploaded[image]|is_image[image]|max_size[image,5120]'
            ];

            if (!$this->validate($validationRule)) {
                return $this->fail('Invalid image', 400);
            }

            $image = $this->request->getFile('image');
            $source = $this->request->getPost('source') ?? 'mobile';

            if (!$image || !$image->isValid()) {
                return $this->fail('Invalid image file', 400);
            }

            // Save image
            $newName = $image->getRandomName();
            $image->move(FCPATH . 'uploads/diagnoses', $newName);
            $imagePath = '/uploads/diagnoses/' . $newName;
            $fullPath = FCPATH . 'uploads/diagnoses/' . $newName;

            // Call ML API
            $mlResponse = $this->callMLAPI($fullPath);
            
            if (!$mlResponse['success']) {
                return $this->fail('ML detection failed: ' . ($mlResponse['error'] ?? 'Unknown error'), 500);
            }

            // Get best detection
            $detections = $mlResponse['detections'] ?? [];
            if (empty($detections)) {
                return $this->fail('No disease detected in image', 400);
            }

            // Sort by confidence
            usort($detections, function($a, $b) {
                return $b['confidence'] <=> $a['confidence'];
            });
            $best = $detections[0];

            // Convert ML class to lowercase for mapping
            $mlClass = strtolower(trim($best['class']));
            $confidence = round($best['confidence'] * 100, 2);
            
            log_message('info', 'ML detected: ' . $mlClass . ' (' . $confidence . '%)');
            
            // Determine disease ID
            $diseaseId = null;
            
            // Check if confidence is too low
            if ($confidence < $this->confidenceThreshold) {
                log_message('info', 'Confidence below threshold, marking as unknown');
                $diseaseId = 9; // Unknown
            } else {
                // Look up in mapping
                $diseaseId = $this->diseaseMapping[$mlClass] ?? null;
                
                if (!$diseaseId) {
                    log_message('warning', 'Class not found in mapping: ' . $mlClass . ', marking as unknown');
                    $diseaseId = 9; // Unknown
                }
            }

            // Get complete disease information from database
            $diseaseInfo = $this->getDiseaseInfo($diseaseId);

            if (!$diseaseInfo) {
                log_message('error', 'Disease info not found for ID: ' . $diseaseId);
                return $this->fail('Disease information not found', 404);
            }

            log_message('info', 'Disease identified: ' . $diseaseInfo['disease_name']);

            // Save to diagnosis table
            $db = \Config\Database::connect();
            $diagnosisData = [
                'user_id' => $userId,
                'image_path' => $imagePath,
                'plant_part_id' => $diseaseInfo['plant_part_id'],
                'disease_id' => $diseaseId,
                'detected_class' => $mlClass,  // Store original ML class
                'confidence' => $confidence,
                'source' => $source,
                'notes' => $diseaseInfo['cause'] ?? 'ML Detection',
                'prevention' => $diseaseInfo['prevention'] ?? null,
                'recommended_action' => $diseaseInfo['recommended_action'] ?? null,
                'diagnosis_date' => date('Y-m-d H:i:s')
            ];
            
            $db->table('diagnosis')->insert($diagnosisData);
            $diagnosisId = $db->insertID();

            // Prepare comprehensive response
            return $this->respond([
                'status' => 'success',
                'diagnosis_id' => $diagnosisId,
                'disease_id' => $diseaseId,
                'disease_name' => $diseaseInfo['disease_name'],
                'disease_type' => $diseaseInfo['disease_type'],
                'affected_part' => $diseaseInfo['plant_part'],
                'confidence' => $confidence,
                'cause' => $diseaseInfo['cause'],
                'pest_info' => $diseaseInfo['pest_info'],
                'treatments' => $diseaseInfo['treatments'],
                'image_path' => base_url($imagePath)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Upload error: ' . $e->getMessage());
            return $this->fail('Server error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get diagnosis history with complete details
     * GET /api/diagnosis/history
     */
    public function history()
    {
        try {
            $userId = $this->getUserId();
            
            if (!$userId) {
                return $this->failUnauthorized('Missing or invalid token');
            }

            $db = \Config\Database::connect();

            $history = $db->table('diagnosis')
                ->select('
                    diagnosis.id,
                    diagnosis.image_path,
                    diagnosis.confidence,
                    diagnosis.diagnosis_date,
                    diagnosis.notes,
                    diagnosis.prevention,
                    diagnosis.recommended_action,
                    diseases.id AS disease_id,
                    diseases.name AS disease_name,
                    diseases.type AS disease_type,
                    diseases.cause AS disease_cause,
                    plant_part.part AS plant_part_name,
                    pests.name AS pest_name,
                    pests.scientific_name AS pest_scientific_name
                ')
                ->join('diseases', 'diseases.id = diagnosis.disease_id', 'left')
                ->join('plant_part', 'plant_part.id = diagnosis.plant_part_id', 'left')
                ->join('pests', 'pests.id = diseases.pest_id', 'left')
                ->where('diagnosis.user_id', $userId)
                ->orderBy('diagnosis.diagnosis_date', 'DESC')
                ->get()
                ->getResultArray();

            // Get treatments for each diagnosis
            foreach ($history as &$record) {
                if ($record['disease_id']) {
                    $treatments = $db->table('treatments')
                        ->where('disease_id', $record['disease_id'])
                        ->get()
                        ->getResultArray();
                    $record['treatments'] = $treatments;
                }
            }

            log_message('info', 'Found ' . count($history) . ' diagnosis records for user ' . $userId);
            
            return $this->respond([
                'status' => 'success',
                'data' => $history,
                'count' => count($history)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'History error: ' . $e->getMessage());
            return $this->fail('Server error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get complete disease information from database
     */
    private function getDiseaseInfo($diseaseId)
    {
        if (!$diseaseId) {
            return null;
        }

        $db = \Config\Database::connect();

        // Get disease details with related information
        $disease = $db->table('diseases')
            ->select('
                diseases.id,
                diseases.name AS disease_name,
                diseases.type AS disease_type,
                diseases.cause,
                diseases.plant_part_id,
                plant_part.part AS plant_part,
                pests.name AS pest_name,
                pests.scientific_name AS pest_scientific_name,
                pests.description AS pest_description,
                pests.damage AS pest_damage
            ')
            ->join('plant_part', 'plant_part.id = diseases.plant_part_id', 'left')
            ->join('pests', 'pests.id = diseases.pest_id', 'left')
            ->where('diseases.id', $diseaseId)
            ->get()
            ->getRowArray();

        if (!$disease) {
            return null;
        }

        // Get all treatments for this disease
        $treatments = $db->table('treatments')
            ->select('
                id,
                description,
                treatment,
                prevention,
                recommended_action,
                plant_part_id
            ')
            ->where('disease_id', $diseaseId)
            ->get()
            ->getResultArray();

        // Combine all prevention and recommended actions
        $allPrevention = [];
        $allActions = [];
        
        foreach ($treatments as $treatment) {
            if (!empty($treatment['prevention'])) {
                $allPrevention[] = $treatment['prevention'];
            }
            if (!empty($treatment['recommended_action'])) {
                $allActions[] = $treatment['recommended_action'];
            }
        }

        // Add pest info if available
        $pestInfo = null;
        if (!empty($disease['pest_name'])) {
            $pestInfo = [
                'name' => $disease['pest_name'],
                'scientific_name' => $disease['pest_scientific_name'],
                'description' => $disease['pest_description'],
                'damage' => $disease['pest_damage']
            ];
        }

        return [
            'disease_id' => $disease['id'],
            'disease_name' => $disease['disease_name'],
            'disease_type' => $disease['disease_type'],
            'cause' => $disease['cause'],
            'plant_part_id' => $disease['plant_part_id'],
            'plant_part' => $disease['plant_part'],
            'pest_info' => $pestInfo,
            'treatments' => $treatments,
            'prevention' => implode(' ', $allPrevention),
            'recommended_action' => implode(' ', $allActions)
        ];
    }

    /**
     * Get authenticated user ID from JWT token
     */
    private function getUserId()
    {
        try {
            $authHeader = $this->request->getHeaderLine('Authorization');
            
            if (!$authHeader) {
                log_message('warning', 'Missing Authorization header');
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
                log_message('error', 'No uid in token');
                return null;
            }

            return $decoded->uid;

        } catch (\Exception $e) {
            log_message('error', 'Auth error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Call Flask ML API
     */
    private function callMLAPI($imagePath)
    {
        try {
            $ch = curl_init();
            $cfile = new \CURLFile($imagePath, 'image/jpeg', 'image.jpg');
            
            curl_setopt_array($ch, [
                CURLOPT_URL => $this->ML_API_URL,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => ['image' => $cfile],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                return ['success' => false, 'error' => $error];
            }
            
            curl_close($ch);
            
            if ($httpCode !== 200) {
                return ['success' => false, 'error' => "HTTP $httpCode"];
            }
            
            $data = json_decode($response, true);
            return $data ?: ['success' => false, 'error' => 'Invalid response'];
            
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}