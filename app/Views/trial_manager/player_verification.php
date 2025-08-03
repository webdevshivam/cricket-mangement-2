<?= $this->extend('layouts/trial_manager') ?>

<?= $this->section('title') ?>Player Verification<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-dark text-light border-warning">
                <div class="card-header bg-warning text-dark">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-search me-2"></i>Player Verification & Payment Collection
                    </h4>
                </div>
                <div class="card-body">
                    <p class="mb-0">Search for players by mobile number to verify registration and collect payments.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <div class="card bg-dark text-light border-warning">
                <div class="card-body">
                    <h5 class="text-warning mb-3">
                        <i class="fas fa-mobile-alt me-2"></i>Search Player
                    </h5>
                    <form id="searchForm">
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-warning text-dark">
                                <i class="fas fa-phone"></i>
                            </span>
                            <input type="tel" class="form-control" id="mobileNumber" 
                                   placeholder="Enter 10-digit mobile number" maxlength="10" required>
                            <button class="btn btn-warning" type="submit">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Player Details Section -->
    <div class="row mb-4" id="playerDetails" style="display: none;">
        <div class="col-12">
            <div class="card bg-dark text-light border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Player Information
                    </h5>
                </div>
                <div class="card-body" id="playerInfo">
                    <!-- Player details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Collection Section -->
    <div class="row mb-4" id="paymentSection" style="display: none;">
        <div class="col-12">
            <div class="card bg-dark text-light border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>Payment Collection
                    </h5>
                </div>
                <div class="card-body">
                    <form id="paymentForm">
                        <input type="hidden" id="playerId" name="player_id">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-warning">Payment Method</label>
                                    <select class="form-select" name="payment_method" required>
                                        <option value="">Select Method</option>
                                        <option value="cash">Cash</option>
                                        <option value="upi">UPI</option>
                                        <option value="card">Card</option>
                                        <option value="online">Online Transfer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-warning">Amount (₹)</label>
                                    <input type="number" class="form-control" name="amount" 
                                           placeholder="Enter amount" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-warning">Transaction ID (Optional)</label>
                                    <input type="text" class="form-control" name="transaction_id" 
                                           placeholder="UPI/Card Transaction ID">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label text-warning">Notes</label>
                                    <textarea class="form-control" name="notes" rows="2" 
                                              placeholder="Any additional notes..."></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-money-bill-wave me-2"></i>Collect Payment
                        </button>
                        <button type="button" class="btn btn-secondary ms-2" onclick="resetForm()">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('searchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    searchPlayer();
});

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    collectPayment();
});

function searchPlayer() {
    const mobile = document.getElementById('mobileNumber').value;

    if (mobile.length !== 10) {
        notyf.error('Please enter a valid 10-digit mobile number');
        return;
    }

    fetch('/trial-manager/search-player', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ mobile: mobile })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayPlayerInfo(data.player);
        } else {
            notyf.error(data.message || 'Player not found');
            hidePlayerDetails();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        notyf.error('An error occurred while searching');
    });
}

function displayPlayerInfo(player) {
    const statusBadge = getStatusBadge(player.payment_status);

    document.getElementById('playerInfo').innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <p><strong class="text-warning">Name:</strong> ${player.name}</p>
                <p><strong class="text-warning">Mobile:</strong> ${player.mobile}</p>
                <p><strong class="text-warning">Email:</strong> ${player.email || 'Not provided'}</p>
                <p><strong class="text-warning">Age:</strong> ${player.age}</p>
            </div>
            <div class="col-md-6">
                <p><strong class="text-warning">Trial City:</strong> ${player.trial_city_name}</p>
                <p><strong class="text-warning">Payment Status:</strong> ${statusBadge}</p>
                <p><strong class="text-warning">Registration Date:</strong> ${formatDate(player.created_at)}</p>
                <p><strong class="text-warning">Total Paid:</strong> ₹${player.total_paid || 0}</p>
            </div>
        </div>
    `;

    document.getElementById('playerId').value = player.id;
    document.getElementById('playerDetails').style.display = 'block';

    if (player.payment_status !== 'full') {
        document.getElementById('paymentSection').style.display = 'block';
    } else {
        document.getElementById('paymentSection').style.display = 'none';
    }
}

function getStatusBadge(status) {
    switch(status) {
        case 'full':
            return '<span class="badge bg-success">Full Payment</span>';
        case 'partial':
            return '<span class="badge bg-warning text-dark">Partial Payment</span>';
        case 'no_payment':
            return '<span class="badge bg-danger">No Payment</span>';
        default:
            return '<span class="badge bg-secondary">Unknown</span>';
    }
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-IN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function collectPayment() {
    const formData = new FormData(document.getElementById('paymentForm'));
    const data = Object.fromEntries(formData);

    fetch('/trial-manager/collect-payment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            notyf.success(data.message);
            // Refresh player info
            searchPlayer();
        } else {
            notyf.error(data.message || 'Failed to collect payment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        notyf.error('An error occurred while collecting payment');
    });
}

function resetForm() {
    document.getElementById('searchForm').reset();
    hidePlayerDetails();
}

function hidePlayerDetails() {
    document.getElementById('playerDetails').style.display = 'none';
    document.getElementById('paymentSection').style.display = 'none';
}

// Initialize Notyf
let notyf = new Notyf({
    duration: 3000,
    ripple: true,
    position: {
        x: 'right',
        y: 'bottom'
    }
});
</script>
<?= $this->endSection() ?>