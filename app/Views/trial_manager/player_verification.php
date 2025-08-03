
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
        }
        .search-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .player-info-card {
            border-left: 4px solid #667eea;
        }
        .fee-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="/trial-manager/dashboard">
                <i class="fas fa-arrow-left me-2"></i>
                <strong>Back to Dashboard</strong>
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <?= session()->get('name') ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><h6 class="dropdown-header"><?= session()->get('trial_name') ?></h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/trial-manager/logout">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Player Search Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="search-section p-4">
                        <h3 class="mb-3"><i class="fas fa-search me-2"></i>Player Verification</h3>
                        <p class="mb-4">Enter player's mobile number to verify their registration status</p>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" id="mobileInput" class="form-control form-control-lg" 
                                       placeholder="Enter 10-digit mobile number" maxlength="10">
                            </div>
                            <div class="col-md-4">
                                <button onclick="searchPlayer()" class="btn btn-light btn-lg w-100">
                                    <i class="fas fa-search me-2"></i>Search Player
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Player Information Section -->
        <div id="playerInfoSection" class="row" style="display: none;">
            <div class="col-12">
                <div class="card player-info-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Player Information</h5>
                    </div>
                    <div class="card-body" id="playerInfoContent">
                        <!-- Player info will be loaded here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Manual Registration Section -->
        <div id="manualRegistrationSection" class="row" style="display: none;">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Manual Player Registration</h5>
                    </div>
                    <div class="card-body">
                        <form id="manualRegistrationForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Player Name</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Mobile Number</label>
                                        <input type="text" name="mobile" id="manualMobile" class="form-control" 
                                               maxlength="10" required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email (Optional)</label>
                                        <input type="email" name="email" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Age</label>
                                        <input type="number" name="age" class="form-control" min="11" max="49" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Cricket Type</label>
                                        <select name="cricket_type" class="form-select" required>
                                            <option value="">Select Cricket Type</option>
                                            <option value="bowler">Bowler (₹999 + ₹199 T-shirt)</option>
                                            <option value="batsman">Batsman (₹999 + ₹199 T-shirt)</option>
                                            <option value="all-rounder">All-rounder (₹1199 + ₹199 T-shirt)</option>
                                            <option value="wicket-keeper">Wicket-keeper (₹1199 + ₹199 T-shirt)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Method</label>
                                        <select name="payment_method" class="form-select" required>
                                            <option value="offline">Offline (Cash)</option>
                                            <option value="online">Online</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Register Player
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Modal -->
    <div class="modal fade" id="resultModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function searchPlayer() {
            const mobile = document.getElementById('mobileInput').value.trim();
            
            if (mobile.length !== 10 || !/^\d+$/.test(mobile)) {
                showModal('Error', 'Please enter a valid 10-digit mobile number.', 'danger');
                return;
            }

            fetch('/trial-manager/search-player', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ mobile: mobile })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.found) {
                    showPlayerInfo(data.player, data.fees);
                } else {
                    showManualRegistration(mobile);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showModal('Error', 'An error occurred while searching for the player.', 'danger');
            });
        }

        function showPlayerInfo(player, fees) {
            const infoHtml = `
                <div class="row">
                    <div class="col-md-8">
                        <h6><strong>Name:</strong> ${player.name}</h6>
                        <p><strong>Mobile:</strong> ${player.mobile}</p>
                        <p><strong>Email:</strong> ${player.email || 'Not provided'}</p>
                        <p><strong>Age:</strong> ${player.age}</p>
                        <p><strong>Cricket Type:</strong> ${player.cricket_type}</p>
                        <p><strong>Current Status:</strong> 
                            <span class="badge bg-${getStatusBadge(player.payment_status)}">
                                ${getStatusText(player.payment_status)}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <div class="fee-info">
                            <h6>Fee Structure:</h6>
                            <p>Trial Fee: ₹${fees.trial}</p>
                            <p>T-shirt Fee: ₹${fees.tshirt}</p>
                            <hr>
                            <h6>Total: ₹${fees.total}</h6>
                        </div>
                    </div>
                </div>
                ${getPaymentActions(player, fees)}
            `;
            
            document.getElementById('playerInfoContent').innerHTML = infoHtml;
            document.getElementById('playerInfoSection').style.display = 'block';
            document.getElementById('manualRegistrationSection').style.display = 'none';
        }

        function showManualRegistration(mobile) {
            document.getElementById('manualMobile').value = mobile;
            document.getElementById('playerInfoSection').style.display = 'none';
            document.getElementById('manualRegistrationSection').style.display = 'block';
        }

        function getStatusBadge(status) {
            switch(status) {
                case 'full': return 'success';
                case 'partial': return 'warning';
                case 'no_payment': return 'danger';
                default: return 'secondary';
            }
        }

        function getStatusText(status) {
            switch(status) {
                case 'full': return 'Full Payment';
                case 'partial': return 'Partial Payment';
                case 'no_payment': return 'No Payment';
                default: return 'Unknown';
            }
        }

        function getPaymentActions(player, fees) {
            if (player.payment_status === 'full') {
                return `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Player is ready for trial!</strong> Full payment received.
                    </div>
                `;
            } else {
                const remainingAmount = player.payment_status === 'partial' ? fees.total - 199 : fees.total;
                return `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Payment Required:</strong> ₹${remainingAmount}
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="number" id="paymentAmount" class="form-control" 
                                   placeholder="Enter amount" value="${remainingAmount}">
                        </div>
                        <div class="col-md-6">
                            <select id="paymentMethod" class="form-select">
                                <option value="offline">Offline (Cash)</option>
                                <option value="online">Online</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button onclick="collectPayment(${player.id})" class="btn btn-success">
                            <i class="fas fa-money-bill me-2"></i>Collect Payment
                        </button>
                    </div>
                `;
            }
        }

        function collectPayment(playerId) {
            const amount = document.getElementById('paymentAmount').value;
            const method = document.getElementById('paymentMethod').value;
            
            if (!amount || amount <= 0) {
                showModal('Error', 'Please enter a valid amount.', 'danger');
                return;
            }

            fetch('/trial-manager/collect-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    player_id: playerId,
                    amount: amount,
                    payment_method: method
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showModal('Success', data.message, 'success');
                    // Refresh the search to show updated status
                    searchPlayer();
                } else {
                    showModal('Error', data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showModal('Error', 'An error occurred while processing payment.', 'danger');
            });
        }

        // Manual registration form submission
        document.getElementById('manualRegistrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            fetch('/trial-manager/register-player', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showModal('Success', data.message, 'success');
                    this.reset();
                    document.getElementById('manualRegistrationSection').style.display = 'none';
                    document.getElementById('mobileInput').value = '';
                } else {
                    showModal('Error', data.message || 'Registration failed', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showModal('Error', 'An error occurred during registration.', 'danger');
            });
        });

        function showModal(title, message, type) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalBody').innerHTML = `
                <div class="alert alert-${type === 'success' ? 'success' : 'danger'}">
                    ${message}
                </div>
            `;
            new bootstrap.Modal(document.getElementById('resultModal')).show();
        }

        // Enter key support for mobile input
        document.getElementById('mobileInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchPlayer();
            }
        });
    </script>
</body>
</html>
