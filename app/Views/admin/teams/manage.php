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
                            <i class="fas fa-trophy me-2"></i>League Players (Paid Only)
                        </h6>
                        
                        <!-- Search Filter -->
                        <?php if (!empty($availablePlayers['league'])): ?>
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-secondary border-secondary">
                                        <i class="fas fa-search text-white"></i>
                                    </span>
                                    <input type="text" class="form-control" id="playerSearch" 
                                           placeholder="Search by name, mobile, or cricket type..." 
                                           onkeyup="filterPlayers()">
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Only paid league players are shown here
                                </small>
                            </div>
                        <?php endif; ?>
                        
                        <div class="available-players-list" style="max-height: 400px; overflow-y: auto;">
                            <?php if (empty($availablePlayers['league'])): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-white mb-3"></i>
                                    <p class="text-white mb-2">No available league players</p>
                                    <small class="text-muted">
                                        Only paid league players who are not assigned to any team will appear here
                                    </small>
                                </div>
                            <?php else: ?>
                                <div id="playersContainer">
                                    <?php foreach ($availablePlayers['league'] as $player): ?>
                                        <div class="player-card mb-2 p-3 border border-secondary rounded" 
                                             data-name="<?= strtolower(esc($player['name'])) ?>"
                                             data-mobile="<?= esc($player['mobile']) ?>"
                                             data-type="<?= strtolower(esc($player['cricketer_type'])) ?>">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <strong class="text-white me-2"><?= esc($player['name']) ?></strong>
                                                        <span class="badge bg-success">PAID</span>
                                                    </div>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-phone me-1"></i><?= esc($player['mobile']) ?>
                                                        </small>
                                                        <small class="text-muted">
                                                            <i class="fas fa-cricket-ball me-1"></i><?= esc($player['cricketer_type']) ?>
                                                        </small>
                                                        <?php if (!empty($player['age'])): ?>
                                                            <small class="text-muted">
                                                                <i class="fas fa-calendar me-1"></i><?= esc($player['age']) ?> years
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <button class="btn btn-success btn-sm" 
                                                        onclick="addPlayerToTeam(<?= $team['id'] ?>, <?= $player['id'] ?>, 'league', '<?= esc($player['name']) ?>')"
                                                        title="Add to team">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <!-- No results message (initially hidden) -->
                                <div id="noResultsMessage" class="text-center py-4" style="display: none;">
                                    <i class="fas fa-search fa-2x text-white mb-2"></i>
                                    <p class="text-white mb-0">No players match your search</p>
                                    <small class="text-muted">Try adjusting your search terms</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filterPlayers() {
    const searchTerm = document.getElementById('playerSearch').value.toLowerCase();
    const playerCards = document.querySelectorAll('.player-card');
    const noResultsMessage = document.getElementById('noResultsMessage');
    let visibleCount = 0;

    playerCards.forEach(card => {
        const name = card.dataset.name || '';
        const mobile = card.dataset.mobile || '';
        const type = card.dataset.type || '';
        
        const isVisible = name.includes(searchTerm) || 
                         mobile.includes(searchTerm) || 
                         type.includes(searchTerm);
        
        if (isVisible) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    // Show/hide no results message
    if (visibleCount === 0 && searchTerm !== '') {
        noResultsMessage.style.display = 'block';
    } else {
        noResultsMessage.style.display = 'none';
    }
}

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
    transition: all 0.2s ease;
    background-color: rgba(255,255,255,0.02);
}

.player-card:hover {
    background-color: rgba(255,255,255,0.08);
    border-color: #ffc107 !important;
    transform: translateY(-1px);
}

.available-players-list {
    border: 1px solid #6c757d;
    border-radius: 0.375rem;
    padding: 0.5rem;
    background-color: rgba(0,0,0,0.2);
}

#playerSearch {
    background-color: #2d3748;
    border-color: #6c757d;
    color: white;
}

#playerSearch::placeholder {
    color: #9ca3af;
}

#playerSearch:focus {
    background-color: #374151;
    border-color: #ffc107;
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

.input-group-text {
    color: white;
}

.fas.fa-cricket-ball:before {
    content: "\f1e3"; /* Using baseball icon as cricket ball alternative */
}
</style>

<?= $this->endSection(); ?>