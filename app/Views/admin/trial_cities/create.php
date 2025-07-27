<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>

<div class="card bg-dark border-warning stats-card">
  <div class="card-body">
    <div class="d-flex justify-content-between">

      <a href="<?= site_url('admin/manage-trial-cities'); ?>" class="btn btn-warning">
        <i class="fas fa-list"></i>
        <span class="d-none d-md-inline">All Trial City</span>
      </a>
    </div>
  </div>
</div>

<div class="card bg-dark border-warning my-5">
  <div class="card-body">
    <h5 class="text-warning mb-3"><i class="fas fa-plus-circle"></i> Add Trial City</h5>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <form action="<?= site_url('admin/manage-trial-cities/save') ?>" class="" method="post">
      <?= csrf_field() ?>

      <div class="row g-3">
        <div class="col-md-6">
          <label for="city_name" class="form-label text-white">City Name</label>
          <select class="form-select" name="city_name" id="city_name" required>
            <option value="">Select City</option>
          </select>
        </div>

        <div class="col-md-6">
          <label for="state" class="form-label text-white">State</label>
          <select class="form-select" name="state" id="state" required>
            <option value="">Select State</option>
          </select>
        </div>

        <div class="col-md-6">
          <label for="trial_date" class="form-label text-white">Trial Date</label>
          <input type="date" class="form-control" name="trial_date" id="trial_date" required>
        </div>

        <div class="col-md-6">
          <label for="trial_venue" class="form-label text-white">Trial Venue</label>
          <input type="text" class="form-control" name="trial_venue" required>
        </div>

        <!-- AI Weather Analysis Section -->
        <div class="col-md-12">
          <div class="card bg-secondary border-info mt-3" id="weather-analysis-card" style="display: none;">
            <div class="card-header bg-info text-dark">
              <h6 class="mb-0"><i class="fas fa-cloud-sun"></i> AI Weather Analysis & Recommendations</h6>
            </div>
            <div class="card-body">
              <div id="weather-loading" style="display: none;">
                <div class="text-center">
                  <div class="spinner-border text-info" role="status">
                    <span class="visually-hidden">Analyzing weather...</span>
                  </div>
                  <p class="mt-2 text-info">Analyzing weather conditions...</p>
                </div>
              </div>
              <div id="weather-results" style="display: none;">
                <div class="row">
                  <div class="col-md-6">
                    <h6 class="text-warning">Weather Forecast</h6>
                    <div id="weather-summary"></div>
                  </div>
                  <div class="col-md-6">
                    <h6 class="text-warning">AI Risk Assessment</h6>
                    <div id="risk-assessment"></div>
                  </div>
                </div>
                <div class="mt-3">
                  <h6 class="text-warning">AI Recommendations</h6>
                  <div id="ai-recommendations"></div>
                </div>
                <div class="mt-3">
                  <div id="overall-advice"></div>
                </div>
              </div>
            </div>
          </div>

          <button type="button" class="btn btn-info mt-2" id="analyze-weather-btn">
            <i class="fas fa-robot"></i> Get AI Weather Analysis
          </button>
        </div>

        <div class="col-md-6">
          <label for="map_link" class="form-label text-white">Map Link (optional)</label>
          <input type="url" class="form-control" name="map_link">
        </div>

        <div class="col-md-6">
          <label for="status" class="form-label text-white">Status</label>
          <select class="form-select" name="status">
            <option value="enabled" selected>Enabled</option>
            <option value="disabled">Disabled</option>
          </select>
        </div>

        <div class="col-md-12">
          <label for="notes" class="form-label text-white">Notes (optional)</label>
          <textarea class="form-control" name="notes" rows="2"></textarea>
        </div>

        <div class="col-md-12 text-end">
          <button type="submit" class="btn btn-warning">
            <i class="fas fa-save me-1"></i> Save
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="<?= base_url('assets/js/weather_analysis.js') ?>"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize location loader
    if (window.locationLoader) {
      console.log('Location loader initialized successfully');
    } else {
      console.error('Location loader not found, initializing fallback');
      // Fallback initialization
      window.locationLoader = new LocationLoader();
    }
  });
</script>
<?= $this->endSection(); ?>