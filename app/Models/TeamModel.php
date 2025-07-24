<?php

namespace App\Models;

use CodeIgniter\Model;

class TeamModel extends Model
{
    protected $table            = 'teams';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'captain_id',
        'coach_name',
        'status',
        'logo',
        'description'
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
        'name' => 'required|min_length[3]|max_length[50]',
        'status' => 'in_list[draft,active,inactive]'
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

    public function getTeamWithPlayers($teamId)
    {
        return $this->select('teams.*,
                             COUNT(team_players.id) as player_count')
            ->join('team_players', 'team_players.team_id = teams.id', 'left')
            ->where('teams.id', $teamId)
            ->groupBy('teams.id')
            ->first();
    }

    public function getAllTeamsWithPlayerCount()
    {
        return $this->select('teams.*,
                             COUNT(team_players.id) as player_count')
            ->join('team_players', 'team_players.team_id = teams.id', 'left')
            ->groupBy('teams.id')
            ->orderBy('teams.id', 'ASC')
            ->findAll();
    }
}
