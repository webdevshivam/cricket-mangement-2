
<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Unassigned Trial Players<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark text-light border-primary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-plus text-primary"></i> Unassigned Trial Players
                    </h4>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkAssignModal">
                            <i class="fas fa-user-check"></i> Bulk Assign
                        </button>
                        <a href="/admin/trial-managers" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Managers
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Filter by Trial City</label>
                            <select class="form-select bg-dark text-white border-secondary" id="cityFilter">
                                <option value="">All Cities</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Search by Mobile</label>
                            <input type="text" class="form-control bg-dark text-white border-secondary" 
                                   id="mobileSearch" placeholder="Enter mobile number">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-outline-info" onclick="applyFilters()">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                            <button class="btn btn-outline-secondary ms-2" onclick="clearFilters()">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </div>

                    <!-- Players Table -->
                    <div class="table-responsive">
                        <table class="table table-dark table-striped">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="form-check-input">
                                    </th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Age</th>
                                    <th>Cricket Type</th>
                                    <th>Trial City</th>
                                    <th>Payment Status</th>
                                    <th>Registered</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="playersTableBody">
                                <tr>
                                    <td colspan="10" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        Loading unassigned players...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Selection Info -->
                    <div id="selectionInfo" class="mt-3 text-info" style="display: none;">
                        <span id="selectedPlayersCount">No players selected</span>
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
                <div class="mb-3">
                    <label class="form-label">Filter Managers by City</label>
                    <select class="form-select bg-dark text-white border-secondary" id="managerCityFilter">
                        <option value="">All Cities</option>
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
let filteredPlayers = [];
let allCities = [];
let allManagers = [];

// Load data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadUnassignedPlayers();
    loadTrialCities();
    loadTrialManagers();
});

function loadUnassignedPlayers() {
    fetch('/admin/trial-managers/unassigned-players')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            allPlayers = data.players;
            filteredPlayers = [...allPlayers];
            renderPlayersTable();
        } else {
            document.getElementById('playersTableBody').innerHTML = 
                '<tr><td colspan="10" class="text-center text-muted">No unassigned players found</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error loading players:', error);
        document.getElementById('playersTableBody').innerHTML = 
            '<tr><td colspan="10" class="text-center text-danger">Error loading players</td></tr>';
    });
}

function loadTrialCities() {
    fetch('/admin/trial-managers/trial-cities')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            allCities = data.cities;
            populateCityFilters();
        }
    })
    .catch(error => console.error('Error loading cities:', error));
}

function loadTrialManagers() {
    fetch('/admin/trial-managers/active')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            allManagers = data.managers;
            populateManagerSelect();
        }
    })
    .catch(error => console.error('Error loading managers:', error));
}

function populateCityFilters() {
    const cityFilter = document.getElementById('cityFilter');
    const managerCityFilter = document.getElementById('managerCityFilter');
    
    const cityOptions = allCities.map(city => 
        `<option value="${city.id}">${escapeHtml(city.city_name)} (${city.player_count} players)</option>`
    ).join('');
    
    cityFilter.innerHTML = '<option value="">All Cities</option>' + cityOptions;
    managerCityFilter.innerHTML = '<option value="">All Cities</option>' + cityOptions;
    
    // Add change handler for manager city filter
    managerCityFilter.addEventListener('change', filterManagersByCity);
}

function populateManagerSelect() {
    const select = document.getElementById('bulkAssignManager');
    const options = allManagers.map(manager => 
        `<option value="${manager.id}" data-city-id="${manager.trial_city_id || ''}">${escapeHtml(manager.name)} - ${escapeHtml(manager.trial_name)} ${manager.city_name ? '(' + escapeHtml(manager.city_name) + ')' : ''}</option>`
    ).join('');
    
    select.innerHTML = '<option value="">Select Manager...</option>' + options;
}

function filterManagersByCity() {
    const selectedCityId = document.getElementById('managerCityFilter').value;
    const managerSelect = document.getElementById('bulkAssignManager');
    
    managerSelect.innerHTML = '<option value="">Select Manager...</option>';
    
    const filteredManagers = selectedCityId ? 
        allManagers.filter(manager => manager.trial_city_id == selectedCityId) : 
        allManagers;
    
    const options = filteredManagers.map(manager => 
        `<option value="${manager.id}" data-city-id="${manager.trial_city_id || ''}">${escapeHtml(manager.name)} - ${escapeHtml(manager.trial_name)} ${manager.city_name ? '(' + escapeHtml(manager.city_name) + ')' : ''}</option>`
    ).join('');
    
    managerSelect.innerHTML += options;
}

