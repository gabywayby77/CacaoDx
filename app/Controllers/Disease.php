<?php

namespace App\Controllers;

use App\Models\DiseaseModel;
use CodeIgniter\Controller;

class Disease extends Controller
{
    protected $diseaseModel;

    public function __construct()
    {
        $this->diseaseModel = new DiseaseModel();
        helper('auth'); // Load auth helper
    }

    /**
     * Show disease list
     * All users can see all diseases (it's a reference library)
     */
    public function index()
    {
        $session = session();

        // Get user name for the header
        $userName = trim(
            ($session->get('first_name') ?? '') . ' ' .
            ($session->get('last_name') ?? '')
        );

        $userId = $session->get('user_id');
        $isAdmin = is_admin();

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Please login first');
        }

        // Get all diseases with plant part names
        $diseases = $this->diseaseModel->getAllWithPlantParts();

        // Get all plant parts for the dropdown in add/edit forms
        $db = \Config\Database::connect();
        $plantParts = $db->table('plant_part')
            ->select('id, part')
            ->orderBy('part', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'userName'   => $userName,
            'diseases'   => $diseases,
            'plantParts' => $plantParts,
            'isAdmin'    => $isAdmin,
        ];

        return view('disease', $data);
    }

    /**
     * Store new disease (ADMIN ONLY)
     */
    public function store()
    {
        if (!can_edit()) {
            return redirect()->back()
                ->with('error', 'Access denied. Admin privileges required.');
        }

        $rules = [
            'name'           => 'required|min_length[3]|max_length[100]',
            'type'           => 'required|max_length[50]',
            'cause'          => 'required|max_length[200]',
            'plant_part_id'  => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please check your input fields');
        }

        $this->diseaseModel->insert([
            'name'          => $this->request->getPost('name'),
            'type'          => $this->request->getPost('type'),
            'cause'         => $this->request->getPost('cause'),
            'plant_part_id' => $this->request->getPost('plant_part_id'),
        ]);

        return redirect()->to('/disease')
            ->with('success', 'Disease added successfully!');
    }

    /**
     * Update disease (ADMIN ONLY)
     */
    public function update()
    {
        if (!can_edit()) {
            return redirect()->back()
                ->with('error', 'Access denied. Admin privileges required.');
        }

        $rules = [
            'id'             => 'required|is_natural_no_zero',
            'name'           => 'required|min_length[3]|max_length[100]',
            'type'           => 'required|max_length[50]',
            'cause'          => 'required|max_length[200]',
            'plant_part_id'  => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please check your input fields');
        }

        $id = $this->request->getPost('id');

        $this->diseaseModel->update($id, [
            'name'          => $this->request->getPost('name'),
            'type'          => $this->request->getPost('type'),
            'cause'         => $this->request->getPost('cause'),
            'plant_part_id' => $this->request->getPost('plant_part_id'),
        ]);

        return redirect()->to('/disease')
            ->with('success', 'Disease updated successfully!');
    }

    /**
     * Delete disease (ADMIN ONLY)
     */
    public function delete()
    {
        if (!can_edit()) {
            return redirect()->back()
                ->with('error', 'Access denied. Admin privileges required.');
        }

        $id = $this->request->getPost('id');

        if (!$id) {
            return redirect()->back()
                ->with('error', 'Invalid disease ID.');
        }

        $this->diseaseModel->delete($id);

        return redirect()->to('/disease')
            ->with('success', 'Disease deleted successfully!');
    }
}