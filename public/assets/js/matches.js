/**
 * Match management JavaScript functionality
 * Handles match scheduling, results, and match statistics
 */

let matchesTable = null;
let matchesData = [];

/**
 * Initialize matches page functionality
 */
function initializeMatches() {
    loadMatchesData();
    initializeMatchesTable();
    initializeMatchFilters();
    bindMatchEvents();
    loadMatchStats();
    
    console.log('Matches module initialized');
}

/**
 * Load matches data
 */
function loadMatchesData() {
    matchesData = getMatchesData();
}

/**
 * Initialize matches DataTable
 */
function initializeMatchesTable() {
    matchesTable = $('#matches-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        data: matchesData,
        columns: [
            {
                data: null,
                title: '<input type="checkbox" id="select-all-matches" class="form-check-input">',
                orderable: false,
                searchable: false,
                width: '40px',
                render: function(data, type, row) {
                    return `<input type="checkbox" class="form-check-input match-checkbox" value="${row.id}">`;
                }
            },
            {
                data: 'matchNumber',
                title: 'Match #',
                width: '80px',
                render: function(data) {
                    return `<span class="badge bg-warning text-dark">#${data}</span>`;
                }
            },
            {
                data: null,
                title: 'Teams',
                render: function(data, type, row) {
                    return `
                        <div class="match-teams">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="team-info">
                                    <strong class="text-warning">${row.team1}</strong>
                                    ${row.team1Score ? `<span class="text-light ms-2">${row.team1Score}</span>` : ''}
                                </div>
                                <div class="vs-indicator">
                                    <small class="text-muted">vs</small>
                                </div>
                                <div class="team-info text-end">
                                    <strong class="text-warning">${row.team2}</strong>
                                    ${row.team2Score ? `<span class="text-light ms-2">${row.team2Score}</span>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                }
            },
            {
                data: 'date',
                title: 'Date & Time',
                render: function(data) {
                    return `
                        <div>
                            <div class="text-light">${formatDate(data)}</div>
                            <small class="text-muted">${formatTime(data)}</small>
                        </div>
                    `;
                }
            },
            {
                data: 'venue',
                title: 'Venue',
                render: function(data) {
                    return `<span class="text-light">${data}</span>`;
                }
            },
            {
                data: 'format',
                title: 'Format',
                render: function(data) {
                    const badges = {
                        'T20': 'bg-success',
                        'ODI': 'bg-primary',
                        'Test': 'bg-info'
                    };
                    return `<span class="badge ${badges[data] || 'bg-secondary'}">${data}</span>`;
                }
            },
            {
                data: 'status',
                title: 'Status',
                render: function(data, type, row) {
                    const badges = {
                        'Scheduled': 'bg-warning text-dark',
                        'Live': 'bg-success',
                        'Completed': 'bg-info',
                        'Cancelled': 'bg-danger',
                        'Postponed': 'bg-secondary'
                    };
                    
                    let statusHtml = `<span class="badge ${badges[data] || 'bg-secondary'}">${data}</span>`;
                    
                    if (data === 'Completed' && row.winner) {
                        statusHtml += `<br><small class="text-muted">Won by ${row.winner}</small>`;
                    }
                    
                    return statusHtml;
                }
            },
            {
                data: null,
                title: 'Actions',
                orderable: false,
                searchable: false,
                width: '120px',
                render: function(data, type, row) {
                    return `
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-outline-info" 
                                    onclick="viewMatchDetails('${row.id}')"
                                    data-bs-toggle="tooltip" 
                                    title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" 
                                    onclick="editMatch('${row.id}')"
                                    data-bs-toggle="tooltip" 
                                    title="Edit Match">
                                <i class="fas fa-edit"></i>
                            </button>
                            ${row.status === 'Scheduled' ? `
                                <button class="btn btn-sm btn-outline-success" 
                                        onclick="startMatch('${row.id}')"
                                        data-bs-toggle="tooltip" 
                                        title="Start Match">
                                    <i class="fas fa-play"></i>
                                </button>
                            ` : ''}
                            ${row.status === 'Live' ? `
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="endMatch('${row.id}')"
                                        data-bs-toggle="tooltip" 
                                        title="End Match">
                                    <i class="fas fa-stop"></i>
                                </button>
                            ` : ''}
                        </div>
                    `;
                }
            }
        ],
        order: [[3, 'desc']],
        pageLength: 25,
        language: {
            emptyTable: "No matches found",
            search: "",
            searchPlaceholder: "Search matches...",
            info: "Showing _START_ to _END_ of _TOTAL_ matches",
            infoEmpty: "Showing 0 to 0 of 0 matches",
            infoFiltered: "(filtered from _MAX_ total matches)",
            lengthMenu: "Show _MENU_ matches per page"
        },
        drawCallback: function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
}

/**
 * Initialize match filters
 */
function initializeMatchFilters() {
    // Team filter
    $('#team-filter').select2({
        placeholder: 'Filter by team...',
        allowClear: true,
        data: getTeamOptionsForFilter()
    });
    
    // Format filter
    $('#format-filter').select2({
        placeholder: 'Filter by format...',
        allowClear: true,
        data: [
            { id: 'T20', text: 'T20' },
            { id: 'ODI', text: 'ODI' },
            { id: 'Test', text: 'Test' }
        ]
    });
    
    // Status filter
    $('#status-filter').select2({
        placeholder: 'Filter by status...',
        allowClear: true,
        data: [
            { id: 'Scheduled', text: 'Scheduled' },
            { id: 'Live', text: 'Live' },
            { id: 'Completed', text: 'Completed' },
            { id: 'Cancelled', text: 'Cancelled' },
            { id: 'Postponed', text: 'Postponed' }
        ]
    });
    
    // Date range filter
    $('#date-from, #date-to').on('change', function() {
        applyMatchFilters();
    });
    
    // Filter change handlers
    $('#team-filter, #format-filter, #status-filter').on('change', function() {
        applyMatchFilters();
    });
}

/**
 * Apply filters to matches table
 */
function applyMatchFilters() {
    const teamFilter = $('#team-filter').val();
    const formatFilter = $('#format-filter').val();
    const statusFilter = $('#status-filter').val();
    const dateFrom = $('#date-from').val();
    const dateTo = $('#date-to').val();
    
    matchesTable.columns().search('');
    
    if (teamFilter) {
        matchesTable.column(2).search(teamFilter);
    }
    
    if (formatFilter) {
        matchesTable.column(5).search(formatFilter);
    }
    
    if (statusFilter) {
        matchesTable.column(6).search(statusFilter);
    }
    
    // Custom date range filtering would be implemented here
    
    matchesTable.draw();
}

/**
 * Bind match-specific events
 */
function bindMatchEvents() {
    // Add match button
    $('#add-match-btn').on('click', function() {
        openAddMatchModal();
    });
    
    // Add match form submission
    $('#add-match-form').on('submit', function(e) {
        e.preventDefault();
        saveMatch();
    });
    
    // Select all checkbox
    $('#select-all-matches').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.match-checkbox').prop('checked', isChecked);
        updateBulkMatchActions();
    });
    
    // Individual checkboxes
    $(document).on('change', '.match-checkbox', function() {
        updateBulkMatchActions();
        updateSelectAllMatchCheckbox();
    });
    
    // Bulk actions
    $('#bulk-cancel-matches').on('click', function() {
        bulkCancelMatches();
    });
    
    $('#bulk-export-matches').on('click', function() {
        bulkExportMatches();
    });
}

