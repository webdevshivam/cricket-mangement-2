<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark border-warning stats-card mb-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <h5 class="card-title text-warning mb-0">
            <i class="fas fa-cog me-2"></i>Manage Tournament: <?= esc($tournament['name']) ?>
        </h5>
        <div>
            <a href="<?= base_url('admin/tournaments/bracket/' . $tournament['id']) ?>" class="btn btn-outline-info me-2">
                <i class="fas fa-sitemap me-2"></i>View Bracket
            </a>
            <a href="<?= base_url('admin/tournaments') ?>" class="btn btn-outline-warning">
                <i class="fas fa-arrow-left me-2"></i>Back to Tournaments
            </a>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-dark border-info">
            <div class="card-body text-center">
                <h6 class="text-info">Status</h6>
                <span class="badge bg-<?= $tournament['status'] === 'completed' ? 'success' : ($tournament['status'] === 'active' ? 'warning' : 'info') ?> fs-6">
                    <?= ucfirst($tournament['status']) ?>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-dark border-warning">
            <div class="card-body text-center">
                <h6 class="text-warning">Current Round</h6>
                <span class="text-light fs-4"><?= $tournament['current_round'] ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-dark border-success">
            <div class="card-body text-center">
                <h6 class="text-success">Type</h6>
                <span class="text-light"><?= ucfirst($tournament['type']) ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-dark border-primary">
            <div class="card-body text-center">
                <button type="button" class="btn btn-primary btn-sm" onclick="openCreateMatchModal()">
                    <i class="fas fa-plus me-1"></i>Add Match
                </button>
            </div>
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

<?php if (!empty($rounds)): ?>
    <?php foreach ($rounds as $roundNumber => $roundMatches): ?>
        <div class="card bg-dark border-secondary mb-4">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-layer-group me-2"></i>
                    Round <?= $roundNumber ?> 
                    <?php 
                    $roundNames = [1 => 'First Round', 2 => 'Quarter Finals', 3 => 'Semi Finals', 4 => 'Final'];
                    if (isset($roundNames[$roundNumber])): 
                    ?>
                        - <?= $roundNames[$roundNumber] ?>
                    <?php endif; ?>
                    <span class="badge bg-info ms-2"><?= count($roundMatches) ?> matches</span>
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($roundMatches as $match): ?>
                        <div class="col-lg-6 col-md-12 mb-3">
                            <div class="card bg-secondary border-warning match-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <small class="text-warning">Match <?= $match['match_number'] ?></small>
                                    <span class="badge bg-<?= $match['status'] === 'completed' ? 'success' : ($match['status'] === 'scheduled' ? 'warning' : 'danger') ?>">
                                        <?= ucfirst($match['status']) ?>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-5 text-center">
                                            <div class="team-name <?= $match['winner_team_id'] == $match['team1_id'] ? 'text-success fw-bold' : 'text-light' ?>">
                                                <?= esc($match['team1_name']) ?>
                                                <?php if ($match['winner_team_id'] == $match['team1_id']): ?>
                                                    <i class="fas fa-crown text-warning ms-1"></i>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($match['team1_score'])): ?>
                                                <div class="score-badge bg-primary text-white rounded px-2 py-1 mt-1">
                                                    <?= $match['team1_score'] ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-2 text-center">
                                            <span class="text-muted">VS</span>
                                        </div>
                                        <div class="col-5 text-center">
                                            <div class="team-name <?= $match['winner_team_id'] == $match['team2_id'] ? 'text-success fw-bold' : 'text-light' ?>">
                                                <?= esc($match['team2_name']) ?>
                                                <?php if ($match['winner_team_id'] == $match['team2_id']): ?>
                                                    <i class="fas fa-crown text-warning ms-1"></i>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($match['team2_score'])): ?>
                                                <div class="score-badge bg-primary text-white rounded px-2 py-1 mt-1">
                                                    <?= $match['team2_score'] ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php if ($match['status'] !== 'completed'): ?>
                                        <div class="text-center mt-3">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-warning btn-sm" 
                                                        onclick="openMatchModal(<?= htmlspecialchars(json_encode($match)) ?>)">
                                                    <i class="fas fa-edit me-1"></i>Set Result
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        onclick="deleteMatch(<?= $match['id'] ?>)">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center mt-2">
                                            <small class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>Match Completed
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="card bg-dark border-secondary">
        <div class="card-body text-center py-5">
            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">No matches scheduled</h5>
            <p class="text-muted">Tournament matches will appear here once they are generated.</p>
        </div>
    </div>
<?php endif; ?>

