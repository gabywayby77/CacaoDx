<?php

namespace App\Models;

use CodeIgniter\Model;

class LogModel extends Model
{
    protected $table = 'activity_log';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'activity', 'log_date'];

    public function getLogsWithUsersPaginated($perPage = 6)
    {
        return $this->select('activity_log.*, users.first_name, users.last_name')
                    ->join('users', 'users.id = activity_log.user_id', 'left')
                    ->orderBy('activity_log.log_date', 'ASC')
                    ->paginate($perPage);
    }

    // ✅ New method for showing ALL logs (no pagination)
    public function getAllLogsWithUsers()
    {
        return $this->select('activity_log.*, users.first_name, users.last_name')
                    ->join('users', 'users.id = activity_log.user_id', 'left')
                    ->orderBy('activity_log.log_date', 'ASC')
                    ->findAll();
    }
}
