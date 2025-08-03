
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            padding: 2rem 0;
        }

        .dashboard-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .action-card {
            text-align: center;
            padding: 2rem;
            border-radius: 15px;
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .action-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .table-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .table-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .badge {
            border-radius: 10px;
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
        }

        .welcome-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="/trial-manager/dashboard">
                <i class="fas fa-clipboard-check me-2" style="color: var(--primary-color);"></i>
                Trial Manager Panel
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-semibold" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1" style="color: var(--primary-color);"></i>
                        <?= session()->get('name') ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><h6 class="dropdown-header"><?= session()->get('trial_name') ?></h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/trial-manager/logout">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid main-content">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">Welcome, <?= session()->get('name') ?>!</h1>
                    <p class="mb-0 fs-5">Managing trials for: <strong><?= session()->get('trial_name') ?></strong></p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fas fa-futbol" style="font-size: 4rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>

        <!-- Statistics Row -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= $stats['total_players'] ?? 0 ?></div>
                    <div class="stats-label">
                        <i class="fas fa-users me-1"></i>Total Players
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <div class="stats-number"><?= $stats['full_payment'] ?? 0 ?></div>
                    <div class="stats-label">
                        <i class="fas fa-check-circle me-1"></i>Full Payment
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                    <div class="stats-number"><?= $stats['partial_payment'] ?? 0 ?></div>
                    <div class="stats-label">
                        <i class="fas fa-exclamation-triangle me-1"></i>Partial Payment
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);">
                    <div class="stats-number">₹<?= number_format($stats['total_collection'] ?? 0) ?></div>
                    <div class="stats-label">
                        <i class="fas fa-money-bill-wave me-1"></i>Total Collection
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4>Player Verification</h4>
                    <p class="text-muted">Search and verify player registration status, collect payments</p>
                    <a href="/trial-manager/player-verification" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Start Verification
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h4>Payment Reports</h4>
                    <p class="text-muted">View detailed payment reports and collection summaries</p>
                    <button class="btn btn-primary" onclick="showPaymentModal()">
                        <i class="fas fa-chart-bar me-2"></i>View Reports
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent Players -->
        <?php if (!empty($recent_players)): ?>
        <div class="row">
            <div class="col-12">
                <div class="table-card">
                    <div class="table-header">
                        <i class="fas fa-clock me-2"></i>Recent Players
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Payment Status</th>
                                    <th>Registration Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_players as $player): ?>
                                <tr>
                                    <td>
                                        <i class="fas fa-user me-2 text-primary"></i>
                                        <?= esc($player['name']) ?>
                                    </td>
                                    <td><?= esc($player['mobile']) ?></td>
                                    <td>
                                        <?php
                                        $badgeClass = match($player['payment_status']) {
                                            'full' => 'bg-success',
                                            'partial' => 'bg-warning',
                                            'no_payment' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        $statusText = match($player['payment_status']) {
                                            'full' => 'Full Payment',
                                            'partial' => 'Partial Payment',
                                            'no_payment' => 'No Payment',
                                            default => 'Unknown'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= $statusText ?></span>
                                    </td>
                                    <td>
                                        <i class="fas fa-calendar me-1 text-muted"></i>
                                        <?= date('M d, Y', strtotime($player['created_at'])) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Payment Report Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Collection Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-success">Online Collection</h5>
                                    <h3 class="text-success">₹<?= number_format($stats['online_collection'] ?? 0) ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-primary">Offline Collection</h5>
                                    <h3 class="text-primary">₹<?= number_format($stats['offline_collection'] ?? 0) ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showPaymentModal() {
            new bootstrap.Modal(document.getElementById('paymentModal')).show();
        }
    </script>
</body>
</html>
