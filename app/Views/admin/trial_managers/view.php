<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Trial Manager Details<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Manager Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-dark text-light border-info">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-tie text-info"></i> <?= esc($manager['name']) ?>
                    </h4>
                    <div>
                        <a href="/admin/trial-managers/edit/<?= $manager['id'] ?>" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="/admin/trial-managers" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Email:</strong> <?= esc($manager['email']) ?></p>
                            <p><strong>Trial Name:</strong> <?= esc($manager['trial_name']) ?></p>
                            <p><strong>City:</strong> <?= esc($manager['city_name'] ?? 'Not Set') ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                <span class="badge bg-<?= $manager['status'] === 'active' ? 'success' : 'danger' ?>">
                                    <?= ucfirst($manager['status']) ?>
                                </span>
                            </p>
                            <p><strong>Created:</strong> <?= date('M d, Y H:i', strtotime($manager['created_at'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3><?= $stats['total_players'] ?></h3>
                    <p class="mb-0">Total Players</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3><?= $stats['full_payment'] ?></h3>
                    <p class="mb-0">Full Payment</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3><?= $stats['partial_payment'] ?></h3>
                    <p class="mb-0">Partial Payment</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3><?= $stats['no_payment'] ?></h3>
                    <p class="mb-0">No Payment</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Collection Summary -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>₹<?= number_format($stats['total_collection'], 2) ?></h4>
                    <p class="mb-0">Total Collection</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-dark border-primary text-light">
                <div class="card-body text-center">
                    <h4>₹<?= number_format($stats['online_collection'], 2) ?></h4>
                    <p class="mb-0">Online Collection</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-dark border-secondary text-light">
                <div class="card-body text-center">
                    <h4>₹<?= number_format($stats['offline_collection'], 2) ?></h4>
                    <p class="mb-0">Offline Collection</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Players List -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark text-light border-warning">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Assigned Players (<?= count($players) ?>)
                    </h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignPlayerModal">
                        <i class="fas fa-plus"></i> Assign Player
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Age</th>
                                    <th>Cricket Type</th>
                                    <th>Payment Status</th>
                                    <th>Registered</th>
                                    <th>Verified</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($players)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No players assigned to this trial manager</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($players as $player): ?>
                                        <tr>
                                            <td><?= esc($player['name']) ?></td>
                                            <td><?= esc($player['mobile']) ?></td>
                                            <td><?= esc($player['email'] ?? 'N/A') ?></td>
                                            <td><?= esc($player['age']) ?></td>
                                            <td><?= esc(ucfirst($player['cricket_type'])) ?></td>
                                            <td>
                                                <?php
                                                $badgeClass = 'secondary';
                                                switch ($player['payment_status']) {
                                                    case 'full': $badgeClass = 'success'; break;
                                                    case 'partial': $badgeClass = 'warning'; break;
                                                    case 'no_payment': $badgeClass = 'danger'; break;
                                                }
                                                ?>
                                                <span class="badge bg-<?= $badgeClass ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $player['payment_status'])) ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($player['created_at'])) ?></td>
                                            <td>
                                                <?php if ($player['verified_at']): ?>
                                                    <span class="badge bg-success">Verified</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="unassignPlayer(<?= $player['id'] ?>, '<?= esc($player['name']) ?>')"
                                                        title="Unassign Player">
                                                    <i class="fas fa-unlink"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Player Modal -->
<div class="modal fade" id="assignPlayerModal" tabindex="-1" aria-labelledby="assignPlayerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 class="modal-title" id="assignPlayerModalLabel">Assign Player to Trial Manager</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Search Players -->
                <div class="mb-3">
                    <label for="playerSearch" class="form-label">Search Players</label>
                    <div class="input-group">
                        <input type="text" class="form-control bg-dark text-white border-secondary" 
                               id="playerSearch" placeholder="Enter mobile number or name">
                        <button class="btn btn-outline-primary" type="button" onclick="searchPlayers()">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>

                <!-- Search Results -->
                <div id="searchResults" class="d-none">
                    <h6 class="text-info">Search Results:</h6>
                    <div class="table-responsive">
                        <table class="table table-dark table-striped">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Cricket Type</th>
                                    <th>Current Assignment</th>
                                </tr>
                            </thead>
                            <tbody id="searchResultsBody">
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Unassigned Players -->
                <div class="mt-4">
                    <h6 class="text-warning">Unassigned Players:</h6>
                    <div id="unassignedPlayers">
                        <div class="text-center">
                            <button class="btn btn-outline-info" onclick="loadUnassignedPlayers()">
                                <i class="fas fa-users"></i> Load Unassigned Players
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="assignSelectedPlayers()" id="assignBtn" disabled>
                    <i class="fas fa-check"></i> Assign Selected Players
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedPlayers = [];
const managerId = <?= $manager['id'] ?>;

function searchPlayers() {
    const query = document.getElementById('playerSearch').value.trim();
    if (query.length < 2) {
        alert('Please enter at least 2 characters to search');
        return;
    }

    fetch('/admin/trial-managers/search-players', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ query: query })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displaySearchResults(data.players);
        } else {
            alert(data.message || 'Search failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during search');
    });
}

