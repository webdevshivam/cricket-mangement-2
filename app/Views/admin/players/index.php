<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>

<div class="card bg-dark border-warning stats-card">
  <div class="card-body">
    <div class="d-flex justify-content-between">
      <h5 class="card-title text-warning">Players Management</h5>
      <a href="<?= site_url('admin/players/add'); ?>" class="btn btn-warning">
        <i class="fas fa-plus"></i>
        <span class="d-none d-md-inline">Add Player</span>
      </a>
    </div>
  </div>
</div>

<form method="get" class="row g-2 mb-3 my-4">
  <div class="col-md-3">
    <select name="city" class="form-select">
      <option value="">All Cities</option>
      <option value="Jaipur" <?= ($city == 'Jaipur') ? 'selected' : '' ?>>Jaipur</option>
      <option value="Delhi" <?= ($city == 'Delhi') ? 'selected' : '' ?>>Delhi</option>
      <!-- Add more cities -->
    </select>
  </div>

  <div class="col-md-3">
    <select name="cricketer_type" class="form-select">
      <option value="">All Roles</option>
      <option value="batsman" <?= ($cricketer_type == 'batsman') ? 'selected' : '' ?>>Batsman</option>
      <option value="bowler" <?= ($cricketer_type == 'bowler') ? 'selected' : '' ?>>Bowler</option>
      <option value="all-rounder" <?= ($cricketer_type == 'all-rounder') ? 'selected' : '' ?>>All-Rounder</option>
    </select>
  </div>

  <div class="col-md-3">
    <select name="payment_status" class="form-select">
      <option value="">All Payment Status</option>
      <option value="pending" <?= ($payment_status == 'pending') ? 'selected' : '' ?>>Pending</option>
      <option value="partial" <?= ($payment_status == 'partial') ? 'selected' : '' ?>>Partial</option>
      <option value="full" <?= ($payment_status == 'full') ? 'selected' : '' ?>>Paid</option>
    </select>
  </div>

  <div class="col-md-3">
    <input type="date" name="date" class="form-control" value="<?= esc($date ?? '') ?>">
  </div>

  <div class="col-md-12 text-end">
    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="<?= base_url('admin/players') ?>" class="btn btn-secondary">Reset</a>
  </div>
</form>

