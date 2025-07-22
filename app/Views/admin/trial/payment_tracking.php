
<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark border-success stats-card mb-4">
  <div class="card-body">
    <div class="row">
      <div class="col-md-8">
        <h4 class="card-title text-success mb-0">
          <i class="fas fa-chart-line"></i> Payment Tracking & Collection Reports
        </h4>
        <p class="text-muted mb-0">Track all trial payments and collections</p>
      </div>
      <div class="col-md-4 text-end">
        <div class="btn-group" role="group">
          <button class="btn btn-outline-success btn-sm" onclick="exportData()">
            <i class="fas fa-download"></i> Export
          </button>
          <button class="btn btn-outline-info btn-sm" onclick="refreshData()">
            <i class="fas fa-refresh"></i> Refresh
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card bg-dark border-success">
      <div class="card-body text-center">
        <h5 class="text-success">Total Collection</h5>
        <h3 class="text-warning">₹<?= number_format($totalCollection ?? 0) ?></h3>
        <small class="text-muted">All time</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-dark border-info">
      <div class="card-body text-center">
        <h5 class="text-info">Today's Collection</h5>
        <h3 class="text-warning">₹<?= number_format($todayCollection ?? 0) ?></h3>
        <small class="text-muted"><?= date('d M Y') ?></small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-dark border-warning">
      <div class="card-body text-center">
        <h5 class="text-warning">Pending Amount</h5>
        <h3 class="text-danger">₹<?= number_format($pendingAmount ?? 0) ?></h3>
        <small class="text-muted">Outstanding</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-dark border-primary">
      <div class="card-body text-center">
        <h5 class="text-primary">Total Students</h5>
        <h3 class="text-white"><?= $totalStudents ?? 0 ?></h3>
        <small class="text-muted">Registered</small>
      </div>
    </div>
  </div>
</div>

<!-- Payment Method Breakdown -->
<div class="row mb-4">
  <div class="col-md-6">
    <div class="card bg-dark border-info">
      <div class="card-header bg-info text-dark">
        <h6 class="mb-0"><i class="fas fa-credit-card"></i> Payment Method Breakdown</h6>
      </div>
      <div class="card-body">
        <div class="row text-center">
          <div class="col-6">
            <div class="border-end">
              <h6 class="text-success">Offline Payment</h6>
              <h5>₹<?= number_format($paymentMethods['offline'] ?? 0) ?></h5>
              <small class="text-muted">Cash collection</small>
            </div>
          </div>
          <div class="col-6">
            <h6 class="text-info">Online Payment</h6>
            <h5>₹<?= number_format($paymentMethods['online'] ?? 0) ?></h5>
            <small class="text-muted">UPI/Card/Bank Transfer</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card bg-dark border-warning">
      <div class="card-header bg-warning text-dark">
        <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Payment Status Distribution</h6>
      </div>
      <div class="card-body">
        <div class="row text-center">
          <div class="col-4">
            <div class="border-end">
              <h6 class="text-danger">No Payment</h6>
              <h5><?= $statusCounts['no_payment'] ?? 0 ?></h5>
            </div>
          </div>
          <div class="col-4">
            <div class="border-end">
              <h6 class="text-warning">Partial</h6>
              <h5><?= $statusCounts['partial'] ?? 0 ?></h5>
            </div>
          </div>
          <div class="col-4">
            <h6 class="text-success">Full Paid</h6>
            <h5><?= $statusCounts['full'] ?? 0 ?></h5>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Date Range Filter -->
<div class="card bg-dark border-warning mb-4">
  <div class="card-body">
    <form method="GET" action="<?= base_url('admin/payment-tracking') ?>">
      <div class="row">
        <div class="col-md-2">
          <label class="form-label text-warning">From Date</label>
          <input type="date" class="form-control bg-dark text-white" name="from_date" 
                 value="<?= esc($from_date ?? date('Y-m-d', strtotime('-30 days'))) ?>">
        </div>
        <div class="col-md-2">
          <label class="form-label text-warning">To Date</label>
          <input type="date" class="form-control bg-dark text-white" name="to_date" 
                 value="<?= esc($to_date ?? date('Y-m-d')) ?>">
        </div>
        <div class="col-md-2">
          <label class="form-label text-warning">Payment Method</label>
          <select class="form-select bg-dark text-white" name="payment_method">
            <option value="">All Methods</option>
            <option value="offline" <?= (isset($payment_method) && $payment_method == 'offline') ? 'selected' : '' ?>>Offline</option>
            <option value="online" <?= (isset($payment_method) && $payment_method == 'online') ? 'selected' : '' ?>>Online</option>
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
          <label class="form-label text-warning">Mobile</label>
          <input type="text" class="form-control bg-dark text-white" name="mobile" 
                 placeholder="Search by mobile" value="<?= esc($mobile ?? '') ?>">
        </div>
        <div class="col-md-1">
          <label class="form-label">&nbsp;</label>
          <button type="submit" class="btn btn-warning form-control">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Payment Details Table -->
