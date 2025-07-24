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

<!-- Upcoming Trial Dates Calendar -->
<div class="card bg-dark border-warning mb-4">
  <div class="card-body">
    <h5 class="card-title text-warning mb-3">
      <i class="fas fa-calendar-alt"></i> Upcoming Trial Dates
    </h5>
    <div class="row">
      <?php if (!empty($cities)): ?>
        <?php 
        $upcomingTrials = array_filter($cities, function($city) {
          return strtotime($city['trial_date']) >= strtotime('today') && $city['status'] === 'enabled';
        });
        usort($upcomingTrials, function($a, $b) {
          return strtotime($a['trial_date']) - strtotime($b['trial_date']);
        });
        ?>
        <?php if (!empty($upcomingTrials)): ?>
          <?php foreach (array_slice($upcomingTrials, 0, 6) as $trial): ?>
            <?php 
            $daysUntil = floor((strtotime($trial['trial_date']) - strtotime('today')) / (60 * 60 * 24));
            $urgencyClass = $daysUntil <= 3 ? 'border-danger' : ($daysUntil <= 7 ? 'border-warning' : 'border-success');
            ?>
            <div class="col-md-4 col-lg-2 mb-3">
              <div class="card bg-secondary <?= $urgencyClass ?> h-100">
                <div class="card-body text-center p-2">
                  <div class="mb-2">
                    <i class="fas fa-map-marker-alt text-warning"></i>
                  </div>
                  <h6 class="card-title text-white mb-1" style="font-size: 0.9rem;">
                    <?= esc($trial['city_name']) ?>
                  </h6>
                  <p class="card-text text-light mb-1" style="font-size: 0.8rem;">
                    <?= date('M d, Y', strtotime($trial['trial_date'])) ?>
                  </p>
                  <small class="text-<?= $daysUntil <= 3 ? 'danger' : ($daysUntil <= 7 ? 'warning' : 'success') ?>">
                    <?= $daysUntil == 0 ? 'Today' : ($daysUntil == 1 ? 'Tomorrow' : $daysUntil . ' days') ?>
                  </small>
                  <?php if (!empty($trial['trial_venue'])): ?>
                    <div class="mt-1">
                      <small class="text-muted" style="font-size: 0.7rem;">
                        <i class="fas fa-building"></i> <?= esc(substr($trial['trial_venue'], 0, 20)) ?>...
                      </small>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12">
            <div class="alert alert-info">
              <i class="fas fa-info-circle"></i> No upcoming trial dates scheduled.
            </div>
          </div>
        <?php endif; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> No trial cities found. Please add some trial cities first.
          </div>
        </div>
      <?php endif; ?>
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
        <?php if (!empty($paginatedCities)) : ?>
          <?php
          $page = $pager->getCurrentPage() ?? 1;
          $perPage = $pager->getPerPage() ?? 10;
          $i = 1 + ($page - 1) * $perPage;
          ?>
          <?php foreach ($paginatedCities as $city) : ?>
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
