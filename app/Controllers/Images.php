<?php

namespace App\Controllers;

class Images extends BaseController
{
    public function __construct()
    {
        helper('auth'); // Load auth helper
    }

    /**
     * View images (Available to all users)
     */
    public function index()
    {
        return view('images');
    }

    /**
     * Upload images (ADMIN ONLY)
     */
    public function upload($folder = null)
    {
        // ✅ Check admin permission
        if (!can_edit()) {
            return redirect()->back()
                ->with('error', 'Access denied. Only admins can upload images.');
        }

        helper(['form', 'url']);

        $allowedFolders = [
            'healthy',
            'black_pod_disease',
            'frosty_pod_rot',
            'mirid_bug'
        ];

        if (!$folder || !in_array($folder, $allowedFolders)) {
            return redirect()->back()->with('error', 'Invalid folder.');
        }

        $uploadPath = FCPATH . "uploads/{$folder}/";

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $files = $this->request->getFiles();

        if (!isset($files['image'])) {
            return redirect()->back()->with('error', 'No files uploaded.');
        }

        foreach ($files['image'] as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $file->move($uploadPath, $file->getRandomName());
            }
        }

        return redirect()->to(base_url('images'))
            ->with('success', 'Images uploaded successfully.');
    }
}