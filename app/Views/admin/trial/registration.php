<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark border-warning stats-card mb-4">
  <div class="card-body d-flex justify-content-between align-items-center">
    <h5 class="card-title text-warning mb-0">Trial Registrations</h5>
  </div>
</div>

<div class="container mt-3">
  <form id="playerForm" method="post" action="<?= site_url('admin/players/bulk_action') ?>">
    <div class="table-responsive">
      <table class="table table-dark table-striped table-bordered">
        <thead>
          <tr>
            <th><input class="form-check-input" type="checkbox" id="selectAll" /></th>
            <th>#</th>
            <th>Name</th>
            <th>Mobile</th>
            <th>City</th>
            <th>Role</th>
            <th>Payment Status</th>
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
                <td><input type="checkbox" class="form-check-input" name="ids[]" value="<?= $reg['id'] ?>" /></td>
                <td><?= $i++ ?></td>
                <td><?= esc($reg['name']) ?></td>
                <td><?= esc($reg['mobile']) ?></td>
                <td><?= esc($reg['city']) ?></td>
                <td><?= esc($reg['cricket_type']) ?></td>
                <td>
                  <select name="payment_status[<?= $reg['id'] ?>]" class="form-select form-select-sm bg-dark text-white">
                    <option value="pending">Pending</option>
                    <option value="partial">Partial</option>
                    <option value="full">Full</option>
                  </select>
                </td>
                <td><?= date('d M Y', strtotime($reg['created_at'] ?? '')) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else : ?>
            <tr>
              <td colspan="8" class="text-center">No registrations found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="mt-3 d-flex gap-2">
      <button type="submit" name="action" value="delete" class="btn btn-danger">Delete Selected</button>
      <button type="submit" name="action" value="update" class="btn btn-primary">Update Payment Status</button>
    </div>
  </form>

  <!-- Pagination Links -->
  <div class="d-flex justify-content-center mt-4">
    <?= $pager->links() ?>
  </div>
</div>

<!-- Trial Registrations Table -->
<div class="card bg-dark border-warning">
  <div class="card-body">
   <div class="mb-3">
        <div class="input-group">
            <input type="text" class="form-control bg-dark text-white" id="phoneSearch" placeholder="Search by phone number">
            <button class="btn btn-outline-warning" type="button" id="searchBtn">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
    </div>

    <div class="mb-3">
        <label for="statusFilter" class="form-label text-warning">Filter by Payment Status:</label>
        <select class="form-select bg-dark text-white" id="statusFilter" onchange="filterByStatus(this.value)">
            <option value="all">All</option>
            <option value="not_verified">Not Verified</option>
            <option value="partial_paid">Partial Paid</option>
            <option value="full_paid">Full Paid</option>
        </select>
    </div>
    <div class="table-responsive">
      <table class="table table-dark table-striped" id="registrationsTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Age</th>
            <th>Cricket Type</th>
            <th>Trial City</th>
            <th>Payment Status</th>
            <th>Actions</th>
            <th>Registered On</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($registrations)): ?>
            <?php foreach ($registrations as $player): ?>
              <tr data-phone="<?= esc($player['mobile']) ?>" data-status="<?= $player['payment_status'] ?? 'not_verified' ?>">
                <td><?= esc($player['id']) ?></td>
                <td>
                  <strong><?= esc($player['name']) ?></strong>
                </td>
                <td>
                  <span class="badge bg-info"><?= esc($player['mobile']) ?></span>
                </td>
                <td><?= esc($player['email']) ?></td>
                <td><?= esc($player['age']) ?> years</td>
                <td>
                  <span class="badge bg-secondary"><?= esc($player['cricket_type']) ?></span>
                </td>
                <td><?= esc($player['trial_city_id']) ?></td>
                <td>
                  <select class="form-select form-select-sm payment-status-select" 
                          data-player-id="<?= esc($player['id']) ?>" 
                          data-player-name="<?= esc($player['name']) ?>"
                          data-player-phone="<?= esc($player['mobile']) ?>">
                    <option value="not_verified" <?= (!isset($player['payment_status']) || $player['payment_status'] == 'not_verified') ? 'selected' : '' ?>>
                      ❌ Not Verified
                    </option>
                    <option value="partial_paid" <?= (isset($player['payment_status']) && $player['payment_status'] == 'partial_paid') ? 'selected' : '' ?>>
                      🎽 Partial Paid (₹199)
                    </option>
                    <option value="full_paid" <?= (isset($player['payment_status']) && $player['payment_status'] == 'full_paid') ? 'selected' : '' ?>>
                      ✅ Full Paid
                    </option>
                  </select>
                </td>
                <td>
                  <button class="btn btn-sm btn-outline-info" onclick="viewPlayerDetails(<?= esc($player['id']) ?>)">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-warning" onclick="markTrialCompleted(<?= esc($player['id']) ?>)">
                    <i class="fas fa-check"></i> Trial Done
                  </button>
                </td>
                <td>
                  <small class="text-muted"><?= date('d M Y', strtotime($player['created_at'])) ?></small>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
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
        select.addEventListener('change', function() {
            const playerId = this.getAttribute('data-player-id');
            const playerName = this.getAttribute('data-player-name');
            const playerPhone = this.getAttribute('data-player-phone');
            const newStatus = this.value;

            // Confirm the change
            let statusText = '';
            switch(newStatus) {
                case 'not_verified':
                    statusText = 'Not Verified';
                    break;
                case 'partial_paid':
                    statusText = 'Partial Paid (₹199 - T-shirt only)';
                    break;
                case 'full_paid':
                    statusText = 'Full Paid (Complete payment)';
                    break;
            }

            if (confirm(`Update ${playerName} (${playerPhone}) payment status to: ${statusText}?`)) {
                updatePaymentStatus(playerId, newStatus, this);
            } else {
                // Reset to previous value if cancelled
                this.selectedIndex = 0;
            }
        });
    });
});

function updatePaymentStatus(playerId, status, selectElement) {
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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            notyf.success("Payment status updated successfully!");

            // Update the row's data attribute
            const row = selectElement.closest('tr');
            row.setAttribute('data-status', status);

            // Update statistics
            updateStatistics();
        } else {
            notyf.error("Failed to update payment status.");
            console.error("Error:", data.message);
        }
    })
    .catch(error => {
        notyf.error("Network error occurred.");
        console.error("Network error:", error);
    });
}

function updateStatistics() {
    const rows = document.querySelectorAll('#registrationsTable tbody tr');
    let notVerified = 0, partialPaid = 0, fullPaid = 0;

    rows.forEach(row => {
        const status = row.getAttribute('data-status');
        switch(status) {
            case 'not_verified':
                notVerified++;
                break;
            case 'partial_paid':
                partialPaid++;
                break;
            case 'full_paid':
                fullPaid++;
                break;
        }
    });

    document.getElementById('notVerifiedCount').textContent = notVerified;
    document.getElementById('partialPaidCount').textContent = partialPaid;
    document.getElementById('fullPaidCount').textContent = fullPaid;
}

function viewPlayerDetails(playerId) {
    // You can implement this to show more detailed player information
    notyf.info('Player details feature - to be implemented');
}

function markTrialCompleted(playerId) {
    if (confirm('Mark this player\'s trial as completed?')) {
        // You can implement trial completion tracking here
        notyf.success('Trial marked as completed!');
    }
}
</script>

<?= $this->endSection(); ?>