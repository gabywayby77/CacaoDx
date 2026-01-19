<?php

namespace App\Models;

use CodeIgniter\Model;

class FarmModel extends Model
{
    protected $table = 'farms';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id','user_type_id','farm_name','barangay','municipality',
        'latitude','longitude','size_in_hectares','cacao_trees',
        'average_yield_kg','last_harvest_date','disease_status',
        'pests_detected','notes','created_date'
    ];
}