<div class="card bg-dark border-warning">
  <div class="card-header bg-warning text-dark">
    <h6 class="mb-0"><i class="fas fa-table"></i> Detailed Payment Records</h6>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-dark table-striped table-bordered">
        <thead>
          <tr>
            <th>#</th>
            <th>Student Details</th>
            <th>Cricket Type</th>
            <th>Trial City</th>
            <th>Payment Status</th>
            <th>Amount Paid</th>
            <th>Pending Amount</th>
            <th>Payment Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($paymentRecords)) : ?>
            <?php
            $currentPage = $pager->getCurrentPage() ?? 1;
            $perPage = $pager->getPerPage() ?? 20;
            $i = 1 + ($currentPage - 1) * $perPage;
            ?>
            <?php foreach ($paymentRecords as $record) : ?>
              <?php
              // Calculate fees based on cricket type
              $trialFees = 0;
              $tshirtFees = 199;
              
              switch(strtolower($record['cricket_type'])) {
                case 'bowler':
                case 'batsman':
                  $trialFees = 999;
                  break;
                case 'all-rounder':
                case 'wicket-keeper':
                  $trialFees = 1199;
                  break;
              }
              
              // Calculate amounts based on payment status
              $totalFees = $tshirtFees + $trialFees;
              $amountPaid = 0;
              $pendingAmount = 0;
              
              switch($record['payment_status'] ?? 'no_payment') {
                case 'no_payment':
                  $amountPaid = 0;
                  $pendingAmount = $totalFees;
                  break;
                case 'partial':
                  $amountPaid = $tshirtFees; // Only T-shirt paid
                  $pendingAmount = $trialFees; // Only trial fees pending
                  break;
                case 'full':
                  $amountPaid = $totalFees;
                  $pendingAmount = 0;
                  break;
              }
              ?>
              <tr>
                <td><?= $i++ ?></td>
                <td>
                  <strong class="text-warning"><?= esc($record['name']) ?></strong><br>
                  <small class="text-info"><?= esc($record['mobile']) ?></small><br>
                  <small class="text-muted"><?= esc($record['email']) ?></small>
                </td>
                <td>
                  <span class="badge bg-secondary">
                    <?= esc(ucwords($record['cricket_type'])) ?>
                  </span><br>
                  <small class="text-muted">Trial: ₹<?= $trialFees ?></small>
                </td>
                <td>
                  <span class="badge bg-primary"><?= esc($record['trial_city_name'] ?? 'N/A') ?></span>
                </td>
                <td>
                  <?php
                  $statusBadge = '';
                  switch($record['payment_status'] ?? 'no_payment') {
                    case 'no_payment':
                      $statusBadge = '<span class="badge bg-danger">❌ No Payment</span>';
                      break;
                    case 'partial':
                      $statusBadge = '<span class="badge bg-warning">🎽 T-shirt Paid</span>';
                      break;
                    case 'full':
                      $statusBadge = '<span class="badge bg-success">✅ Full Paid</span>';
                      break;
                  }
                  echo $statusBadge;
                  ?>
                </td>
                <td>
                  <span class="badge bg-success fs-6">₹<?= number_format($amountPaid) ?></span>
                  <?php if ($record['payment_status'] == 'partial') : ?>
                    <br><small class="text-muted">T-shirt only</small>
                  <?php elseif ($record['payment_status'] == 'full') : ?>
                    <br><small class="text-muted">Complete</small>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($pendingAmount > 0) : ?>
                    <span class="badge bg-danger fs-6">₹<?= number_format($pendingAmount) ?></span>
                    <br><small class="text-muted">Trial fees</small>
                  <?php else : ?>
                    <span class="badge bg-success">₹0</span>
                    <br><small class="text-muted">Clear</small>
                  <?php endif; ?>
                </td>
                <td>
                  <small class="text-muted">
                    Reg: <?= date('d M Y', strtotime($record['created_at'])) ?><br>
                    <?php if ($record['verified_at']) : ?>
                      Verified: <?= date('d M Y', strtotime($record['verified_at'])) ?>
                    <?php else : ?>
                      <span class="text-warning">Not verified</span>
                    <?php endif; ?>
                  </small>
                </td>
                <td>
                  <div class="btn-group-vertical" role="group">
                    <?php if ($pendingAmount > 0) : ?>
                      <button class="btn btn-sm btn-success" 
                              onclick="quickCollectPayment(<?= esc($record['id']) ?>, '<?= esc($record['name']) ?>', '<?= esc($record['mobile']) ?>', <?= $pendingAmount ?>)"
                              title="Collect Pending Amount">
                        <i class="fas fa-money-bill"></i> ₹<?= $pendingAmount ?>
                      </button>
                    <?php endif; ?>
                    <button class="btn btn-sm btn-outline-info" 
                            onclick="viewPaymentHistory(<?= esc($record['id']) ?>)" 
                            title="View Payment History">
                      <i class="fas fa-history"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else : ?>
            <tr>
              <td colspan="9" class="text-center text-muted">No payment records found.</td>
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

