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
    }

    /**
     * Show disease list
     */
    public function index()
    {
        $data = [
            'diseases' => $this->diseaseModel->findAll(),
        ];

        return view('disease', $data);
    }

    /**
     * Store new disease
     */
    public function store()
    {
        $rules = [
            'name'           => 'required|min_length[3]',
            'type'           => 'required',
            'cause'          => 'required',
            'plant_part_id'  => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->validator->listErrors());
        }

        $this->diseaseModel->insert([
            'name'          => $this->request->getPost('name'),
            'type'          => $this->request->getPost('type'),
            'cause'         => $this->request->getPost('cause'),
            'plant_part_id' => $this->request->getPost('plant_part_id'),
        ]);

        return redirect()->to('/disease')
            ->with('success', 'Disease added successfully.');
    }

    /**
     * Update disease
     */
    public function update()
    {
        $rules = [
            'id'             => 'required|is_natural_no_zero',
            'name'           => 'required|min_length[3]',
            'type'           => 'required',
            'cause'          => 'required',
            'plant_part_id'  => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->validator->listErrors());
        }

        $id = $this->request->getPost('id');

        $this->diseaseModel->update($id, [
            'name'          => $this->request->getPost('name'),
            'type'          => $this->request->getPost('type'),
            'cause'         => $this->request->getPost('cause'),
            'plant_part_id' => $this->request->getPost('plant_part_id'),
        ]);

        return redirect()->to('/disease')
            ->with('success', 'Disease updated successfully.');
    }

    /**
     * Delete disease
     */
    public function delete()
    {
        $id = $this->request->getPost('id');

        if (! $id) {
            return redirect()->back()
                ->with('error', 'Invalid disease ID.');
        }

        $this->diseaseModel->delete($id);

        return redirect()->to('/disease')
            ->with('success', 'Disease deleted successfully.');
    }
}
