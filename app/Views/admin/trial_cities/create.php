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
          <input type="text" class="form-control" name="city_name" required>
        </div>

        <div class="col-md-6">
          <label for="state" class="form-label text-white">State</label>
          <input type="text" class="form-control" name="state" required>
        </div>

        <div class="col-md-6">
          <label for="trial_date" class="form-label text-white">Trial Date</label>
          <input type="date" class="form-control" name="trial_date" required>
        </div>

        <div class="col-md-6">
          <label for="trial_venue" class="form-label text-white">Trial Venue</label>
          <input type="text" class="form-control" name="trial_venue" required>
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
<?= $this->endSection(); ?>
