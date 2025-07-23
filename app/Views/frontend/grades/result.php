
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Grade Result</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
        }
        
        .grade-result-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .result-card {
            background: linear-gradient(145deg, #1a1a1a, #000000);
            border: 2px solid #ffd700;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(255, 215, 0, 0.1);
            padding: 40px;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        
        .player-info {
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid #ffd700;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .player-info h4 {
            color: #ffd700;
            margin-bottom: 15px;
        }
        
        .player-detail {
            color: #cccccc;
            margin-bottom: 8px;
        }
        
        .grade-card {
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            color: #000;
        }
        
        .grade-card i {
            color: #000;
            margin-bottom: 15px;
        }
        
        .grade-card h2, .grade-card h3, .grade-card h4, .grade-card h5 {
            color: #000;
            margin-bottom: 10px;
        }
        
        .no-grade-card {
            background: rgba(23, 162, 184, 0.1);
            border: 2px solid #17a2b8;
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
        }
        
        .no-grade-card i {
            color: #17a2b8;
            margin-bottom: 15px;
        }
        
        .no-grade-card h5 {
            color: #17a2b8;
            margin-bottom: 15px;
        }
        
        .no-grade-card p {
            color: #cccccc;
        }
        
        .btn-back {
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            border: none;
            color: #000;
            font-weight: bold;
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background: linear-gradient(45deg, #ffed4e, #ffd700);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 215, 0, 0.3);
            color: #000;
            text-decoration: none;
        }
        
        .header-icon {
            color: #ffd700;
            font-size: 3rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="grade-result-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="result-card">
                        <div class="header-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        
                        <!-- Player Information -->
                        <div class="player-info">
                            <h4><i class="fas fa-user me-2"></i>Player Information</h4>
                            <div class="player-detail">
                                <strong>Name:</strong> <?= esc($player['name']) ?>
                            </div>
                            <div class="player-detail">
                                <strong>Mobile:</strong> <?= esc($player['mobile']) ?>
                            </div>
                            <div class="player-detail">
                                <strong>Email:</strong> <?= esc($player['email']) ?>
                            </div>
                            <?php if (!empty($player['age'])): ?>
                            <div class="player-detail">
                                <strong>Age:</strong> <?= esc($player['age']) ?> years
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Grade Information -->
                        <?php if ($grade): ?>
                            <div class="grade-card">
                                <i class="fas fa-trophy fa-3x mb-3"></i>
                                <h3 class="mb-2">Congratulations!</h3>
                                <h4 class="mb-3">You have been assigned to</h4>
                                <h2 class="fw-bold mb-3"><?= esc($grade['title']) ?></h2>
                                <p class="mb-2"><?= esc($grade['description']) ?></p>
                                <h5 class="mb-0">League Fee: ₹<?= esc($grade['league_fee']) ?></h5>
                            </div>
                        <?php else: ?>
                            <div class="no-grade-card">
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <h5>Grade Not Assigned Yet</h5>
                                <p class="mb-0">Your grade will be assigned soon. Please check back later or contact the administration.</p>
                            </div>
                        <?php endif; ?>

                        <a href="<?= site_url('grades/check') ?>" class="btn-back">
                            <i class="fas fa-arrow-left me-2"></i>Check Another Number
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