<div class="container mt-4">
  <h4 class="mb-3">Player List</h4>

  <form action="<?= base_url('admin/players/delete-multiple') ?>" method="post" id="bulkDeleteForm">
    <div class="table-responsive">
      <table class="table table-dark table-striped">
        <thead class="table-dark">
          <tr>
            <th><input type="checkbox" class="form-check-input" id="selectAll"></th> <!-- Select all checkbox -->
            <th>#</th>
            <th>Name</th>
            <th>Mobile</th>
            <th>City</th>
            <th>Cricketer Type</th>
            <th>Trial City</th>
            <th>Payment Status</th>
            <th>Actions</th> <!-- NEW COLUMN -->
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($players)) : ?>
            <?php
            // Safe check in case pager is null (fallback to 1)
            $currentPage = isset($pager) ? $pager->getCurrentPage() : 1;
            $perPage = isset($pager) ? $pager->getPerPage() : 10;
            $i = 1 + ($currentPage - 1) * $perPage;
            ?>
            <?php foreach ($players as $player) : ?>
              <tr>
                <td><input type="checkbox" class="player-checkbox form-check-input" name="selected[]" value="<?= $player['id'] ?>"></td>
                <td><?= $i++ ?></td>
                <td><?= esc($player['name']) ?></td>
                <td><?= esc($player['mobile_number']) ?></td>
                <td><?= esc($player['city']) ?></td>
                <td><?= esc($player['cricketer_type']) ?></td>
                <td>






                  <?php
                  $trialCityModel = new \App\Models\TrialcitiesModel();
                  $trialCity = $trialCityModel->find($player['trial_city']);
                  if ($trialCity) {
                    echo esc($trialCity['city_name']);
                  } else {
                    echo 'N/A';
                  }
                  ?>
                </td>
                <td>
                  <select class="form-select form-select-sm payment-status"
                    data-id="<?= $player['id'] ?>">
                    <option value="pending" <?= $player['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="partial" <?= $player['payment_status'] === 'partial' ? 'selected' : '' ?>>Partial</option>
                    <option value="full" <?= $player['payment_status'] === 'full' ? 'selected' : '' ?>>Full</option>
                  </select>
                </td>
                <td>
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="<?= base_url('admin/players/view/' . $player['id']) ?>"
                      class="btn btn-info"
                      data-bs-toggle="tooltip"
                      title="View Profile">
                      <i class="fas fa-eye"></i>
                    </a>

                    <a href="<?= base_url('admin/players/edit/' . $player['id']) ?>"
                      class="btn btn-warning"
                      data-bs-toggle="tooltip"
                      title="Edit Info">
                      <i class="fas fa-edit"></i>
                    </a>

                    <a href="<?= base_url('admin/grades/assign/' . $player['id']) ?>"
                      class="btn btn-primary"
                      data-bs-toggle="tooltip"
                      title="Assign Grade">
                      <i class="fas fa-graduation-cap"></i>
                    </a>

                    <a href="<?= base_url('admin/players/send-receipt/' . $player['id']) ?>"
                      class="btn btn-success"
                      data-bs-toggle="tooltip"
                      title="Send Receipt">
                      <i class="fas fa-file-invoice"></i>
                    </a>

                    <a href="<?= base_url('admin/players/delete/' . $player['id']) ?>"
                      class="btn btn-danger"
                      data-bs-toggle="tooltip"
                      title="Delete Player"
                      onclick="return confirm('Are you sure you want to delete this player?')">
                      <i class="fas fa-trash-alt"></i>
                    </a>
                  </div>
                </td>


              </tr>
            <?php endforeach ?>
          <?php else : ?>
            <tr>
              <td colspan="10" class="text-center">No players found.</td>
            </tr>
          <?php endif ?>
        </tbody>

      </table>
    </div>

    <div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mt-3">

      <!-- Delete Button -->
      <button type="submit" class="btn btn-danger"
        onclick="return confirm('Are you sure you want to delete selected players?')">
        <i class="fas fa-trash-alt"></i> Delete Selected
      </button>

      <!-- Grade Select Dropdown -->
      <select name="grade_id" class="form-select bg-dark text-warning border-warning" style="width: 200px;" required>
        <option value="">-- Select Grade --</option>
        <?php foreach ($grades as $grade): ?>
          <option value="<?= $grade['id'] ?>">
            <?= esc($grade['title']) ?> (₹<?= esc($grade['league_fee']) ?>)
          </option>
        <?php endforeach; ?>
      </select>

      <!-- Assign Grade Button -->
      <button type="submit" formaction="<?= site_url('admin/players/assign-grade') ?>"
        class="btn btn-warning"
        onclick="return confirm('Assign selected grade to selected players?')">
        <i class="fas fa-user-check"></i> Assign Selected Grade
      </button>

    </div>

  </form>

  <!-- Pagination -->
  <div class="d-flex justify-content-center">
    <div class="d-flex justify-content-center mt-4">
      <?= $pager->links() ?>
    </div>


  </div>
</div>


<script>
  document.addEventListener("DOMContentLoaded", function() {
    const selects = document.querySelectorAll(".payment-status");

    selects.forEach(select => {
      select.addEventListener("change", function() {
        const playerId = this.getAttribute("data-id");
        const newStatus = this.value;

        fetch("<?= base_url('admin/players/update-payment-status') ?>", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify({
              id: playerId,
              payment_status: newStatus
            }),
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {

              notyf.success("Payment status updated successfully!");
              conspole.log("Payment status updated for player ID:", playerId);
            } else {
              notyf.error("Failed to update payment status.");
              console.error("Error updating payment status for player ID:", playerId, data.error);
            }
          });
      });
    });
  });

  document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("selectAll").addEventListener("change", function() {
      const checkboxes = document.querySelectorAll(".player-checkbox");
      checkboxes.forEach(cb => cb.checked = this.checked);
    });
  });
</script>

<!-- Add your player listing logic here -->

<?= $this->endSection(); ?>
