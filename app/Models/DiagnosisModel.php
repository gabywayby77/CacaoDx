<?php

namespace App\Models;

use CodeIgniter\Model;

class DiagnosisModel extends Model
{
    protected $table = 'diagnosis';
    protected $primaryKey = 'id';
    protected $allowedFields = [];

    // Get relations + LIMIT + pagination
    public function getPaginated($perPage, $page)
    {
        $builder = $this->db->table('diagnosis d');

        // Select the correct columns
        $builder->select('
            d.id,
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

        // Get total rows for pagination
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
}
