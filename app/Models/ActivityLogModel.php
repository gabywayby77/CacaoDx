<?php

namespace App\Models;
use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_log';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'activity', 'log_date'];
    protected $useTimestamps = false; // log_date is handled by DB

    // Optional: join with user table if you want user names
    public function getLogsWithUser()
    {
        return $this->select('activity_log.id, activity_log.activity, activity_log.log_date, users.first_name, users.last_name')
                    ->join('users', 'users.id = activity_log.user_id')
                    ->orderBy('log_date', 'DESC')
                    ->findAll();
    }
}
