/**
 * Team management JavaScript functionality
 * Handles team CRUD operations, player assignments, and team statistics
 */

let teamsData = [];

/**
 * Initialize teams page functionality
 */
function initializeTeams() {
    loadTeamsData();
    loadTeamStats();
    bindTeamEvents();
    renderTeamsGrid();
    
    console.log('Teams module initialized');
}

/**
 * Load teams data
 */
function loadTeamsData() {
    teamsData = getTeamsData();
}

/**
 * Load team statistics
 */
function loadTeamStats() {
    const stats = calculateTeamStats();
    
    $('#total-teams-count').text(formatNumber(stats.totalTeams));
    $('#total-team-players').text(formatNumber(stats.totalPlayers));
    $('#best-team').text(stats.bestTeam);
    $('#avg-win-rate').text(stats.avgWinRate + '%');
}

/**
 * Calculate team statistics
 */
function calculateTeamStats() {
    const totalTeams = teamsData.length;
    const totalPlayers = teamsData.reduce((sum, team) => sum + team.players.length, 0);
    const bestTeam = teamsData.reduce((best, team) => 
        team.winRate > (best ? best.winRate : 0) ? team : best, null
    );
    const avgWinRate = totalTeams > 0 ? 
        (teamsData.reduce((sum, team) => sum + team.winRate, 0) / totalTeams).toFixed(1) : 0;
    
    return {
        totalTeams,
        totalPlayers,
        bestTeam: bestTeam ? bestTeam.name : 'N/A',
        avgWinRate
    };
}

/**
 * Render teams grid
 */
function renderTeamsGrid() {
    const container = $('#teams-grid');
    container.empty();
    
    if (teamsData.length === 0) {
        container.html(`
            <div class="col-12">
                <div class="card bg-dark border-warning text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Teams Found</h5>
                        <p class="text-muted">Create your first team to get started</p>
                        <button class="btn btn-warning" onclick="$('#add-team-btn').click()">
                            <i class="fas fa-plus"></i> Add Team
                        </button>
                    </div>
                </div>
            </div>
        `);
        return;
    }
    
    teamsData.forEach(team => {
        const teamCard = createTeamCard(team);
        container.append(teamCard);
    });
}

/**
 * Create team card HTML
 */
