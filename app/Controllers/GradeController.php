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
        $trialPlayerModel = new \App\Models\TrialPlayerModel();
        $gradeModel = new GradeModel();

        // Get only verified trial players (full payment completed)
        $data['players'] = $trialPlayerModel->where('payment_status', 'full')
                                           ->where('verified_at IS NOT NULL')
                                           ->findAll();
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
            // Check if a grade assignment already exists for this trial player
            $existing = $gradeAssignModel->where('trial_player_id', $playerId)->first();

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
                    'trial_player_id' => $playerId,
                    'grade_id'        => $gradeId,
                    'assigned_at'     => date('Y-m-d H:i:s'),
                    'assigned_by'     => session()->get('user_id'),
                    'status'          => 'active',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Grades assigned/updated successfully.');
    }

    // Frontend methods for players to check their grades
    public function checkGrade()
    {
        return view('frontend/grades/check');
    }

    public function getGradeByMobile()
    {
        $mobile = $this->request->getPost('mobile');
        
        if (!$mobile) {
            return redirect()->back()->with('error', 'Please enter your mobile number.');
        }

        $trialPlayerModel = new \App\Models\TrialPlayerModel();
        $gradeAssignModel = new GradeAssignModel();
        $gradeModel = new GradeModel();

        // Find trial player by mobile
        $player = $trialPlayerModel->where('mobile', $mobile)->first();

        if (!$player) {
            return redirect()->back()->with('error', 'No player found with this mobile number.');
        }

        // Get assigned grade
        $gradeAssignment = $gradeAssignModel->where('trial_player_id', $player['id'])
                                           ->where('status', 'active')
                                           ->first();

        $data['player'] = $player;
        $data['grade'] = null;
        
        if ($gradeAssignment) {
            $data['grade'] = $gradeModel->find($gradeAssignment['grade_id']);
        }

        return view('frontend/grades/result', $data);
    }
}
