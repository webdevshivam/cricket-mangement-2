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

    public function assign()
    {
        $trialPlayerModel = new \App\Models\TrialPlayerModel();
        $gradeModel = new GradeModel();
        $gradeAssignModel = new GradeAssignModel();

        // Get players who already have grade assignments
        $assignedPlayerIds = $gradeAssignModel->select('trial_player_id')
                                             ->where('status', 'active')
                                             ->where('trial_player_id IS NOT NULL')
                                             ->findColumn('trial_player_id');

        // Get only verified trial players (full payment completed) who don't have grade assignments
        $playersQuery = $trialPlayerModel->where('payment_status', 'full')
                                        ->where('verified_at IS NOT NULL');
        
        if (!empty($assignedPlayerIds)) {
            $playersQuery->whereNotIn('id', $assignedPlayerIds);
        }
        
        $data['players'] = $playersQuery->findAll();
        $data['grades'] = $gradeModel->where('status', 'active')->findAll();

        return view('admin/grades/assign', $data);
    }

    public function assignGrade()
    {
        $playerIds = $this->request->getPost('selected');
        $gradeId = $this->request->getPost('grade_id');

        if (!$playerIds || !$gradeId) {
            return redirect()->to('admin/grades/assign')->with('error', 'Please select at least one player and a grade.');
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

        return redirect()->to('admin/grades/assign')->with('success', 'Grades assigned/updated successfully.');
    }

    // Admin methods for managing grade assignments
    public function viewAssignments()
    {
        $gradeAssignModel = new GradeAssignModel();
        $trialPlayerModel = new \App\Models\TrialPlayerModel();
        $gradeModel = new GradeModel();

        // Get all active grade assignments with player and grade details
        $assignments = $gradeAssignModel->select('grade_assign.*, trial_players.name as player_name, trial_players.mobile, grades.title as grade_title')
                                       ->join('trial_players', 'trial_players.id = grade_assign.trial_player_id')
                                       ->join('grades', 'grades.id = grade_assign.grade_id')
                                       ->where('grade_assign.status', 'active')
                                       ->findAll();

        $data['assignments'] = $assignments;
        return view('admin/grades/assignments', $data);
    }

    public function updateAssignment($id)
    {
        $gradeId = $this->request->getPost('grade_id');
        
        if (!$gradeId) {
            return redirect()->back()->with('error', 'Please select a grade.');
        }

        $gradeAssignModel = new GradeAssignModel();
        $gradeAssignModel->update($id, [
            'grade_id' => $gradeId,
            'assigned_at' => date('Y-m-d H:i:s'),
            'assigned_by' => session()->get('user_id')
        ]);

        return redirect()->back()->with('success', 'Grade assignment updated successfully.');
    }

    public function deleteAssignment($id)
    {
        $gradeAssignModel = new GradeAssignModel();
        $gradeAssignModel->update($id, ['status' => 'inactive']);

        return redirect()->back()->with('success', 'Grade assignment removed successfully.');
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
            return redirect()->to('grades/check')->with('error', 'Please enter your mobile number.');
        }

        $trialPlayerModel = new \App\Models\TrialPlayerModel();
        $gradeAssignModel = new GradeAssignModel();
        $gradeModel = new GradeModel();

        // Find trial player by mobile
        $player = $trialPlayerModel->where('mobile', $mobile)->first();

        if (!$player) {
            return redirect()->to('grades/check')->with('error', 'No player found with this mobile number. Please check your mobile number and try again.');
        }

        // Check if player is verified and has full payment
        if ($player['payment_status'] !== 'full' || empty($player['verified_at'])) {
            return redirect()->to('grades/check')->with('error', 'Your account is not yet verified or payment is incomplete. Please contact administration.');
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