/**
 * Load match statistics
 */
function loadMatchStats() {
    const stats = calculateMatchStats();
    
    $('#total-matches-count').text(formatNumber(stats.totalMatches));
    $('#completed-matches').text(formatNumber(stats.completedMatches));
    $('#upcoming-matches').text(formatNumber(stats.upcomingMatches));
    $('#live-matches').text(formatNumber(stats.liveMatches));
}

/**
 * Calculate match statistics
 */
function calculateMatchStats() {
    const totalMatches = matchesData.length;
    const completedMatches = matchesData.filter(m => m.status === 'Completed').length;
    const upcomingMatches = matchesData.filter(m => m.status === 'Scheduled').length;
    const liveMatches = matchesData.filter(m => m.status === 'Live').length;
    
    return {
        totalMatches,
        completedMatches,
        upcomingMatches,
        liveMatches
    };
}

/**
 * Open add match modal
 */
function openAddMatchModal() {
    $('#add-match-modal').modal('show');
    initializeAddMatchForm();
}

/**
 * Initialize add match form
 */
function initializeAddMatchForm() {
    // Initialize team selects
    $('#match-team1, #match-team2').select2({
        placeholder: 'Select team...',
        data: getTeamOptionsForFilter(),
        dropdownParent: $('#add-match-modal')
    });
    
    // Initialize venue select
    $('#match-venue').select2({
        placeholder: 'Select venue...',
        allowClear: true,
        data: getVenueOptions(),
        dropdownParent: $('#add-match-modal')
    });
    
    // Set default date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    $('#match-date').val(tomorrow.toISOString().split('T')[0]);
    
    // Set default time
    $('#match-time').val('14:00');
}

