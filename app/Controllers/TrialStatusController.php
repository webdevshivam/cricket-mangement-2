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

        // Add random motivational lines for cricket players
        $motivationLines = [
            "Your cricket journey starts here! Keep practicing and stay dedicated until your dream becomes reality!",
            "Champions are made through consistent effort. Keep pushing forward and never give up!",
            "Every great cricketer started with a dream. Work hard until you make yours a reality!",
            "Success in cricket comes to those who never quit. Stay strong and keep fighting!",
            "Your potential is unlimited. Keep working hard and believe in yourself until dreams come true!",
            "Cricket is not just a game, it's a passion. Follow yours with dedication and hard work!",
            "The field is waiting for your talent. Show them what you've got through relentless practice!",
            "Practice makes perfect. Keep honing your skills every day until you achieve greatness!",
            "Great cricketers are made, not born. You're on the right path - keep working hard!",
            "Your dedication today will be your success tomorrow. Never stop until dreams are fulfilled!",
            "Every ball you face, every run you score brings you closer to your dream. Keep going!",
            "The hardest worker in the room becomes the champion. Make sure that's you!",
            "Dreams don't work unless you do. Put in the effort until your cricket dreams come alive!",
            "Talent gets you noticed, but hard work gets you selected. Keep grinding until success!",
            "Cricket teaches patience and perseverance. Apply both until you reach your goals!",
            "Champions don't become champions in the ring. They become champions in training!",
            "Your bat is your pen, the pitch is your paper. Write your success story with hard work!",
            "Every legend was once a beginner who refused to give up. Be that legend!",
            "The pitch doesn't care about your background, only your performance. Work until you shine!",
            "Cricket is a game of inches and moments. Prepare through hard work for your moment!",
            "Sweat in training saves tears in matches. Work hard until your skills become unstoppable!",
            "The only way to prove you're a good sport is to lose gracefully and work harder next time!",
            "Your cricket bat should be your best friend - spend time with it until mastery is achieved!",
            "In cricket, as in life, the harder you work, the luckier you get. Keep working!"
        ];

        $data['player'] = $player;
        $data['grade'] = null;
        $data['motivation'] = $motivationLines[array_rand($motivationLines)];

        if ($gradeAssignment) {
            $data['grade'] = $gradeModel->find($gradeAssignment['grade_id']);
        }

        return view('frontend/trial/status_result', $data);
    }
}
