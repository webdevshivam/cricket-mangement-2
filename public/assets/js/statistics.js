/**
 * Statistics and analytics JavaScript functionality
 * Handles comprehensive cricket statistics, charts, and reports
 */

let statisticsCharts = {};

/**
 * Initialize statistics page functionality
 */
function initializeStatistics() {
    loadStatisticsData();
    initializeStatisticsCharts();
    bindStatisticsEvents();
    
    console.log('Statistics module initialized');
}

/**
 * Load statistics data
 */
function loadStatisticsData() {
    loadOverallStats();
    loadPlayerStats();
    loadTeamStats();
    loadMatchStats();
}

/**
 * Load overall statistics
 */
function loadOverallStats() {
    const stats = getOverallStatistics();
    
    $('#total-runs-all').text(formatNumber(stats.totalRuns));
    $('#total-wickets-all').text(formatNumber(stats.totalWickets));
    $('#total-sixes').text(formatNumber(stats.totalSixes));
    $('#total-fours').text(formatNumber(stats.totalFours));
}

/**
 * Initialize statistics charts
 */
function initializeStatisticsCharts() {
    initializeRunsChart();
    initializeWicketsChart();
    initializeTeamPerformanceChart();
    initializePlayerComparisonChart();
    initializeMatchTrendsChart();
    initializeVenueStatsChart();
}

/**
 * Initialize runs analysis chart
 */
