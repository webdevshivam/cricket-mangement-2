
<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Unassigned Players<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-dark text-light border-warning">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-slash me-2"></i>Unassigned Players Management
                    </h4>
                    <div>
                        <button class="btn btn-outline-dark me-2" onclick="refreshData()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                        <a href="<?= base_url('admin/trial-managers') ?>" class="btn btn-outline-dark">
                            <i class="fas fa-arrow-left me-1"></i>Back to Managers
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-0">Assign unassigned players to trial managers based on their trial cities.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-dark text-light border-secondary">
                <div class="card-body">
                    <h6 class="text-warning mb-3">
                        <i class="fas fa-filter me-2"></i>Filter by Trial City
                    </h6>
                    <select id="cityFilter" class="form-select" onchange="filterByCity()">
                        <option value="">All Cities</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark text-light border-secondary">
                <div class="card-body">
                    <h6 class="text-warning mb-3">
                        <i class="fas fa-user-tie me-2"></i>Select Trial Manager
                    </h6>
                    <select id="managerSelect" class="form-select">
                        <option value="">Select Manager</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark text-light border-secondary">
                <div class="card-body">
                    <h6 class="text-warning mb-3">
                        <i class="fas fa-tasks me-2"></i>Bulk Actions
                    </h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-warning flex-fill" onclick="selectAll()">
                            <i class="fas fa-check-square me-1"></i>Select All
                        </button>
                        <button class="btn btn-outline-warning flex-fill" onclick="clearSelection()">
                            <i class="fas fa-square me-1"></i>Clear All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-dark text-light border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-warning fw-bold">Selected Players: </span>
                            <span id="selectedCount" class="badge bg-warning text-dark">0</span>
                        </div>
                        <button class="btn btn-warning" onclick="assignSelectedPlayers()" disabled id="assignBtn">
                            <i class="fas fa-user-plus me-2"></i>Assign Selected Players
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Players Table -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark text-light border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>Unassigned Players
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-hover" id="playersTable">
                            <thead class="table-warning text-dark">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                                    </th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Age</th>
                                    <th>Trial City</th>
                                    <th>Payment Status</th>
                                    <th>Registration Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="playersTableBody">
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="spinner-border text-warning" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Loading players...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let unassignedPlayers = [];
let trialManagers = [];
let selectedPlayers = new Set();

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadTrialManagers();
    loadUnassignedPlayers();
});

// Load trial managers
function loadTrialManagers() {
    fetch('/admin/trial-managers/get-active-managers')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                trialManagers = data.managers;
                populateManagerSelect();
            } else {
                notyf.error('Failed to load trial managers');
            }
        })
        .catch(error => {
            console.error('Error loading managers:', error);
            notyf.error('Error loading trial managers');
        });
}

// Load unassigned players
function loadUnassignedPlayers() {
    fetch('/admin/trial/unassigned-players')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                unassignedPlayers = data.players;
                populatePlayersTable();
                populateCityFilter();
            } else {
                notyf.error('Failed to load unassigned players');
                document.getElementById('playersTableBody').innerHTML = 
                    '<tr><td colspan="9" class="text-center">No unassigned players found</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error loading players:', error);
            notyf.error('Error loading unassigned players');
            document.getElementById('playersTableBody').innerHTML = 
                '<tr><td colspan="9" class="text-center text-danger">Error loading players</td></tr>';
        });
}

// Populate manager select dropdown
function populateManagerSelect() {
    const select = document.getElementById('managerSelect');
    select.innerHTML = '<option value="">Select Manager</option>';
    
    trialManagers.forEach(manager => {
        const option = document.createElement('option');
        option.value = manager.id;
        option.textContent = `${manager.name} (${manager.city_name || 'No City'})`;
        option.dataset.cityId = manager.trial_city_id;
        select.appendChild(option);
    });
}

// Populate city filter
function populateCityFilter() {
    const select = document.getElementById('cityFilter');
    const cities = [...new Set(unassignedPlayers.map(p => p.trial_city_name).filter(Boolean))];
    
    select.innerHTML = '<option value="">All Cities</option>';
    cities.forEach(city => {
        const option = document.createElement('option');
        option.value = city;
        option.textContent = city;
        select.appendChild(option);
    });
}

