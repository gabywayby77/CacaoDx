<?php

namespace App\Controllers;
use App\Models\ActivityLogModel;

class Logs extends BaseController
{
    public function index()
    {
        // Load auth helper for role checks
        helper('auth');
        
        $model = new ActivityLogModel();

        // Pagination
        $currentPage = $this->request->getGet('page') ?? 1;
        $perPage = 20;  // Changed from 10 to 20

        // Total logs (get before pagination)
        $totalLogs = $model->countAllResults(false);

        // Total pages
        $totalPages = ceil($totalLogs / $perPage);

        // Fetch logs for current page
        $logs = $model->orderBy('log_date', 'DESC')
                      ->findAll($perPage, ($currentPage - 1) * $perPage);

        // Get logged-in user name
        $userName = session()->get('first_name') . ' ' . session()->get('last_name');

        // Pass data to the view
        return view('activity_log', [
            'logs' => $logs,
            'currentPage' => (int)$currentPage,
            'totalPages' => $totalPages,
            'totalLogs' => $totalLogs,
            'userName' => $userName
        ]);
    }
}