<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\DiagnosisModel;
use App\Models\DiseaseModel;
use App\Models\FarmModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();

        $userName = trim(
            ($session->get('first_name') ?? '') . ' ' .
            ($session->get('last_name') ?? '')
        );

        $userModel      = new UserModel();
        $diagnosisModel = new DiagnosisModel();
        $diseaseModel   = new DiseaseModel();
        $farmModel      = new FarmModel();

        // NEW USERS (latest 5)
        $newUsers = $userModel
            ->orderBy('registered_at', 'DESC')
            ->limit(5)
            ->find();

        // COUNT USERS BY TYPE FOR PIE CHART
        $db = \Config\Database::connect();
        
        // Count farmers (user_type_id = 2)
        $farmerCount = $db->table('users')
            ->where('user_type_id', 2)
            ->countAllResults();
        
        // Count admins (role = 'admin')
        $adminCount = $db->table('users')
            ->where('role', 'admin')
            ->countAllResults();
        
        // Count regular users (user_type_id = 1 AND role = 'user')
        $regularUserCount = $db->table('users')
            ->where('user_type_id', 1)
            ->where('role', 'user')
            ->countAllResults();

        // ✅ CHANGED: Total diagnosis count (total scans made)
        $totalDiagnosisCount = $diagnosisModel->getTotalCount();

        // ✅ NEW: Get unique users who made diagnoses
        $uniqueDiagnosisUsers = $db->table('diagnosis')
            ->select('user_id')
            ->distinct()
            ->countAllResults();

        $data = [
            'userName'         => $userName,
            'totalUsers'       => $userModel->countAll(),
            
            // ✅ CHANGED: This now shows total diagnosis scans
            'totalDiagnosis'   => $totalDiagnosisCount,
            
            // ✅ NEW: Number of disease types in database
            'totalDiseases'    => $diseaseModel->countAll(),
            
            // ✅ NEW: Unique users who scanned
            'uniqueScanUsers'  => $uniqueDiagnosisUsers,
            
            'farms'            => $farmModel->findAll(),
            'newUsers'         => $newUsers,
            
            // Chart data
            'farmerCount'      => $farmerCount,
            'adminCount'       => $adminCount,
            'regularUserCount' => $regularUserCount,
        ];

        return view('dashboard', $data);
    }
}