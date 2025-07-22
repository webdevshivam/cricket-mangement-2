/**
 * Dashboard specific JavaScript functionality
 * Handles dashboard charts, statistics, and real-time updates
 */

// Dashboard variables
let matchChart = null;
let teamChart = null;
let dashboardInterval = null;

/**
 * Initialize dashboard functionality
 */
function initializeDashboard() {
    loadDashboardStats();
    initializeDashboardCharts();
    loadRecentActivities();
    loadUpcomingMatches();
    startDashboardUpdates();
    
    console.log('Dashboard initialized');
}

/**
 * Load dashboard statistics
 */
function loadDashboardStats() {
    // Get data from sample data or API
    const stats = getStatsData();
    
    // Animate counters
    animateCounter('#total-players', stats.totalPlayers);
    animateCounter('#total-teams', stats.totalTeams);
    animateCounter('#total-matches', stats.totalMatches);
    animateCounter('#active-tournaments', stats.activeTournaments);
}

/**
 * Initialize dashboard charts
 */
function initializeDashboardCharts() {
    initializeMatchChart();
    initializeTeamChart();
}

/**
 * Initialize match statistics chart
 */
function initializeMatchChart() {
    const ctx = document.getElementById('matchChart');
    if (!ctx) return;
    
    const matchData = getMatchChartData();
    
    matchChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: matchData.labels,
            datasets: [{
                label: 'Matches Played',
                data: matchData.matches,
                borderColor: '#ffd700',
                backgroundColor: 'rgba(255, 215, 0, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Total Runs',
                data: matchData.runs,
                borderColor: '#ff6b6b',
                backgroundColor: 'rgba(255, 107, 107, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#444444'
                    },
                    ticks: {
                        color: '#ffffff'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        color: '#ffffff'
                    }
                },
                x: {
                    grid: {
                        color: '#444444'
                    },
                    ticks: {
                        color: '#ffffff'
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#ffffff'
                    }
                },
                tooltip: {
                    backgroundColor: '#2d2d2d',
                    titleColor: '#ffd700',
                    bodyColor: '#ffffff',
                    borderColor: '#ffd700',
                    borderWidth: 1
                }
            }
        }
    });
}

/**
 * Initialize team performance chart
 */
