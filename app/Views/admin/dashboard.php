<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<!-- Header Section -->
<div class="row mb-4">
  <div class="col-md-8">
    <h2 class="text-warning">
      <i class="fas fa-tachometer-alt"></i> Cricket League Dashboard
      <button
        class="btn btn-sm btn-outline-warning ms-2"
        data-bs-toggle="tooltip"
        title="Dashboard shows real-time statistics of your cricket league">
        <i class="fas fa-info-circle"></i>
      </button>
    </h2>
    <p class="text-muted">Welcome back! Here's what's happening in your cricket league today.</p>
  </div>
  <div class="col-md-4 text-end">
    <div class="btn-group" role="group">
      <button class="btn btn-outline-warning btn-sm" onclick="refreshDashboard()">
        <i class="fas fa-sync-alt"></i> Refresh
      </button>
      <button class="btn btn-outline-warning btn-sm" onclick="exportDashboardData('pdf')">
        <i class="fas fa-download"></i> Export
      </button>
    </div>
  </div>
</div>

<!-- Quick Actions Bar -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card bg-dark border-warning">
      <div class="card-body">
        <h6 class="text-warning mb-3"><i class="fas fa-bolt"></i> Quick Actions</h6>
        <div class="row">
          <div class="col-md-2 col-sm-4 col-6 mb-2">
            <a href="<?= base_url('admin/trial/registration') ?>" class="btn btn-outline-info btn-sm w-100">
              <i class="fas fa-user-plus"></i><br>Trial Registration
            </a>
          </div>
          <div class="col-md-2 col-sm-4 col-6 mb-2">
            <a href="<?= base_url('admin/league/registration') ?>" class="btn btn-outline-success btn-sm w-100">
              <i class="fas fa-trophy"></i><br>League Registration
            </a>
          </div>
          <div class="col-md-2 col-sm-4 col-6 mb-2">
            <a href="<?= base_url('admin/trial/payment-tracking') ?>" class="btn btn-outline-warning btn-sm w-100">
              <i class="fas fa-credit-card"></i><br>Payment Tracking
            </a>
          </div>
          <div class="col-md-2 col-sm-4 col-6 mb-2">
            <a href="<?= base_url('admin/grades') ?>" class="btn btn-outline-primary btn-sm w-100">
              <i class="fas fa-graduation-cap"></i><br>Grades
            </a>
          </div>
          <div class="col-md-2 col-sm-4 col-6 mb-2">
            <a href="<?= base_url('admin/players') ?>" class="btn btn-outline-secondary btn-sm w-100">
              <i class="fas fa-users"></i><br>Players
            </a>
          </div>
          <div class="col-md-2 col-sm-4 col-6 mb-2">
            <a href="<?= base_url('admin/qr-code-setting') ?>" class="btn btn-outline-light btn-sm w-100">
              <i class="fas fa-qrcode"></i><br>QR Settings
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Primary Stats Cards -->
<div class="row mb-4">
  <div class="col-md-3 mb-3">
    <div class="card bg-dark border-warning stats-card clickable-card" onclick="window.location='<?= base_url('admin/players') ?>'">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="card-title text-warning">Total Players</h6>
            <h3 class="text-white" id="total-players"><?= $totalPlayers ?? 0 ?></h3>
            <small class="text-success">
              <i class="fas fa-arrow-up"></i> +<?= $newPlayersThisWeek ?? 0 ?> this week
            </small>
          </div>
          <div class="stats-icon">
            <i class="fas fa-users text-warning"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-3 mb-3">
    <div class="card bg-dark border-success stats-card clickable-card" onclick="window.location='<?= base_url('admin/trial/registration') ?>'">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="card-title text-success">Trial Students</h6>
            <h3 class="text-white" id="trial-students"><?= $trialStudents ?? 0 ?></h3>
            <small class="text-info">
              <i class="fas fa-clock"></i> <?= $pendingTrials ?? 0 ?> pending
            </small>
          </div>
          <div class="stats-icon">
            <i class="fas fa-user-clock text-success"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-3 mb-3">
    <div class="card bg-dark border-info stats-card clickable-card" onclick="window.location='<?= base_url('admin/league/registration') ?>'">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="card-title text-info">League Players</h6>
            <h3 class="text-white" id="league-players"><?= $leaguePlayers ?? 0 ?></h3>
            <small class="text-warning">
              <i class="fas fa-trophy"></i> Active in league
            </small>
          </div>
          <div class="stats-icon">
            <i class="fas fa-medal text-info"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-3 mb-3">
    <div class="card bg-dark border-danger stats-card clickable-card" onclick="window.location='<?= base_url('admin/trial/payment-tracking') ?>'">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="card-title text-danger">Revenue</h6>
            <h3 class="text-white" id="total-revenue">₹<?= number_format($totalRevenue ?? 0) ?></h3>
            <small class="text-success">
              <i class="fas fa-rupee-sign"></i> ₹<?= number_format($todayRevenue ?? 0) ?> today
            </small>
          </div>
          <div class="stats-icon">
            <i class="fas fa-chart-line text-danger"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Secondary Stats Cards -->