<!-- Quick Payment Collection Modal -->
<div class="modal fade" id="quickPaymentModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-dark border-success">
      <div class="modal-header border-success">
        <h5 class="modal-title text-success">Quick Payment Collection</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="quickPaymentDetails">
          <div class="alert alert-info">
            <strong>Student:</strong> <span id="quickStudentName"></span><br>
            <strong>Phone:</strong> <span id="quickStudentPhone"></span><br>
            <strong>Amount to Collect:</strong> ₹<span id="quickAmountToCollect"></span>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select class="form-select bg-dark text-white" id="quickPaymentMethod">
              <option value="offline">Offline (Cash)</option>
              <option value="online">Online (UPI/Card/Transfer)</option>
            </select>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Transaction Reference (Optional)</label>
            <input type="text" class="form-control bg-dark text-white" id="quickTransactionRef" 
                   placeholder="Enter transaction ID or reference">
          </div>
        </div>
      </div>
      <div class="modal-footer border-success">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" onclick="confirmQuickPayment()">
          <i class="fas fa-check"></i> Collect Payment
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

<script>
const notyf = new Notyf();
let currentQuickPaymentData = {};

function quickCollectPayment(studentId, studentName, studentPhone, amount) {
    currentQuickPaymentData = {
        id: studentId,
        name: studentName,
        phone: studentPhone,
        amount: amount
    };
    
    document.getElementById('quickStudentName').textContent = studentName;
    document.getElementById('quickStudentPhone').textContent = studentPhone;
    document.getElementById('quickAmountToCollect').textContent = amount;
    
    const modal = new bootstrap.Modal(document.getElementById('quickPaymentModal'));
    modal.show();
}

function confirmQuickPayment() {
    const paymentMethod = document.getElementById('quickPaymentMethod').value;
    const transactionRef = document.getElementById('quickTransactionRef').value;
    
    fetch("<?= base_url('admin/payment-tracking/collect-payment') ?>", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({
            student_id: currentQuickPaymentData.id,
            amount: currentQuickPaymentData.amount,
            payment_method: paymentMethod,
            transaction_ref: transactionRef,
            payment_status: 'full'
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            notyf.success(`Payment of ₹${currentQuickPaymentData.amount} collected successfully!`);
            bootstrap.Modal.getInstance(document.getElementById('quickPaymentModal')).hide();
            setTimeout(() => location.reload(), 1500);
        } else {
            notyf.error(data.message || "Failed to collect payment.");
        }
    })
    .catch(error => {
        notyf.error("Network error occurred.");
        console.error("Network error:", error);
    });
}

function viewPaymentHistory(studentId) {
    // This can be expanded to show detailed payment history
    alert('Payment history feature coming soon for student ID: ' + studentId);
}

function exportData() {
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '<?= base_url('admin/payment-tracking/export') ?>';
    
    // Add current filter values as hidden inputs
    const filters = ['from_date', 'to_date', 'payment_method', 'trial_city', 'mobile'];
    filters.forEach(filter => {
        const input = document.querySelector(`[name="${filter}"]`);
        if (input && input.value) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = filter;
            hiddenInput.value = input.value;
            form.appendChild(hiddenInput);
        }
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function refreshData() {
    location.reload();
}
</script>

<?= $this->endSection(); ?>
