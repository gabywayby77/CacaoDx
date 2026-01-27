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
        $session = session();

        // Get user name for the header
        $userName = trim(
            ($session->get('first_name') ?? '') . ' ' .
            ($session->get('last_name') ?? '')
        );

        $data = [
            'userName' => $userName,
        ];

        return view('images', $data);
    }

    /**
     * Upload images (ADMIN ONLY)
     */
    public function upload($folder = null)
    {
        // ✅ Check admin permission
        if (!can_edit()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied. Only admins can upload images.'
            ]);
        }

        helper(['form', 'url']);

        $allowedFolders = [
            'healthy',
            'black_pod_disease',
            'frosty_pod_rot',
            'mirid_bug'
        ];

        if (!$folder || !in_array($folder, $allowedFolders)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid folder.'
            ]);
        }

        $uploadPath = FCPATH . "uploads/{$folder}/";

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $files = $this->request->getFiles();

        if (!isset($files['image']) || empty($files['image'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No files uploaded.'
            ]);
        }

        $uploadedCount = 0;
        $errors = [];

        foreach ($files['image'] as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                // Validate file type
                if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg'])) {
                    $errors[] = $file->getName() . ' - Invalid file type';
                    continue;
                }

                // Validate file size (max 5MB)
                if ($file->getSize() > 5242880) {
                    $errors[] = $file->getName() . ' - File too large (max 5MB)';
                    continue;
                }

                $file->move($uploadPath, $file->getRandomName());
                $uploadedCount++;
            } else {
                $errors[] = $file->getName() . ' - Upload failed';
            }
        }

        if ($uploadedCount > 0) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "{$uploadedCount} image(s) uploaded successfully!" . 
                            (!empty($errors) ? ' (Some files failed: ' . implode(', ', $errors) . ')' : '')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Upload failed: ' . implode(', ', $errors)
            ]);
        }
    }

    /**
     * View all images in a folder
     */
    public function viewFolder($folder = null)
    {
        $session = session();

        $userName = trim(
            ($session->get('first_name') ?? '') . ' ' .
            ($session->get('last_name') ?? '')
        );

        $allowedFolders = [
            'healthy' => 'Healthy Pods',
            'black_pod_disease' => 'Black Pod Disease',
            'frosty_pod_rot' => 'Frosty Pod Rot',
            'mirid_bug' => 'Mirid Bug Damage'
        ];

        if (!$folder || !isset($allowedFolders[$folder])) {
            return redirect()->to('images')->with('error', 'Invalid folder.');
        }

        $uploadPath = FCPATH . "uploads/{$folder}/";

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $images = array_values(array_diff(scandir($uploadPath), ['.', '..']));

        $data = [
            'userName' => $userName,
            'folder' => $folder,
            'folderLabel' => $allowedFolders[$folder],
            'images' => $images,
        ];

        return view('images_view', $data);
    }

    /**
     * Delete image (ADMIN ONLY)
     */
    public function deleteImage($folder = null, $filename = null)
    {
        if (!can_edit()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied. Only admins can delete images.'
            ]);
        }

        $allowedFolders = ['healthy', 'black_pod_disease', 'frosty_pod_rot', 'mirid_bug'];

        if (!$folder || !in_array($folder, $allowedFolders) || !$filename) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request.'
            ]);
        }

        $filePath = FCPATH . "uploads/{$folder}/{$filename}";

        if (file_exists($filePath)) {
            unlink($filePath);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Image deleted successfully.'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Image not found.'
        ]);
    }
}