<?php

namespace App\Controllers;
use App\Models\ActivityLogModel;

class Logs extends BaseController
{
    public function index()
    {
        $model = new ActivityLogModel();

        // Pagination
        $currentPage = $this->request->getGet('page') ?? 1;
        $perPage = 10;

        // Total logs
        $totalLogs = $model->countAllResults();

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
            'userName' => $userName
        ]);
    }
}
