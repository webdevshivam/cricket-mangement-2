<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark border-warning stats-card mb-4">
  <div class="card-body d-flex justify-content-between align-items-center">
    <h5 class="card-title text-warning mb-0">Trial Registrations</h5>
  </div>
</div>

<div class="container mt-3">
  <!-- Search and Filter Section -->
  <div class="card bg-dark border-warning mb-4">
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="input-group mb-3">
            <input type="text" class="form-control bg-dark text-white" id="phoneSearch" placeholder="Search by phone number">
            <button class="btn btn-outline-warning" type="button" id="searchBtn">
              <i class="fas fa-search"></i> Search
            </button>
          </div>
        </div>
        <div class="col-md-6">
          <select class="form-select bg-dark text-white" id="statusFilter" onchange="filterByStatus(this.value)">
            <option value="all">All Payment Status</option>
            <option value="no_payment">No Payment</option>
            <option value="partial">Partial Paid</option>
            <option value="full">Full Paid</option>
          </select>
        </div>
      </div>
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
              <th>Cricket Type</th>
              <th>City</th>
              <th>Payment Status</th>
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
                <tr data-phone="<?= esc($reg['mobile']) ?>" data-status="<?= $reg['payment_status'] ?? 'not_verified' ?>">
                  <td><?= $i++ ?></td>
                  <td><strong><?= esc($reg['name']) ?></strong></td>
                  <td><span class="badge bg-info"><?= esc($reg['mobile']) ?></span></td>
                  <td><?= esc($reg['email']) ?></td>
                  <td><?= esc($reg['age']) ?> years</td>
                  <td><span class="badge bg-secondary"><?= esc($reg['cricket_type']) ?></span></td>
                  <td><?= esc($reg['city']) ?></td>
                  <td>
                    <select class="form-select form-select-sm payment-status-select bg-dark text-white" 
                            data-player-id="<?= esc($reg['id']) ?>" 
                            data-player-name="<?= esc($reg['name']) ?>"
                            data-player-phone="<?= esc($reg['mobile']) ?>">
                      <option value="no_payment" <?= (!isset($reg['payment_status']) || $reg['payment_status'] == 'no_payment') ? 'selected' : '' ?>>
                        ❌ No Payment
                      </option>
                      <option value="partial" <?= (isset($reg['payment_status']) && $reg['payment_status'] == 'partial') ? 'selected' : '' ?>>
                        🎽 Partial Paid (₹199)
                      </option>
                      <option value="full" <?= (isset($reg['payment_status']) && $reg['payment_status'] == 'full') ? 'selected' : '' ?>>
                        ✅ Full Paid
                      </option>
                    </select>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-info" onclick="viewPlayerDetails(<?= esc($reg['id']) ?>)">
                      <i class="fas fa-eye"></i>
                    </button>
                  </td>
                  <td>
                    <small class="text-muted"><?= date('d M Y', strtotime($reg['created_at'])) ?></small>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="10" class="text-center text-muted">No trial registrations found.</td>
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

<!-- Player Details Modal -->
<div class="modal fade" id="playerDetailsModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-dark border-warning">
      <div class="modal-header border-warning">
        <h5 class="modal-title text-warning">Player Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="playerDetailsContent">
        <!-- Content will be loaded dynamically -->
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

<script>
const notyf = new Notyf();

// Search functionality
document.getElementById('searchBtn').addEventListener('click', function() {
    const phoneNumber = document.getElementById('phoneSearch').value.trim();
    if (phoneNumber.length === 0) {
        notyf.error('Please enter a phone number to search');
        return;
    }

    const rows = document.querySelectorAll('#registrationsTable tbody tr');
    let found = false;

    rows.forEach(row => {
        const phone = row.getAttribute('data-phone');
        if (phone && phone.includes(phoneNumber)) {
            row.style.display = '';
            row.style.backgroundColor = '#3d4465';
            found = true;
        } else {
            row.style.display = 'none';
        }
    });

    if (!found) {
        notyf.error('No player found with this phone number');
        showAllRows();
    } else {
        notyf.success('Player found!');
    }
});

// Clear search when input is empty
document.getElementById('phoneSearch').addEventListener('input', function() {
    if (this.value.trim() === '') {
        showAllRows();
    }
});

// Filter by status
function filterByStatus(status) {
    const rows = document.querySelectorAll('#registrationsTable tbody tr');

    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        if (status === 'all' || rowStatus === status) {
            row.style.display = '';
            row.style.backgroundColor = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function showAllRows() {
    const rows = document.querySelectorAll('#registrationsTable tbody tr');
    rows.forEach(row => {
        row.style.display = '';
        row.style.backgroundColor = '';
    });
}

// Payment status update
document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('.payment-status-select');

    statusSelects.forEach(select => {
        // Store original value
        const originalValue = select.value;

        select.addEventListener('change', function() {
            const playerId = this.getAttribute('data-player-id');
            const playerName = this.getAttribute('data-player-name');
            const playerPhone = this.getAttribute('data-player-phone');
            const newStatus = this.value;
            const oldStatus = originalValue;

            // Confirm the change
            let statusText = '';
            switch(newStatus) {
                case 'no_payment':
                    statusText = 'No Payment';
                    break;
                case 'partial':
                    statusText = 'Partial Paid (₹199 - T-shirt only)';
                    break;
                case 'full':
                    statusText = 'Full Paid (Complete payment)';
                    break;
            }

            if (confirm(`Update ${playerName} (${playerPhone}) payment status to: ${statusText}?`)) {
                updatePaymentStatus(playerId, newStatus, this);
            } else {
                // Reset to previous value if cancelled
                this.value = oldStatus;
            }
        });
    });
});

function updatePaymentStatus(playerId, status, selectElement) {
    // Show loading state
    selectElement.disabled = true;

    fetch("<?= base_url('admin/trial-registration/update-payment-status') ?>", {
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

            // Update the row's data attribute
            const row = selectElement.closest('tr');
            row.setAttribute('data-status', status);
        } else {
            notyf.error(data.message || "Failed to update payment status.");
            console.error("Error:", data.message);

            // Reset select to original value on error
            selectElement.selectedIndex = 0;
        }
    })
    .catch(error => {
        selectElement.disabled = false;
        notyf.error("Network error occurred. Please check your connection.");
        console.error("Network error:", error);

        // Reset select to original value on error
        selectElement.selectedIndex = 0;
    });
}

function viewPlayerDetails(playerId) {
    notyf.info('Player details feature - to be implemented');
}
</script>

<?= $this->endSection(); ?>