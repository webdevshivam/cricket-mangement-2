<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TournamentModel;
use App\Models\TournamentMatchModel;
use App\Models\TeamModel;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class TournamentController extends BaseController
{
    public function index()
    {
        $tournamentModel = new TournamentModel();
        $data['tournaments'] = $tournamentModel->orderBy('created_at', 'DESC')->findAll();

        return view('admin/tournaments/index', $data);
    }

    public function create()
    {
        $teamModel = new TeamModel();
        $data['teams'] = $teamModel->where('status !=', 'inactive')->findAll();

        return view('admin/tournaments/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[100]',
            'description' => 'permit_empty|max_length[500]',
            'type' => 'required|in_list[knockout,round_robin]',
            'start_date' => 'required|valid_date'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $tournamentModel = new TournamentModel();

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'type' => $this->request->getPost('type'),
            'status' => 'draft',
            'current_round' => 1,
            'start_date' => $this->request->getPost('start_date')
        ];

        if ($tournamentModel->insert($data)) {
            $tournamentId = $tournamentModel->getInsertID();

            // If it's a knockout tournament, generate initial matches
            if ($this->request->getPost('type') === 'knockout') {
                $this->generateKnockoutMatches($tournamentId);
            }

            return redirect()->to('admin/tournaments')->with('success', 'Tournament created successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to create tournament.');
        }
    }

    public function manage($tournamentId)
    {
        $tournamentModel = new TournamentModel();
        $tournamentMatchModel = new TournamentMatchModel();
        $teamModel = new TeamModel();

        $data['tournament'] = $tournamentModel->find($tournamentId);
        if (!$data['tournament']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Tournament not found');
        }

        $data['matches'] = $tournamentMatchModel->getTournamentMatches($tournamentId);
        $data['teams'] = $teamModel->findAll();

        // Group matches by round
        $data['rounds'] = [];
        foreach ($data['matches'] as $match) {
            $data['rounds'][$match['round_number']][] = $match;
        }

        return view('admin/tournaments/manage', $data);
    }

    public function updateMatch()
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
            $tournamentMatchModel = new TournamentMatchModel();
            $tournamentModel = new TournamentModel();

            $updateData = [
                'winner_team_id' => $data['winner_team_id'],
                'team1_score' => $data['team1_score'] ?? null,
                'team2_score' => $data['team2_score'] ?? null,
                'status' => 'completed',
                'notes' => $data['notes'] ?? null
            ];

            if ($tournamentMatchModel->update($data['match_id'], $updateData)) {
                // Check if round is complete and generate next round if needed
                $match = $tournamentMatchModel->find($data['match_id']);
                $this->checkAndGenerateNextRound($match['tournament_id'], $match['round_number']);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Match result updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update match result'
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'Update match error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while updating match'
            ]);
        }
    }

    private function generateKnockoutMatches($tournamentId)
    {
        $teamModel = new TeamModel();
        $tournamentMatchModel = new TournamentMatchModel();

        // Get all active teams (16 teams)
        $teams = $teamModel->where('status !=', 'inactive')->findAll(16);

        if (count($teams) < 16) {
            return false;
        }

        // Shuffle teams for random matchups
        shuffle($teams);

        // Generate Round 1 matches (16 teams -> 8 matches)
        for ($i = 0; $i < 16; $i += 2) {
            $matchData = [
                'tournament_id' => $tournamentId,
                'round_number' => 1,
                'match_number' => ($i / 2) + 1,
                'team1_id' => $teams[$i]['id'],
                'team2_id' => $teams[$i + 1]['id'],
                'status' => 'scheduled'
            ];

            $tournamentMatchModel->insert($matchData);
        }

        return true;
    }

    private function checkAndGenerateNextRound($tournamentId, $currentRound)
    {
        $tournamentMatchModel = new TournamentMatchModel();
        $tournamentModel = new TournamentModel();

        // Get all matches in current round
        $currentRoundMatches = $tournamentMatchModel->where('tournament_id', $tournamentId)
            ->where('round_number', $currentRound)
            ->findAll();

        // Check if all matches in current round are completed
        $completedMatches = array_filter($currentRoundMatches, function ($match) {
            return $match['status'] === 'completed' && !empty($match['winner_team_id']);
        });

        if (count($completedMatches) === count($currentRoundMatches)) {
            // Get winners for next round
            $winners = $tournamentMatchModel->getRoundWinners($tournamentId, $currentRound);

            if (count($winners) === 1) {
                // Tournament is complete
                $tournamentModel->update($tournamentId, [
                    'status' => 'completed',
                    'winner_team_id' => $winners[0]['winner_team_id']
                ]);
            } else if (count($winners) > 1) {
                // Generate next round
                $nextRound = $currentRound + 1;
                $matchNumber = 1;

                for ($i = 0; $i < count($winners); $i += 2) {
                    if (isset($winners[$i + 1])) {
                        $matchData = [
                            'tournament_id' => $tournamentId,
                            'round_number' => $nextRound,
                            'match_number' => $matchNumber,
                            'team1_id' => $winners[$i]['winner_team_id'],
                            'team2_id' => $winners[$i + 1]['winner_team_id'],
                            'status' => 'scheduled'
                        ];

                        $tournamentMatchModel->insert($matchData);
                        $matchNumber++;
                    }
                }

                // Update tournament current round
                $tournamentModel->update($tournamentId, ['current_round' => $nextRound]);
            }
        }
    }

    public function bracket($tournamentId)
    {
        $tournamentModel = new TournamentModel();
        $tournamentMatchModel = new TournamentMatchModel();

        $data['tournament'] = $tournamentModel->find($tournamentId);
        if (!$data['tournament']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Tournament not found');
        }

        $data['matches'] = $tournamentMatchModel->getTournamentMatches($tournamentId);

        // Group matches by round
        $data['rounds'] = [];
        foreach ($data['matches'] as $match) {
            $data['rounds'][$match['round_number']][] = $match;
        }

        return view('admin/tournaments/bracket', $data);
    }
}