function loadUnassignedPlayers() {
    fetch('/admin/trial-managers/unassigned-players')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayUnassignedPlayers(data.players);
        } else {
            alert(data.message || 'Failed to load unassigned players');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while loading players');
    });
}

function displaySearchResults(players) {
    const resultsBody = document.getElementById('searchResultsBody');
    const resultsDiv = document.getElementById('searchResults');

    resultsBody.innerHTML = '';

    if (players.length === 0) {
        resultsBody.innerHTML = '<tr><td colspan="6" class="text-center">No players found</td></tr>';
    } else {
        players.forEach(player => {
            const row = createPlayerRow(player, true);
            resultsBody.appendChild(row);
        });
    }

    resultsDiv.classList.remove('d-none');
}

function displayUnassignedPlayers(players) {
    const container = document.getElementById('unassignedPlayers');

    if (players.length === 0) {
        container.innerHTML = '<p class="text-center text-muted">No unassigned players found</p>';
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-dark table-striped"><thead><tr>';
    html += '<th>Select</th><th>Name</th><th>Mobile</th><th>Email</th><th>Cricket Type</th><th>Registered</th>';
    html += '</tr></thead><tbody>';

    players.forEach(player => {
        html += `<tr>
            <td><input type="checkbox" class="form-check-input player-checkbox" value="${player.id}" onchange="togglePlayerSelection(${player.id})"></td>
            <td>${player.name}</td>
            <td>${player.mobile}</td>
            <td>${player.email || 'N/A'}</td>
            <td>${player.cricket_type}</td>
            <td>${new Date(player.created_at).toLocaleDateString()}</td>
        </tr>`;
    });

    html += '</tbody></table></div>';
    container.innerHTML = html;
}

function createPlayerRow(player, showAssignment = false) {
    const row = document.createElement('tr');

    let assignmentInfo = 'Unassigned';
    if (player.trial_manager_id) {
        assignmentInfo = player.manager_name || 'Assigned to another manager';
    }

    row.innerHTML = `
        <td><input type="checkbox" class="form-check-input player-checkbox" value="${player.id}" onchange="togglePlayerSelection(${player.id})"></td>
        <td>${player.name}</td>
        <td>${player.mobile}</td>
        <td>${player.email || 'N/A'}</td>
        <td>${player.cricket_type}</td>
        ${showAssignment ? `<td><span class="badge ${player.trial_manager_id ? 'bg-warning' : 'bg-success'}">${assignmentInfo}</span></td>` : `<td>${new Date(player.created_at).toLocaleDateString()}</td>`}
    `;

    return row;
}

function togglePlayerSelection(playerId) {
    const checkbox = document.querySelector(`input[value="${playerId}"]`);

    if (checkbox.checked) {
        if (!selectedPlayers.includes(playerId)) {
            selectedPlayers.push(playerId);
        }
    } else {
        selectedPlayers = selectedPlayers.filter(id => id !== playerId);
    }

    document.getElementById('assignBtn').disabled = selectedPlayers.length === 0;
}

function assignSelectedPlayers() {
    if (selectedPlayers.length === 0) {
        alert('Please select at least one player');
        return;
    }

    const confirmMsg = `Are you sure you want to assign ${selectedPlayers.length} player(s) to this trial manager?`;
    if (!confirm(confirmMsg)) {
        return;
    }

    fetch('/admin/trial-managers/assign-players', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            manager_id: managerId,
            player_ids: selectedPlayers
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload(); // Refresh the page to show updated assignments
        } else {
            alert(data.message || 'Assignment failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during assignment');
    });
}

// Unassign player function
function unassignPlayer(playerId, playerName) {
    if (!confirm(`Are you sure you want to unassign ${playerName} from this trial manager?`)) {
        return;
    }

    fetch(`/admin/trial-managers/unassign-player/${playerId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload(); // Refresh the page to show updated assignments
        } else {
            alert(data.message || 'Unassignment failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during unassignment');
    });
}

// Reset modal when closed
document.getElementById('assignPlayerModal').addEventListener('hidden.bs.modal', function () {
    selectedPlayers = [];
    document.getElementById('playerSearch').value = '';
    document.getElementById('searchResults').classList.add('d-none');
    document.getElementById('unassignedPlayers').innerHTML = '<div class="text-center"><button class="btn btn-outline-info" onclick="loadUnassignedPlayers()"><i class="fas fa-users"></i> Load Unassigned Players</button></div>';
    document.getElementById('assignBtn').disabled = true;
});
</script>
<?= $this->endSection() ?>