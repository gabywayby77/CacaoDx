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
}
