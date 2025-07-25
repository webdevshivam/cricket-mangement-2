<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark border-warning stats-card mb-4">
  <div class="card-body d-flex justify-content-between align-items-center">
    <h5 class="card-title text-warning mb-0">Trial Day Verification & Payment Collection</h5>
    <div>
      <span class="badge bg-info me-2">Total Players: <?= count($registrations) ?></span>
      <span class="badge bg-warning me-2">Offline: ₹<?= number_format($collectionStats['offline'] ?? 0) ?></span>
      <span class="badge bg-success me-2">Online: ₹<?= number_format($collectionStats['online'] ?? 0) ?></span>
    </div>
  </div>
</div>

<div class="container mt-3">
  <!-- Quick Mobile Search Section -->
  <div class="card bg-dark border-success mb-4">
    <div class="card-header bg-success text-dark">
      <h6 class="mb-0"><i class="fas fa-mobile-alt"></i> Quick Mobile Search & Payment Collection</h6>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="input-group">
            <input type="text" class="form-control bg-dark text-white" id="quickMobileSearch" 
                   placeholder="Enter mobile number to find student" maxlength="10">
            <button class="btn btn-success" onclick="quickSearchByMobile()">
              <i class="fas fa-search"></i> Find Student
            </button>
          </div>
        </div>
        <div class="col-md-6">
          <div class="text-center">
            <h6 class="text-warning">Today's Collection Summary</h6>
            <div class="row">
              <div class="col">Cash: <span class="badge bg-success">₹<?= number_format($collectionStats['cash'] ?? 0) ?></span></div>
              <div class="col">UPI: <span class="badge bg-info">₹<?= number_format($collectionStats['upi'] ?? 0) ?></span></div>
              <div class="col">Online: <span class="badge bg-primary">₹<?= number_format($collectionStats['online'] ?? 0) ?></span></div>
              <div class="col">Total: <span class="badge bg-warning">₹<?= number_format($collectionStats['total'] ?? 0) ?></span></div>
            </div>
            <div class="text-center mt-2">
              <small class="text-muted">*Trial fees only (T-shirt fees excluded)</small>
            </div></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Student Found Result -->
  <div id="studentSearchResult" class="card bg-dark border-info mb-4" style="display: none;">
    <div class="card-header bg-info text-dark">
      <h6 class="mb-0"><i class="fas fa-user-check"></i> Student Details</h6>
    </div>
    <div class="card-body" id="studentDetails">
      <!-- Student details will be populated here -->
    </div>
  </div>

  <!-- Date Filter & Advanced Search -->
  <div class="card bg-dark border-warning mb-4">
    <div class="card-body">
      <form method="GET" action="<?= base_url('admin/trial-verification') ?>">
        <div class="row">
          <div class="col-md-2">
            <label class="form-label text-warning">Date Filter</label>
            <input type="date" class="form-control bg-dark text-white" name="date_filter" 
                   value="<?= esc($date_filter ?? date('Y-m-d')) ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label text-warning">Mobile Search</label>
            <input type="text" class="form-control bg-dark text-white" name="phone" 
                   placeholder="Search by phone number" value="<?= esc($phone ?? '') ?>">
          </div>
          <div class="col-md-2">
            <label class="form-label text-warning">Payment Status</label>
            <select class="form-select bg-dark text-white" name="payment_status">
              <option value="">All Status</option>
              <option value="no_payment" <?= (isset($payment_status) && $payment_status == 'no_payment') ? 'selected' : '' ?>>No Payment</option>
              <option value="partial" <?= (isset($payment_status) && $payment_status == 'partial') ? 'selected' : '' ?>>Partial Paid</option>
              <option value="full" <?= (isset($payment_status) && $payment_status == 'full') ? 'selected' : '' ?>>Full Paid</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label text-warning">Trial City</label>
            <select class="form-select bg-dark text-white" name="trial_city">
              <option value="">All Cities</option>
              <?php foreach ($trial_cities as $city) : ?>
                <option value="<?= $city['id'] ?>" <?= (isset($trial_city) && $trial_city == $city['id']) ? 'selected' : '' ?>>
                  <?= esc($city['city_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-warning form-control">
              <i class="fas fa-search"></i> Search
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Verification Table -->
  <div class="card bg-dark border-warning">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-dark table-striped table-bordered">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Mobile</th>
              <th>Cricket Type</th>
              <th>Trial City</th>
              <th>Current Status</th>
              <th>Due Amount</th>
              <th>Actions</th>
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

                // Calculate due amount based on payment status
                $dueAmount = 0;
                $dueDescription = '';

                switch($reg['payment_status'] ?? 'no_payment') {
                  case 'no_payment':
                    $dueAmount = $tshirtFees + $trialFees;
                    $dueDescription = "T-shirt (₹{$tshirtFees}) + Trial Fees (₹{$trialFees})";
                    break;
                  case 'partial':
                    $dueAmount = $trialFees;
                    $dueDescription = "Trial Fees (₹{$trialFees}) - T-shirt paid";
                    break;
                  case 'full':
                    $dueAmount = 0;
                    $dueDescription = "All dues clear";
                    break;
                }
                ?>
                <tr data-student-id="<?= esc($reg['id']) ?>" data-mobile="<?= esc($reg['mobile']) ?>">
                  <td><?= $i++ ?></td>
                  <td><strong class="text-warning"><?= esc($reg['name']) ?></strong></td>
                  <td><span class="badge bg-info"><?= esc($reg['mobile']) ?></span></td>
                  <td>
                    <span class="badge bg-secondary">
                      <?= esc(ucwords($reg['cricket_type'])) ?>
                      <small class="text-muted">(₹<?= $trialFees ?>)</small>
                    </span>
                  </td>
                  <td><span class="badge bg-primary"><?= esc($reg['trial_city_name'] ?? 'N/A') ?></span></td>
                  <td>
                    <?php
                    $statusBadge = '';
                    switch($reg['payment_status'] ?? 'no_payment') {
                      case 'no_payment':
                        $statusBadge = '<span class="badge bg-danger">❌ No Payment</span>';
                        break;
                      case 'partial':
                        $statusBadge = '<span class="badge bg-warning">🎽 Partial Paid</span>';
                        break;
                      case 'full':
                        $statusBadge = '<span class="badge bg-success">✅ Full Paid</span>';
                        break;
                    }
                    echo $statusBadge;
                    ?>
                  </td>
                  <td>
                    <div class="text-center">
                      <div class="badge <?= $dueAmount > 0 ? 'bg-danger' : 'bg-success' ?> mb-1">
                        ₹<?= $dueAmount ?>
                      </div>
                      <div class="small text-muted">
                        <?= $dueDescription ?>
                      </div>
                    </div>
                  </td>
                  <td>
                    <?php if ($dueAmount > 0) : ?>
                      <button class="btn btn-sm btn-success" 
                              onclick="collectPaymentOnSpot(<?= esc($reg['id']) ?>, '<?= esc($reg['name']) ?>', '<?= esc($reg['mobile']) ?>', <?= $dueAmount ?>, '<?= $reg['payment_status'] ?? 'no_payment' ?>')"
                              title="Collect Payment">
                        <i class="fas fa-money-bill"></i> Collect ₹<?= $dueAmount ?>
                      </button>
                    <?php else : ?>
                      <button class="btn btn-sm btn-outline-success" disabled>
                        <i class="fas fa-check"></i> Verified
                      </button>
                    <?php endif; ?>

                    <button class="btn btn-sm btn-outline-info" 
                            onclick="markTrialCompleted(<?= esc($reg['id']) ?>)" 
                            title="Mark Trial Completed">
                      <i class="fas fa-clipboard-check"></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="8" class="text-center text-muted">No trial registrations found.</td>
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
<div class="modal fade" id="paymentCollectionModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-dark border-warning">
      <div class="modal-header border-warning">
        <h5 class="modal-title text-warning">Collect Payment on Trial Day</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="paymentDetails">
          <div class="alert alert-info">
            <strong>Student:</strong> <span id="studentName"></span><br>
            <strong>Phone:</strong> <span id="studentPhone"></span><br>
            <strong>Amount to Collect:</strong> ₹<span id="amountToCollect"></span>
          </div>

          <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select class="form-select bg-dark text-white" id="paymentMethod">
              <option value="offline">Offline (Cash)</option>
              <option value="online">Online (UPI/Card/Transfer)</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Transaction Reference (Optional)</label>
            <input type="text" class="form-control bg-dark text-white" id="transactionRef" 
                   placeholder="Enter transaction ID or reference">
          </div>

          <div class="mb-3">
            <label class="form-label">Notes (Optional)</label>
            <textarea class="form-control bg-dark text-white" id="paymentNotes" rows="2" 
                      placeholder="Additional notes about payment"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer border-warning">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" onclick="confirmSpotPayment()">
          <i class="fas fa-check"></i> Confirm Payment
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

<script>
const notyf = new Notyf();
let currentStudentData = {};

// Quick mobile search functionality
function quickSearchByMobile() {
    const mobile = document.getElementById('quickMobileSearch').value.trim();

    if (!mobile || mobile.length < 10) {
        notyf.error('Please enter a valid 10-digit mobile number');
        return;
    }

    fetch("<?= base_url('admin/trial-verification/search-by-mobile') ?>", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({ mobile: mobile }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.student) {
            displayStudentSearchResult(data.student);
        } else {
            notyf.error(data.message || 'Student not found');
            document.getElementById('studentSearchResult').style.display = 'none';
        }
    })
    .catch(error => {
        notyf.error("Network error occurred.");
        console.error("Network error:", error);
    });
}

function displayStudentSearchResult(student) {
    // Calculate fees and due amount
    const trialFees = (student.cricket_type === 'bowler' || student.cricket_type === 'batsman') ? 999 : 1199;
    const tshirtFees = 199;

    let dueAmount = 0;
    let dueDescription = '';

    switch(student.payment_status || 'no_payment') {
        case 'no_payment':
            dueAmount = tshirtFees + trialFees;
            dueDescription = `T-shirt (₹${tshirtFees}) + Trial Fees (₹${trialFees})`;
            break;
        case 'partial':
            dueAmount = trialFees;
            dueDescription = `Trial Fees (₹${trialFees}) - T-shirt paid`;
            break;
        case 'full':
            dueAmount = 0;
            dueDescription = "All dues clear";
            break;
    }

    const statusBadge = {
        'no_payment': '<span class="badge bg-danger">❌ No Payment</span>',
        'partial': '<span class="badge bg-warning">🎽 Partial Paid</span>',
        'full': '<span class="badge bg-success">✅ Full Paid</span>'
    }[student.payment_status || 'no_payment'];

    document.getElementById('studentDetails').innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h5 class="text-warning">${student.name}</h5>
                <p><strong>Mobile:</strong> <span class="badge bg-info">${student.mobile}</span></p>
                <p><strong>Cricket Type:</strong> <span class="badge bg-secondary">${student.cricket_type} (₹${trialFees})</span></p>
                <p><strong>Trial City:</strong> <span class="badge bg-primary">${student.trial_city_name || 'N/A'}</span></p>
            </div>
            <div class="col-md-6">
                <p><strong>Payment Status:</strong> ${statusBadge}</p>
                <p><strong>Due Amount:</strong> <span class="badge ${dueAmount > 0 ? 'bg-danger' : 'bg-success'} fs-5">₹${dueAmount}</span></p>
                <p><small class="text-muted">${dueDescription}</small></p>
                ${dueAmount > 0 ? `
                    <button class="btn btn-success" onclick="collectPaymentOnSpot(${student.id}, '${student.name}', '${student.mobile}', ${dueAmount}, '${student.payment_status || 'no_payment'}')">
                        <i class="fas fa-money-bill"></i> Collect ₹${dueAmount}
                    </button>
                ` : `
                    <button class="btn btn-outline-success" disabled>
                        <i class="fas fa-check"></i> All Paid
                    </button>
                `}
            </div>
        </div>
    `;

    document.getElementById('studentSearchResult').style.display = 'block';
    document.getElementById('quickMobileSearch').value = '';
}

function collectPaymentOnSpot(studentId, studentName, studentPhone, amount, currentStatus) {
    currentStudentData = {
        id: studentId,
        name: studentName,
        phone: studentPhone,
        amount: amount,
        currentStatus: currentStatus
    };

    document.getElementById('studentName').textContent = studentName;
    document.getElementById('studentPhone').textContent = studentPhone;
    document.getElementById('amountToCollect').textContent = amount;

    const modal = new bootstrap.Modal(document.getElementById('paymentCollectionModal'));
    modal.show();
}

function confirmSpotPayment() {
    const paymentMethod = document.getElementById('paymentMethod').value;
    const transactionRef = document.getElementById('transactionRef').value;
    const notes = document.getElementById('paymentNotes').value;

    // Determine new payment status
    let newStatus = 'full'; // Always full when collecting on spot

    fetch("<?= base_url('admin/trial-verification/collect-spot-payment') ?>", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({
            student_id: currentStudentData.id,
            amount: currentStudentData.amount,
            payment_method: paymentMethod,
            transaction_ref: transactionRef,
            notes: notes,
            payment_status: newStatus,
            collected_on_trial_day: true
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            notyf.success(`Payment of ₹${currentStudentData.amount} collected successfully!`);
            bootstrap.Modal.getInstance(document.getElementById('paymentCollectionModal')).hide();
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

function markTrialCompleted(studentId) {
    if (confirm('Mark this student\'s trial as completed?')) {
        fetch("<?= base_url('admin/trial-verification/mark-trial-completed') ?>", {
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
                notyf.success("Trial marked as completed!");
                setTimeout(() => location.reload(), 1000);
            } else {
                notyf.error(data.message || "Failed to mark trial as completed.");
            }
        })
        .catch(error => {
            notyf.error("Network error occurred.");
            console.error("Network error:", error);
        });
    }
}

// Auto-focus on mobile search and handle enter key
document.addEventListener('DOMContentLoaded', function() {
    const quickMobileInput = document.getElementById('quickMobileSearch');
    if (quickMobileInput) {
        quickMobileInput.focus();
        quickMobileInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                quickSearchByMobile();
            }
        });
    }
});
</script>

<?= $this->endSection(); ?>