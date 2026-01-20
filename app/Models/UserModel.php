<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';   // users table
    protected $primaryKey = 'id'; // PK

    protected $allowedFields = [
        'first_name',
        'last_name',
        'email',
        'password',
        'user_type_id',
        'contact_number',
        'registered_at',
        'status',
        'farm_location'
    ];
    
    protected $useTimestamps = false; // since you are using registered_at, not created_at/updated_at

    // Optionally, you can auto-hash password here
    // protected function beforeInsert(array $data)
    // {
    //     if (isset($data['data']['password'])) {
    //         $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
    //     }
    //     return $data;
    // }
}