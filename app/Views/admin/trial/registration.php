
<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark border-warning stats-card mb-4">
  <div class="card-body d-flex justify-content-between align-items-center">
    <h5 class="card-title text-warning mb-0">Trial Student Verification</h5>
    <div>
      <span class="badge bg-info me-2">Total: <?= count($registrations) ?></span>
      <button class="btn btn-sm btn-warning" onclick="showBulkActions()">
        <i class="fas fa-tasks"></i> Bulk Actions
      </button>
      <button class="btn btn-sm btn-outline-success ms-2" onclick="exportToPDF('trial')">
        <i class="fas fa-file-pdf"></i> Export PDF
      </button>
    </div>
  </div>
</div>

<div class="container mt-3">
  <!-- Search and Filter Section -->
  <div class="card bg-dark border-warning mb-4">
    <div class="card-body">
      <form method="GET" action="<?= base_url('admin/trial-registration') ?>">
        <div class="row">
          <div class="col-md-3">
            <input type="text" class="form-control bg-dark text-white" name="phone" 
                   placeholder="Search by phone" value="<?= esc($phone ?? '') ?>">
          </div>
          <div class="col-md-3">
            <select class="form-select bg-dark text-white" name="payment_status">
              <option value="">All Payment Status</option>
              <option value="no_payment" <?= (isset($payment_status) && $payment_status == 'no_payment') ? 'selected' : '' ?>>No Payment</option>
              <option value="partial" <?= (isset($payment_status) && $payment_status == 'partial') ? 'selected' : '' ?>>Partial Paid</option>
              <option value="full" <?= (isset($payment_status) && $payment_status == 'full') ? 'selected' : '' ?>>Full Paid</option>
            </select>
          </div>
          <div class="col-md-3">
            <select class="form-select bg-dark text-white" name="trial_city">
              <option value="">All Trial Cities</option>
              <?php foreach ($trial_cities as $city) : ?>
                <option value="<?= $city['id'] ?>" <?= (isset($trial_city) && $trial_city == $city['id']) ? 'selected' : '' ?>>
                  <?= esc($city['city_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-warning me-2">
              <i class="fas fa-search"></i> Filter
            </button>
            <a href="<?= base_url('admin/trial-registration') ?>" class="btn btn-secondary">
              <i class="fas fa-refresh"></i> Reset
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Bulk Actions Panel (Hidden by default) -->
  <div class="card bg-dark border-info mb-4" id="bulkActionsPanel" style="display: none;">
    <div class="card-body">
      <div class="row align-items-center">
        <div class="col-md-6">
          <label class="form-label text-info">
            <input type="checkbox" id="selectAll" class="form-check-input me-2">
            Select All Students
          </label>
          <span id="selectedCount" class="badge bg-info ms-2">0 selected</span>
        </div>
        <div class="col-md-6 text-end">
          <select class="form-select bg-dark text-white d-inline-block" id="bulkStatus" style="width: auto;">
            <option value="">Select Status</option>
            <option value="no_payment">No Payment</option>
            <option value="partial">Partial Paid</option>
            <option value="full">Full Paid</option>
          </select>
          <button class="btn btn-info ms-2" onclick="bulkUpdateStatus()">
            <i class="fas fa-edit"></i> Update Selected
          </button>
          <button class="btn btn-danger ms-2" onclick="bulkDeleteStudents()">
            <i class="fas fa-trash"></i> Delete Selected
          </button>
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
              <th width="40px">
                <input type="checkbox" id="masterCheckbox" class="form-check-input">
              </th>
              <th>#</th>
              <th>Name</th>
              <th>Mobile</th>
              <th>Email</th>
              <th>Age</th>
              <th>Cricket Type</th>
              <th>Trial City</th>
              <th>Payment Status</th>
              <th>Remaining Amount</th>
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
                <?php
                // Calculate fees based on cricket type
                $trialFees = 0;
                $tshirtFees = 199;
                
                switch(strtolower($reg['cricket_type'])) {
                  case 'bowler':
                  case 'batsman':
                    $trialFees = 999;
                    break;
                  case 'all-rounder':
                  case 'wicket-keeper':
                    $trialFees = 1199;
                    break;
                }
                
                // Calculate remaining amount based on payment status
                $remainingAmount = 0;
                
                switch($reg['payment_status']) {
                  case 'no_payment':
                    $remainingAmount = $tshirtFees + $trialFees;
                    break;
                  case 'partial':
                    $remainingAmount = $trialFees;
                    break;
                  case 'full':
                    $remainingAmount = 0;
                    break;
                }
                ?>
                <tr data-phone="<?= esc($reg['mobile']) ?>" data-status="<?= $reg['payment_status'] ?? 'no_payment' ?>">
                  <td>
                    <input type="checkbox" class="form-check-input student-checkbox" 
                           data-student-id="<?= esc($reg['id']) ?>">
                  </td>
                  <td><?= $i++ ?></td>
                  <td><strong><?= esc($reg['name']) ?></strong></td>
                  <td><span class="badge bg-info"><?= esc($reg['mobile']) ?></span></td>
                  <td><?= esc($reg['email']) ?></td>
                  <td><?= esc($reg['age']) ?> years</td>
                  <td><span class="badge bg-secondary"><?= esc($reg['cricket_type']) ?></span></td>
                  <td><span class="badge bg-primary"><?= esc($reg['trial_city_name'] ?? 'N/A') ?></span></td>
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
                    <span class="badge <?= $remainingAmount > 0 ? 'bg-danger' : 'bg-success' ?>">
                      ₹<?= $remainingAmount ?>
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-info" onclick="viewPlayerDetails(<?= esc($reg['id']) ?>)" title="View Details">
                      <i class="fas fa-eye"></i>
                    </button>
                    <?php if ($remainingAmount > 0) : ?>
                      <button class="btn btn-sm btn-outline-success" onclick="collectPayment(<?= esc($reg['id']) ?>, <?= $remainingAmount ?>)" title="Collect Payment">
                        <i class="fas fa-money-bill"></i>
                      </button>
                    <?php endif; ?>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteStudent(<?= esc($reg['id']) ?>, '<?= esc($reg['name']) ?>')" title="Delete Student">
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
                <td colspan="12" class="text-center text-muted">No trial registrations found.</td>
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

<!-- Payment Collection Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-dark border-warning">
      <div class="modal-header border-warning">
        <h5 class="modal-title text-warning">Collect Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="paymentContent">
          <p><strong>Student:</strong> <span id="paymentStudentName"></span></p>
          <p><strong>Phone:</strong> <span id="paymentStudentPhone"></span></p>
          <p><strong>Remaining Amount:</strong> ₹<span id="paymentAmount"></span></p>
          
          <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select class="form-select bg-dark text-white" id="paymentMethod">
              <option value="cash">Cash</option>
              <option value="upi">UPI</option>
              <option value="card">Card</option>
              <option value="online">Online Transfer</option>
            </select>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Transaction Reference (Optional)</label>
            <input type="text" class="form-control bg-dark text-white" id="transactionRef" 
                   placeholder="Enter transaction ID or reference">
          </div>
        </div>
      </div>
      <div class="modal-footer border-warning">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" onclick="confirmPayment()">Confirm Payment</button>
      </div>
    </div>
  </div>
</div>

<!-- Player Details Modal -->
<div class="modal fade" id="playerDetailsModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-dark border-warning">
      <div class="modal-header border-warning">
        <h5 class="modal-title text-warning">Student Details</h5>
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
let currentPaymentStudentId = null;

// Show/Hide bulk actions
function showBulkActions() {
    const panel = document.getElementById('bulkActionsPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

// Master checkbox functionality
document.getElementById('masterCheckbox').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.student-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateSelectedCount();
});

// Individual checkbox functionality
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('student-checkbox')) {
        updateSelectedCount();
    }
});