<!-- Create Match Modal -->
<div class="modal fade" id="createMatchModal" tabindex="-1" aria-labelledby="createMatchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-primary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-primary" id="createMatchModalLabel">Create New Match</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createMatchForm">
                    <input type="hidden" id="tournamentId" name="tournament_id" value="<?= $tournament['id'] ?>">

                    <div class="mb-3">
                        <label for="roundNumber" class="form-label text-light">Round Number</label>
                        <input type="number" class="form-control bg-dark text-light border-secondary" 
                               id="roundNumber" name="round_number" min="1" value="<?= $tournament['current_round'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="team1Select" class="form-label text-light">Team 1</label>
                        <select class="form-select bg-dark text-light border-secondary" id="team1Select" name="team1_id" required>
                            <option value="">Select Team 1</option>
                            <?php foreach ($availableTeams as $team): ?>
                                <option value="<?= $team['id'] ?>"><?= esc($team['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="team2Select" class="form-label text-light">Team 2</label>
                        <select class="form-select bg-dark text-light border-secondary" id="team2Select" name="team2_id" required>
                            <option value="">Select Team 2</option>
                            <?php foreach ($availableTeams as $team): ?>
                                <option value="<?= $team['id'] ?>"><?= esc($team['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="matchDate" class="form-label text-light">Match Date (Optional)</label>
                        <input type="datetime-local" class="form-control bg-dark text-light border-secondary" 
                               id="matchDate" name="match_date">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="createMatch()">
                    <i class="fas fa-plus me-1"></i>Create Match
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Match Result Modal -->
<div class="modal fade" id="matchModal" tabindex="-1" aria-labelledby="matchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-warning">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-warning" id="matchModalLabel">Set Match Result</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="matchResultForm">
                    <input type="hidden" id="matchId" name="match_id">

                    <div class="mb-3">
                        <label class="form-label text-light">Select Winner</label>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-success team-btn" data-team-id="" data-team-name="">
                                <span class="team-name"></span>
                            </button>
                            <button type="button" class="btn btn-outline-success team-btn" data-team-id="" data-team-name="">
                                <span class="team-name"></span>
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <label for="team1Score" class="form-label text-light">Team 1 Score</label>
                            <input type="number" class="form-control bg-dark text-light border-secondary" 
                                   id="team1Score" name="team1_score" min="0">
                        </div>
                        <div class="col-6">
                            <label for="team2Score" class="form-label text-light">Team 2 Score</label>
                            <input type="number" class="form-control bg-dark text-light border-secondary" 
                                   id="team2Score" name="team2_score" min="0">
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="notes" class="form-label text-light">Notes (Optional)</label>
                        <textarea class="form-control bg-dark text-light border-secondary" 
                                  id="notes" name="notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="saveMatchResult()">
                    <i class="fas fa-save me-1"></i>Save Result
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.match-card {
    transition: transform 0.2s ease-in-out;
}

.match-card:hover {
    transform: translateY(-2px);
}

.team-name {
    font-weight: 500;
}

.score-badge {
    font-size: 0.8rem;
    font-weight: bold;
    display: inline-block;
}

.team-btn.selected {
    background-color: #198754 !important;
    border-color: #198754 !important;
    color: white !important;
}
</style>

<script>
let selectedWinnerId = null;
let currentMatch = null;

function openMatchModal(match) {
    currentMatch = match;
    selectedWinnerId = null;

    document.getElementById('matchId').value = match.id;
    document.getElementById('team1Score').value = match.team1_score || '';
    document.getElementById('team2Score').value = match.team2_score || '';
    document.getElementById('notes').value = match.notes || '';

    // Set up team buttons
    const teamBtns = document.querySelectorAll('.team-btn');
    teamBtns[0].setAttribute('data-team-id', match.team1_id);
    teamBtns[0].setAttribute('data-team-name', match.team1_name);
    teamBtns[0].querySelector('.team-name').textContent = match.team1_name;

    teamBtns[1].setAttribute('data-team-id', match.team2_id);
    teamBtns[1].setAttribute('data-team-name', match.team2_name);
    teamBtns[1].querySelector('.team-name').textContent = match.team2_name;

    // Reset button states
    teamBtns.forEach(btn => btn.classList.remove('selected'));

    // Add click handlers
    teamBtns.forEach(btn => {
        btn.onclick = function() {
            teamBtns.forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            selectedWinnerId = this.getAttribute('data-team-id');
        };
    });

    new bootstrap.Modal(document.getElementById('matchModal')).show();
}

function saveMatchResult() {
    if (!selectedWinnerId) {
        showError('Please select a winner');
        return;
    }

    const formData = {
        match_id: document.getElementById('matchId').value,
        winner_team_id: selectedWinnerId,
        team1_score: document.getElementById('team1Score').value,
        team2_score: document.getElementById('team2Score').value,
        notes: document.getElementById('notes').value
    };

    fetch('<?= base_url('admin/tournaments/update-match') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message);
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('An error occurred while saving the match result');
    });

    bootstrap.Modal.getInstance(document.getElementById('matchModal')).hide();
}

function showSuccess(message) {
    if (typeof notyf !== 'undefined') {
        notyf.success(message);
    } else {
        alert('Success: ' + message);
    }
}

function openCreateMatchModal() {
    new bootstrap.Modal(document.getElementById('createMatchModal')).show();
}

function createMatch() {
    const form = document.getElementById('createMatchForm');
    const formData = new FormData(form);

    const team1Id = formData.get('team1_id');
    const team2Id = formData.get('team2_id');

    if (!team1Id || !team2Id) {
        showError('Please select both teams');
        return;
    }

    if (team1Id === team2Id) {
        showError('A team cannot play against itself');
        return;
    }

    const data = {
        tournament_id: formData.get('tournament_id'),
        round_number: parseInt(formData.get('round_number')),
        team1_id: parseInt(team1Id),
        team2_id: parseInt(team2Id),
        match_date: formData.get('match_date') || null
    };

    fetch('<?= base_url('admin/tournaments/create-match') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message);
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('An error occurred while creating the match');
    });

    bootstrap.Modal.getInstance(document.getElementById('createMatchModal')).hide();
}

function deleteMatch(matchId) {
    if (!confirm('Are you sure you want to delete this match?')) {
        return;
    }

    fetch('<?= base_url('admin/tournaments/delete-match') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ match_id: matchId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message);
            // Reload page after showing message
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('An error occurred while deleting the match. Please try again.');
    });
}

function showError(message) {
    if (typeof notyf !== 'undefined') {
        notyf.error(message);
    } else {
        alert('Error: ' + message);
    }
}
</script>

<?= $this->endSection(); ?>