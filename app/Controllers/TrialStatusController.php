<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TrialPlayerModel;
use App\Models\GradeAssignModel;
use App\Models\GradeModel;
use CodeIgniter\HTTP\ResponseInterface;

class TrialStatusController extends BaseController
{
    public function checkStatus()
    {
        return view('frontend/trial/check_status');
    }

    public function getStatus()
    {
        $mobile = $this->request->getPost('mobile');

        if (!$mobile) {
            return redirect()->back()->with('error', 'Mobile number is required.');
        }

        $model = new TrialPlayerModel();
        $gradeAssignModel = new GradeAssignModel();
        $gradeModel = new GradeModel();

        $player = $model->where('mobile', $mobile)->first();

        if (!$player) {
            return redirect()->back()->with('error', 'No registration found with mobile number ' . $mobile . '. Please check the number or register for the trial first.');
        }

        // Get assigned grade if exists (for trial players, use trial_player_id)
        $gradeAssignment = $gradeAssignModel->where('trial_player_id', $player['id'])
            ->where('status', 'active')
            ->first();

        $data['player'] = $player;
        $data['grade'] = null;

        if ($gradeAssignment) {
            $data['grade'] = $gradeModel->find($gradeAssignment['grade_id']);
        }

        return view('frontend/trial/status_result', $data);
    }
}
