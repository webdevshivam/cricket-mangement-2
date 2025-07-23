
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Grade Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .grade-card {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            border-radius: 15px;
        }
        .player-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 8px 15px;
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-circle text-primary fa-3x mb-3"></i>
                            <h2 class="card-title">Player Details</h2>
                        </div>

                        <!-- Player Information -->
                        <div class="player-info mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-1">Name</h6>
                                    <p class="h5 mb-3"><?= esc($player['name']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-1">Mobile</h6>
                                    <p class="h5 mb-3"><?= esc($player['mobile']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-1">Age</h6>
                                    <p class="h5 mb-3"><?= esc($player['age']) ?> years</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-1">Cricket Type</h6>
                                    <p class="h5 mb-3"><?= ucfirst(esc($player['cricket_type'])) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-1">Payment Status</h6>
                                    <span class="badge <?= $player['payment_status'] == 'full' ? 'bg-success' : ($player['payment_status'] == 'partial' ? 'bg-warning' : 'bg-danger') ?> status-badge">
                                        <?= ucfirst(str_replace('_', ' ', esc($player['payment_status']))) ?>
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-1">Registration Date</h6>
                                    <p class="h6 mb-3"><?= date('d M Y', strtotime($player['created_at'])) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Grade Information -->
                        <?php if ($grade): ?>
                            <div class="grade-card p-4 text-center">
                                <i class="fas fa-trophy fa-3x mb-3"></i>
                                <h3 class="mb-2">Congratulations!</h3>
                                <h4 class="mb-3">You have been assigned to</h4>
                                <h2 class="fw-bold mb-3"><?= esc($grade['title']) ?></h2>
                                <p class="mb-2"><?= esc($grade['description']) ?></p>
                                <h5 class="mb-0">League Fee: ₹<?= esc($grade['league_fee']) ?></h5>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <h5>Grade Not Assigned Yet</h5>
                                <p class="mb-0">Your grade will be assigned soon. Please check back later or contact the administration.</p>
                            </div>
                        <?php endif; ?>

                        <!-- Action Buttons -->
                        <div class="text-center mt-4">
                            <a href="<?= site_url('grades/check') ?>" class="btn btn-outline-primary">
                                <i class="fas fa-search me-2"></i>Check Another Number
                            </a>
                            <?php if ($grade): ?>
                                <button class="btn btn-success ms-2" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>Print Details
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
