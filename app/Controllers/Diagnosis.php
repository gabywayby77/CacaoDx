<?php

namespace App\Controllers;

use App\Models\DiagnosisModel;
use App\Controllers\BaseController;

class Diagnosis extends BaseController
{
    public function index()
    {
        $model = new DiagnosisModel();

        // Get page number from URL (?page=2)
        $page = $this->request->getVar('page') ?? 1;
        $perPage = 5;

        // Fetch with pagination
        $data = $model->getPaginated($perPage, $page);

        return view('diagnosis', $data);
    }
}
