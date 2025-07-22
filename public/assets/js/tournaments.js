/**
 * Tournament management JavaScript functionality
 * Handles tournament creation, management, and bracket generation
 */

let tournamentsData = [];

/**
 * Initialize tournaments page functionality
 */
function initializeTournaments() {
    loadTournamentsData();
    loadTournamentStats();
    bindTournamentEvents();
    renderTournamentsGrid();
    
    console.log('Tournaments module initialized');
}

/**
 * Load tournaments data
 */
function loadTournamentsData() {
    tournamentsData = getTournamentsData();
}

/**
 * Load tournament statistics
 */
function loadTournamentStats() {
    const stats = calculateTournamentStats();
    
    $('#total-tournaments-count').text(formatNumber(stats.totalTournaments));
    $('#active-tournaments-count').text(formatNumber(stats.activeTournaments));
    $('#completed-tournaments').text(formatNumber(stats.completedTournaments));
    $('#total-participants').text(formatNumber(stats.totalParticipants));
}

/**
 * Calculate tournament statistics
 */
function calculateTournamentStats() {
    const totalTournaments = tournamentsData.length;
    const activeTournaments = tournamentsData.filter(t => t.status === 'Active').length;
    const completedTournaments = tournamentsData.filter(t => t.status === 'Completed').length;
    const totalParticipants = tournamentsData.reduce((sum, t) => sum + t.teams.length, 0);
    
    return {
        totalTournaments,
        activeTournaments,
        completedTournaments,
        totalParticipants
    };
}

/**
 * Render tournaments grid
 */
function renderTournamentsGrid() {
    const container = $('#tournaments-grid');
    container.empty();
    
    if (tournamentsData.length === 0) {
        container.html(`
            <div class="col-12">
                <div class="card bg-dark border-warning text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Tournaments Found</h5>
                        <p class="text-muted">Create your first tournament to get started</p>
                        <button class="btn btn-warning" onclick="$('#add-tournament-btn').click()">
                            <i class="fas fa-plus"></i> Create Tournament
                        </button>
                    </div>
                </div>
            </div>
        `);
        return;
    }
    
    tournamentsData.forEach(tournament => {
        const tournamentCard = createTournamentCard(tournament);
        container.append(tournamentCard);
    });
}

/**
 * Create tournament card HTML
 */