function updateSelectedCount() {
    const selected = document.querySelectorAll('.student-checkbox:checked');
    document.getElementById('selectedCount').textContent = selected.length + ' selected';
}

// Bulk update status
function bulkUpdateStatus() {
    const selected = document.querySelectorAll('.student-checkbox:checked');
    const status = document.getElementById('bulkStatus').value;
    
    if (selected.length === 0) {
        notyf.error('Please select at least one student');
        return;
    }
    
    if (!status) {
        notyf.error('Please select a status');
        return;
    }
    
    if (!confirm(`Update payment status for ${selected.length} students to: ${status}?`)) {
        return;
    }
    
    const studentIds = Array.from(selected).map(cb => cb.getAttribute('data-student-id'));
    
    fetch("<?= base_url('admin/trial-registration/bulk-update-payment-status') ?>", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({
            student_ids: studentIds,
            payment_status: status
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            notyf.success(`Updated ${studentIds.length} students successfully!`);
            setTimeout(() => location.reload(), 1500);
        } else {
            notyf.error(data.message || "Failed to update payment status.");
        }
    })
    .catch(error => {
        notyf.error("Network error occurred. Please check your connection.");
        console.error("Network error:", error);
    });
}

// Bulk delete students
function bulkDeleteStudents() {
    const selected = document.querySelectorAll('.student-checkbox:checked');
    
    if (selected.length === 0) {
        notyf.error('Please select at least one student to delete');
        return;
    }
    
    if (!confirm(`Are you sure you want to delete ${selected.length} selected students? This action cannot be undone!`)) {
        return;
    }
    
    const studentIds = Array.from(selected).map(cb => cb.getAttribute('data-student-id'));
    
    fetch("<?= base_url('admin/trial-registration/bulk-delete') ?>", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({
            student_ids: studentIds
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            notyf.success(`Deleted ${studentIds.length} students successfully!`);
            setTimeout(() => location.reload(), 1500);
        } else {
            notyf.error(data.message || "Failed to delete students.");
        }
    })
    .catch(error => {
        notyf.error("Network error occurred. Please check your connection.");
        console.error("Network error:", error);
    });
}

