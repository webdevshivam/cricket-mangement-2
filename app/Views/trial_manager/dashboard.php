
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .stats-card {
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-user-tie me-2"></i>
                <strong>Trial Manager</strong>
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
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

    <div class="container-fluid py-4">
        <!-- Welcome Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h2 class="mb-3"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
                        <p class="lead mb-0">Welcome to <?= session()->get('trial_name') ?> Management Panel</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card text-center">
                    <div class="card-body">
                        <div class="stat-icon text-primary mb-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4 class="mb-1"><?= $stats['total_players'] ?></h4>
                        <p class="text-muted mb-0">Total Players</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card text-center">
                    <div class="card-body">
                        <div class="stat-icon text-success mb-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h4 class="mb-1"><?= $stats['full_payment'] ?></h4>
                        <p class="text-muted mb-0">Full Payment</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card text-center">
                    <div class="card-body">
                        <div class="stat-icon text-warning mb-3">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h4 class="mb-1"><?= $stats['partial_payment'] ?></h4>
                        <p class="text-muted mb-0">Partial Payment</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card text-center">
                    <div class="card-body">
                        <div class="stat-icon text-danger mb-3">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <h4 class="mb-1"><?= $stats['no_payment'] ?></h4>
                        <p class="text-muted mb-0">No Payment</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Collection Summary -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card stats-card text-center">
                    <div class="card-body">
                        <div class="stat-icon text-info mb-3">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                        <h4 class="mb-1">₹<?= number_format($stats['total_collection'], 2) ?></h4>
                        <p class="text-muted mb-0">Total Collection</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stats-card text-center">
                    <div class="card-body">
                        <div class="stat-icon text-primary mb-3">
                            <i class="fas fa-globe"></i>
                        </div>
                        <h4 class="mb-1">₹<?= number_format($stats['online_collection'], 2) ?></h4>
                        <p class="text-muted mb-0">Online Collection</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stats-card text-center">
                    <div class="card-body">
                        <div class="stat-icon text-secondary mb-3">
                            <i class="fas fa-money-bill"></i>
                        </div>
                        <h4 class="mb-1">₹<?= number_format($stats['offline_collection'], 2) ?></h4>
                        <p class="text-muted mb-0">Offline Collection</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <a href="/trial-manager/player-verification" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-search me-2"></i>Player Verification
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="/trial-manager/reports" class="btn btn-outline-primary btn-lg w-100">
                                    <i class="fas fa-chart-bar me-2"></i>View Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Players -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Players</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Payment Status</th>
                                        <th>Registered</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_players)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No players registered yet</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_players as $player): ?>
                                            <tr>
                                                <td><?= esc($player['name']) ?></td>
                                                <td><?= esc($player['mobile']) ?></td>
                                                <td>
                                                    <?php
                                                    $badgeClass = 'secondary';
                                                    switch ($player['payment_status']) {
                                                        case 'full': $badgeClass = 'success'; break;
                                                        case 'partial': $badgeClass = 'warning'; break;
                                                        case 'no_payment': $badgeClass = 'danger'; break;
                                                    }
                                                    ?>
                                                    <span class="badge bg-<?= $badgeClass ?>">
                                                        <?= ucfirst(str_replace('_', ' ', $player['payment_status'])) ?>
                                                    </span>
                                                </td>
                                                <td><?= date('M d, Y H:i', strtotime($player['created_at'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