/**
 * Save match data
 */
function saveMatch() {
    const formData = new FormData($('#add-match-form')[0]);
    
    // Validation
    if (formData.get('team1') === formData.get('team2')) {
        showError('A team cannot play against itself!');
        return;
    }
    
    showLoading();
    
    // Simulate API call
    setTimeout(() => {
        const matchDate = new Date(formData.get('date') + 'T' + formData.get('time'));
        
        const newMatch = {
            id: generateId(),
            matchNumber: matchesData.length + 1,
            team1: $('#match-team1 option:selected').text(),
            team2: $('#match-team2 option:selected').text(),
            date: matchDate,
            venue: $('#match-venue option:selected').text(),
            format: formData.get('format'),
            status: 'Scheduled',
            team1Score: null,
            team2Score: null,
            winner: null,
            createdDate: new Date()
        };
        
        // Add to table
        matchesTable.row.add(newMatch).draw();
        matchesData.push(newMatch);
        
        // Close modal
        $('#add-match-modal').modal('hide');
        
        // Show success
        showSuccess('Match scheduled successfully!');
        showCelebration();
        
        // Update stats
        loadMatchStats();
        
        hideLoading();
    }, 1500);
}

/**
 * View match details
 */
function viewMatchDetails(matchId) {
    const match = matchesData.find(m => m.id === matchId);
    if (!match) return;
    
    const detailsHtml = `
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card bg-dark border-warning">
                    <div class="card-body text-center">
                        <h4 class="text-warning mb-3">Match #${match.matchNumber}</h4>
                        <div class="row">
                            <div class="col-5 text-end">
                                <h5 class="text-light">${match.team1}</h5>
                                ${match.team1Score ? `<h3 class="text-warning">${match.team1Score}</h3>` : ''}
                            </div>
                            <div class="col-2 d-flex align-items-center justify-content-center">
                                <span class="badge bg-warning text-dark fs-6">VS</span>
                            </div>
                            <div class="col-5 text-start">
                                <h5 class="text-light">${match.team2}</h5>
                                ${match.team2Score ? `<h3 class="text-warning">${match.team2Score}</h3>` : ''}
                            </div>
                        </div>
                        ${match.winner ? `<div class="mt-3"><span class="badge bg-success fs-6">Winner: ${match.winner}</span></div>` : ''}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card bg-dark border-warning">
                    <div class="card-header">
                        <h6 class="text-warning mb-0">Match Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-4"><strong class="text-warning">Date:</strong></div>
                            <div class="col-8 text-light">${formatDate(match.date)}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4"><strong class="text-warning">Time:</strong></div>
                            <div class="col-8 text-light">${formatTime(match.date)}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4"><strong class="text-warning">Venue:</strong></div>
                            <div class="col-8 text-light">${match.venue}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4"><strong class="text-warning">Format:</strong></div>
                            <div class="col-8"><span class="badge bg-primary">${match.format}</span></div>
                        </div>
                        <div class="row">
                            <div class="col-4"><strong class="text-warning">Status:</strong></div>
                            <div class="col-8">
                                <span class="badge ${getStatusBadgeClass(match.status)}">${match.status}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card bg-dark border-warning">
                    <div class="card-header">
                        <h6 class="text-warning mb-0">Match Actions</h6>
                    </div>
                    <div class="card-body">
                        ${getMatchActionButtons(match)}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    Swal.fire({
        title: 'Match Details',
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
 * Get status badge class
 */
function getStatusBadgeClass(status) {
    const classes = {
        'Scheduled': 'bg-warning text-dark',
        'Live': 'bg-success',
        'Completed': 'bg-info',
        'Cancelled': 'bg-danger',
        'Postponed': 'bg-secondary'
    };
    return classes[status] || 'bg-secondary';
}

/**
 * Get match action buttons
 */
function getMatchActionButtons(match) {
    let buttons = `
        <button class="btn btn-outline-warning btn-sm mb-2" onclick="editMatch('${match.id}')">
            <i class="fas fa-edit"></i> Edit Match
        </button><br>
    `;
    
    if (match.status === 'Scheduled') {
        buttons += `
            <button class="btn btn-outline-success btn-sm mb-2" onclick="startMatch('${match.id}')">
                <i class="fas fa-play"></i> Start Match
            </button><br>
            <button class="btn btn-outline-danger btn-sm mb-2" onclick="cancelMatch('${match.id}')">
                <i class="fas fa-times"></i> Cancel Match
            </button><br>
        `;
    }
    
    if (match.status === 'Live') {
        buttons += `
            <button class="btn btn-outline-danger btn-sm mb-2" onclick="endMatch('${match.id}')">
                <i class="fas fa-stop"></i> End Match
            </button><br>
        `;
    }
    
    buttons += `
        <button class="btn btn-outline-info btn-sm" onclick="generateMatchReport('${match.id}')">
            <i class="fas fa-file-alt"></i> Generate Report
        </button>
    `;
    
    return buttons;
}

/**
 * Start match
 */
function startMatch(matchId) {
    Swal.fire({
        title: 'Start Match?',
        text: 'This will mark the match as live and start tracking.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, start match!',
        background: '#2d2d2d',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            updateMatchStatus(matchId, 'Live');
            showSuccess('Match started successfully!');
        }
    });
}

/**
 * End match
 */
function endMatch(matchId) {
    // This would open a modal to enter match results
    showSuccess('Match end functionality will be implemented with scorecard entry!');
}

/**
 * Cancel match
 */
function cancelMatch(matchId) {
    Swal.fire({
        title: 'Cancel Match?',
        text: 'This will cancel the scheduled match.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, cancel match!',
        background: '#2d2d2d',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            updateMatchStatus(matchId, 'Cancelled');
            showSuccess('Match cancelled successfully!');
        }
    });
}

/**
 * Update match status
 */
function updateMatchStatus(matchId, status) {
    const match = matchesData.find(m => m.id === matchId);
    if (match) {
        match.status = status;
        matchesTable.ajax.reload();
        loadMatchStats();
    }
}

/**
 * Generate match report
 */
function generateMatchReport(matchId) {
    showLoading();
    
    setTimeout(() => {
        showSuccess('Match report generated successfully!');
        hideLoading();
    }, 2000);
}

/**
 * Edit match
 */
function editMatch(matchId) {
    showSuccess('Match editing functionality will be implemented soon!');
}

/**
 * Update bulk match actions
 */
function updateBulkMatchActions() {
    const selectedCount = $('.match-checkbox:checked').length;
    const bulkActions = $('#bulk-match-actions');
    
    if (selectedCount > 0) {
        bulkActions.show();
        $('#selected-matches-count').text(selectedCount);
    } else {
        bulkActions.hide();
    }
}

/**
 * Update select all checkbox
 */
function updateSelectAllMatchCheckbox() {
    const totalCheckboxes = $('.match-checkbox').length;
    const checkedCheckboxes = $('.match-checkbox:checked').length;
    
    $('#select-all-matches').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
    $('#select-all-matches').prop('checked', checkedCheckboxes === totalCheckboxes && totalCheckboxes > 0);
}

/**
 * Bulk cancel matches
 */
function bulkCancelMatches() {
    const selectedIds = $('.match-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) return;
    
    Swal.fire({
        title: 'Cancel Selected Matches?',
        text: `Are you sure you want to cancel ${selectedIds.length} match(es)?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, cancel them!',
        background: '#2d2d2d',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            selectedIds.forEach(id => updateMatchStatus(id, 'Cancelled'));
            $('.match-checkbox').prop('checked', false);
            updateBulkMatchActions();
            showSuccess(`${selectedIds.length} match(es) cancelled successfully!`);
        }
    });
}

