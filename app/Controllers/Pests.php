<?php

namespace App\Controllers;

use App\Models\PestModel;

class Pests extends BaseController
{
    protected $pestModel;

    public function __construct()
    {
        $this->pestModel = new PestModel();
        helper('auth'); // Load auth helper
    }

    public function index()
    {
        $session = session();

        // Get user name for the header
        $userName = trim(
            ($session->get('first_name') ?? '') . ' ' .
            ($session->get('last_name') ?? '')
        );

        return view('pests', [
            'userName' => $userName,
            'pests' => $this->pestModel->findAll()
        ]);
    }

    public function store()
    {
        if (!can_edit()) {
            return redirect()->back()
                ->with('error', 'Access denied. Admin privileges required.');
        }

        $rules = [
            'name'            => 'required|min_length[3]|max_length[100]',
            'scientific_name' => 'required|min_length[3]|max_length[150]',
            'family'          => 'permit_empty|max_length[100]',
            'description'     => 'required|min_length[10]',
            'damage'          => 'required|min_length[10]',
            'plant_part_id'   => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please check your input fields');
        }

        $this->pestModel->insert($this->request->getPost([
            'name',
            'scientific_name',
            'family',
            'description',
            'damage',
            'plant_part_id',
        ]));

        return redirect()->to('/pests')->with('success', 'Pest added successfully!');
    }

    public function update()
    {
        if (!can_edit()) {
            return redirect()->back()
                ->with('error', 'Access denied. Admin privileges required.');
        }

        $id = $this->request->getPost('id');

        if (!$id) {
            return redirect()->back()->with('error', 'Missing pest ID');
        }

        $rules = [
            'name'            => 'required|min_length[3]|max_length[100]',
            'scientific_name' => 'required|min_length[3]|max_length[150]',
            'family'          => 'permit_empty|max_length[100]',
            'description'     => 'required|min_length[10]',
            'damage'          => 'required|min_length[10]',
            'plant_part_id'   => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please check your input fields');
        }

        $this->pestModel->update($id, $this->request->getPost([
            'name',
            'scientific_name',
            'family',
            'description',
            'damage',
            'plant_part_id',
        ]));

        return redirect()->to('/pests')->with('success', 'Pest updated successfully!');
    }

    public function delete()
    {
        if (!can_edit()) {
            return redirect()->back()
                ->with('error', 'Access denied. Admin privileges required.');
        }

        $id = $this->request->getPost('id');

        if (!$id) {
            return redirect()->back()->with('error', 'Invalid pest ID');
        }

        $this->pestModel->delete($id);

        return redirect()->to('/pests')->with('success', 'Pest deleted successfully!');
    }
}