// Populate players table
function populatePlayersTable(filteredPlayers = null) {
    const players = filteredPlayers || unassignedPlayers;
    const tbody = document.getElementById('playersTableBody');
    
    if (players.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">No players found</td></tr>';
        return;
    }
    
    tbody.innerHTML = players.map(player => `
        <tr data-city="${player.trial_city_name || ''}" data-player-id="${player.id}">
            <td>
                <input type="checkbox" class="player-checkbox" value="${player.id}" 
                       onchange="updateSelection(${player.id})">
            </td>
            <td>
                <i class="fas fa-user me-2 text-warning"></i>
                ${escapeHtml(player.name)}
            </td>
            <td>${escapeHtml(player.mobile)}</td>
            <td>${escapeHtml(player.email || 'N/A')}</td>
            <td>${player.age}</td>
            <td>
                <span class="badge bg-info">${escapeHtml(player.trial_city_name || 'Not Set')}</span>
            </td>
            <td>${getPaymentStatusBadge(player.payment_status)}</td>
            <td>${formatDate(player.created_at)}</td>
            <td>
                <button class="btn btn-sm btn-outline-warning" onclick="assignSinglePlayer(${player.id})" 
                        title="Assign Individual">
                    <i class="fas fa-user-plus"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

// Filter players by city
function filterByCity() {
    const selectedCity = document.getElementById('cityFilter').value;
    
    if (!selectedCity) {
        populatePlayersTable();
        updateManagerOptions();
        return;
    }
    
    const filteredPlayers = unassignedPlayers.filter(player => 
        player.trial_city_name === selectedCity
    );
    
    populatePlayersTable(filteredPlayers);
    updateManagerOptions(selectedCity);
    clearSelection();
}

// Update manager options based on selected city
function updateManagerOptions(cityName = null) {
    const select = document.getElementById('managerSelect');
    const options = select.querySelectorAll('option');
    
    options.forEach(option => {
        if (option.value === '') return; // Skip default option
        
        const manager = trialManagers.find(m => m.id == option.value);
        if (!manager) return;
        
        if (cityName) {
            // Show only managers from the selected city
            const showOption = manager.city_name === cityName;
            option.style.display = showOption ? 'block' : 'none';
            option.disabled = !showOption;
        } else {
            // Show all managers
            option.style.display = 'block';
            option.disabled = false;
        }
    });
    
    // Reset selection if current selection is not valid
    const currentSelection = select.value;
    if (currentSelection) {
        const currentManager = trialManagers.find(m => m.id == currentSelection);
        if (cityName && currentManager && currentManager.city_name !== cityName) {
            select.value = '';
        }
    }
}

// Helper functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getPaymentStatusBadge(status) {
    const badges = {
        'full': '<span class="badge bg-success">Full Payment</span>',
        'partial': '<span class="badge bg-warning text-dark">Partial Payment</span>',
        'no_payment': '<span class="badge bg-danger">No Payment</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-IN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Selection functions
function updateSelection(playerId) {
    const checkbox = document.querySelector(`input[value="${playerId}"]`);
    
    if (checkbox.checked) {
        selectedPlayers.add(playerId);
    } else {
        selectedPlayers.delete(playerId);
    }
    
    updateSelectionUI();
}

function selectAll() {
    const visibleCheckboxes = document.querySelectorAll('.player-checkbox:not([style*="display: none"])');
    visibleCheckboxes.forEach(checkbox => {
        checkbox.checked = true;
        selectedPlayers.add(parseInt(checkbox.value));
    });
    
    document.getElementById('selectAllCheckbox').checked = visibleCheckboxes.length > 0;
    updateSelectionUI();
}

function clearSelection() {
    selectedPlayers.clear();
    document.querySelectorAll('.player-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAllCheckbox').checked = false;
    updateSelectionUI();
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    
    if (selectAllCheckbox.checked) {
        selectAll();
    } else {
        clearSelection();
    }
}

function updateSelectionUI() {
    const count = selectedPlayers.size;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('assignBtn').disabled = count === 0 || !document.getElementById('managerSelect').value;
}

// Manager selection change
document.getElementById('managerSelect').addEventListener('change', function() {
    updateSelectionUI();
});

// Assignment functions
function assignSelectedPlayers() {
    const managerId = document.getElementById('managerSelect').value;
    const playerIds = Array.from(selectedPlayers);
    
    if (!managerId) {
        notyf.error('Please select a trial manager');
        return;
    }
    
    if (playerIds.length === 0) {
        notyf.error('Please select at least one player');
        return;
    }
    
    assignPlayers(playerIds, managerId);
}

function assignSinglePlayer(playerId) {
    const managerId = document.getElementById('managerSelect').value;
    
    if (!managerId) {
        notyf.error('Please select a trial manager first');
        return;
    }
    
    assignPlayers([playerId], managerId);
}

function assignPlayers(playerIds, managerId) {
    const manager = trialManagers.find(m => m.id == managerId);
    
    fetch('/admin/trial-managers/assign-players', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            player_ids: playerIds,
            manager_id: managerId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            notyf.success(`Successfully assigned ${playerIds.length} player(s) to ${manager.name}`);
            refreshData();
        } else {
            notyf.error(data.message || 'Failed to assign players');
        }
    })
    .catch(error => {
        console.error('Error assigning players:', error);
        notyf.error('Error assigning players');
    });
}

function refreshData() {
    clearSelection();
    loadUnassignedPlayers();
    loadTrialManagers();
}

// Initialize Notyf
let notyf = new Notyf({
    duration: 4000,
    ripple: true,
    position: {
        x: 'right',
        y: 'bottom'
    }
});
</script>
<?= $this->endSection() ?>
