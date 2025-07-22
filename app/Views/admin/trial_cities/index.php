<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark border-warning stats-card mb-3">
  <div class="card-body">
    <div class="d-flex justify-content-between">
      <h5 class="card-title text-warning">Trial City Management</h5>
      <a href="<?= site_url('admin/manage-trial-cities/add'); ?>" class="btn btn-warning">
        <i class="fas fa-plus"></i> <span class="d-none d-md-inline">Add Trial City</span>
      </a>
    </div>
  </div>
</div>

<div class="card bg-dark border-warning">
  <div class="card-body table-responsive">
    <table class="table table-bordered table-striped table-dark align-middle text-white">
      <thead class="table-warning text-dark">
        <tr>
          <th>#</th>
          <th>City</th>
          <th>State</th>
          <th>Trial Date</th>
          <th>Venue</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($cities)) : ?>
          <?php
          $page = $pager->getCurrentPage() ?? 1;
          $perPage = $pager->getPerPage() ?? 10;
          $i = 1 + ($page - 1) * $perPage;
          ?>
          <?php foreach ($cities as $city) : ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= esc($city['city_name']) ?></td>
              <td><?= esc($city['state']) ?></td>
              <td><?= esc($city['trial_date']) ?></td>
              <td><?= esc($city['trial_venue']) ?></td>
              <td>
                <span class="badge bg-<?= $city['status'] === 'enabled' ? 'success' : 'secondary' ?>">
                  <?= ucfirst($city['status']) ?>
                </span>
              </td>
              <td>
                <div class="btn-group btn-group-sm" role="group">
                  <a href="<?= site_url('admin/manage-trial-cities/edit/' . $city['id']) ?>"
                    class="btn btn-warning" data-bs-toggle="tooltip" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  <a href="<?= site_url('admin/manage-trial-cities/delete/' . $city['id']) ?>"
                    class="btn btn-danger" onclick="return confirm('Delete this city?')"
                    data-bs-toggle="tooltip" title="Delete">
                    <i class="fas fa-trash-alt"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="7" class="text-center">No trial cities found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
      <?= $pager->links() ?>
    </div>
  </div>
</div>

<!-- Tooltip Activation -->
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(el => new bootstrap.Tooltip(el));
  });
</script>

<?= $this->endSection(); ?>
