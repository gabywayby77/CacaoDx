<?php

namespace App\Models;

use CodeIgniter\Model;

class DiseaseModel extends Model
{
    protected $table      = 'diseases';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'type',
        'cause',
        'plant_part_id',
    ];

    // ✅ ENABLE TIMESTAMPS
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all diseases with plant part names
     * 
     * @return array
     */
    public function getAllWithPlantParts()
    {
        return $this->select('diseases.*, plant_part.part as plant_part_name')
            ->join('plant_part', 'plant_part.id = diseases.plant_part_id', 'left')
            ->orderBy('diseases.name', 'ASC')
            ->findAll();
    }

    /**
     * Get a single disease with plant part name
     * 
     * @param int $id
     * @return array|null
     */
    public function getWithPlantPart($id)
    {
        return $this->select('diseases.*, plant_part.part as plant_part_name')
            ->join('plant_part', 'plant_part.id = diseases.plant_part_id', 'left')
            ->where('diseases.id', $id)
            ->first();
    }
}