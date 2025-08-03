<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TeamModel;
use App\Models\TeamPlayerModel;
use App\Models\LeaguePlayerModel;
use App\Models\TrialPlayerModel;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class TeamController extends BaseController
{
    public function index()
    {
        $teamModel = new TeamModel();
        $data['teams'] = $teamModel->getAllTeamsWithPlayerCount();

        // Ensure we have exactly 16 teams
        $this->ensureSixteenTeams();
        $data['teams'] = $teamModel->getAllTeamsWithPlayerCount();

        return view('admin/teams/index', $data);
    }

    public function manageTeam($teamId)
    {
        $teamModel = new TeamModel();
        $teamPlayerModel = new TeamPlayerModel();

        $data['team'] = $teamModel->find($teamId);
        if (!$data['team']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Team not found');
        }

        $data['teamPlayers'] = $teamPlayerModel->getTeamPlayers($teamId);
        $data['availablePlayers'] = $teamPlayerModel->getAvailablePlayers($teamId);

        // Get league players for team assignment (only selected players)
        $leagueModel = new \App\Models\LeaguePlayerModel();
        $data['league_players'] = $leagueModel->where('payment_status', 'paid')
                                             ->where('status', 'selected')
                                             ->findAll();

        return view('admin/teams/manage', $data);
    }

    public function updateTeam($teamId)
    {
        $teamModel = new TeamModel();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[50]',
            'coach_name' => 'permit_empty|max_length[100]',
            'description' => 'permit_empty|max_length[500]',
            'status' => 'required|in_list[draft,active,inactive]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'coach_name' => $this->request->getPost('coach_name'),
            'description' => $this->request->getPost('description'),
            'status' => $this->request->getPost('status')
        ];

        if ($teamModel->update($teamId, $data)) {
            return redirect()->back()->with('success', 'Team updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to update team.');
        }
    }

    public function addPlayer()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            $data = $this->request->getJSON(true);
            $teamPlayerModel = new TeamPlayerModel();

            // Check if team already has 11 players
            $currentCount = $teamPlayerModel->where('team_id', $data['team_id'])->countAllResults();
            if ($currentCount >= 11) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Team already has maximum 11 players'
                ]);
            }

            // Check if player is already assigned to another team
            $existingAssignment = $teamPlayerModel->where('player_id', $data['player_id'])
                ->where('player_type', 'league')
                ->first();
            if ($existingAssignment) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Player is already assigned to another team'
                ]);
            }

            $insertData = [
                'team_id' => $data['team_id'],
                'player_id' => $data['player_id'],
                'player_type' => 'league',
                'position' => $data['position'] ?? null,
                'jersey_number' => $data['jersey_number'] ?? null
            ];

            if ($teamPlayerModel->insert($insertData)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Player added to team successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to add player to team'
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'Add player to team error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while adding player'
            ]);
        }
    }

    public function removePlayer()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            $data = $this->request->getJSON(true);
            $teamPlayerModel = new TeamPlayerModel();

            if ($teamPlayerModel->delete($data['assignment_id'])) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Player removed from team successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to remove player from team'
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'Remove player from team error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while removing player'
            ]);
        }
    }

    public function setCaptain()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            $data = $this->request->getJSON(true);
            $teamPlayerModel = new TeamPlayerModel();
            $teamModel = new TeamModel();

            // Remove current captain status from all players in the team
            $teamPlayerModel->where('team_id', $data['team_id'])
                ->set(['is_captain' => 0])
                ->update();

            // Set new captain
            $teamPlayerModel->update($data['assignment_id'], ['is_captain' => 1]);

            // Update team captain_id
            $assignment = $teamPlayerModel->find($data['assignment_id']);
            $teamModel->update($data['team_id'], ['captain_id' => $assignment['player_id']]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Captain set successfully'
            ]);
        } catch (Exception $e) {
            log_message('error', 'Set captain error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while setting captain'
            ]);
        }
    }

    private function ensureSixteenTeams()
    {
        $teamModel = new TeamModel();
        $currentTeams = $teamModel->countAllResults();

        if ($currentTeams < 16) {
            for ($i = $currentTeams + 1; $i <= 16; $i++) {
                $teamModel->insert([
                    'name' => 'Team ' . $i,
                    'status' => 'draft'
                ]);
            }
        }
    }
}