// Collect payment functionality
function collectPayment(studentId, amount) {
    currentPaymentStudentId = studentId;
    
    // Find student details from the table
    const row = document.querySelector(`[data-player-id="${studentId}"]`).closest('tr');
    const studentName = row.querySelector('strong').textContent;
    const studentPhone = row.querySelector('.badge').textContent;
    
    document.getElementById('paymentStudentName').textContent = studentName;
    document.getElementById('paymentStudentPhone').textContent = studentPhone;
    document.getElementById('paymentAmount').textContent = amount;
    
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}

function confirmPayment() {
    const paymentMethod = document.getElementById('paymentMethod').value;
    const transactionRef = document.getElementById('transactionRef').value;
    const amount = document.getElementById('paymentAmount').textContent;
    
    // Determine new payment status based on amount
    let newStatus = 'full'; // Default to full if collecting remaining amount
    
    fetch("<?= base_url('admin/trial-registration/collect-payment') ?>", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({
            student_id: currentPaymentStudentId,
            amount: parseFloat(amount),
            payment_method: paymentMethod,
            transaction_ref: transactionRef,
            payment_status: newStatus
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            notyf.success("Payment collected successfully!");
            bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
            setTimeout(() => location.reload(), 1500);
        } else {
            notyf.error(data.message || "Failed to collect payment.");
        }
    })
    .catch(error => {
        notyf.error("Network error occurred. Please check your connection.");
        console.error("Network error:", error);
    });
}

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
                this.value = originalValue;
            }
        });
    });
});

function updatePaymentStatus(playerId, status, selectElement) {
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
            
            // Update the row's data attribute and remaining amount
            const row = selectElement.closest('tr');
            row.setAttribute('data-status', status);
            
            // Update remaining amount badge
            const remainingBadge = row.querySelector('td:nth-child(10) .badge');
            
            // Get cricket type from the row to calculate correct fees
            const cricketTypeBadge = row.querySelector('td:nth-child(7) .badge');
            const cricketType = cricketTypeBadge ? cricketTypeBadge.textContent.toLowerCase() : '';
            
            let trialFees = 0;
            const tshirtFees = 199;
            
            if (cricketType.includes('bowler') || cricketType.includes('batsman')) {
                trialFees = 999;
            } else if (cricketType.includes('all-rounder') || cricketType.includes('wicket-keeper')) {
                trialFees = 1199;
            }
            
            let remainingAmount = 0;
            switch(status) {
                case 'no_payment':
                    remainingAmount = tshirtFees + trialFees;
                    break;
                case 'partial':
                    remainingAmount = trialFees;
                    break;
                case 'full':
                    remainingAmount = 0;
                    break;
            }
            
            remainingBadge.textContent = `₹${remainingAmount}`;
            remainingBadge.className = `badge ${remainingAmount > 0 ? 'bg-danger' : 'bg-success'}`;
            
            // Show/hide collect payment button
            const collectBtn = row.querySelector('.btn-outline-success');
            if (collectBtn) {
                collectBtn.style.display = remainingAmount > 0 ? 'inline-block' : 'none';
            }
            
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

function viewPlayerDetails(playerId) {
    notyf.success('Student details feature - to be implemented');
}

// Individual delete student
function deleteStudent(studentId, studentName) {
    if (!confirm(`Are you sure you want to delete ${studentName}? This action cannot be undone!`)) {
        return;
    }
    
    fetch("<?= base_url('admin/trial-registration/delete') ?>", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({
            student_id: studentId
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            notyf.success(`${studentName} deleted successfully!`);
            setTimeout(() => location.reload(), 1500);
        } else {
            notyf.error(data.message || "Failed to delete student.");
        }
    })
    .catch(error => {
        notyf.error("Network error occurred. Please check your connection.");
        console.error("Network error:", error);
    });
}

function exportToPDF(type) {
    const searchParams = new URLSearchParams(window.location.search);
    const exportUrl = `<?= base_url('admin/trial-registration/export-pdf') ?>?${searchParams.toString()}`;
    
    // Show loading notification
    notyf.success('Generating PDF export...');
    
    // Create a temporary link to download the PDF
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = `trial-registrations-${new Date().toISOString().split('T')[0]}.pdf`;
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
