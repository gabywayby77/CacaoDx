<?php

namespace App\Controllers;

use App\Models\DiagnosisModel;
use App\Controllers\BaseController;

class Diagnosis extends BaseController
{
    public function index()
    {
        // Load auth helper for role checks
        helper('auth');
        
        $session = session();
        $model = new DiagnosisModel();

        // Get page number from URL (?page=2)
        $page = $this->request->getVar('page') ?? 1;
        $perPage = 5;

        // ✅ USER-SPECIFIC FILTERING
        $userId = $session->get('user_id');
        $isAdmin = is_admin();

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Please login first');
        }

        // Get paginated data with user filter
        $data = $model->getPaginated($perPage, $page, $userId, $isAdmin);
        
        // Add user info to the data array
        $data['isAdmin'] = $isAdmin;
        $data['currentUserId'] = $userId;
        $data['totalRecords'] = $data['total'];

        return view('diagnosis', $data);
    }
}