<div class="row mb-4">
  <div class="col-md-2 col-sm-4 col-6 mb-3">
    <div class="card bg-dark border-secondary stats-card">
      <div class="card-body text-center">
        <i class="fas fa-city text-secondary fa-2x mb-2"></i>
        <h6 class="text-secondary">Cities</h6>
        <h4 class="text-white"><?= $totalCities ?? 0 ?></h4>
      </div>
    </div>
  </div>
  <div class="col-md-2 col-sm-4 col-6 mb-3">
    <div class="card bg-dark border-primary stats-card">
      <div class="card-body text-center">
        <i class="fas fa-graduation-cap text-primary fa-2x mb-2"></i>
        <h6 class="text-primary">Grades</h6>
        <h4 class="text-white"><?= $totalGrades ?? 0 ?></h4>
      </div>
    </div>
  </div>
  <div class="col-md-2 col-sm-4 col-6 mb-3">
    <div class="card bg-dark border-warning stats-card">
      <div class="card-body text-center">
        <i class="fas fa-check-circle text-warning fa-2x mb-2"></i>
        <h6 class="text-warning">Verified</h6>
        <h4 class="text-white"><?= $verifiedPlayers ?? 0 ?></h4>
      </div>
    </div>
  </div>
  <div class="col-md-2 col-sm-4 col-6 mb-3">
    <div class="card bg-dark border-info stats-card">
      <div class="card-body text-center">
        <i class="fas fa-hourglass-half text-info fa-2x mb-2"></i>
        <h6 class="text-info">Pending</h6>
        <h4 class="text-white"><?= $pendingVerifications ?? 0 ?></h4>
      </div>
    </div>
  </div>
  <div class="col-md-2 col-sm-4 col-6 mb-3">
    <div class="card bg-dark border-success stats-card">
      <div class="card-body text-center">
        <i class="fas fa-money-bill-wave text-success fa-2x mb-2"></i>
        <h6 class="text-success">Paid</h6>
        <h4 class="text-white"><?= $paidStudents ?? 0 ?></h4>
      </div>
    </div>
  </div>
  <div class="col-md-2 col-sm-4 col-6 mb-3">
    <div class="card bg-dark border-danger stats-card">
      <div class="card-body text-center">
        <i class="fas fa-exclamation-triangle text-danger fa-2x mb-2"></i>
        <h6 class="text-danger">Unpaid</h6>
        <h4 class="text-white"><?= $unpaidStudents ?? 0 ?></h4>
      </div>
    </div>
  </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
  <div class="col-md-8">
    <div class="card bg-dark border-warning">
      <div class="card-header bg-dark border-warning">
        <h5 class="text-warning mb-0">
          <i class="fas fa-chart-line"></i> Match Statistics
        </h5>
      </div>
      <div class="card-body">
        <canvas id="matchChart" height="100"></canvas>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card bg-dark border-warning">
      <div class="card-header bg-dark border-warning">
        <h5 class="text-warning mb-0">
          <i class="fas fa-chart-pie"></i> Team Performance
        </h5>
      </div>
      <div class="card-body">
        <canvas id="teamChart" height="200"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Management Sections -->
