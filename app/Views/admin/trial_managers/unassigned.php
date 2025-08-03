
<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Unassigned Players<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark text-light border-warning">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-times text-warning"></i> Unassigned Players
                    </h4>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkAssignModal">
                            <i class="fas fa-users"></i> Bulk Assign
                        </button>
                        <button class="btn btn-success" onclick="loadUnassignedPlayers()">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-striped" id="unassignedPlayersTable">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                    </th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Age</th>
                                    <th>Cricket Type</th>
                                    <th>Trial City</th>
                                    <th>Payment Status</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="playersTableBody">
                                <tr>
                                    <td colspan="10" class="text-center">Loading players...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Assign Modal -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Assign Players</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Select Trial Manager</label>
                    <select class="form-select bg-dark text-white border-secondary" id="bulkAssignManager">
                        <option value="">Select Manager...</option>
                    </select>
                </div>
                <div id="selectedPlayersCount" class="text-info">
                    No players selected
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="bulkAssignPlayers()">Assign Players</button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedPlayers = [];
let allPlayers = [];

// Load unassigned players on page load
document.addEventListener('DOMContentLoaded', function() {
    loadUnassignedPlayers();
    loadTrialManagers();
});

function loadUnassignedPlayers() {
    fetch('/admin/trial-managers/unassigned-players')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            allPlayers = data.players;
            displayUnassignedPlayers(data.players);
        } else {
            document.getElementById('playersTableBody').innerHTML = 
                '<tr><td colspan="10" class="text-center text-danger">Failed to load players</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('playersTableBody').innerHTML = 
            '<tr><td colspan="10" class="text-center text-danger">Error loading players</td></tr>';
    });
}

function displayUnassignedPlayers(players) {
    const tbody = document.getElementById('playersTableBody');
    
    if (players.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" class="text-center">No unassigned players found</td></tr>';
        return;
    }

    tbody.innerHTML = players.map(player => `
        <tr>
            <td>
                <input type="checkbox" class="player-checkbox" value="${player.id}" 
                       onchange="updateSelectedPlayers(${player.id}, this.checked)">
            </td>
            <td>${escapeHtml(player.name)}</td>
            <td>${escapeHtml(player.mobile)}</td>
            <td>${escapeHtml(player.email || 'N/A')}</td>
            <td>${player.age}</td>
            <td>${escapeHtml(player.cricket_type)}</td>
            <td>${escapeHtml(player.trial_city_name || 'Not Set')}</td>
            <td>
                <span class="badge bg-${getPaymentBadge(player.payment_status)}">
                    ${formatPaymentStatus(player.payment_status)}
                </span>
            </td>
            <td>${formatDate(player.created_at)}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="quickAssign(${player.id})">
                    <i class="fas fa-user-plus"></i> Quick Assign
                </button>
            </td>
        </tr>
    `).join('');
}

function loadTrialManagers() {
    fetch('/admin/trial-managers/active')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('bulkAssignManager');
            select.innerHTML = '<option value="">Select Manager...</option>' +
                data.managers.map(manager => 
                    `<option value="${manager.id}">${escapeHtml(manager.name)} - ${escapeHtml(manager.trial_name)}</option>`
                ).join('');
        }
    })
    .catch(error => console.error('Error loading managers:', error));
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.player-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
        updateSelectedPlayers(parseInt(checkbox.value), checkbox.checked);
    });
}

function updateSelectedPlayers(playerId, isSelected) {
    if (isSelected) {
        if (!selectedPlayers.includes(playerId)) {
            selectedPlayers.push(playerId);
        }
    } else {
        selectedPlayers = selectedPlayers.filter(id => id !== playerId);
    }
    
    document.getElementById('selectedPlayersCount').textContent = 
        `${selectedPlayers.length} player(s) selected`;
    
    // Update "select all" checkbox
    const totalCheckboxes = document.querySelectorAll('.player-checkbox').length;
    const selectAllCheckbox = document.getElementById('selectAll');
    selectAllCheckbox.indeterminate = selectedPlayers.length > 0 && selectedPlayers.length < totalCheckboxes;
    selectAllCheckbox.checked = selectedPlayers.length === totalCheckboxes && totalCheckboxes > 0;
}

function bulkAssignPlayers() {
    const managerId = document.getElementById('bulkAssignManager').value;
    
    if (!managerId) {
        alert('Please select a trial manager');
        return;
    }
    
    if (selectedPlayers.length === 0) {
        alert('Please select at least one player');
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
            selectedPlayers = [];
            document.getElementById('selectedPlayersCount').textContent = 'No players selected';
            loadUnassignedPlayers();
            bootstrap.Modal.getInstance(document.getElementById('bulkAssignModal')).hide();
        } else {
            alert(data.message || 'Assignment failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during assignment');
    });
}

function quickAssign(playerId) {
    // Open a simple prompt for quick assignment
    const managerId = prompt('Enter Trial Manager ID for quick assignment:');
    if (managerId && !isNaN(managerId)) {
        fetch('/admin/trial-managers/assign-players', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                manager_id: parseInt(managerId),
                player_ids: [playerId]
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                loadUnassignedPlayers();
            } else {
                alert(data.message || 'Assignment failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred during assignment');
        });
    }
}

// Utility functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getPaymentBadge(status) {
    switch (status) {
        case 'full': return 'success';
        case 'partial': return 'warning';
        case 'no_payment': return 'danger';
        default: return 'secondary';
    }
}

function formatPaymentStatus(status) {
    switch (status) {
        case 'full': return 'Full Payment';
        case 'partial': return 'Partial Payment';
        case 'no_payment': return 'No Payment';
        default: return 'Unknown';
    }
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}
</script>
<?= $this->endSection() ?>
