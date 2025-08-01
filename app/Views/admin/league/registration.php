<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark border-warning stats-card mb-4">
  <div class="card-body d-flex justify-content-between align-items-center">
    <h5 class="card-title text-warning mb-0">League Player Registration</h5>
    <div>
      <span class="badge bg-info me-2">Total: <?= count($registrations) ?></span>
      <button class="btn btn-sm btn-outline-success" onclick="exportToPDF('league')">
        <i class="fas fa-file-pdf"></i> Export PDF
      </button>
    </div>
  </div>
</div>

<div class="container mt-3">
  <!-- Search and Filter Section -->
  <div class="card bg-dark border-warning mb-4">
    <div class="card-body">
      <form method="GET" action="<?= base_url('admin/league-registration') ?>">
        <div class="row">
          <div class="col-md-2">
            <input type="text" class="form-control bg-dark text-white" name="phone"
              placeholder="Search by phone" value="<?= esc($phone ?? '') ?>">
          </div>
          <div class="col-md-2">
            <select class="form-select bg-dark text-white" name="payment_status">
              <option value="">All Payment Status</option>
              <option value="unpaid" <?= (isset($payment_status) && $payment_status == 'unpaid') ? 'selected' : '' ?>>Unpaid</option>
              <option value="paid" <?= (isset($payment_status) && $payment_status == 'paid') ? 'selected' : '' ?>>Paid</option>
            </select>
          </div>
          <div class="col-md-2">
            <select class="form-select bg-dark text-white" name="age_group">
              <option value="">All Age Groups</option>
              <option value="under_16" <?= (isset($age_group) && $age_group == 'under_16') ? 'selected' : '' ?>>Under 16</option>
              <option value="above_16" <?= (isset($age_group) && $age_group == 'above_16') ? 'selected' : '' ?>>Above 16</option>
            </select>
          </div>
          <div class="col-md-4">
            <button type="submit" class="btn btn-warning me-2">
              <i class="fas fa-search"></i> Filter
            </button>
            <a href="<?= base_url('admin/league-registration') ?>" class="btn btn-secondary">
              <i class="fas fa-refresh"></i> Reset
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Registrations Table -->
  <div class="card bg-dark border-warning">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-dark table-striped table-bordered" id="registrationsTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Mobile</th>
              <th>Email</th>
              <th>Age</th>
              <th>Cricketer Type</th>
              <th>Age Group</th>
              <th>Trial City</th>
              <th>Payment Status</th>
              <th>Assigned Grade</th>
              <th>Documents</th>
              <th>Actions</th>
              <th>Registered On</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($registrations)) : ?>
              <?php
              $currentPage = $pager->getCurrentPage() ?? 1;
              $perPage = $pager->getPerPage() ?? 10;
              $i = 1 + ($currentPage - 1) * $perPage;
              ?>
              <?php foreach ($registrations as $reg) : ?>
                <tr>
                  <td><?= $i++ ?></td>
                  <td><strong><?= esc($reg['name']) ?></strong></td>
                  <td><span class="badge bg-info"><?= esc($reg['mobile']) ?></span></td>
                  <td><?= esc($reg['email']) ?></td>
                  <td><?= esc($reg['age']) ?> years</td>
                  <td><span class="badge bg-secondary"><?= esc($reg['cricketer_type']) ?></span></td>
                  <td><span class="badge bg-primary"><?= esc(str_replace('_', ' ', ucfirst($reg['age_group']))) ?></span></td>
                  <td><span class="badge bg-success"><?= esc($reg['trial_city_name'] ?? 'N/A') ?></span></td>
                  <td>
                    <select class="form-select form-select-sm payment-status-select bg-dark text-white"
                      data-player-id="<?= esc($reg['id']) ?>"
                      data-player-name="<?= esc($reg['name']) ?>"
                      data-player-phone="<?= esc($reg['mobile']) ?>">
                      <option value="unpaid" <?= (!isset($reg['payment_status']) || $reg['payment_status'] == 'unpaid') ? 'selected' : '' ?>>
                        ❌ Unpaid
                      </option>
                      <option value="paid" <?= (isset($reg['payment_status']) && $reg['payment_status'] == 'paid') ? 'selected' : '' ?>>
                        ✅ Paid
                      </option>
                    </select>
                  </td>
                  <td>
                    <select class="form-select form-select-sm grade-select bg-dark text-white"
                      data-player-id="<?= esc($reg['id']) ?>"
                      data-player-name="<?= esc($reg['name']) ?>">
                      <option value="">Select Grade</option>
                      <?php foreach ($grades as $grade): ?>
                        <option value="<?= $grade['id'] ?>" <?= (isset($reg['assigned_grade_id']) && $reg['assigned_grade_id'] == $grade['id']) ? 'selected' : '' ?>>
                          <?= esc($grade['title']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <?php if (!empty($reg['aadhar_document'])): ?>
                        <a href="<?= base_url('admin/league-registration/view-document/' . $reg['id'] . '/aadhar_document') ?>"
                          target="_blank" class="btn btn-sm btn-outline-info" title="View Aadhar Document" data-bs-toggle="tooltip">
                          <i class="fas fa-id-card"></i>
                        </a>
                      <?php endif; ?>

                      <?php if (!empty($reg['marksheet_document'])): ?>
                        <a href="<?= base_url('admin/league-registration/view-document/' . $reg['id'] . '/marksheet_document') ?>"
                          target="_blank" class="btn btn-sm btn-outline-success" title="View Marksheet" data-bs-toggle="tooltip">
                          <i class="fas fa-graduation-cap"></i>
                        </a>
                      <?php endif; ?>

                      <?php if (!empty($reg['dob_proof'])): ?>
                        <a href="<?= base_url('admin/league-registration/view-document/' . $reg['id'] . '/dob_proof') ?>"
                          target="_blank" class="btn btn-sm btn-outline-warning" title="View DOB Proof" data-bs-toggle="tooltip">
                          <i class="fas fa-birthday-cake"></i>
                        </a>
                      <?php endif; ?>

                      <?php if (!empty($reg['photo'])): ?>
                        <a href="<?= base_url('admin/league-registration/view-document/' . $reg['id'] . '/photo') ?>"
                          target="_blank" class="btn btn-sm btn-outline-secondary" title="View Photo" data-bs-toggle="tooltip">
                          <i class="fas fa-camera"></i>
                        </a>
                      <?php endif; ?>
                    </div>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-danger" onclick="deletePlayer(<?= esc($reg['id']) ?>, '<?= esc($reg['name']) ?>')" title="Delete Player">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                  <td>
                    <small class="text-muted"><?= date('d M Y', strtotime($reg['created_at'])) ?></small>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="13" class="text-center text-muted">No league registrations found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if (isset($pager)): ?>
        <div class="d-flex justify-content-center mt-3">
          <?= $pager->links() ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

<script>
  const notyf = new Notyf();

  // Initialize tooltips
  document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  });

  // Individual payment status update
  document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('.payment-status-select');

    statusSelects.forEach(select => {
      const originalValue = select.value;

      select.addEventListener('change', function() {
        const playerId = this.getAttribute('data-player-id');
        const playerName = this.getAttribute('data-player-name');
        const playerPhone = this.getAttribute('data-player-phone');
        const newStatus = this.value;

        let statusText = '';
        switch (newStatus) {
          case 'unpaid':
            statusText = 'Unpaid';
            break;
          case 'paid':
            statusText = 'Paid';
            break;
        }

        if (confirm(`Update ${playerName} (${playerPhone}) payment status to: ${statusText}?`)) {
          updatePaymentStatus(playerId, newStatus, this);
        } else {
          this.value = originalValue;
        }
      });
    });

    // Grade selection handlers
    const gradeSelects = document.querySelectorAll('.grade-select');
    gradeSelects.forEach(select => {
      const originalValue = select.value;

      select.addEventListener('change', function() {
        const playerId = this.getAttribute('data-player-id');
        const playerName = this.getAttribute('data-player-name');
        const gradeId = this.value;
        const gradeName = this.options[this.selectedIndex].text;

        if (gradeId) {
          if (confirm(`Assign grade "${gradeName}" to ${playerName}?`)) {
            updatePlayerGrade(playerId, gradeId, this);
          } else {
            this.value = originalValue;
          }
        }
      });
    });
  });

  function updatePaymentStatus(playerId, status, selectElement) {
    selectElement.disabled = true;

    fetch("<?= base_url('admin/league-registration/update-payment-status') ?>", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({
          id: playerId,
          payment_status: status
        }),
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        selectElement.disabled = false;

        if (data.success) {
          notyf.success("Payment status updated successfully!");
        } else {
          notyf.error(data.message || "Failed to update payment status.");
          selectElement.selectedIndex = 0;
        }
      })
      .catch(error => {
        selectElement.disabled = false;
        notyf.error("Network error occurred. Please check your connection.");
        selectElement.selectedIndex = 0;
      });
  }

  function updatePlayerGrade(playerId, gradeId, selectElement) {
    selectElement.disabled = true;

    fetch("<?= base_url('admin/league-registration/update-grade') ?>", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({
          player_id: playerId,
          grade_id: gradeId
        }),
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        selectElement.disabled = false;

        if (data.success) {
          notyf.success("Grade updated successfully!");
        } else {
          notyf.error(data.message || "Failed to update grade.");
          selectElement.selectedIndex = 0;
        }
      })
      .catch(error => {
        selectElement.disabled = false;
        notyf.error("Network error occurred. Please check your connection.");
        selectElement.selectedIndex = 0;
      });
  }

  function deletePlayer(playerId, playerName) {
    if (!confirm(`Are you sure you want to delete ${playerName}? This action cannot be undone!`)) {
      return;
    }

    fetch("<?= base_url('admin/league-registration/delete') ?>", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({
          player_id: playerId
        }),
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          notyf.success(`${playerName} deleted successfully!`);
          setTimeout(() => location.reload(), 1500);
        } else {
          notyf.error(data.message || "Failed to delete player.");
        }
      })
      .catch(error => {
        notyf.error("Network error occurred. Please check your connection.");
        console.error("Network error:", error);
      });
  }

  function exportToPDF(type) {
    const searchParams = new URLSearchParams(window.location.search);
    const exportUrl = `<?= base_url('admin/league-registration/export-pdf') ?>?${searchParams.toString()}`;
    
    // Show loading notification
    notyf.success('Generating PDF export...');
    
    // Create a temporary link to download the PDF
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = `league-registrations-${new Date().toISOString().split('T')[0]}.pdf`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Show success notification after a delay
    setTimeout(() => {
      notyf.success('PDF export completed!');
    }, 2000);
  }
</script>

<?= $this->endSection(); ?>