function applyFilters() {
    const cityFilter = document.getElementById('cityFilter').value;
    const mobileSearch = document.getElementById('mobileSearch').value.trim();
    
    filteredPlayers = allPlayers.filter(player => {
        const cityMatch = !cityFilter || player.trial_city_id == cityFilter;
        const mobileMatch = !mobileSearch || player.mobile.includes(mobileSearch);
        return cityMatch && mobileMatch;
    });
    
    renderPlayersTable();
    clearSelection();
}

function clearFilters() {
    document.getElementById('cityFilter').value = '';
    document.getElementById('mobileSearch').value = '';
    filteredPlayers = [...allPlayers];
    renderPlayersTable();
    clearSelection();
}

function renderPlayersTable() {
    const tbody = document.getElementById('playersTableBody');
    
    if (filteredPlayers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted">No players found with current filters</td></tr>';
        return;
    }
    
    const rows = filteredPlayers.map(player => `
        <tr>
            <td>
                <input type="checkbox" class="form-check-input player-checkbox" 
                       value="${player.id}" onchange="updateSelectedPlayers(${player.id}, this.checked)">
            </td>
            <td>${escapeHtml(player.name)}</td>
            <td>${escapeHtml(player.mobile)}</td>
            <td>${escapeHtml(player.email)}</td>
            <td>${player.age}</td>
            <td><span class="badge bg-info">${escapeHtml(player.cricket_type)}</span></td>
            <td>${player.trial_city_name ? escapeHtml(player.trial_city_name) : '<span class="text-muted">Not Set</span>'}</td>
            <td>${getPaymentStatusBadge(player.payment_status)}</td>
            <td>${formatDate(player.created_at)}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="assignSinglePlayer(${player.id})">
                    <i class="fas fa-user-check"></i> Assign
                </button>
            </td>
        </tr>
    `).join('');
    
    tbody.innerHTML = rows;
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.player-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
        const playerId = parseInt(checkbox.value);
        updateSelectedPlayers(playerId, checkbox.checked);
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
    
    updateSelectionDisplay();
    updateSelectAllState();
}

function updateSelectAllState() {
    const checkboxes = document.querySelectorAll('.player-checkbox');
    const selectAll = document.getElementById('selectAll');
    
    if (checkboxes.length === 0) {
        selectAll.checked = false;
        selectAll.indeterminate = false;
        return;
    }
    
    const checkedCount = selectedPlayers.length;
    const totalVisible = checkboxes.length;
    
    if (checkedCount === 0) {
        selectAll.checked = false;
        selectAll.indeterminate = false;
    } else if (checkedCount === totalVisible) {
        selectAll.checked = true;
        selectAll.indeterminate = false;
    } else {
        selectAll.checked = false;
        selectAll.indeterminate = true;
    }
}

function updateSelectionDisplay() {
    const selectionInfo = document.getElementById('selectionInfo');
    const countDisplay = document.getElementById('selectedPlayersCount');
    
    if (selectedPlayers.length > 0) {
        selectionInfo.style.display = 'block';
        countDisplay.textContent = `${selectedPlayers.length} player(s) selected`;
    } else {
        selectionInfo.style.display = 'none';
        countDisplay.textContent = 'No players selected';
    }
}

function clearSelection() {
    selectedPlayers = [];
    document.querySelectorAll('.player-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    document.getElementById('selectAll').indeterminate = false;
    updateSelectionDisplay();
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
    
    // Get selected manager details
    const selectedManager = allManagers.find(m => m.id == managerId);
    const selectedManagerCity = selectedManager ? selectedManager.trial_city_id : null;
    
    // Check if selected players are from the same city as manager
    if (selectedManagerCity) {
        const incompatiblePlayers = selectedPlayers.filter(playerId => {
            const player = allPlayers.find(p => p.id == playerId);
            return player && player.trial_city_id && player.trial_city_id != selectedManagerCity;
        });
        
        if (incompatiblePlayers.length > 0) {
            const message = `Warning: ${incompatiblePlayers.length} selected player(s) are from different trial cities and cannot be assigned to this manager. Continue with remaining players?`;
            if (!confirm(message)) {
                return;
            }
            // Remove incompatible players from selection
            selectedPlayers = selectedPlayers.filter(id => !incompatiblePlayers.includes(id));
        }
    }
    
    if (selectedPlayers.length === 0) {
        alert('No compatible players selected for assignment');
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
            clearSelection();
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

function assignSinglePlayer(playerId) {
    selectedPlayers = [playerId];
    document.getElementById('bulkAssignModal').querySelector('.modal-title').textContent = 'Assign Player';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('bulkAssignModal')).show();
}

function getPaymentStatusBadge(status) {
    const badges = {
        'no_payment': '<span class="badge bg-danger">No Payment</span>',
        'partial': '<span class="badge bg-warning text-dark">Partial</span>',
        'full': '<span class="badge bg-success">Full Payment</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
<?= $this->endSection() ?>
