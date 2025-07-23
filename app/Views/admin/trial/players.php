
<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Trial Players<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card bg-dark text-light border-warning">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="card-title mb-0">
            <i class="fas fa-users text-warning"></i> Trial Players
          </h4>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-info btn-sm" onclick="exportToExcel()">
              <i class="fas fa-file-excel"></i> Export
            </button>
            <a href="<?= base_url('admin/trial-registration') ?>" class="btn btn-outline-warning btn-sm">
              <i class="fas fa-cog"></i> Manage Registrations
            </a>
          </div>
        </div>

        <!-- Filter Section -->
        <div class="card-body">
          <form method="GET" action="<?= base_url('admin/trial-players') ?>" class="mb-4">
            <div class="row">
              <div class="col-md-3 mb-3">
                <input type="text" name="search" class="form-control" placeholder="Search by name, mobile, email..." value="<?= esc($search) ?>">
              </div>
              <div class="col-md-3 mb-3">
                <select name="payment_status" class="form-select">
                  <option value="">All Payment Status</option>
                  <option value="no_payment" <?= $payment_status === 'no_payment' ? 'selected' : '' ?>>No Payment</option>
                  <option value="partial" <?= $payment_status === 'partial' ? 'selected' : '' ?>>Partial Payment</option>
                  <option value="full" <?= $payment_status === 'full' ? 'selected' : '' ?>>Full Payment</option>
                </select>
              </div>
              <div class="col-md-3 mb-3">
                <select name="trial_city" class="form-select">
                  <option value="">All Cities</option>
                  <?php foreach ($trial_cities as $city): ?>
                    <option value="<?= $city['id'] ?>" <?= $trial_city == $city['id'] ? 'selected' : '' ?>>
                      <?= esc($city['city_name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-3 mb-3">
                <button type="submit" class="btn btn-warning me-2">
                  <i class="fas fa-search"></i> Filter
                </button>
                <a href="<?= base_url('admin/trial-players') ?>" class="btn btn-secondary">
                  <i class="fas fa-refresh"></i> Reset
                </a>
              </div>
            </div>
          </form>

          <!-- Players Count -->
          <div class="row mb-3">
            <div class="col-12">
              <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                Total Players: <strong><?= $pager->getTotal() ?></strong>
              </div>
            </div>
          </div>

          <!-- Players Table -->
          <div class="table-responsive">
            <table class="table table-dark table-striped table-hover">
              <thead class="table-warning">
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Mobile</th>
                  <th>Email</th>
                  <th>Age</th>
                  <th>Cricket Type</th>
                  <th>Trial City</th>
                  <th>Payment Status</th>
                  <th>Registration Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($players)): ?>
                  <?php foreach ($players as $player): ?>
                    <tr>
                      <td><?= esc($player['id']) ?></td>
                      <td>
                        <strong><?= esc($player['name']) ?></strong>
                      </td>
                      <td>
                        <i class="fas fa-phone text-info"></i>
                        <?= esc($player['mobile']) ?>
                      </td>
                      <td>
                        <i class="fas fa-envelope text-info"></i>
                        <?= esc($player['email']) ?>
                      </td>
                      <td>
                        <span class="badge bg-secondary"><?= esc($player['age']) ?> years</span>
                      </td>
                      <td>
                        <span class="badge bg-primary"><?= ucfirst(str_replace('_', ' ', esc($player['cricket_type']))) ?></span>
                      </td>
                      <td>
                        <i class="fas fa-map-marker-alt text-warning"></i>
                        <?= esc($player['trial_city_name'] ?? 'N/A') ?>
                      </td>
                      <td>
                        <?php
                        $statusClass = match($player['payment_status']) {
                          'full' => 'bg-success',
                          'partial' => 'bg-warning text-dark',
                          'no_payment' => 'bg-danger',
                          default => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?= $statusClass ?>">
                          <?= ucfirst(str_replace('_', ' ', esc($player['payment_status']))) ?>
                        </span>
                      </td>
                      <td>
                        <small class="text-muted">
                          <?= date('d M Y, h:i A', strtotime($player['created_at'])) ?>
                        </small>
                      </td>
                      <td>
                        <div class="btn-group">
                          <button class="btn btn-sm btn-outline-info" onclick="viewPlayerDetails(<?= $player['id'] ?>)" title="View Details">
                            <i class="fas fa-eye"></i>
                          </button>
                          <a href="mailto:<?= esc($player['email']) ?>" class="btn btn-sm btn-outline-primary" title="Send Email">
                            <i class="fas fa-envelope"></i>
                          </a>
                          <a href="tel:<?= esc($player['mobile']) ?>" class="btn btn-sm btn-outline-success" title="Call">
                            <i class="fas fa-phone"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                      <i class="fas fa-users fa-3x mb-3"></i>
                      <br>No trial players found.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <?php if ($pager->getPageCount() > 1): ?>
            <div class="d-flex justify-content-center mt-4">
              <?= $pager->links() ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Player Details Modal -->
<div class="modal fade" id="playerDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content bg-dark text-light">
      <div class="modal-header border-warning">
        <h5 class="modal-title">
          <i class="fas fa-user text-warning"></i> Player Details
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="playerDetailsContent">
        <!-- Player details will be loaded here -->
      </div>
    </div>
  </div>
</div>

<script>
function viewPlayerDetails(playerId) {
  // You can implement this to show detailed player information
  notyf.info('Player details feature - to be implemented');
}

function exportToExcel() {
  notyf.info('Excel export feature - to be implemented');
}
</script>
<?= $this->endSection() ?>
