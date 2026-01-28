<?php

namespace App\Models;

use CodeIgniter\Model;

class FeedbackModel extends Model
{
    protected $table      = 'feedback';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'rating',
        'comments',
    ];

    // ✅ ENABLE TIMESTAMPS
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all feedback with user names (ADMIN)
     * 
     * @return array
     */
    public function getAllWithUserNames()
    {
        return $this->select('feedback.*, CONCAT(users.first_name, " ", users.last_name) as user_name')
            ->join('users', 'users.id = feedback.user_id', 'left')
            ->orderBy('feedback.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get feedback by user ID (USER)
     * 
     * @param int $userId
     * @return array
     */
    public function getByUserId($userId)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get feedback count by user
     * 
     * @param int $userId
     * @return int
     */
    public function countByUserId($userId)
    {
        return $this->where('user_id', $userId)->countAllResults();
    }

    /**
     * Get average rating for all feedback
     * 
     * @return float
     */
    public function getAverageRating()
    {
        $result = $this->selectAvg('rating')->first();
        return $result ? round($result['rating'], 1) : 0;
    }

    /**
     * Get rating distribution
     * 
     * @return array
     */
    public function getRatingDistribution()
    {
        return $this->select('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'DESC')
            ->findAll();
    }
}