<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\GradeAssignModel;
use App\Models\GradeModel;
use App\Models\PlayersModel;
use CodeIgniter\HTTP\ResponseInterface;

class GradeController extends BaseController
{
    public function index()
    {


        $model = new GradeModel();
        $data['grades'] = $model->paginate(10);
        $data['pager']  = $model->pager;
        return view('admin/grades/index', $data);
    }
    public function create()
    {
        return view('admin/grades/create');
    }

    public function save()
    {
        $model = new GradeModel();

        $model->save([
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'league_fee' => $this->request->getPost('league_fee'),
            'status' => $this->request->getPost('status')
        ]);

        return redirect()->to('admin/grades');
    }

    public function edit($id)
    {
        $model = new GradeModel();
        $data['grade'] = $model->find($id);

        return view('admin/grades/edit', $data);
    }

    public function update($id)
    {
        $model = new GradeModel();

        $model->update($id, [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'league_fee' => $this->request->getPost('league_fee'),
            'status' => $this->request->getPost('status')
        ]);

        return redirect()->to('admin/grades');
    }
    public function delete($id)
    {
        $model = new GradeModel();
        $model->delete($id);

        return redirect()->to('admin/grades');
    }

    public function assignSave()
    {

        $playerModel = new PlayersModel();
        $gradeModel = new GradeModel();

        $data['players'] = $playerModel->findAll();
        $data['grades'] = $gradeModel->where('status', 'active')->findAll();

        return view('admin/grades/assign', $data);
    }

    public function assignGrade()
    {
        $playerIds = $this->request->getPost('selected');
        $gradeId = $this->request->getPost('grade_id');

        if (!$playerIds || !$gradeId) {
            return redirect()->back()->with('error', 'Please select at least one player and a grade.');
        }

        $gradeAssignModel = new GradeAssignModel();

        foreach ($playerIds as $playerId) {
            // Check if a grade assignment already exists for this player
            $existing = $gradeAssignModel->where('player_id', $playerId)->first();

            if ($existing) {
                // Update the existing grade assignment
                $gradeAssignModel->update($existing['id'], [
                    'grade_id'    => $gradeId,
                    'assigned_at' => date('Y-m-d H:i:s'),
                    'assigned_by' => session()->get('user_id'),
                    'status'      => 'active',
                ]);
            } else {
                // Insert new grade assignment
                $gradeAssignModel->insert([
                    'player_id'   => $playerId,
                    'grade_id'    => $gradeId,
                    'assigned_at' => date('Y-m-d H:i:s'),
                    'assigned_by' => session()->get('user_id'),
                    'status'      => 'active',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Grades assigned/updated successfully.');
    }
}
