<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark border-warning stats-card mb-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <h5 class="card-title text-warning mb-0">
            <i class="fas fa-edit me-2"></i>Manage <?= esc($team['name']) ?>
        </h5>
        <div>
            <a href="<?= base_url('admin/teams') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-2"></i>Back to Teams
            </a>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Team Information -->
    <div class="col-lg-4 mb-4">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Team Information</h6>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/teams/update/' . $team['id']) ?>" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Team Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= esc($team['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="coach_name" class="form-label">Coach Name</label>
                        <input type="text" class="form-control" id="coach_name" name="coach_name" 
                               value="<?= esc($team['coach_name'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3"><?= esc($team['description'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="draft" <?= $team['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="active" <?= $team['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $team['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-warning w-100">
                        <i class="fas fa-save me-2"></i>Update Team
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Current Players -->
    <div class="col-lg-8 mb-4">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-users me-2"></i>Current Players (<?= count($teamPlayers) ?>/11)
                </h6>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addPlayerModal">
                    <i class="fas fa-plus me-2"></i>Add Player
                </button>
            </div>
            <div class="card-body">
                <?php if (empty($teamPlayers)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <p>No players assigned to this team yet.</p>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPlayerModal">
                            <i class="fas fa-plus me-2"></i>Add First Player
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-striped">
                            <thead>
                                <tr>
                                    <th>Jersey #</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Contact</th>
                                    <th>Position</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($teamPlayers as $player): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?= $player['jersey_number'] ?: '-' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong>
                                                <?= esc($player['league_name'] ?: $player['trial_name']) ?>
                                            </strong>
                                            <?php if ($player['is_captain']): ?>
                                                <span class="badge bg-warning text-dark ms-1">C</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $player['player_type'] === 'league' ? 'success' : 'info' ?>">
                                                <?= ucfirst($player['player_type']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small>
                                                <?= esc($player['league_mobile'] ?: $player['trial_mobile']) ?><br>
                                                <span class="text-muted">
                                                    <?= esc($player['league_email'] ?: $player['trial_email']) ?>
                                                </span>
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                <?= esc($player['league_type'] ?: $player['trial_type']) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php if (!$player['is_captain']): ?>
                                                <button class="btn btn-outline-warning btn-sm" 
                                                        onclick="setCaptain(<?= $team['id'] ?>, <?= $player['id'] ?>)">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-warning">
                                                    <i class="fas fa-star"></i> Captain
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-outline-danger btn-sm" 
                                                    onclick="removePlayer(<?= $player['id'] ?>, '<?= esc($player['league_name'] ?: $player['trial_name']) ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Player Modal -->
<div class="modal fade" id="addPlayerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white">
                    <i class="fas fa-plus me-2"></i>Add Player to <?= esc($team['name']) ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- League Players -->
                    <div class="col-md-12">
                        <h6 class="text-success mb-3">
                            <i class="fas fa-trophy me-2"></i>League Players
                        </h6>
                        <div class="available-players-list" style="max-height: 400px; overflow-y: auto;">
                            <?php if (empty($availablePlayers['league'])): ?>
                                <p class="text-muted">No available league players</p>
                            <?php else: ?>
                                <?php foreach ($availablePlayers['league'] as $player): ?>
                                    <div class="player-card mb-2 p-2 border border-secondary rounded">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?= esc($player['name']) ?></strong><br>
                                                <small class="text-muted">
                                                    <?= esc($player['mobile']) ?> | <?= esc($player['cricketer_type']) ?>
                                                </small>
                                            </div>
                                            <button class="btn btn-success btn-sm" 
                                                    onclick="addPlayerToTeam(<?= $team['id'] ?>, <?= $player['id'] ?>, 'league', '<?= esc($player['name']) ?>')">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function addPlayerToTeam(teamId, playerId, playerType, playerName) {
    if (confirm(`Add ${playerName} to the team?`)) {
        fetch('<?= base_url('admin/teams/add-player') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                team_id: teamId,
                player_id: playerId,
                player_type: playerType
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the player.');
        });
    }
}

function removePlayer(assignmentId, playerName) {
    if (confirm(`Remove ${playerName} from the team?`)) {
        fetch('<?= base_url('admin/teams/remove-player') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                assignment_id: assignmentId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while removing the player.');
        });
    }
}

function setCaptain(teamId, assignmentId) {
    if (confirm('Set this player as team captain?')) {
        fetch('<?= base_url('admin/teams/set-captain') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                team_id: teamId,
                assignment_id: assignmentId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while setting captain.');
        });
    }
}
</script>

<style>
.player-card {
    transition: background-color 0.2s ease;
}

.player-card:hover {
    background-color: rgba(255,255,255,0.05);
}

.available-players-list {
    border: 1px solid #6c757d;
    border-radius: 0.375rem;
    padding: 0.5rem;
}
</style>

<?= $this->endSection(); ?>