<?php

namespace App\Models;

use CodeIgniter\Model;

class PestModel extends Model
{
    protected $table      = 'pests';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'name',
        'scientific_name',
        'family',
        'description',
        'damage',
        'plant_part_id',
    ];

    protected $useTimestamps = false;
}
