
<?php

namespace App\Models;

use CodeIgniter\Model;

class TeamPlayerModel extends Model
{
    protected $table            = 'team_players';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'team_id', 'player_id', 'player_type', 'position', 'jersey_number', 'is_captain', 'is_vice_captain'
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
        'team_id' => 'required|integer',
        'player_id' => 'required|integer',
        'player_type' => 'in_list[league]',
        'jersey_number' => 'permit_empty|integer|greater_than[0]|less_than[100]'
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

    public function getTeamPlayers($teamId)
    {
        return $this->select('team_players.*, 
                             league_players.name as league_name, 
                             league_players.email as league_email,
                             league_players.mobile as league_mobile,
                             league_players.cricketer_type as league_type')
                    ->join('league_players', 'league_players.id = team_players.player_id', 'left')
                    ->where('team_players.team_id', $teamId)
                    ->where('team_players.player_type', 'league')
                    ->orderBy('team_players.jersey_number', 'ASC')
                    ->findAll();
    }

    public function getAvailablePlayers($teamId = null)
    {
        $assignedPlayerIds = $this->select('player_id')
                                 ->where('player_type', 'league')
                                 ->findAll();
        
        $assignedLeague = array_column($assignedPlayerIds, 'player_id');

        $leagueModel = new \App\Models\LeaguePlayerModel();

        $availableLeague = $leagueModel->where('payment_status', 'paid');
        if (!empty($assignedLeague)) {
            $availableLeague->whereNotIn('id', $assignedLeague);
        }
        $leaguePlayers = $availableLeague->findAll();

        return [
            'league' => $leaguePlayers,
            'trial' => []
        ];
    }
}
