<?php

namespace App\Controllers;

use App\Models\FarmModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $farmModel = new FarmModel();

        $farms = $farmModel->findAll();

        return view('dashboard', [
            'farms' => $farms,
            'userName' => session()->get('first_name') . ' ' . session()->get('last_name'),
            'totalUsers' => 0,
            'totalDiagnosis' => 0,
            'totalDiseases' => 0
        ]);
    }
}
