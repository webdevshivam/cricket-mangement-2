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
        <div class="row g-3">
          <!-- Filter Row -->
          <div class="col-md-3">
            <label class="form-label text-warning small">Search by Phone</label>
            <input type="text" class="form-control bg-dark text-white" name="phone"
              placeholder="Search by phone" value="<?= esc($phone ?? '') ?>">
          </div>
          <div class="col-md-2">
            <label class="form-label text-warning small">Payment Status</label>
            <select class="form-select bg-dark text-white" name="payment_status">
              <option value="">All Payment Status</option>
              <option value="unpaid" <?= (isset($payment_status) && $payment_status == 'unpaid') ? 'selected' : '' ?>>Unpaid</option>
              <option value="paid" <?= (isset($payment_status) && $payment_status == 'paid') ? 'selected' : '' ?>>Paid</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label text-warning small">Age Group</label>
            <select class="form-select bg-dark text-white" name="age_group">
              <option value="">All Age Groups</option>
              <option value="under_16" <?= (isset($age_group) && $age_group == 'under_16') ? 'selected' : '' ?>>Under 16</option>
              <option value="above_16" <?= (isset($age_group) && $age_group == 'above_16') ? 'selected' : '' ?>>Above 16</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label text-warning small">Filter Actions</label>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-warning">
                <i class="fas fa-search"></i> Filter
              </button>
              <a href="<?= base_url('admin/league-registration') ?>" class="btn btn-secondary">
                <i class="fas fa-refresh"></i> Reset
              </a>
            </div>
          </div>
          <div class="col-md-2">
            <label class="form-label text-warning small">Bulk Actions</label>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-success btn-sm" onclick="bulkUpdateStatus('selected')" title="Mark Selected Players as Selected">
                <i class="fas fa-check"></i> Selected
              </button>
              <button type="button" class="btn btn-danger btn-sm" onclick="bulkUpdateStatus('not_selected')" title="Mark Selected Players as Not Selected">
                <i class="fas fa-times"></i> Not Selected
              </button>
            </div>
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
              <th width="50">
                <input type="checkbox" class="form-check-input" id="selectAll">
                <small class="text-muted d-block">#</small>
              </th>
              <th width="150">Player Details</th>
              <th width="120">Contact</th>
              <th width="80">Age & Type</th>
              <th width="100">Age Group</th>
              <th width="120">Payment Status</th>
              <th width="120">Selection Status</th>
              <th width="120">Assigned Grade</th>
              <th width="100">Documents</th>
              <th width="80">Actions</th>
              <th width="100">Registered On</th>
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
                  <td>
                    <input type="checkbox" class="form-check-input player-checkbox" value="<?= esc($reg['id']) ?>">
                    <small class="text-muted d-block"><?= $i++ ?></small>
                  </td>
                  <td>
                    <div>
                      <strong class="text-white"><?= esc($reg['name']) ?></strong>
                      <small class="text-muted d-block"><?= esc($reg['age']) ?> years</small>
                    </div>
                  </td>
                  <td>
                    <div>
                      <span class="badge bg-info mb-1"><?= esc($reg['mobile']) ?></span>
                      <small class="text-muted d-block"><?= esc($reg['email']) ?></small>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-secondary"><?= esc($reg['cricketer_type']) ?></span>
                  </td>
                  <td>
                    <span class="badge bg-primary"><?= esc(str_replace('_', ' ', ucfirst($reg['age_group']))) ?></span>
                  </td>
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
                    <select class="form-select form-select-sm status-select bg-dark text-white"
                      data-player-id="<?= esc($reg['id']) ?>"
                      data-player-name="<?= esc($reg['name']) ?>">
                      <option value="not_selected" <?= (!isset($reg['status']) || $reg['status'] == 'not_selected') ? 'selected' : '' ?>>
                        ❌ Not Selected
                      </option>
                      <option value="selected" <?= (isset($reg['status']) && $reg['status'] == 'selected') ? 'selected' : '' ?>>
                        ✅ Selected
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
                    <div class="btn-group btn-group-sm d-flex flex-wrap" style="gap: 2px;">
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
                <td colspan="11" class="text-center text-muted">No league registrations found.</td>
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
    const paymentStatusSelects = document.querySelectorAll('.payment-status-select');

    paymentStatusSelects.forEach(select => {
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

    // Status selection handlers
    const statusSelects = document.querySelectorAll('.status-select');
    statusSelects.forEach(select => {
      const originalValue = select.value;

      select.addEventListener('change', function() {
        const playerId = this.getAttribute('data-player-id');
        const playerName = this.getAttribute('data-player-name');
        const newStatus = this.value;
        const statusText = newStatus === 'selected' ? 'Selected' : 'Not Selected';

        if (confirm(`Update ${playerName} status to: ${statusText}?`)) {
          updatePlayerStatus(playerId, newStatus, this);
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

  function updatePlayerStatus(playerId, status, selectElement) {
    selectElement.disabled = true;

    fetch("<?= base_url('admin/league-registration/update-status') ?>", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({
          player_id: playerId,
          status: status
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
          notyf.success("Status updated successfully!");
        } else {
          notyf.error(data.message || "Failed to update status.");
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

  function bulkUpdateStatus(status) {
    const checkedBoxes = document.querySelectorAll('.player-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
      notyf.error('Please select at least one player to update.');
      return;
    }

    const playerIds = Array.from(checkedBoxes).map(checkbox => checkbox.value);
    const statusText = status === 'selected' ? 'Selected' : 'Not Selected';
    
    if (!confirm(`Are you sure you want to mark ${playerIds.length} player(s) as ${statusText}?`)) {
      return;
    }

    fetch("<?= base_url('admin/league-registration/bulk-update-status') ?>", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({
          player_ids: playerIds,
          status: status
        }),
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          notyf.success(data.message);
          setTimeout(() => location.reload(), 1500);
        } else {
          notyf.error(data.message || "Failed to bulk update status.");
        }
      })
      .catch(error => {
        notyf.error("Network error occurred. Please check your connection.");
        console.error("Network error:", error);
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

  // Select All checkbox functionality
  document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.player-checkbox');
    checkboxes.forEach(checkbox => {
      checkbox.checked = this.checked;
    });
  });
</script>

<?= $this->endSection(); ?>