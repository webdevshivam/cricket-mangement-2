<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>
<div class="card bg-dark border-warning stats-card my-4">
  <div class="card-body">
    <div class="d-flex justify-content-between">

      <a href="<?= site_url('admin/manage-trial-cities'); ?>" class="btn btn-warning">
        <i class="fas fa-list"></i>
        <span class="d-none d-md-inline">All Trial City</span>
      </a>
    </div>
  </div>
</div>
<div class="card bg-dark border-warning">
  <div class="card-body">
    <h5 class="text-warning mb-3"><i class="fas fa-edit"></i> Edit Trial City</h5>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <form action="<?= site_url('admin/manage-trial-cities/update/' . $city['id']) ?>" method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= $city['id'] ?>">

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label text-white">City Name</label>
          <input type="text" class="form-control" name="city_name" value="<?= esc($city['city_name']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label text-white">State</label>
          <input type="text" class="form-control" name="state" value="<?= esc($city['state']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label text-white">Trial Date</label>
          <input type="date" class="form-control" name="trial_date" value="<?= esc($city['trial_date']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label text-white">Trial Venue</label>
          <input type="text" class="form-control" name="trial_venue" value="<?= esc($city['trial_venue']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label text-white">Map Link</label>
          <input type="url" class="form-control" name="map_link" value="<?= esc($city['map_link']) ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label text-white">Status</label>
          <select class="form-select" name="status">
            <option value="enabled" <?= $city['status'] == 'enabled' ? 'selected' : '' ?>>Enabled</option>
            <option value="disabled" <?= $city['status'] == 'disabled' ? 'selected' : '' ?>>Disabled</option>
          </select>


        </div>
        <div class="col-12">
          <label class="form-label text-white">Notes</label>
          <textarea class="form-control" name="notes" rows="2"><?= esc($city['notes']) ?></textarea>
        </div>
        <div class="col-12 text-end">
          <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i> Update</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection(); ?>
