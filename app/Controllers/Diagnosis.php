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
        
        $model = new DiagnosisModel();

        // Get page number from URL (?page=2)
        $page = $this->request->getVar('page') ?? 1;
        $perPage = 5;

        // Get total count of all records
        $totalRecords = $model->countAllResults(false);

        // Fetch with pagination
        $data = $model->getPaginated($perPage, $page);
        
        // Add total records to the data array
        $data['totalRecords'] = $totalRecords;

        return view('diagnosis', $data);
    }
}