function initializeRunsChart() {
    const ctx = document.getElementById('runsChart');
    if (!ctx) return;
    
    const data = getRunsChartData();
    
    statisticsCharts.runsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Runs Scored',
                data: data.runs,
                backgroundColor: 'rgba(255, 215, 0, 0.8)',
                borderColor: '#ffd700',
                borderWidth: 2
            }, {
                label: 'Balls Faced',
                data: data.balls,
                backgroundColor: 'rgba(255, 107, 107, 0.8)',
                borderColor: '#ff6b6b',
                borderWidth: 2,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#444444' },
                    ticks: { color: '#ffffff' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: { color: '#ffffff' }
                },
                x: {
                    grid: { color: '#444444' },
                    ticks: { color: '#ffffff' }
                }
            },
            plugins: {
                legend: { labels: { color: '#ffffff' } },
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
 * Initialize wickets analysis chart
 */
function initializeWicketsChart() {
    const ctx = document.getElementById('wicketsChart');
    if (!ctx) return;
    
    const data = getWicketsChartData();
    
    statisticsCharts.wicketsChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.values,
                backgroundColor: [
                    '#ffd700', '#ff6b6b', '#4ecdc4', '#45b7d1', '#f9ca24', '#f0932b'
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
                    labels: { color: '#ffffff', padding: 15 }
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
 * Initialize team performance comparison chart
 */
function initializeTeamPerformanceChart() {
    const ctx = document.getElementById('teamPerformanceChart');
    if (!ctx) return;
    
    const data = getTeamPerformanceData();
    
    statisticsCharts.teamPerformanceChart = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Batting', 'Bowling', 'Fielding', 'Consistency', 'Strategy', 'Fitness'],
            datasets: data.teams.map((team, index) => ({
                label: team.name,
                data: team.stats,
                borderColor: team.color,
                backgroundColor: team.color + '20',
                borderWidth: 2,
                pointBackgroundColor: team.color,
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2
            }))
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100,
                    grid: { color: '#444444' },
                    angleLines: { color: '#444444' },
                    pointLabels: { color: '#ffffff' },
                    ticks: { color: '#ffffff', display: false }
                }
            },
            plugins: {
                legend: { labels: { color: '#ffffff' } },
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
 * Initialize player comparison chart
 */
function initializePlayerComparisonChart() {
    const ctx = document.getElementById('playerComparisonChart');
    if (!ctx) return;
    
    const data = getPlayerComparisonData();
    
    statisticsCharts.playerComparisonChart = new Chart(ctx, {
        type: 'horizontalBar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Runs',
                data: data.runs,
                backgroundColor: 'rgba(255, 215, 0, 0.8)',
                borderColor: '#ffd700',
                borderWidth: 1
            }, {
                label: 'Strike Rate',
                data: data.strikeRate,
                backgroundColor: 'rgba(255, 107, 107, 0.8)',
                borderColor: '#ff6b6b',
                borderWidth: 1,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: '#444444' },
                    ticks: { color: '#ffffff' }
                },
                y: {
                    grid: { color: '#444444' },
                    ticks: { color: '#ffffff' }
                }
            },
            plugins: {
                legend: { labels: { color: '#ffffff' } },
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
 * Initialize match trends chart
 */
function initializeMatchTrendsChart() {
    const ctx = document.getElementById('matchTrendsChart');
    if (!ctx) return;
    
    const data = getMatchTrendsData();
    
    statisticsCharts.matchTrendsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Average Score',
                data: data.avgScore,
                borderColor: '#ffd700',
                backgroundColor: 'rgba(255, 215, 0, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Wickets per Match',
                data: data.wicketsPerMatch,
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
                    grid: { color: '#444444' },
                    ticks: { color: '#ffffff' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: { color: '#ffffff' }
                },
                x: {
                    grid: { color: '#444444' },
                    ticks: { color: '#ffffff' }
                }
            },
            plugins: {
                legend: { labels: { color: '#ffffff' } },
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
 * Initialize venue statistics chart
 */
function initializeVenueStatsChart() {
    const ctx = document.getElementById('venueStatsChart');
    if (!ctx) return;
    
    const data = getVenueStatsData();
    
    statisticsCharts.venueStatsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Matches Played',
                data: data.matches,
                backgroundColor: 'rgba(255, 215, 0, 0.8)',
                borderColor: '#ffd700',
                borderWidth: 1
            }, {
                label: 'Average Attendance',
                data: data.attendance,
                backgroundColor: 'rgba(68, 236, 212, 0.8)',
                borderColor: '#4ecdc4',
                borderWidth: 1,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#444444' },
                    ticks: { color: '#ffffff' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: { color: '#ffffff' }
                },
                x: {
                    grid: { color: '#444444' },
                    ticks: { color: '#ffffff' }
                }
            },
            plugins: {
                legend: { labels: { color: '#ffffff' } },
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
 * Load player statistics
 */
function loadPlayerStats() {
    const stats = getTopPlayerStats();
    
    // Top run scorers
    const runScorersHtml = stats.topRunScorers.map(player => `
        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-secondary rounded">
            <div class="d-flex align-items-center">
                <img src="${player.avatar}" alt="${player.name}" class="rounded-circle me-2" width="30" height="30">
                <div>
                    <div class="text-light fw-bold">${player.name}</div>
                    <small class="text-muted">${player.team}</small>
                </div>
            </div>
            <div class="text-warning fw-bold">${formatNumber(player.runs)}</div>
        </div>
    `).join('');
    $('#top-run-scorers').html(runScorersHtml);
    
    // Top wicket takers
    const wicketTakersHtml = stats.topWicketTakers.map(player => `
        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-secondary rounded">
            <div class="d-flex align-items-center">
                <img src="${player.avatar}" alt="${player.name}" class="rounded-circle me-2" width="30" height="30">
                <div>
                    <div class="text-light fw-bold">${player.name}</div>
                    <small class="text-muted">${player.team}</small>
                </div>
            </div>
            <div class="text-warning fw-bold">${player.wickets}</div>
        </div>
    `).join('');
    $('#top-wicket-takers').html(wicketTakersHtml);
}

/**
 * Load team statistics
 */
function loadTeamStats() {
    const teamStats = getTeamStatistics();
    
    const teamStatsHtml = teamStats.map(team => `
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="team-logo me-2" style="background: ${team.color}; width: 25px; height: 25px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-weight: bold; font-size: 10px;">${team.shortName}</span>
                    </div>
                    <span class="text-warning">${team.name}</span>
                </div>
            </td>
            <td class="text-center">${team.matches}</td>
            <td class="text-center">${team.wins}</td>
            <td class="text-center">${team.losses}</td>
            <td class="text-center">${team.draws}</td>
            <td class="text-center">${team.winRate}%</td>
            <td class="text-center">${formatNumber(team.runs)}</td>
            <td class="text-center">${team.average.toFixed(2)}</td>
        </tr>
    `).join('');
    
    $('#team-stats-table tbody').html(teamStatsHtml);
}

/**
 * Load match statistics
 */
function loadMatchStats() {
    const matchStats = getMatchStatistics();
    
    $('#highest-team-score').text(matchStats.highestTeamScore);
    $('#lowest-team-score').text(matchStats.lowestTeamScore);
    $('#highest-individual').text(matchStats.highestIndividual);
    $('#best-bowling').text(matchStats.bestBowling);
}

/**
 * Bind statistics events
 */
function bindStatisticsEvents() {
    // Filter change handlers
    $('#stats-season-filter, #stats-format-filter, #stats-team-filter').on('change', function() {
        refreshStatistics();
    });
    
    // Export buttons
    $('#export-stats-pdf').on('click', function() {
        exportStatistics('pdf');
    });
    
    $('#export-stats-excel').on('click', function() {
        exportStatistics('excel');
    });
    
    // Refresh button
    $('#refresh-statistics').on('click', function() {
        refreshStatistics();
    });
}

/**
 * Refresh statistics
 */
function refreshStatistics() {
    showLoading();
    
    setTimeout(() => {
        // Reload all data
        loadStatisticsData();
        
        // Update all charts
        Object.values(statisticsCharts).forEach(chart => {
            chart.update();
        });
        
        showSuccess('Statistics refreshed successfully!');
        hideLoading();
    }, 1500);
}

/**
 * Export statistics
 */
function exportStatistics(format) {
    showLoading();
    
    setTimeout(() => {
        showSuccess(`Statistics exported as ${format.toUpperCase()} successfully!`);
        hideLoading();
    }, 2000);
}

// Data getter functions
function getOverallStatistics() {
    return {
        totalRuns: 45678,
        totalWickets: 892,
        totalSixes: 234,
        totalFours: 1567
    };
}

function getRunsChartData() {
    return {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        runs: [1200, 1500, 1800, 2200, 1900, 2400],
        balls: [800, 900, 1100, 1300, 1150, 1400]
    };
}

function getWicketsChartData() {
    return {
        labels: ['Bowled', 'Caught', 'LBW', 'Run Out', 'Stumped', 'Hit Wicket'],
        values: [150, 280, 120, 95, 45, 8]
    };
}

function getTeamPerformanceData() {
    return {
        teams: [
            {
                name: 'Lions',
                color: '#ffd700',
                stats: [85, 78, 82, 90, 88, 85]
            },
            {
                name: 'Tigers',
                color: '#ff6600',
                stats: [78, 85, 75, 82, 80, 88]
            },
            {
                name: 'Eagles',
                color: '#0066cc',
                stats: [82, 72, 88, 85, 78, 80]
            }
        ]
    };
}

function getPlayerComparisonData() {
    return {
        labels: ['John Smith', 'Mike Johnson', 'David Wilson', 'Chris Brown', 'Tom Anderson'],
        runs: [1250, 980, 1150, 890, 1050],
        strikeRate: [145.5, 132.8, 128.9, 156.2, 142.1]
    };
}

function getMatchTrendsData() {
    return {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'],
        avgScore: [165, 172, 158, 180, 175, 168],
        wicketsPerMatch: [7.2, 8.1, 6.8, 7.9, 7.5, 8.3]
    };
}

function getVenueStatsData() {
    return {
        labels: ['Stadium A', 'Stadium B', 'Stadium C', 'Central Ground', 'North Ground'],
        matches: [12, 15, 8, 20, 10],
        attendance: [15000, 18000, 12000, 22000, 14000]
    };
}

function getTopPlayerStats() {
    return {
        topRunScorers: [
            { name: 'John Smith', team: 'Lions', runs: 1250, avatar: 'https://ui-avatars.com/api/?name=John+Smith&background=ffd700&color=1a1a1a' },
            { name: 'David Wilson', team: 'Eagles', runs: 1150, avatar: 'https://ui-avatars.com/api/?name=David+Wilson&background=0066cc&color=ffffff' },
            { name: 'Tom Anderson', team: 'Warriors', runs: 1050, avatar: 'https://ui-avatars.com/api/?name=Tom+Anderson&background=ff6600&color=ffffff' },
            { name: 'Mike Johnson', team: 'Tigers', runs: 980, avatar: 'https://ui-avatars.com/api/?name=Mike+Johnson&background=ff6600&color=000000' },
            { name: 'Chris Brown', team: 'Hawks', runs: 890, avatar: 'https://ui-avatars.com/api/?name=Chris+Brown&background=0066cc&color=ffffff' }
        ],
        topWicketTakers: [
            { name: 'Mike Johnson', team: 'Tigers', wickets: 45, avatar: 'https://ui-avatars.com/api/?name=Mike+Johnson&background=ff6600&color=000000' },
            { name: 'Alex Wilson', team: 'Lions', wickets: 38, avatar: 'https://ui-avatars.com/api/?name=Alex+Wilson&background=ffd700&color=1a1a1a' },
            { name: 'Sam Davis', team: 'Eagles', wickets: 35, avatar: 'https://ui-avatars.com/api/?name=Sam+Davis&background=0066cc&color=ffffff' },
            { name: 'Ryan Clark', team: 'Hawks', wickets: 32, avatar: 'https://ui-avatars.com/api/?name=Ryan+Clark&background=0066cc&color=ffffff' },
            { name: 'Jake Miller', team: 'Warriors', wickets: 28, avatar: 'https://ui-avatars.com/api/?name=Jake+Miller&background=ff6600&color=ffffff' }
        ]
    };
}

function getTeamStatistics() {
    return [
        { name: 'Lions', shortName: 'LIO', color: '#ffd700', matches: 25, wins: 18, losses: 5, draws: 2, winRate: 72, runs: 4250, average: 170.0 },
        { name: 'Tigers', shortName: 'TIG', color: '#ff6600', matches: 22, wins: 14, losses: 6, draws: 2, winRate: 64, runs: 3680, average: 167.3 },
        { name: 'Eagles', shortName: 'EAG', color: '#0066cc', matches: 20, wins: 12, losses: 7, draws: 1, winRate: 60, runs: 3340, average: 167.0 },
        { name: 'Hawks', shortName: 'HAW', color: '#4ecdc4', matches: 24, wins: 11, losses: 11, draws: 2, winRate: 46, runs: 3920, average: 163.3 },
        { name: 'Warriors', shortName: 'WAR', color: '#f9ca24', matches: 21, wins: 9, losses: 10, draws: 2, winRate: 43, runs: 3360, average: 160.0 },
        { name: 'Knights', shortName: 'KNI', color: '#6c5ce7', matches: 18, wins: 6, losses: 11, draws: 1, winRate: 33, runs: 2880, average: 160.0 }
    ];
}

function getMatchStatistics() {
    return {
        highestTeamScore: '245/4 (20 overs)',
        lowestTeamScore: '78 (15.2 overs)',
        highestIndividual: '156* by John Smith',
        bestBowling: '6/18 by Mike Johnson'
    };
}

// Export function
window.initializeStatistics = initializeStatistics;
