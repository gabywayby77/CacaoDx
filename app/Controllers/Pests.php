<?php

namespace App\Controllers;

use App\Models\PestModel;

class Pests extends BaseController
{
    protected $pestModel;

    public function __construct()
    {
        $this->pestModel = new PestModel();
    }

    public function index()
    {
        return view('pests', [
            'pests' => $this->pestModel->findAll()
        ]);
    }

    public function store()
    {
        $this->pestModel->insert($this->request->getPost([
            'name',
            'scientific_name',
            'family',
            'description',
            'damage',
            'plant_part_id',
        ]));

        return redirect()->to('/pests')->with('success', 'Pest added');
    }

    public function update()
    {
        $id = $this->request->getPost('id');

        if (!$id) {
            return redirect()->back()->with('error', 'Missing pest ID');
        }

        $this->pestModel->update($id, $this->request->getPost([
            'name',
            'scientific_name',
            'family',
            'description',
            'damage',
            'plant_part_id',
        ]));

        return redirect()->to('/pests')->with('success', 'Pest updated');
    }

    public function delete()
    {
        $id = $this->request->getPost('id');

        if ($id) {
            $this->pestModel->delete($id);
        }

        return redirect()->to('/pests')->with('success', 'Pest deleted');
    }
}
