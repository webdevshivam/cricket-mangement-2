/**
 * Player management JavaScript functionality
 * Handles player CRUD operations, search, filters, and data tables
 */

let playersTable = null;

/**
 * Initialize players page functionality
 */
function initializePlayers() {
    initializePlayersTable();
    initializePlayerFilters();
    bindPlayerEvents();
    loadPlayerStats();
    
    console.log('Players module initialized');
}

/**
 * Initialize players DataTable
 */
function initializePlayersTable() {
    playersTable = $('#players-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: false,
        data: getPlayersData(),
        columns: [
            {
                data: null,
                title: '<input type="checkbox" id="select-all-players" class="form-check-input">',
                orderable: false,
                searchable: false,
                width: '40px',
                render: function(data, type, row) {
                    return `<input type="checkbox" class="form-check-input player-checkbox" value="${row.id}">`;
                }
            },
            {
                data: 'avatar',
                title: 'Photo',
                orderable: false,
                searchable: false,
                width: '60px',
                render: function(data, type, row) {
                    return `<img src="${data}" alt="${row.name}" class="rounded-circle" width="40" height="40">`;
                }
            },
            {
                data: 'name',
                title: 'Player Name',
                render: function(data, type, row) {
                    return `
                        <div>
                            <strong class="text-warning">${data}</strong>
                            <br>
                            <small class="text-muted">${row.email}</small>
                        </div>
                    `;
                }
            },
            {
                data: 'position',
                title: 'Position',
                render: function(data) {
                    const badges = {
                        'Batsman': 'bg-primary',
                        'Bowler': 'bg-success',
                        'All-rounder': 'bg-warning text-dark',
                        'Wicket-keeper': 'bg-info'
                    };
                    return `<span class="badge ${badges[data] || 'bg-secondary'}">${data}</span>`;
                }
            },
            {
                data: 'team',
                title: 'Team',
                render: function(data) {
                    return data ? `<span class="text-warning">${data}</span>` : '<span class="text-muted">Unassigned</span>';
                }
            },
            {
                data: 'matches',
                title: 'Matches',
                className: 'text-center'
            },
            {
                data: 'runs',
                title: 'Runs',
                className: 'text-center',
                render: function(data) {
                    return formatNumber(data);
                }
            },
            {
                data: 'average',
                title: 'Average',
                className: 'text-center',
                render: function(data) {
                    return data.toFixed(2);
                }
            },
            {
                data: 'status',
                title: 'Status',
                render: function(data) {
                    const badges = {
                        'Active': 'bg-success',
                        'Injured': 'bg-danger',
                        'Suspended': 'bg-warning text-dark',
                        'Retired': 'bg-secondary'
                    };
                    return `<span class="badge ${badges[data] || 'bg-secondary'}">${data}</span>`;
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
                                    data-action="view" 
                                    data-target="${row.id}"
                                    data-bs-toggle="tooltip" 
                                    title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" 
                                    data-action="edit" 
                                    data-target="${row.id}"
                                    data-bs-toggle="tooltip" 
                                    title="Edit Player">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" 
                                    data-action="delete" 
                                    data-target="${row.id}"
                                    data-bs-toggle="tooltip" 
                                    title="Delete Player">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[2, 'asc']],
        pageLength: 25,
        language: {
            emptyTable: "No players found",
            search: "",
            searchPlaceholder: "Search players...",
            info: "Showing _START_ to _END_ of _TOTAL_ players",
            infoEmpty: "Showing 0 to 0 of 0 players",
            infoFiltered: "(filtered from _MAX_ total players)",
            lengthMenu: "Show _MENU_ players per page",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        drawCallback: function() {
            // Re-initialize tooltips after table redraw
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
}

/**
 * Initialize player filters
 */
function initializePlayerFilters() {
    // Team filter
    $('#team-filter').select2({
        placeholder: 'Filter by team...',
        allowClear: true,
        data: getTeamOptions()
    });
    
    // Position filter
    $('#position-filter').select2({
        placeholder: 'Filter by position...',
        allowClear: true,
        data: [
            { id: 'Batsman', text: 'Batsman' },
            { id: 'Bowler', text: 'Bowler' },
            { id: 'All-rounder', text: 'All-rounder' },
            { id: 'Wicket-keeper', text: 'Wicket-keeper' }
        ]
    });
    
    // Status filter
    $('#status-filter').select2({
        placeholder: 'Filter by status...',
        allowClear: true,
        data: [
            { id: 'Active', text: 'Active' },
            { id: 'Injured', text: 'Injured' },
            { id: 'Suspended', text: 'Suspended' },
            { id: 'Retired', text: 'Retired' }
        ]
    });
    
    // Filter change handlers
    $('#team-filter, #position-filter, #status-filter').on('change', function() {
        applyPlayerFilters();
    });
}

/**
 * Apply filters to players table
 */
function applyPlayerFilters() {
    const teamFilter = $('#team-filter').val();
    const positionFilter = $('#position-filter').val();
    const statusFilter = $('#status-filter').val();
    
    playersTable.columns().search('');
    
    if (teamFilter) {
        playersTable.column(4).search(teamFilter);
    }
    
    if (positionFilter) {
        playersTable.column(3).search(positionFilter);
    }
    
    if (statusFilter) {
        playersTable.column(8).search(statusFilter);
    }
    
    playersTable.draw();
}

/**
 * Bind player-specific events
 */
function bindPlayerEvents() {
    // Add player button
    $('#add-player-btn').on('click', function() {
        openAddPlayerModal();
    });
    
    // Select all checkbox
    $('#select-all-players').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.player-checkbox').prop('checked', isChecked);
        updateBulkActionButtons();
    });
    
    // Individual checkboxes
    $(document).on('change', '.player-checkbox', function() {
        updateBulkActionButtons();
        updateSelectAllCheckbox();
    });
    
    // Bulk actions
    $('#bulk-delete-btn').on('click', function() {
        bulkDeletePlayers();
    });
    
    $('#bulk-export-btn').on('click', function() {
        bulkExportPlayers();
    });
    
    // Quick stats refresh
    $('#refresh-player-stats').on('click', function() {
        loadPlayerStats();
    });
}

/**
 * Open add player modal
 */
function openAddPlayerModal() {
    $('#add-player-modal').modal('show');
    initializeAddPlayerForm();
}

/**
 * Initialize add player form
 */
function initializeAddPlayerForm() {
    // Initialize form wizard if not already done
    if (!$('#add-player-wizard').hasClass('wizard-initialized')) {
        initializeFormWizard('add-player-wizard');
        $('#add-player-wizard').addClass('wizard-initialized');
    }
    
    // Initialize team select
    $('#player-team').select2({
        placeholder: 'Select team...',
        allowClear: true,
        data: getTeamOptions(),
        dropdownParent: $('#add-player-modal')
    });
    
    // Initialize position select
    $('#player-position').select2({
        placeholder: 'Select position...',
        data: [
            { id: 'Batsman', text: 'Batsman' },
            { id: 'Bowler', text: 'Bowler' },
            { id: 'All-rounder', text: 'All-rounder' },
            { id: 'Wicket-keeper', text: 'Wicket-keeper' }
        ],
        dropdownParent: $('#add-player-modal')
    });
    
    // Form validation
    $('#add-player-form').on('submit', function(e) {
        e.preventDefault();
        savePlayer();
    });
}

/**
 * Save player data
 */
function savePlayer() {
    const formData = new FormData($('#add-player-form')[0]);
    
    showLoading();
    
    // Simulate API call
    setTimeout(() => {
        const newPlayer = {
            id: generateId(),
            name: formData.get('name'),
            email: formData.get('email'),
            phone: formData.get('phone'),
            position: formData.get('position'),
            team: formData.get('team') || null,
            avatar: 'https://ui-avatars.com/api/?name=' + encodeURIComponent(formData.get('name')) + '&background=ffd700&color=1a1a1a',
            matches: 0,
            runs: 0,
            average: 0.00,
            status: 'Active',
            dateJoined: new Date()
        };
        
        // Add to table
        playersTable.row.add(newPlayer).draw();
        
        // Close modal
        $('#add-player-modal').modal('hide');
        
        // Show success
        showSuccess('Player added successfully!');
        showCelebration();
        
        // Update stats
        loadPlayerStats();
        
        hideLoading();
    }, 1500);
}

/**
 * Update bulk action buttons
 */
function updateBulkActionButtons() {
    const selectedCount = $('.player-checkbox:checked').length;
    const bulkActions = $('#bulk-actions');
    
    if (selectedCount > 0) {
        bulkActions.show();
        $('#selected-count').text(selectedCount);
    } else {
        bulkActions.hide();
    }
}

/**
 * Update select all checkbox
 */
function updateSelectAllCheckbox() {
    const totalCheckboxes = $('.player-checkbox').length;
    const checkedCheckboxes = $('.player-checkbox:checked').length;
    
    $('#select-all-players').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
    $('#select-all-players').prop('checked', checkedCheckboxes === totalCheckboxes && totalCheckboxes > 0);
}

/**
 * Bulk delete players
 */
function bulkDeletePlayers() {
    const selectedIds = $('.player-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) return;
    
    Swal.fire({
        title: 'Delete Selected Players?',
        text: `Are you sure you want to delete ${selectedIds.length} player(s)? This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete them!',
        background: '#2d2d2d',
        color: '#ffffff'
    }).then((result) => {
        if (result.isConfirmed) {
            performBulkDelete(selectedIds);
        }
    });
}

/**
 * Perform bulk delete operation
 */
function performBulkDelete(selectedIds) {
    showLoading();
    
    setTimeout(() => {
        // Remove selected rows
        selectedIds.forEach(id => {
            playersTable.rows().every(function() {
                const data = this.data();
                if (data.id === id) {
                    this.remove();
                }
            });
        });
        
        playersTable.draw();
        
        // Clear selections
        $('.player-checkbox').prop('checked', false);
        $('#select-all-players').prop('checked', false);
        updateBulkActionButtons();
        
        showSuccess(`${selectedIds.length} player(s) deleted successfully!`);
        loadPlayerStats();
        hideLoading();
    }, 1500);
}

/**
 * Bulk export players
 */
function bulkExportPlayers() {
    const selectedIds = $('.player-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        showError('Please select players to export');
        return;
    }
    
    showLoading();
    
    setTimeout(() => {
        showSuccess(`${selectedIds.length} player(s) exported successfully!`);
        hideLoading();
    }, 2000);
}

/**
 * Load player statistics
 */
function loadPlayerStats() {
    const stats = calculatePlayerStats();
    
    $('#total-active-players').text(formatNumber(stats.activeCount));
    $('#total-runs-scored').text(formatNumber(stats.totalRuns));
    $('#average-player-score').text(stats.averageScore.toFixed(2));
    $('#top-performer').text(stats.topPerformer);
}

/**
 * Calculate player statistics
 */
function calculatePlayerStats() {
    const data = playersTable ? playersTable.data().toArray() : getPlayersData();
    
    const activeCount = data.filter(player => player.status === 'Active').length;
    const totalRuns = data.reduce((sum, player) => sum + player.runs, 0);
    const averageScore = totalRuns / data.length || 0;
    const topPerformer = data.reduce((top, player) => 
        player.runs > (top ? top.runs : 0) ? player : top, null
    );
    
    return {
        activeCount,
        totalRuns,
        averageScore,
        topPerformer: topPerformer ? topPerformer.name : 'N/A'
    };
}

/**
 * Get team options for dropdowns
 */
function getTeamOptions() {
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
 * Get players data
 */
function getPlayersData() {
    return [
        {
            id: '1',
            name: 'John Smith',
            email: 'john.smith@email.com',
            phone: '+1234567890',
            position: 'Batsman',
            team: 'Lions',
            avatar: 'https://ui-avatars.com/api/?name=John+Smith&background=ffd700&color=1a1a1a',
            matches: 25,
            runs: 1250,
            average: 50.00,
            status: 'Active',
            dateJoined: new Date('2023-01-15')
        },
        {
            id: '2',
            name: 'Mike Johnson',
            email: 'mike.johnson@email.com',
            phone: '+1234567891',
            position: 'Bowler',
            team: 'Tigers',
            avatar: 'https://ui-avatars.com/api/?name=Mike+Johnson&background=ffd700&color=1a1a1a',
            matches: 22,
            runs: 450,
            average: 20.45,
            status: 'Active',
            dateJoined: new Date('2023-02-01')
        },
        {
            id: '3',
            name: 'David Wilson',
            email: 'david.wilson@email.com',
            phone: '+1234567892',
            position: 'All-rounder',
            team: 'Eagles',
            avatar: 'https://ui-avatars.com/api/?name=David+Wilson&background=ffd700&color=1a1a1a',
            matches: 30,
            runs: 1150,
            average: 38.33,
            status: 'Active',
            dateJoined: new Date('2022-12-10')
        },
        {
            id: '4',
            name: 'Chris Brown',
            email: 'chris.brown@email.com',
            phone: '+1234567893',
            position: 'Wicket-keeper',
            team: 'Hawks',
            avatar: 'https://ui-avatars.com/api/?name=Chris+Brown&background=ffd700&color=1a1a1a',
            matches: 28,
            runs: 980,
            average: 35.00,
            status: 'Injured',
            dateJoined: new Date('2023-03-05')
        },
        {
            id: '5',
            name: 'Tom Anderson',
            email: 'tom.anderson@email.com',
            phone: '+1234567894',
            position: 'Batsman',
            team: 'Warriors',
            avatar: 'https://ui-avatars.com/api/?name=Tom+Anderson&background=ffd700&color=1a1a1a',
            matches: 20,
            runs: 890,
            average: 44.50,
            status: 'Active',
            dateJoined: new Date('2023-04-12')
        }
    ];
}

// Export function
window.initializePlayers = initializePlayers;