function createTeamCard(team) {
    return `
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card bg-dark border-warning team-card h-100" data-team-id="${team.id}">
                <div class="card-header bg-dark border-warning d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="team-logo me-2" style="background: ${team.primaryColor}; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <span style="color: ${team.secondaryColor}; font-weight: bold; font-size: 12px;">${team.shortName}</span>
                        </div>
                        <h6 class="text-warning mb-0">${team.name}</h6>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-warning dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="#" onclick="viewTeamDetails('${team.id}')">
                                <i class="fas fa-eye"></i> View Details
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="editTeam('${team.id}')">
                                <i class="fas fa-edit"></i> Edit Team
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="manageSquad('${team.id}')">
                                <i class="fas fa-users"></i> Manage Squad
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteTeam('${team.id}')">
                                <i class="fas fa-trash"></i> Delete Team
                            </a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">Captain</small>
                            <div class="text-light">${team.captain || 'Not assigned'}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Coach</small>
                            <div class="text-light">${team.coach || 'Not assigned'}</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-4 text-center">
                            <div class="text-warning h5 mb-0">${team.players.length}</div>
                            <small class="text-muted">Players</small>
                        </div>
                        <div class="col-4 text-center">
                            <div class="text-warning h5 mb-0">${team.matchesPlayed}</div>
                            <small class="text-muted">Matches</small>
                        </div>
                        <div class="col-4 text-center">
                            <div class="text-warning h5 mb-0">${team.winRate}%</div>
                            <small class="text-muted">Win Rate</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Performance</small>
                            <small class="text-muted">${team.wins}W ${team.losses}L ${team.draws}D</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: ${team.winRate}%; background-color: ${team.primaryColor};"></div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <span class="badge bg-secondary">
                            <i class="fas fa-map-marker-alt"></i> ${team.city || 'Unknown'}
                        </span>
                        <span class="badge" style="background-color: ${team.status === 'Active' ? '#28a745' : '#6c757d'};">
                            ${team.status}
                        </span>
                    </div>
                </div>
                <div class="card-footer bg-dark border-warning">
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-sm btn-outline-warning" onclick="viewTeamDetails('${team.id}')">
                            <i class="fas fa-chart-bar"></i> Stats
                        </button>
                        <button class="btn btn-sm btn-outline-warning" onclick="manageSquad('${team.id}')">
                            <i class="fas fa-users"></i> Squad
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="editTeam('${team.id}')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

/**
 * Bind team-specific events
 */
function bindTeamEvents() {
    // Add team button
    $('#add-team-btn').on('click', function() {
        openAddTeamModal();
    });
    
    // Add team form submission
    $('#add-team-form').on('submit', function(e) {
        e.preventDefault();
        saveTeam();
    });
}

/**
 * Open add team modal
 */
function openAddTeamModal() {
    $('#add-team-modal').modal('show');
    initializeAddTeamForm();
}

/**
 * Initialize add team form
 */
function initializeAddTeamForm() {
    // Initialize captain select
    $('#team-captain').select2({
        placeholder: 'Select captain...',
        allowClear: true,
        data: getAvailablePlayersForCaptain(),
        dropdownParent: $('#add-team-modal')
    });
    
    // Auto-generate short name from team name
    $('#team-name').on('input', function() {
        const name = $(this).val();
        const shortName = name.split(' ').map(word => word.charAt(0)).join('').substring(0, 3).toUpperCase();
        $('#team-short-name').val(shortName);
    });
}

/**
 * Save team data
 */
function saveTeam() {
    const formData = new FormData($('#add-team-form')[0]);
    
    showLoading();
    
    // Simulate API call
    setTimeout(() => {
        const newTeam = {
            id: generateId(),
            name: formData.get('name'),
            shortName: formData.get('short_name'),
            captain: formData.get('captain'),
            coach: formData.get('coach'),
            city: formData.get('city'),
            founded: formData.get('founded'),
            description: formData.get('description'),
            primaryColor: formData.get('primary_color'),
            secondaryColor: formData.get('secondary_color'),
            players: [],
            matchesPlayed: 0,
            wins: 0,
            losses: 0,
            draws: 0,
            winRate: 0,
            status: 'Active',
            createdDate: new Date()
        };
        
        // Add to teams data
        teamsData.push(newTeam);
        
        // Close modal
        $('#add-team-modal').modal('hide');
        
        // Refresh display
        renderTeamsGrid();
        loadTeamStats();
        
        // Show success
        showSuccess('Team created successfully!');
        showCelebration();
        
        hideLoading();
    }, 1500);
}

/**
 * View team details
 */
function viewTeamDetails(teamId) {
    const team = teamsData.find(t => t.id === teamId);
    if (!team) return;
    
    $('#team-details-title').html(`<i class="fas fa-shield-alt"></i> ${team.name} Details`);
    
    const detailsHtml = `
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-dark border-warning">
                    <div class="card-body text-center">
                        <div class="team-logo mx-auto mb-3" style="background: ${team.primaryColor}; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <span style="color: ${team.secondaryColor}; font-weight: bold; font-size: 24px;">${team.shortName}</span>
                        </div>
                        <h5 class="text-warning">${team.name}</h5>
                        <p class="text-muted">${team.description || 'No description available'}</p>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="text-warning h4">${team.wins}</div>
                                <small class="text-muted">Wins</small>
                            </div>
                            <div class="col-4">
                                <div class="text-warning h4">${team.losses}</div>
                                <small class="text-muted">Losses</small>
                            </div>
                            <div class="col-4">
                                <div class="text-warning h4">${team.draws}</div>
                                <small class="text-muted">Draws</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card bg-dark border-warning">
                    <div class="card-header">
                        <h6 class="text-warning mb-0">Team Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong class="text-warning">Captain:</strong>
                                <p class="text-light">${team.captain || 'Not assigned'}</p>
                            </div>
                            <div class="col-md-6">
                                <strong class="text-warning">Coach:</strong>
                                <p class="text-light">${team.coach || 'Not assigned'}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <strong class="text-warning">Home City:</strong>
                                <p class="text-light">${team.city || 'Not specified'}</p>
                            </div>
                            <div class="col-md-6">
                                <strong class="text-warning">Founded:</strong>
                                <p class="text-light">${team.founded || 'Unknown'}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <strong class="text-warning">Total Players:</strong>
                                <p class="text-light">${team.players.length}</p>
                            </div>
                            <div class="col-md-6">
                                <strong class="text-warning">Win Rate:</strong>
                                <p class="text-light">${team.winRate}%</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card bg-dark border-warning mt-3">
                    <div class="card-header">
                        <h6 class="text-warning mb-0">Squad Members</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            ${team.players.map(player => `
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        <img src="${player.avatar}" alt="${player.name}" class="rounded-circle me-2" width="30" height="30">
                                        <div>
                                            <div class="text-light">${player.name}</div>
                                            <small class="text-muted">${player.position}</small>
                                        </div>
                                    </div>
                                </div>
                            `).join('') || '<p class="text-muted">No players assigned to this team</p>'}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#team-details-content').html(detailsHtml);
    $('#team-details-modal').modal('show');
}

/**
 * Edit team
 */
