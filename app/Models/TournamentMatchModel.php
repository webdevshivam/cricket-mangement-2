
<?php

namespace App\Models;

use CodeIgniter\Model;

class TournamentMatchModel extends Model
{
    protected $table            = 'tournament_matches';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tournament_id',
        'round_number',
        'match_number',
        'team1_id',
        'team2_id',
        'winner_team_id',
        'team1_score',
        'team2_score',
        'status',
        'match_date',
        'notes'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'tournament_id' => 'required|integer',
        'round_number' => 'required|integer|greater_than[0]',
        'team1_id' => 'required|integer',
        'team2_id' => 'required|integer',
        'status' => 'in_list[scheduled,completed,cancelled]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getTournamentMatches($tournamentId, $roundNumber = null)
    {
        $query = $this->select('tournament_matches.*,
                               t1.name as team1_name,
                               t2.name as team2_name,
                               winner.name as winner_name')
            ->join('teams t1', 't1.id = tournament_matches.team1_id', 'left')
            ->join('teams t2', 't2.id = tournament_matches.team2_id', 'left')
            ->join('teams winner', 'winner.id = tournament_matches.winner_team_id', 'left')
            ->where('tournament_matches.tournament_id', $tournamentId)
            ->orderBy('tournament_matches.round_number', 'ASC')
            ->orderBy('tournament_matches.match_number', 'ASC');

        if ($roundNumber !== null) {
            $query->where('tournament_matches.round_number', $roundNumber);
        }

        return $query->findAll();
    }

    public function getRoundWinners($tournamentId, $roundNumber)
    {
        return $this->select('winner_team_id')
            ->where('tournament_id', $tournamentId)
            ->where('round_number', $roundNumber)
            ->where('status', 'completed')
            ->whereNotNull('winner_team_id')
            ->findAll();
    }
}