function createTournamentCard(tournament) {
    const progressPercentage = calculateTournamentProgress(tournament);
    
    return `
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card bg-dark border-warning tournament-card h-100" data-tournament-id="${tournament.id}">
                <div class="card-header bg-dark border-warning d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-warning mb-0">${tournament.name}</h6>
                        <small class="text-muted">${tournament.format} • ${tournament.type}</small>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-warning dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="#" onclick="viewTournamentDetails('${tournament.id}')">
                                <i class="fas fa-eye"></i> View Details
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="viewBracket('${tournament.id}')">
                                <i class="fas fa-sitemap"></i> View Bracket
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="editTournament('${tournament.id}')">
                                <i class="fas fa-edit"></i> Edit Tournament
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteTournament('${tournament.id}')">
                                <i class="fas fa-trash"></i> Delete Tournament
                            </a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">Start Date</small>
                            <div class="text-light">${formatDate(tournament.startDate)}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">End Date</small>
                            <div class="text-light">${formatDate(tournament.endDate)}</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-4 text-center">
                            <div class="text-warning h6 mb-0">${tournament.teams.length}</div>
                            <small class="text-muted">Teams</small>
                        </div>
                        <div class="col-4 text-center">
                            <div class="text-warning h6 mb-0">${tournament.matches.completed}</div>
                            <small class="text-muted">Completed</small>
                        </div>
                        <div class="col-4 text-center">
                            <div class="text-warning h6 mb-0">${tournament.matches.total}</div>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Progress</small>
                            <small class="text-muted">${progressPercentage}%</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: ${progressPercentage}%"></div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge ${getStatusBadgeClass(tournament.status)}">${tournament.status}</span>
                        ${tournament.winner ? `<span class="badge bg-success">Winner: ${tournament.winner}</span>` : ''}
                    </div>
                </div>
                <div class="card-footer bg-dark border-warning">
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-sm btn-outline-warning" onclick="viewTournamentDetails('${tournament.id}')">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn btn-sm btn-outline-warning" onclick="viewBracket('${tournament.id}')">
                            <i class="fas fa-sitemap"></i> Bracket
                        </button>
                        ${tournament.status === 'Active' ? `
                            <button class="btn btn-sm btn-warning" onclick="manageTournament('${tournament.id}')">
                                <i class="fas fa-cog"></i> Manage
                            </button>
                        ` : `
                            <button class="btn btn-sm btn-warning" onclick="editTournament('${tournament.id}')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        `}
                    </div>
                </div>
            </div>
        </div>
    `;
}

/**
 * Calculate tournament progress
 */
function calculateTournamentProgress(tournament) {
    if (tournament.matches.total === 0) return 0;
    return Math.round((tournament.matches.completed / tournament.matches.total) * 100);
}

/**
 * Bind tournament-specific events
 */
function bindTournamentEvents() {
    // Add tournament button
    $('#add-tournament-btn').on('click', function() {
        openAddTournamentModal();
    });
    
    // Add tournament form submission
    $('#add-tournament-form').on('submit', function(e) {
        e.preventDefault();
        saveTournament();
    });
    
    // Tournament type change handler
    $('#tournament-type').on('change', function() {
        updateTournamentOptions();
    });
}

/**
 * Open add tournament modal
 */
function openAddTournamentModal() {
    $('#add-tournament-modal').modal('show');
    initializeAddTournamentForm();
}

/**
 * Initialize add tournament form
 */
function initializeAddTournamentForm() {
    // Initialize teams select
    $('#tournament-teams').select2({
        placeholder: 'Select participating teams...',
        multiple: true,
        data: getAvailableTeams(),
        dropdownParent: $('#add-tournament-modal')
    });
    
    // Set default dates
    const today = new Date();
    const nextWeek = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);
    
    $('#tournament-start-date').val(today.toISOString().split('T')[0]);
    $('#tournament-end-date').val(nextWeek.toISOString().split('T')[0]);
    
    updateTournamentOptions();
}

/**
 * Update tournament options based on type
 */
function updateTournamentOptions() {
    const type = $('#tournament-type').val();
    const optionsContainer = $('#tournament-options');
    
    let optionsHtml = '';
    
    if (type === 'Knockout') {
        optionsHtml = `
            <div class="mb-3">
                <label class="form-label">Bracket Structure</label>
                <select class="form-select" name="bracket_structure">
                    <option value="single">Single Elimination</option>
                    <option value="double">Double Elimination</option>
                </select>
            </div>
        `;
    } else if (type === 'Round Robin') {
        optionsHtml = `
            <div class="mb-3">
                <label class="form-label">Number of Rounds</label>
                <select class="form-select" name="rounds">
                    <option value="1">Single Round</option>
                    <option value="2">Double Round</option>
                </select>
            </div>
        `;
    } else if (type === 'League') {
        optionsHtml = `
            <div class="mb-3">
                <label class="form-label">League Format</label>
                <select class="form-select" name="league_format">
                    <option value="home_away">Home & Away</option>
                    <option value="neutral">Neutral Venue</option>
                </select>
            </div>
        `;
    }
    
    optionsContainer.html(optionsHtml);
}

/**
 * Save tournament data
 */
function saveTournament() {
    const formData = new FormData($('#add-tournament-form')[0]);
    const selectedTeams = $('#tournament-teams').val();
    
    if (!selectedTeams || selectedTeams.length < 2) {
        showError('Please select at least 2 teams for the tournament');
        return;
    }
    
    showLoading();
    
    // Simulate API call
    setTimeout(() => {
        const newTournament = {
            id: generateId(),
            name: formData.get('name'),
            description: formData.get('description'),
            format: formData.get('format'),
            type: formData.get('type'),
            startDate: new Date(formData.get('start_date')),
            endDate: new Date(formData.get('end_date')),
            teams: selectedTeams.map(teamId => ({
                id: teamId,
                name: teamId // In real app, would fetch team details
            })),
            matches: {
                total: calculateTotalMatches(selectedTeams.length, formData.get('type')),
                completed: 0,
                scheduled: 0
            },
            status: 'Scheduled',
            winner: null,
            createdDate: new Date()
        };
        
        // Add to tournaments data
        tournamentsData.push(newTournament);
        
        // Close modal
        $('#add-tournament-modal').modal('hide');
        
        // Refresh display
        renderTournamentsGrid();
        loadTournamentStats();
        
        // Show success
        showSuccess('Tournament created successfully!');
        showCelebration();
        
        hideLoading();
    }, 1500);
}

/**
 * Calculate total matches for tournament
 */
function calculateTotalMatches(teamCount, type) {
    switch (type) {
        case 'Knockout':
            return teamCount - 1;
        case 'Round Robin':
            return (teamCount * (teamCount - 1)) / 2;
        case 'League':
            return teamCount * (teamCount - 1);
        default:
            return teamCount;
    }
}

/**
 * View tournament details
 */
function viewTournamentDetails(tournamentId) {
    const tournament = tournamentsData.find(t => t.id === tournamentId);
    if (!tournament) return;
    
    const detailsHtml = `
        <div class="row">
            <div class="col-md-6">
                <div class="card bg-dark border-warning">
                    <div class="card-header">
                        <h6 class="text-warning mb-0">Tournament Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-4"><strong class="text-warning">Name:</strong></div>
                            <div class="col-8 text-light">${tournament.name}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4"><strong class="text-warning">Format:</strong></div>
                            <div class="col-8"><span class="badge bg-primary">${tournament.format}</span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4"><strong class="text-warning">Type:</strong></div>
                            <div class="col-8"><span class="badge bg-info">${tournament.type}</span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4"><strong class="text-warning">Start Date:</strong></div>
                            <div class="col-8 text-light">${formatDate(tournament.startDate)}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4"><strong class="text-warning">End Date:</strong></div>
                            <div class="col-8 text-light">${formatDate(tournament.endDate)}</div>
                        </div>
                        <div class="row">
                            <div class="col-4"><strong class="text-warning">Status:</strong></div>
                            <div class="col-8">
                                <span class="badge ${getStatusBadgeClass(tournament.status)}">${tournament.status}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card bg-dark border-warning">
                    <div class="card-header">
                        <h6 class="text-warning mb-0">Participating Teams</h6>
                    </div>
                    <div class="card-body">
                        ${tournament.teams.map(team => `
                            <div class="d-flex align-items-center mb-2">
                                <div class="team-logo me-2" style="background: #ffd700; width: 25px; height: 25px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <span style="color: #1a1a1a; font-weight: bold; font-size: 10px;">${team.name.substring(0, 2)}</span>
                                </div>
                                <span class="text-light">${team.name}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
                
                <div class="card bg-dark border-warning mt-3">
                    <div class="card-header">
                        <h6 class="text-warning mb-0">Progress</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="text-warning h4">${tournament.matches.completed}</div>
                                <small class="text-muted">Completed</small>
                            </div>
                            <div class="col-4">
                                <div class="text-warning h4">${tournament.matches.scheduled}</div>
                                <small class="text-muted">Scheduled</small>
                            </div>
                            <div class="col-4">
                                <div class="text-warning h4">${tournament.matches.total}</div>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height: 10px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: ${calculateTournamentProgress(tournament)}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    Swal.fire({
        title: 'Tournament Details',
        html: detailsHtml,
        width: '900px',
        showCloseButton: true,
        showConfirmButton: false,
        background: '#2d2d2d',
        color: '#ffffff',
        customClass: {
            popup: 'border border-warning'
        }
    });
}

/**
 * View tournament bracket
 */
function viewBracket(tournamentId) {
    const tournament = tournamentsData.find(t => t.id === tournamentId);
    if (!tournament) return;
    
    // Generate bracket visualization
    const bracketHtml = generateBracketHtml(tournament);
    
    Swal.fire({
        title: `${tournament.name} - Tournament Bracket`,
        html: bracketHtml,
        width: '1200px',
        showCloseButton: true,
        showConfirmButton: false,
        background: '#2d2d2d',
        color: '#ffffff',
        customClass: {
            popup: 'border border-warning'
        }
    });
}

/**
 * Generate bracket HTML
 */
function generateBracketHtml(tournament) {
    if (tournament.type === 'Knockout') {
        return generateKnockoutBracket(tournament);
    } else if (tournament.type === 'Round Robin') {
        return generateRoundRobinTable(tournament);
    } else {
        return '<p class="text-center text-muted">Bracket visualization not available for this tournament type</p>';
    }
}

/**
 * Generate knockout bracket
 */
function generateKnockoutBracket(tournament) {
    return `
        <div class="tournament-bracket">
            <div class="text-center mb-4">
                <h5 class="text-warning">Knockout Tournament Bracket</h5>
                <p class="text-muted">Single elimination format</p>
            </div>
            <div class="bracket-container">
                <div class="bracket-round">
                    <h6 class="text-warning text-center mb-3">Quarter Finals</h6>
                    ${tournament.teams.slice(0, 8).map((team, index) => `
                        <div class="bracket-match mb-3">
                            <div class="team ${index % 2 === 0 ? 'team-top' : 'team-bottom'}">
                                <span class="team-name">${team.name}</span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
            <div class="text-center mt-4">
                <button class="btn btn-warning" onclick="generateFullBracket('${tournament.id}')">
                    <i class="fas fa-sitemap"></i> Generate Full Bracket
                </button>
            </div>
        </div>
    `;
}

/**
 * Generate round robin table
 */
function generateRoundRobinTable(tournament) {
    return `
        <div class="round-robin-table">
            <div class="text-center mb-4">
                <h5 class="text-warning">Round Robin Tournament</h5>
                <p class="text-muted">All teams play against each other</p>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-striped">
                    <thead>
                        <tr>
                            <th>Team</th>
                            <th>Played</th>
                            <th>Won</th>
                            <th>Lost</th>
                            <th>Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tournament.teams.map(team => `
                            <tr>
                                <td class="text-warning">${team.name}</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;
}

/**
 * Edit tournament
 */
function editTournament(tournamentId) {
    showSuccess('Tournament editing functionality will be implemented soon!');
}

/**
 * Manage tournament
 */
function manageTournament(tournamentId) {
    showSuccess('Tournament management functionality will be implemented soon!');
}

/**
 * Delete tournament
 */
function deleteTournament(tournamentId) {
    const tournament = tournamentsData.find(t => t.id === tournamentId);
    if (!tournament) return;
    
    Swal.fire({
        title: 'Delete Tournament?',
        text: `Are you sure you want to delete ${tournament.name}? This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        background: '#2d2d2d',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            performTournamentDelete(tournamentId);
        }
    });
}

/**
 * Perform tournament delete operation
 */
function performTournamentDelete(tournamentId) {
    showLoading();
    
    setTimeout(() => {
        // Remove tournament from data
        tournamentsData = tournamentsData.filter(t => t.id !== tournamentId);
        
        // Refresh display
        renderTournamentsGrid();
        loadTournamentStats();
        
        showSuccess('Tournament deleted successfully!');
        hideLoading();
    }, 1000);
}

/**
 * Get available teams
 */
function getAvailableTeams() {
    return [
        { id: 'Lions', text: 'Lions' },
        { id: 'Tigers', text: 'Tigers' },
        { id: 'Eagles', text: 'Eagles' },
        { id: 'Hawks', text: 'Hawks' },
        { id: 'Warriors', text: 'Warriors' },
        { id: 'Knights', text: 'Knights' },
        { id: 'Sharks', text: 'Sharks' },
        { id: 'Panthers', text: 'Panthers' }
    ];
}

/**
 * Get tournaments data
 */
function getTournamentsData() {
    return [
        {
            id: '1',
            name: 'Summer Championship 2025',
            description: 'Annual summer tournament featuring the best teams',
            format: 'T20',
            type: 'Knockout',
            startDate: new Date('2025-08-01'),
            endDate: new Date('2025-08-15'),
            teams: [
                { id: 'Lions', name: 'Lions' },
                { id: 'Tigers', name: 'Tigers' },
                { id: 'Eagles', name: 'Eagles' },
                { id: 'Hawks', name: 'Hawks' },
                { id: 'Warriors', name: 'Warriors' },
                { id: 'Knights', name: 'Knights' }
            ],
            matches: {
                total: 5,
                completed: 2,
                scheduled: 3
            },
            status: 'Active',
            winner: null,
            createdDate: new Date('2025-07-01')
        },
        {
            id: '2',
            name: 'Premier League 2025',
            description: 'Round robin tournament with all teams',
            format: 'ODI',
            type: 'Round Robin',
            startDate: new Date('2025-09-01'),
            endDate: new Date('2025-10-31'),
            teams: [
                { id: 'Lions', name: 'Lions' },
                { id: 'Tigers', name: 'Tigers' },
                { id: 'Eagles', name: 'Eagles' },
                { id: 'Hawks', name: 'Hawks' }
            ],
            matches: {
                total: 6,
                completed: 6,
                scheduled: 0
            },
            status: 'Completed',
            winner: 'Lions',
            createdDate: new Date('2025-06-15')
        }
    ];
}

// Export function
window.initializeTournaments = initializeTournaments;