function editTeam(teamId) {
    const team = teamsData.find(t => t.id === teamId);
    if (!team) return;
    
    // Populate form with team data
    $('#team-name').val(team.name);
    $('#team-short-name').val(team.shortName);
    $('#team-coach').val(team.coach);
    $('#team-city').val(team.city);
    $('#team-founded').val(team.founded);
    $('#team-description').val(team.description);
    $('#team-primary-color').val(team.primaryColor);
    $('#team-secondary-color').val(team.secondaryColor);
    
    // Change modal title and button
    $('#add-team-modal .modal-title').html('<i class="fas fa-edit"></i> Edit Team');
    $('#add-team-modal button[type="submit"]').html('<i class="fas fa-save"></i> Update Team');
    
    // Store team ID for update
    $('#add-team-form').data('team-id', teamId);
    
    $('#add-team-modal').modal('show');
}

/**
 * Manage squad
 */
function manageSquad(teamId) {
    // This would open a squad management modal
    showSuccess('Squad management feature will be implemented soon!');
}

/**
 * Delete team
 */
function deleteTeam(teamId) {
    const team = teamsData.find(t => t.id === teamId);
    if (!team) return;
    
    Swal.fire({
        title: 'Delete Team?',
        text: `Are you sure you want to delete ${team.name}? This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        background: '#2d2d2d',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            performTeamDelete(teamId);
        }
    });
}

/**
 * Perform team delete operation
 */
function performTeamDelete(teamId) {
    showLoading();
    
    setTimeout(() => {
        // Remove team from data
        teamsData = teamsData.filter(t => t.id !== teamId);
        
        // Refresh display
        renderTeamsGrid();
        loadTeamStats();
        
        showSuccess('Team deleted successfully!');
        hideLoading();
    }, 1000);
}

/**
 * Get available players for captain selection
 */
function getAvailablePlayersForCaptain() {
    // This would normally fetch from API
    return [
        { id: '1', text: 'John Smith (Lions)' },
        { id: '2', text: 'Mike Johnson (Tigers)' },
        { id: '3', text: 'David Wilson (Eagles)' },
        { id: '4', text: 'Chris Brown (Hawks)' },
        { id: '5', text: 'Tom Anderson (Warriors)' }
    ];
}

/**
 * Get teams data
 */
function getTeamsData() {
    return [
        {
            id: '1',
            name: 'Lions',
            shortName: 'LIO',
            captain: 'John Smith',
            coach: 'Robert Johnson',
            city: 'New York',
            founded: 2015,
            description: 'The mighty Lions, known for their aggressive batting style',
            primaryColor: '#ffd700',
            secondaryColor: '#1a1a1a',
            players: [
                { id: '1', name: 'John Smith', position: 'Batsman', avatar: 'https://ui-avatars.com/api/?name=John+Smith&background=ffd700&color=1a1a1a' },
                { id: '6', name: 'Alex Wilson', position: 'Bowler', avatar: 'https://ui-avatars.com/api/?name=Alex+Wilson&background=ffd700&color=1a1a1a' }
            ],
            matchesPlayed: 25,
            wins: 18,
            losses: 5,
            draws: 2,
            winRate: 72,
            status: 'Active',
            createdDate: new Date('2023-01-01')
        },
        {
            id: '2',
            name: 'Tigers',
            shortName: 'TIG',
            captain: 'Mike Johnson',
            coach: 'Sarah Davis',
            city: 'Los Angeles',
            founded: 2018,
            description: 'Tigers are known for their fierce bowling attack',
            primaryColor: '#ff6600',
            secondaryColor: '#000000',
            players: [
                { id: '2', name: 'Mike Johnson', position: 'Bowler', avatar: 'https://ui-avatars.com/api/?name=Mike+Johnson&background=ff6600&color=000000' }
            ],
            matchesPlayed: 22,
            wins: 14,
            losses: 6,
            draws: 2,
            winRate: 64,
            status: 'Active',
            createdDate: new Date('2023-02-01')
        },
        {
            id: '3',
            name: 'Eagles',
            shortName: 'EAG',
            captain: 'David Wilson',
            coach: 'Mark Thompson',
            city: 'Chicago',
            founded: 2020,
            description: 'Eagles soar high with their balanced team composition',
            primaryColor: '#0066cc',
            secondaryColor: '#ffffff',
            players: [
                { id: '3', name: 'David Wilson', position: 'All-rounder', avatar: 'https://ui-avatars.com/api/?name=David+Wilson&background=0066cc&color=ffffff' }
            ],
            matchesPlayed: 20,
            wins: 12,
            losses: 7,
            draws: 1,
            winRate: 60,
            status: 'Active',
            createdDate: new Date('2023-03-01')
        }
    ];
}

// Export function
window.initializeTeams = initializeTeams;
