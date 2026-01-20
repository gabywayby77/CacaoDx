<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PestModel;
use CodeIgniter\API\ResponseTrait;

class Pests extends BaseController
{
    use ResponseTrait;
    
    protected $pestModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->pestModel = new PestModel();
    }

    /**
     * GET /api/pests
     * Used by PestClassification screen
     */
    public function index()
    {
        try {
            $pests = $this->pestModel
                ->select('
                    pests.id,
                    pests.name,
                    pests.scientific_name,
                    pests.family,
                    pests.description,
                    pests.damage,
                    pests.image,
                    plant_part.part AS plant_part
                ')
                ->join('plant_part', 'plant_part.id = pests.plant_part_id', 'left')
                ->findAll();

            log_message('info', 'Fetched ' . count($pests) . ' pests');
            
            return $this->respond([
                'status' => 'success',
                'data' => $pests,
                'count' => count($pests)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Pests index error: ' . $e->getMessage());
            return $this->fail('Error fetching pests: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/pests/{id}
     * For PestDetails screen
     */
    public function show($id = null)
    {
        try {
            if (!$id) {
                return $this->fail('Pest ID is required', 400);
            }

            $pest = $this->pestModel
                ->select('
                    pests.*,
                    plant_part.part AS plant_part
                ')
                ->join('plant_part', 'plant_part.id = pests.plant_part_id', 'left')
                ->where('pests.id', $id)
                ->first();

            if (!$pest) {
                return $this->fail('Pest not found', 404);
            }

            log_message('info', 'Fetched pest: ' . $id);
            
            return $this->respond($pest);
        } catch (\Exception $e) {
            log_message('error', 'Pest show error: ' . $e->getMessage());
            return $this->fail('Error fetching pest: ' . $e->getMessage(), 500);
        }
    }
}