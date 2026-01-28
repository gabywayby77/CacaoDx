<?php

namespace App\Models;

use CodeIgniter\Model;

class DiagnosisModel extends Model
{
    protected $table = 'diagnosis';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'disease_id', 
        'treatment_id',
        'confidence',
        'notes',
        'prevention',
        'recommended_action',
        'diagnosis_date'
    ];

    /**
     * Get total count of all diagnosis records
     */
    public function getTotalCount()
    {
        return $this->db->table($this->table)->countAllResults();
    }

    /**
     * Get paginated diagnosis with joins and user filtering
     * 
     * @param int $perPage - Records per page
     * @param int $page - Current page number
     * @param int $userId - Current user's ID
     * @param bool $isAdmin - Whether current user is admin
     * @return array
     */
    public function getPaginated($perPage, $page, $userId = null, $isAdmin = false)
    {
        $builder = $this->db->table('diagnosis d');

        // Select the correct columns
        $builder->select('
            d.id,
            d.user_id,
            d.confidence,
            d.notes,
            d.prevention,
            d.recommended_action,
            d.diagnosis_date,
            u.first_name,
            u.last_name,
            dis.name AS disease_name,
            t.treatment AS treatment_name,
            t.prevention AS treatment_prevention,
            t.recommended_action AS treatment_action
        ');

        // Joins
        $builder->join('users u', 'u.id = d.user_id', 'left');
        $builder->join('diseases dis', 'dis.id = d.disease_id', 'left');
        $builder->join('treatments t', 't.id = d.treatment_id', 'left');

        // ✅ USER-SPECIFIC FILTERING
        // If NOT admin and userId is provided, filter by user_id
        if (!$isAdmin && $userId) {
            $builder->where('d.user_id', $userId);
        }
        // If admin, show all records (no filter)

        // Order by most recent first
        $builder->orderBy('d.diagnosis_date', 'DESC');

        // Get total rows for pagination (after filtering)
        $total = $builder->countAllResults(false);

        // Apply limit and offset for pagination
        $builder->limit($perPage, ($page - 1) * $perPage);
        $results = $builder->get()->getResultArray();

        return [
            'diagnosis'   => $results,
            'total'       => $total,
            'perPage'     => $perPage,
            'currentPage' => (int) $page,
            'totalPages'  => ceil($total / $perPage),
        ];
    }

    /**
     * Get diagnosis count by user ID
     */
    public function countByUserId($userId)
    {
        return $this->where('user_id', $userId)->countAllResults();
    }

    /**
     * Get recent diagnoses for a user
     */
    public function getRecentByUserId($userId, $limit = 5)
    {
        $builder = $this->db->table('diagnosis d');
        
        $builder->select('
            d.id,
            d.confidence,
            d.diagnosis_date,
            dis.name AS disease_name
        ')
        ->join('diseases dis', 'dis.id = d.disease_id', 'left')
        ->where('d.user_id', $userId)
        ->orderBy('d.diagnosis_date', 'DESC')
        ->limit($limit);

        return $builder->get()->getResultArray();
    }
}