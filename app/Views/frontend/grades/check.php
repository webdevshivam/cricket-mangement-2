
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Your Grade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
        }
        
        .grade-check-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .grade-card {
            background: linear-gradient(145deg, #1a1a1a, #000000);
            border: 2px solid #ffd700;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(255, 215, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }
        
        .grade-card h2 {
            color: #ffd700;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .grade-card p {
            color: #cccccc;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-label {
            color: #ffd700;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .form-control {
            background-color: #2a2a2a;
            border: 2px solid #444;
            border-radius: 10px;
            color: #ffffff;
            padding: 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            background-color: #2a2a2a;
            border-color: #ffd700;
            box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25);
            color: #ffffff;
        }
        
        .form-control::placeholder {
            color: #888;
        }
        
        .form-text {
            color: #aaa;
            font-size: 14px;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            border: none;
            color: #000;
            font-weight: bold;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 18px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #ffed4e, #ffd700);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 215, 0, 0.3);
            color: #000;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background-color: #dc3545;
            color: #fff;
        }
        
        .alert-success {
            background-color: #28a745;
            color: #fff;
        }
        
        .info-text {
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid #ffd700;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin-top: 20px;
        }
        
        .info-text small {
            color: #ffd700;
            font-size: 14px;
        }
        
        .header-icon {
            color: #ffd700;
            font-size: 3rem;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="grade-check-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="grade-card">
                        <div class="text-center header-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        
                        <div class="card-header text-center">
                            <h2 class="card-title">Check Your Grade</h2>
                            <p class="text-muted">Enter your mobile number to view your assigned grade</p>
                        </div>

                        <!-- Flash Messages -->
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= session()->getFlashdata('success') ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= site_url('grades/check-mobile') ?>" method="post">
                            <?= csrf_field(); ?>
                            
                            <div class="mb-4">
                                <label for="mobile" class="form-label">
                                    <i class="fas fa-mobile-alt me-2"></i>Mobile Number
                                </label>
                                <input type="tel" class="form-control" id="mobile" name="mobile" 
                                       placeholder="Enter your mobile number" maxlength="10" required>
                                <div class="form-text">Enter the mobile number you used during trial registration</div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-search me-2"></i>Check My Grade
                                </button>
                            </div>
                        </form>

                        <div class="info-text">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                Grades are assigned only to verified trial players
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