function initializeTeamChart() {
    const ctx = document.getElementById('teamChart');
    if (!ctx) return;
    
    const teamData = getTeamChartData();
    
    teamChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: teamData.labels,
            datasets: [{
                data: teamData.values,
                backgroundColor: [
                    '#ffd700',
                    '#ff6b6b',
                    '#4ecdc4',
                    '#45b7d1',
                    '#f9ca24',
                    '#f0932b'
                ],
                borderColor: '#2d2d2d',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#ffffff',
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: '#2d2d2d',
                    titleColor: '#ffd700',
                    bodyColor: '#ffffff',
                    borderColor: '#ffd700',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

/**
 * Load recent activities
 */
function loadRecentActivities() {
    const activities = getRecentActivities();
    const container = $('#activity-feed');
    
    container.empty();
    
    activities.forEach(activity => {
        const activityHtml = `
            <div class="activity-item d-flex align-items-center animate-fade-in">
                <div class="activity-icon">
                    <i class="${activity.icon}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="text-light">${activity.title}</div>
                    <small class="text-muted">${formatDate(activity.date)} at ${formatTime(activity.date)}</small>
                </div>
            </div>
        `;
        container.append(activityHtml);
    });
}

/**
 * Load upcoming matches
 */
function loadUpcomingMatches() {
    const matches = getUpcomingMatches();
    const container = $('#upcoming-matches');
    
    container.empty();
    
    if (matches.length === 0) {
        container.html(`
            <div class="text-center text-muted py-3">
                <i class="fas fa-calendar-times fa-2x mb-2"></i>
                <p>No upcoming matches scheduled</p>
            </div>
        `);
        return;
    }
    
    matches.forEach(match => {
        const matchHtml = `
            <div class="match-item animate-slide-up">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-warning mb-1">${match.team1} vs ${match.team2}</h6>
                        <small class="text-muted">
                            <i class="fas fa-calendar"></i> ${formatDate(match.date)}
                            <i class="fas fa-clock ms-2"></i> ${formatTime(match.date)}
                        </small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-warning text-dark">${match.status}</span>
                        <br>
                        <small class="text-muted">${match.venue}</small>
                    </div>
                </div>
            </div>
        `;
        container.append(matchHtml);
    });
}

/**
 * Animate counter numbers
 * @param {string} selector - Element selector
 * @param {number} target - Target number
 */
function animateCounter(selector, target) {
    const element = $(selector);
    const current = parseInt(element.text()) || 0;
    const increment = Math.ceil((target - current) / 30);
    
    if (current < target) {
        element.text(current + increment);
        setTimeout(() => animateCounter(selector, target), 50);
    } else {
        element.text(target);
    }
}

/**
 * Start dashboard real-time updates
 */
function startDashboardUpdates() {
    // Update dashboard every 30 seconds
    dashboardInterval = setInterval(() => {
        updateDashboardStats();
        updateRecentActivities();
    }, 30000);
}

/**
 * Stop dashboard updates
 */
function stopDashboardUpdates() {
    if (dashboardInterval) {
        clearInterval(dashboardInterval);
        dashboardInterval = null;
    }
}

/**
 * Update dashboard statistics
 */
function updateDashboardStats() {
    // Simulate real-time updates
    const stats = getStatsData();
    
    // Add small random variations
    stats.totalPlayers += Math.floor(Math.random() * 3);
    stats.totalMatches += Math.floor(Math.random() * 2);
    
    // Update counters
    $('#total-players').text(formatNumber(stats.totalPlayers));
    $('#total-teams').text(formatNumber(stats.totalTeams));
    $('#total-matches').text(formatNumber(stats.totalMatches));
    $('#active-tournaments').text(formatNumber(stats.activeTournaments));
}

/**
 * Update recent activities
 */
function updateRecentActivities() {
    // Simulate new activity
    const newActivities = [
        {
            icon: 'fas fa-user-plus',
            title: 'New player registered: John Smith',
            date: new Date()
        },
        {
            icon: 'fas fa-trophy',
            title: 'Match completed: Team A vs Team B',
            date: new Date()
        }
    ];
    
    const randomActivity = newActivities[Math.floor(Math.random() * newActivities.length)];
    
    // Add new activity to the top
    const activityHtml = `
        <div class="activity-item d-flex align-items-center animate-fade-in">
            <div class="activity-icon">
                <i class="${randomActivity.icon}"></i>
            </div>
            <div class="flex-grow-1">
                <div class="text-light">${randomActivity.title}</div>
                <small class="text-muted">${formatDate(randomActivity.date)} at ${formatTime(randomActivity.date)}</small>
            </div>
        </div>
    `;
    
    const container = $('#activity-feed');
    container.prepend(activityHtml);
    
    // Remove old activities (keep only last 5)
    container.children().slice(5).remove();
}

/**
 * Get statistics data
 * @returns {object} Statistics data
 */
function getStatsData() {
    return {
        totalPlayers: 150,
        totalTeams: 12,
        totalMatches: 85,
        activeTournaments: 3
    };
}

/**
 * Get match chart data
 * @returns {object} Match chart data
 */
function getMatchChartData() {
    return {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        matches: [12, 19, 15, 25, 22, 30],
        runs: [1200, 1900, 1500, 2500, 2200, 3000]
    };
}

/**
 * Get team chart data
 * @returns {object} Team chart data
 */
function getTeamChartData() {
    return {
        labels: ['Wins', 'Losses', 'Draws', 'No Result'],
        values: [45, 25, 15, 5]
    };
}

/**
 * Get recent activities
 * @returns {array} Recent activities
 */
function getRecentActivities() {
    return [
        {
            icon: 'fas fa-user-plus',
            title: 'New player added: Mike Johnson',
            date: new Date(Date.now() - 1000 * 60 * 30) // 30 minutes ago
        },
        {
            icon: 'fas fa-trophy',
            title: 'Tournament "Summer League" started',
            date: new Date(Date.now() - 1000 * 60 * 60 * 2) // 2 hours ago
        },
        {
            icon: 'fas fa-calendar-check',
            title: 'Match scheduled: Lions vs Tigers',
            date: new Date(Date.now() - 1000 * 60 * 60 * 4) // 4 hours ago
        },
        {
            icon: 'fas fa-edit',
            title: 'Team profile updated: Eagles',
            date: new Date(Date.now() - 1000 * 60 * 60 * 6) // 6 hours ago
        }
    ];
}

/**
 * Get upcoming matches
 * @returns {array} Upcoming matches
 */
function getUpcomingMatches() {
    return [
        {
            team1: 'Lions',
            team2: 'Tigers',
            date: new Date(Date.now() + 1000 * 60 * 60 * 24), // Tomorrow
            venue: 'Stadium A',
            status: 'Scheduled'
        },
        {
            team1: 'Eagles',
            team2: 'Hawks',
            date: new Date(Date.now() + 1000 * 60 * 60 * 48), // Day after tomorrow
            venue: 'Stadium B',
            status: 'Scheduled'
        },
        {
            team1: 'Warriors',
            team2: 'Knights',
            date: new Date(Date.now() + 1000 * 60 * 60 * 72), // 3 days
            venue: 'Stadium C',
            status: 'Scheduled'
        }
    ];
}

/**
 * Refresh dashboard
 */
function refreshDashboard() {
    showLoading();
    
    // Simulate refresh delay
    setTimeout(() => {
        loadDashboardStats();
        loadRecentActivities();
        loadUpcomingMatches();
        
        // Update charts
        if (matchChart) {
            const newData = getMatchChartData();
            matchChart.data.datasets[0].data = newData.matches;
            matchChart.data.datasets[1].data = newData.runs;
            matchChart.update();
        }
        
        if (teamChart) {
            const newData = getTeamChartData();
            teamChart.data.datasets[0].data = newData.values;
            teamChart.update();
        }
        
        hideLoading();
        showSuccess('Dashboard refreshed successfully!');
    }, 1500);
}

// Event handlers
$(document).ready(function() {
    // Refresh button handler
    $('body').on('click', '[data-action="refresh-dashboard"]', function() {
        refreshDashboard();
    });
    
    // Export dashboard data
    $('body').on('click', '[data-action="export-dashboard"]', function() {
        const format = $(this).data('format') || 'pdf';
        exportDashboardData(format);
    });
});

/**
 * Export dashboard data
 * @param {string} format - Export format
 */
function exportDashboardData(format) {
    showLoading();
    
    // Simulate export process
    setTimeout(() => {
        showSuccess(`Dashboard data exported as ${format.toUpperCase()} successfully!`);
        hideLoading();
    }, 2000);
}

// Cleanup function
function cleanupDashboard() {
    stopDashboardUpdates();
    
    if (matchChart) {
        matchChart.destroy();
        matchChart = null;
    }
    
    if (teamChart) {
        teamChart.destroy();
        teamChart = null;
    }
}

// Auto-cleanup when page changes
$(window).on('beforeunload', cleanupDashboard);

// Export functions
window.initializeDashboard = initializeDashboard;
window.refreshDashboard = refreshDashboard;