<div class="row mb-4">
  <div class="col-md-4">
    <div class="card bg-dark border-warning">
      <div class="card-header bg-dark border-warning d-flex justify-content-between">
        <h5 class="text-warning mb-0">
          <i class="fas fa-clock"></i> Recent Activities
        </h5>
        <small class="text-muted">Last 24 hours</small>
      </div>
      <div class="card-body" style="max-height: 300px; overflow-y: auto;">
        <div class="activity-feed" id="activity-feed">
          <?php if (isset($recentActivities) && !empty($recentActivities)): ?>
            <?php foreach ($recentActivities as $activity): ?>
              <div class="activity-item d-flex align-items-center mb-3">
                <div class="activity-icon me-3">
                  <i class="<?= $activity['icon'] ?? 'fas fa-circle' ?> text-warning"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="text-light"><?= $activity['description'] ?></div>
                  <small class="text-muted"><?= date('M d, Y H:i', strtotime($activity['created_at'])) ?></small>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="text-center text-muted py-3">
              <i class="fas fa-clock fa-2x mb-2"></i>
              <p>No recent activities</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card bg-dark border-success">
      <div class="card-header bg-dark border-success d-flex justify-content-between">
        <h5 class="text-success mb-0">
          <i class="fas fa-tasks"></i> Pending Tasks
        </h5>
        <span class="badge bg-danger"><?= count($pendingTasks ?? []) ?></span>
      </div>
      <div class="card-body" style="max-height: 300px; overflow-y: auto;">
        <div class="pending-tasks">
          <?php if (isset($pendingTasks) && !empty($pendingTasks)): ?>
            <?php foreach ($pendingTasks as $task): ?>
              <div class="task-item border-bottom border-secondary pb-2 mb-2">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <h6 class="text-light mb-1"><?= $task['title'] ?></h6>
                    <small class="text-muted"><?= $task['description'] ?></small>
                  </div>
                  <span class="badge bg-<?= $task['priority'] == 'high' ? 'danger' : ($task['priority'] == 'medium' ? 'warning' : 'secondary') ?>">
                    <?= ucfirst($task['priority']) ?>
                  </span>
                </div>
                <div class="mt-2">
                  <a href="<?= $task['action_url'] ?>" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-arrow-right"></i> Take Action
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="text-center text-muted py-3">
              <i class="fas fa-check-circle fa-2x mb-2"></i>
              <p>All tasks completed!</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card bg-dark border-info">
      <div class="card-header bg-dark border-info">
        <h5 class="text-info mb-0">
          <i class="fas fa-chart-pie"></i> Quick Stats
        </h5>
      </div>
      <div class="card-body">
        <div class="quick-stats">
          <div class="stat-item d-flex justify-content-between mb-3">
            <span class="text-muted">Payment Success Rate</span>
            <span class="text-success"><?= number_format(($paidStudents ?? 0) / max(($totalPlayers ?? 1), 1) * 100, 1) ?>%</span>
          </div>
          <div class="stat-item d-flex justify-content-between mb-3">
            <span class="text-muted">Trial Conversion</span>
            <span class="text-warning"><?= number_format(($leaguePlayers ?? 0) / max(($trialStudents ?? 1), 1) * 100, 1) ?>%</span>
          </div>
          <div class="stat-item d-flex justify-content-between mb-3">
            <span class="text-muted">Verification Pending</span>
            <span class="text-danger"><?= $pendingVerifications ?? 0 ?></span>
          </div>
          <div class="stat-item d-flex justify-content-between mb-3">
            <span class="text-muted">Average Revenue/Day</span>
            <span class="text-info">₹<?= number_format(($totalRevenue ?? 0) / 30) ?></span>
          </div>
          <hr class="border-secondary">
          <div class="text-center">
            <a href="<?= base_url('admin/reports') ?>" class="btn btn-outline-info btn-sm">
              <i class="fas fa-chart-bar"></i> View Detailed Reports
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- System Health & Quick Links -->
<div class="row">
  <div class="col-md-8">
    <div class="card bg-dark border-primary">
      <div class="card-header bg-dark border-primary">
        <h5 class="text-primary mb-0">
          <i class="fas fa-link"></i> Quick Management Links
        </h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <h6 class="text-warning mb-3">Registration Management</h6>
            <ul class="list-unstyled">
              <li><a href="<?= base_url('admin/trial-registration') ?>" class="text-light text-decoration-none"><i class="fas fa-user-plus text-success"></i> Manage Trial Registrations</a></li>
              <li><a href="<?= base_url('admin/trial-verification') ?>" class="text-light text-decoration-none"><i class="fas fa-user-check text-warning"></i> Verify Trial Students</a></li>
              <li><a href="<?= base_url('admin/league-registration') ?>" class="text-light text-decoration-none"><i class="fas fa-trophy text-info"></i> League Registrations</a></li>
              <li><a href="<?= base_url('admin/grades/assign') ?>" class="text-light text-decoration-none"><i class="fas fa-graduation-cap text-primary"></i> Assign Grades</a></li>
            </ul>
          </div>
          <div class="col-md-6">
            <h6 class="text-warning mb-3">Financial Management</h6>
            <ul class="list-unstyled">
              <li><a href="<?= base_url('admin/trial/payment-tracking') ?>" class="text-light text-decoration-none"><i class="fas fa-credit-card text-success"></i> Payment Tracking</a></li>
              <li><a href="<?= base_url('admin/payments/collections') ?>" class="text-light text-decoration-none"><i class="fas fa-money-bill-wave text-warning"></i> Collections Report</a></li>
              <li><a href="<?= base_url('admin/payments/pending') ?>" class="text-light text-decoration-none"><i class="fas fa-exclamation-triangle text-danger"></i> Pending Payments</a></li>
              <li><a href="<?= base_url('admin/financial/reports') ?>" class="text-light text-decoration-none"><i class="fas fa-chart-bar text-info"></i> Financial Reports</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card bg-dark border-secondary">
      <div class="card-header bg-dark border-secondary">
        <h5 class="text-secondary mb-0">
          <i class="fas fa-heart"></i> System Health
        </h5>
      </div>
      <div class="card-body">
        <div class="system-health">
          <div class="health-item d-flex justify-content-between mb-2">
            <span class="text-muted">Database</span>
            <span class="badge bg-success">Online</span>
          </div>
          <div class="health-item d-flex justify-content-between mb-2">
            <span class="text-muted">Payment Gateway</span>
            <span class="badge bg-success">Active</span>
          </div>
          <div class="health-item d-flex justify-content-between mb-2">
            <span class="text-muted">Email Service</span>
            <span class="badge bg-warning">Limited</span>
          </div>
          <div class="health-item d-flex justify-content-between mb-2">
            <span class="text-muted">Storage</span>
            <span class="badge bg-success">85% Free</span>
          </div>
          <hr class="border-secondary">
          <div class="text-center">
            <small class="text-muted">Last updated: <?= date('M d, Y H:i') ?></small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="<?= base_url('assets/js/dashboard.js') ?>"></script>
<script>
$(document).ready(function() {
    // Initialize dashboard
    initializeRealDashboard();
    
    // Auto-refresh every 5 minutes
    setInterval(function() {
        loadRealDashboardStats();
    }, 300000);
});
</script>
<?= $this->endSection(); ?>
