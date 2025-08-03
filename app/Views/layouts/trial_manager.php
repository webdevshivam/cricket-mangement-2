
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - Trial Manager Portal</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    
    <style>
        :root {
            --primary-color: #d4af37;
            --secondary-color: #1a1a1a;
            --accent-color: #2c2c2c;
            --text-light: #ffffff;
            --text-muted: #a0a0a0;
        }

        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2c2c2c 100%);
            color: var(--text-light);
            min-height: 100vh;
        }

        .sidebar {
            background: linear-gradient(180deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            min-height: 100vh;
            border-right: 3px solid var(--primary-color);
        }

        .sidebar .nav-link {
            color: var(--text-muted);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 10px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover {
            color: var(--primary-color);
            background: rgba(212, 175, 55, 0.1);
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            color: var(--text-light);
            background: var(--primary-color);
        }

        .main-content {
            background: var(--secondary-color);
            min-height: 100vh;
        }

        .navbar {
            background: var(--accent-color) !important;
            border-bottom: 2px solid var(--primary-color);
        }

        .card {
            background: var(--accent-color);
            border: 1px solid var(--primary-color);
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: var(--secondary-color);
        }

        .btn-primary:hover {
            background: #b8941f;
            border-color: #b8941f;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <!-- Logo/Brand -->
                    <div class="text-center mb-4 p-3">
                        <h4 class="text-warning">
                            <i class="fas fa-futbol me-2"></i>
                            Trial Manager
                        </h4>
                        <small class="text-muted">Management Portal</small>
                    </div>

                    <!-- Navigation -->
                    <ul class="nav flex-column">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a class="nav-link <?= (current_url() == base_url('trial-manager/dashboard')) ? 'active' : '' ?>" 
                               href="<?= base_url('trial-manager/dashboard') ?>">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>

                        <!-- Player Verification -->
                        <li class="nav-item">
                            <a class="nav-link <?= (current_url() == base_url('trial-manager/player-verification')) ? 'active' : '' ?>" 
                               href="<?= base_url('trial-manager/player-verification') ?>">
                                <i class="fas fa-user-check me-2"></i>
                                Player Verification
                            </a>
                        </li>

                        <!-- Separator -->
                        <hr class="my-3 text-muted">

                        <!-- Logout -->
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="<?= base_url('trial-manager/logout') ?>">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Top Navigation Bar -->
                <nav class="navbar navbar-expand-lg navbar-dark mb-4">
                    <div class="container-fluid">
                        <span class="navbar-brand">
                            Welcome, <?= session()->get('tm_name') ?? 'Trial Manager' ?>
                        </span>
                        <div class="navbar-nav ms-auto">
                            <span class="navbar-text text-warning">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <?= session()->get('tm_trial_name') ?? 'Trial Location' ?>
                            </span>
                        </div>
                    </div>
                </nav>

                <!-- Content Area -->
                <div class="content-wrapper">
                    <?= $this->renderSection('content') ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