/**
 * Bulk export matches
 */
function bulkExportMatches() {
    const selectedIds = $('.match-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        showError('Please select matches to export');
        return;
    }
    
    showLoading();
    
    setTimeout(() => {
        showSuccess(`${selectedIds.length} match(es) exported successfully!`);
        hideLoading();
    }, 2000);
}

/**
 * Get team options for filter
 */
function getTeamOptionsForFilter() {
    return [
        { id: 'Lions', text: 'Lions' },
        { id: 'Tigers', text: 'Tigers' },
        { id: 'Eagles', text: 'Eagles' },
        { id: 'Hawks', text: 'Hawks' },
        { id: 'Warriors', text: 'Warriors' },
        { id: 'Knights', text: 'Knights' }
    ];
}

/**
 * Get venue options
 */
function getVenueOptions() {
    return [
        { id: 'stadium-a', text: 'Stadium A' },
        { id: 'stadium-b', text: 'Stadium B' },
        { id: 'stadium-c', text: 'Stadium C' },
        { id: 'ground-central', text: 'Central Ground' },
        { id: 'ground-north', text: 'North Ground' },
        { id: 'ground-south', text: 'South Ground' }
    ];
}

/**
 * Get matches data
 */
function getMatchesData() {
    return [
        {
            id: '1',
            matchNumber: 1,
            team1: 'Lions',
            team2: 'Tigers',
            date: new Date('2025-07-20T14:00:00'),
            venue: 'Stadium A',
            format: 'T20',
            status: 'Scheduled',
            team1Score: null,
            team2Score: null,
            winner: null,
            createdDate: new Date('2025-07-15')
        },
        {
            id: '2',
            matchNumber: 2,
            team1: 'Eagles',
            team2: 'Hawks',
            date: new Date('2025-07-18T16:00:00'),
            venue: 'Stadium B',
            format: 'ODI',
            status: 'Live',
            team1Score: '185/6 (45 overs)',
            team2Score: '120/4 (32 overs)',
            winner: null,
            createdDate: new Date('2025-07-10')
        },
        {
            id: '3',
            matchNumber: 3,
            team1: 'Warriors',
            team2: 'Knights',
            date: new Date('2025-07-10T14:00:00'),
            venue: 'Central Ground',
            format: 'T20',
            status: 'Completed',
            team1Score: '165/8 (20 overs)',
            team2Score: '142/9 (20 overs)',
            winner: 'Warriors',
            createdDate: new Date('2025-07-05')
        }
    ];
}

// Export function
window.initializeMatches = initializeMatches;
