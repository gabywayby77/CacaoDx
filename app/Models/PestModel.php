<?php

namespace App\Models;

use CodeIgniter\Model;

class PestModel extends Model
{
    protected $table = 'pests';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'scientific_name',
        'family',
        'description',
        'damage',
        'plant_part_id',
        'image'  // Added image field
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;
}