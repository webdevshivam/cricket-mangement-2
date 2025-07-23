
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check League Status - MPCL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
        }
        .status-card {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #ffd700;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.3);
        }
        .text-golden {
            color: #ffd700 !important;
        }
        .btn-golden {
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            border: none;
            color: #000;
            font-weight: bold;
            border-radius: 25px;
            padding: 12px 30px;
            transition: all 0.3s ease;
        }
        .btn-golden:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
            color: #000;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #ffd700;
            color: #fff;
            border-radius: 10px;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: #ffd700;
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
            color: #fff;
        }
        .form-control::placeholder {
            color: #ccc;
        }
    </style>
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-4">
                <div class="card status-card">
                    <div class="card-header text-center border-0 pt-4">
                        <h2 class="text-golden mb-0">
                            <i class="fas fa-search me-2"></i>Check League Status
                        </h2>
                        <p class="text-light mt-2">Enter your mobile number to check your registration status</p>
                    </div>
                    
                    <div class="card-body px-4 pb-4">
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="<?= base_url('league-status-check') ?>" method="post">
                            <div class="mb-4">
                                <label for="mobile" class="form-label text-golden">
                                    <i class="fas fa-phone me-2"></i>Mobile Number
                                </label>
                                <input type="tel" class="form-control" id="mobile" name="mobile" 
                                       placeholder="Enter your 10-digit mobile number" 
                                       pattern="[0-9]{10}" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-golden">
                                    <i class="fas fa-search me-2"></i>Check Status
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <a href="<?= base_url('league-registration') ?>" class="text-golden text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>Back to Registration
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
