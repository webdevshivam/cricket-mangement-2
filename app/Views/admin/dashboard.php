<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>
<div class="row mb-4">
  <div class="col-12">
    <h2 class="text-warning">
      <i class="fas fa-tachometer-alt"></i> Dashboard Overview
      <button
        class="btn btn-sm btn-outline-warning ms-2"
        data-bs-toggle="tooltip"
        title="Dashboard shows real-time statistics of your cricket league">
        <i class="fas fa-info-circle"></i>
      </button>
    </h2>
  </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
  <div class="col-md-3 mb-3">
    <div class="card bg-dark border-warning stats-card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="card-title text-warning">Total Players</h6>
            <h3 class="text-white" id="total-players">0</h3>
          </div>
          <div class="stats-icon">
            <i class="fas fa-users text-warning"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-3 mb-3">
    <div class="card bg-dark border-warning stats-card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="card-title text-warning">Total Teams</h6>
            <h3 class="text-white" id="total-teams">0</h3>
          </div>
          <div class="stats-icon">
            <i class="fas fa-shield-alt text-warning"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-3 mb-3">
    <div class="card bg-dark border-warning stats-card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="card-title text-warning">Matches Played</h6>
            <h3 class="text-white" id="total-matches">0</h3>
          </div>
          <div class="stats-icon">
            <i class="fas fa-calendar-check text-warning"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-3 mb-3">
    <div class="card bg-dark border-warning stats-card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="card-title text-warning">
              Active Tournaments
            </h6>
            <h3 class="text-white" id="active-tournaments">0</h3>
          </div>
          <div class="stats-icon">
            <i class="fas fa-trophy text-warning"></i>
          </div>
        </div>
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

<!-- Recent Activities -->
<div class="row">
  <div class="col-md-6">
    <div class="card bg-dark border-warning">
      <div class="card-header bg-dark border-warning">
        <h5 class="text-warning mb-0">
          <i class="fas fa-clock"></i> Recent Activities
        </h5>
      </div>
      <div class="card-body">
        <div class="activity-feed" id="activity-feed">
          <!-- Activities will be loaded here -->
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card bg-dark border-warning">
      <div class="card-header bg-dark border-warning">
        <h5 class="text-warning mb-0">
          <i class="fas fa-calendar-alt"></i> Upcoming Matches
        </h5>
      </div>
      <div class="card-body">
        <div class="upcoming-matches" id="upcoming-matches">
          <!-- Upcoming matches will be loaded here -->
        </div>
      </div>
    </div>
  </div>
</div>


<?= $this->endSection(); ?>
