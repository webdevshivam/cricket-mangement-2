
<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Admin Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Dashboard Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-light">
            <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
        </h1>
        <div class="d-none d-sm-inline-block">
            <span class="text-muted">Welcome back, <?= session()->get('name') ?? 'Admin' ?>!</span>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-primary border-0 text-white h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Players</div>
                            <div class="h5 mb-0 font-weight-bold">1,234</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success border-0 text-white h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Active Teams</div>
                            <div class="h5 mb-0 font-weight-bold">56</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users-cog fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-info border-0 text-white h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Tournaments</div>
                            <div class="h5 mb-0 font-weight-bold">12</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trophy fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-warning border-0 text-white h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Trial Players</div>
                            <div class="h5 mb-0 font-weight-bold">89</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Management Cards -->
    <div class="row">
        <!-- Player Management -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card bg-dark border-primary h-100">
                <div class="card-body text-center">
                    <div class="text-primary mb-3">
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                    <h5 class="card-title text-light">Player Management</h5>
                    <p class="card-text text-muted">Add, edit, and manage players</p>
                    <a href="/admin/players" class="btn btn-outline-primary">
                        <i class="fas fa-users me-2"></i>Manage Players
                    </a>
                </div>
            </div>
        </div>

        <!-- Team Management -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card bg-dark border-success h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-3">
                        <i class="fas fa-users-cog fa-3x"></i>
                    </div>
                    <h5 class="card-title text-light">Team Management</h5>
                    <p class="card-text text-muted">Create and manage teams</p>
                    <a href="/admin/teams" class="btn btn-outline-success">
                        <i class="fas fa-users-cog me-2"></i>Manage Teams
                    </a>
                </div>
            </div>
        </div>

        <!-- Tournament Management -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card bg-dark border-info h-100">
                <div class="card-body text-center">
                    <div class="text-info mb-3">
                        <i class="fas fa-trophy fa-3x"></i>
                    </div>
                    <h5 class="card-title text-light">Tournaments</h5>
                    <p class="card-text text-muted">Create and manage tournaments</p>
                    <a href="/admin/tournaments" class="btn btn-outline-info">
                        <i class="fas fa-trophy me-2"></i>Manage Tournaments
                    </a>
                </div>
            </div>
        </div>

        <!-- Trial Registration -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card bg-dark border-warning h-100">
                <div class="card-body text-center">
                    <div class="text-warning mb-3">
                        <i class="fas fa-user-clock fa-3x"></i>
                    </div>
                    <h5 class="card-title text-light">Trial Registration</h5>
                    <p class="card-text text-muted">Manage trial player registrations</p>
                    <a href="/admin/trial-registration" class="btn btn-outline-warning">
                        <i class="fas fa-user-clock me-2"></i>View Trials
                    </a>
                </div>
            </div>
        </div>

        <!-- League Registration -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card bg-dark border-danger h-100">
                <div class="card-body text-center">
                    <div class="text-danger mb-3">
                        <i class="fas fa-medal fa-3x"></i>
                    </div>
                    <h5 class="card-title text-light">League Registration</h5>
                    <p class="card-text text-muted">Manage league player registrations</p>
                    <a href="/admin/league/registration" class="btn btn-outline-danger">
                        <i class="fas fa-medal me-2"></i>View League
                    </a>
                </div>
            </div>
        </div>

        <!-- Grade Management -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card bg-dark border-secondary h-100">
                <div class="card-body text-center">
                    <div class="text-secondary mb-3">
                        <i class="fas fa-graduation-cap fa-3x"></i>
                    </div>
                    <h5 class="card-title text-light">Grade Management</h5>
                    <p class="card-text text-muted">Manage player grades and assignments</p>
                    <a href="/admin/grades" class="btn btn-outline-secondary">
                        <i class="fas fa-graduation-cap me-2"></i>Manage Grades
                    </a>
                </div>
            </div>
        </div>

        <!-- Trial Cities -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card bg-dark border-light h-100">
                <div class="card-body text-center">
                    <div class="text-light mb-3">
                        <i class="fas fa-map-marker-alt fa-3x"></i>
                    </div>
                    <h5 class="card-title text-light">Trial Cities</h5>
                    <p class="card-text text-muted">Manage trial cities and locations</p>
                    <a href="/admin/trial-cities" class="btn btn-outline-light">
                        <i class="fas fa-map-marker-alt me-2"></i>Manage Cities
                    </a>
                </div>
            </div>
        </div>

        <!-- API Settings -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card bg-dark border-primary h-100">
                <div class="card-body text-center">
                    <div class="text-primary mb-3">
                        <i class="fas fa-cog fa-3x"></i>
                    </div>
                    <h5 class="card-title text-light">API Settings</h5>
                    <p class="card-text text-muted">Configure API and system settings</p>
                    <a href="/admin/api-settings" class="btn btn-outline-primary">
                        <i class="fas fa-cog me-2"></i>API Settings
                    </a>
                </div>
            </div>
        </div>

        <!-- OTP Settings -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card bg-dark border-success h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-3">
                        <i class="fas fa-mobile-alt fa-3x"></i>
                    </div>
                    <h5 class="card-title text-light">OTP Settings</h5>
                    <p class="card-text text-muted">Configure OTP verification settings</p>
                    <a href="/admin/otp-settings" class="btn btn-outline-success">
                        <i class="fas fa-mobile-alt me-2"></i>OTP Settings
                    </a>
                </div>
            </div>
        </div>

        <!-- QR Code Settings -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card bg-dark border-info h-100">
                <div class="card-body text-center">
                    <div class="text-info mb-3">
                        <i class="fas fa-qrcode fa-3x"></i>
                    </div>
                    <h5 class="card-title text-light">QR Code Settings</h5>
                    <p class="card-text text-muted">Configure QR code generation settings</p>
                    <a href="/admin/qr-code-setting" class="btn btn-outline-info">
                        <i class="fas fa-qrcode me-2"></i>QR Settings
                    </a>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card bg-dark border-warning h-100">
                <div class="card-body text-center">
                    <div class="text-warning mb-3">
                        <i class="fas fa-key fa-3x"></i>
                    </div>
                    <h5 class="card-title text-light">Change Password</h5>
                    <p class="card-text text-muted">Update your admin password</p>
                    <a href="/admin/change-password" class="btn btn-outline-warning">
                        <i class="fas fa-lock me-2"></i>Change Password
                    </a>
                </div>
            </div>
        </div>

        <!-- Manage Admins -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card bg-dark border-danger h-100">
                <div class="card-body text-center">
                    <div class="text-danger mb-3">
                        <i class="fas fa-users-cog fa-3x"></i>
                    </div>
                    <h5 class="card-title text-light">Manage Admins</h5>
                    <p class="card-text text-muted">Add, edit, or remove admin users</p>
                    <a href="/admin/manage-admins" class="btn btn-outline-danger">
                        <i class="fas fa-users-cog me-2"></i>Manage Admins
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card bg-dark border-secondary">
                <div class="card-header bg-secondary text-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Recent Activity
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-striped">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Activity</th>
                                    <th>User</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?= date('Y-m-d H:i:s') ?></td>
                                    <td>New player registration</td>
                                    <td>John Doe</td>
                                    <td><span class="badge bg-success">Success</span></td>
                                </tr>
                                <tr>
                                    <td><?= date('Y-m-d H:i:s', strtotime('-1 hour')) ?></td>
                                    <td>Tournament created</td>
                                    <td>Admin</td>
                                    <td><span class="badge bg-info">Completed</span></td>
                                </tr>
                                <tr>
                                    <td><?= date('Y-m-d H:i:s', strtotime('-2 hours')) ?></td>
                                    <td>Team assignment</td>
                                    <td>Jane Smith</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
