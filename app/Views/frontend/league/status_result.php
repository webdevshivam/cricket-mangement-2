<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>League Status - MPCL</title>
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
        .status-paid {
            color: #28a745;
        }
        .status-unpaid {
            color: #dc3545;
        }
        .info-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #ffd700;
        }
    </style>
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
        <div class="row justify-content-center w-100">
            <div class="col-md-8 col-lg-6">
                <div class="card status-card">
                    <div class="card-header text-center border-0 pt-4">
                        <h2 class="text-golden mb-0">
                            <i class="fas fa-user-check me-2"></i>League Registration Status
                        </h2>
                    </div>

                    <div class="card-body px-4 pb-4">
                        <div class="info-item">
                            <h5 class="text-golden mb-3">Player Information</h5>
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="text-light mb-2">
                                        <strong>Name:</strong> <?= esc($player['name']) ?>
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Mobile:</strong> <?= esc($player['mobile']) ?>
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Email:</strong> <?= esc($player['email']) ?>
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-light mb-2">
                                        <strong>Age:</strong> <?= esc($player['age']) ?> years
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Cricketer Type:</strong> <?= esc(ucfirst(str_replace('-', ' ', $player['cricketer_type']))) ?>
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Age Group:</strong> <?= esc(ucfirst(str_replace('_', ' ', $player['age_group']))) ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <h5 class="text-golden mb-3">Payment Status</h5>
                            <span class="<?= $player['payment_status'] == 'paid' ? 'status-paid' : 'status-unpaid' ?>">
                                        <?= $player['payment_status'] == 'paid' ? 'Paid' : 'Unpaid' ?>
                                    </span>
                        </div>

                        <?php if ($grade): ?>
                        <div class="info-item">
                            <h5 class="text-golden mb-3">Grade Assignment</h5>
                            <p class="text-light">
                                <i class="fas fa-award me-2"></i>
                                <strong>Assigned Grade:</strong> <?= esc($grade['grade_name']) ?>
                            </p>
                            <?php if (!empty($grade['description'])): ?>
                                <p class="text-light small">
                                    <?= esc($grade['description']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="info-item">
                            <h5 class="text-golden mb-3">Registration Details</h5>
                            <p class="text-light mb-2">
                                <strong>Registered On:</strong> <?= date('d M Y, h:i A', strtotime($player['created_at'])) ?>
                            </p>
                            <p class="text-light mb-0">
                                <strong>Registration ID:</strong> MPCL<?= str_pad($player['id'], 4, '0', STR_PAD_LEFT) ?>
                            </p>
                        </div>

                        <div class="text-center mt-4">
                            <a href="<?= base_url('league-status') ?>" class="btn btn-golden me-3">
                                <i class="fas fa-search me-2"></i>Check Another
                            </a>
                            <a href="<?= base_url('league-registration') ?>" class="btn btn-outline-warning">
                                <i class="fas fa-plus me-2"></i>New Registration
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
```

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>League Status - MPCL</title>
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
        .status-paid {
            color: #28a745;
        }
        .status-unpaid {
            color: #dc3545;
        }
        .info-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #ffd700;
        }
    </style>
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
        <div class="row justify-content-center w-100">
            <div class="col-md-8 col-lg-6">
                <div class="card status-card">
                    <div class="card-header text-center border-0 pt-4">
                        <h2 class="text-golden mb-0">
                            <i class="fas fa-user-check me-2"></i>League Registration Status
                        </h2>
                    </div>

                    <div class="card-body px-4 pb-4">
                        <div class="info-item">
                            <h5 class="text-golden mb-3">Player Information</h5>
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="text-light mb-2">
                                        <strong>Name:</strong> <?= esc($player['name']) ?>
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Mobile:</strong> <?= esc($player['mobile']) ?>
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Email:</strong> <?= esc($player['email']) ?>
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-light mb-2">
                                        <strong>Age:</strong> <?= esc($player['age']) ?> years
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Cricketer Type:</strong> <?= esc(ucfirst(str_replace('-', ' ', $player['cricketer_type']))) ?>
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Age Group:</strong> <?= esc(ucfirst(str_replace('_', ' ', $player['age_group']))) ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        
                            
                                
                                    
                                        
                                        
                                            
                                                <?php 
                                                    switch($player['payment_status']) {
                                                        case 'no_payment':
                                                            echo 'No Payment';
                                                            break;
                                                        case 'partial':
                                                            echo 'Partial Paid';
                                                            break;
                                                        case 'full':
                                                            echo 'Full Paid';
                                                            break;
                                                        default:
                                                            echo ucfirst($player['payment_status']);
                                                    }
                                                ?>
                                            
                                        
                                    
                                

                        <?php if ($grade): ?>
                        <div class="info-item">
                            <h5 class="text-golden mb-3">Grade Assignment</h5>
                            <p class="text-light">
                                <i class="fas fa-award me-2"></i>
                                <strong>Assigned Grade:</strong> <?= esc($grade['grade_name']) ?>
                            </p>
                            <?php if (!empty($grade['description'])): ?>
                                <p class="text-light small">
                                    <?= esc($grade['description']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="info-item">
                            <h5 class="text-golden mb-3">Registration Details</h5>
                            <p class="text-light mb-2">
                                <strong>Registered On:</strong> <?= date('d M Y, h:i A', strtotime($player['created_at'])) ?>
                            </p>
                            <p class="text-light mb-0">
                                <strong>Registration ID:</strong> MPCL<?= str_pad($player['id'], 4, '0', STR_PAD_LEFT) ?>
                            </p>
                        </div>

                        <div class="text-center mt-4">
                            <a href="<?= base_url('league-status') ?>" class="btn btn-golden me-3">
                                <i class="fas fa-search me-2"></i>Check Another
                            </a>
                            <a href="<?= base_url('league-registration') ?>" class="btn btn-outline-warning">
                                <i class="fas fa-plus me-2"></i>New Registration
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
```

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>League Status - MPCL</title>
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
        .status-paid {
            color: #28a745;
        }
        .status-unpaid {
            color: #dc3545;
        }
        .info-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #ffd700;
        }
    </style>
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
        <div class="row justify-content-center w-100">
            <div class="col-md-8 col-lg-6">
                <div class="card status-card">
                    <div class="card-header text-center border-0 pt-4">
                        <h2 class="text-golden mb-0">
                            <i class="fas fa-user-check me-2"></i>League Registration Status
                        </h2>
                    </div>

                    <div class="card-body px-4 pb-4">
                        <div class="info-item">
                            <h5 class="text-golden mb-3">Player Information</h5>
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="text-light mb-2">
                                        <strong>Name:</strong> <?= esc($player['name']) ?>
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Mobile:</strong> <?= esc($player['mobile']) ?>
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Email:</strong> <?= esc($player['email']) ?>
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-light mb-2">
                                        <strong>Age:</strong> <?= esc($player['age']) ?> years
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Cricketer Type:</strong> <?= esc(ucfirst(str_replace('-', ' ', $player['cricketer_type']))) ?>
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Age Group:</strong> <?= esc(ucfirst(str_replace('_', ' ', $player['age_group']))) ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        

                        <?php if ($grade): ?>
                        <div class="info-item">
                            <h5 class="text-golden mb-3">Grade Assignment</h5>
                            <p class="text-light">
                                <i class="fas fa-award me-2"></i>
                                <strong>Assigned Grade:</strong> <?= esc($grade['grade_name']) ?>
                            </p>
                            <?php if (!empty($grade['description'])): ?>
                                <p class="text-light small">
                                    <?= esc($grade['description']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="info-item">
                            <h5 class="text-golden mb-3">Registration Details</h5>
                            <p class="text-light mb-2">
                                <strong>Registered On:</strong> <?= date('d M Y, h:i A', strtotime($player['created_at'])) ?>
                            </p>
                            <p class="text-light mb-0">
                                <strong>Registration ID:</strong> MPCL<?= str_pad($player['id'], 4, '0', STR_PAD_LEFT) ?>
                            </p>
                        </div>

                        <div class="text-center mt-4">
                            <a href="<?= base_url('league-status') ?>" class="btn btn-golden me-3">
                                <i class="fas fa-search me-2"></i>Check Another
                            </a>
                            <a href="<?= base_url('league-registration') ?>" class="btn btn-outline-warning">
                                <i class="fas fa-plus me-2"></i>New Registration
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
```

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>League Status - MPCL</title>
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
        .status-paid {
            color: #28a745;
        }
        .status-unpaid {
            color: #dc3545;
        }
        .info-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #ffd700;
        }
    </style>
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
        <div class="row justify-content-center w-100">
            <div class="col-md-8 col-lg-6">
                <div class="card status-card">
                    <div class="card-header text-center border-0 pt-4">
                        <h2 class="text-golden mb-0">
                            <i class="fas fa-user-check me-2"></i>League Registration Status
                        </h2>
                    </div>

                    <div class="card-body px-4 pb-4">
                        <div class="info-item">
                            <h5 class="text-golden mb-3">Player Information</h5>
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="text-light mb-2">
                                        <strong>Name:</strong> <?= esc($player['name']) ?>
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Mobile:</strong> <?= esc($player['mobile']) ?>
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Email:</strong> <?= esc($player['email']) ?>
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-light mb-2">
                                        <strong>Age:</strong> <?= esc($player['age']) ?> years
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Cricketer Type:</strong> <?= esc(ucfirst(str_replace('-', ' ', $player['cricketer_type']))) ?>
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Age Group:</strong> <?= esc(ucfirst(str_replace('_', ' ', $player['age_group']))) ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <h5 class="text-golden mb-3">Status Message</h5>
                            <?php if ($player['payment_status'] == 'paid'): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Congratulations!</strong> You have paid all the fees. Your match will be scheduled soon and we will inform you.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Payment Pending!</strong> Please complete your payment to participate in the league.
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($grade): ?>
                        <div class="info-item">
                            <h5 class="text-golden mb-3">Grade Assignment</h5>
                            <p class="text-light">
                                <i class="fas fa-award me-2"></i>
                                <strong>Assigned Grade:</strong> <?= esc($grade['grade_name']) ?>
                            </p>
                            <?php if (!empty($grade['description'])): ?>
                                <p class="text-light small">
                                    <?= esc($grade['description']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="info-item">
                            <h5 class="text-golden mb-3">Registration Details</h5>
                            <p class="text-light mb-2">
                                <strong>Registered On:</strong> <?= date('d M Y, h:i A', strtotime($player['created_at'])) ?>
                            </p>
                            <p class="text-light mb-0">
                                <strong>Registration ID:</strong> MPCL<?= str_pad($player['id'], 4, '0', STR_PAD_LEFT) ?>
                            </p>
                        </div>

                        <div class="text-center mt-4">
                            <a href="<?= base_url('league-status') ?>" class="btn btn-golden me-3">
                                <i class="fas fa-search me-2"></i>Check Another
                            </a>
                            <a href="<?= base_url('league-registration') ?>" class="btn btn-outline-warning">
                                <i class="fas fa-plus me-2"></i>New Registration
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
```

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>League Status - MPCL</title>
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
        .status-paid {
            color: #28a745;
        }
        .status-unpaid {
            color: #dc3545;
        }
        .info-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #ffd700;
        }
    </style>
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
        <div class="row justify-content-center w-100">
            <div class="col-md-8 col-lg-6">
                <div class="card status-card">
                    <div class="card-header text-center border-0 pt-4">
                        <h2 class="text-golden mb-0">
                            <i class="fas fa-user-check me-2"></i>League Registration Status
                        </h2>
                    </div>

                    <div class="card-body px-4 pb-4">
                        <div class="info-item">
                            <h5 class="text-golden mb-3">Player Information</h5>
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="text-light mb-2">
                                        <strong>Name:</strong> <?= esc($player['name']) ?>
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Mobile:</strong> <?= esc($player['mobile']) ?>
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Email:</strong> <?= esc($player['email']) ?>
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-light mb-2">
                                        <strong>Age:</strong> <?= esc($player['age']) ?> years
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Cricketer Type:</strong> <?= esc(ucfirst(str_replace('-', ' ', $player['cricketer_type']))) ?>
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Age Group:</strong> <?= esc(ucfirst(str_replace('_', ' ', $player['age_group']))) ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <h5 class="text-golden mb-3">Payment Status</h5>
                            
                                    
                                        <?= $player['payment_status'] == 'paid' ? 'Paid' : 'Unpaid' ?>
                                    
                                
                        </div>

                        <?php if ($grade): ?>
                        <div class="info-item">
                            <h5 class="text-golden mb-3">Grade Assignment</h5>
                            <p class="text-light">
                                <i class="fas fa-award me-2"></i>
                                <strong>Assigned Grade:</strong> <?= esc($grade['grade_name']) ?>
                            </p>
                            <?php if (!empty($grade['description'])): ?>
                                <p class="text-light small">
                                    <?= esc($grade['description']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="info-item">
                            <h5 class="text-golden mb-3">Registration Details</h5>
                            <p class="text-light mb-2">
                                <strong>Registered On:</strong> <?= date('d M Y, h:i A', strtotime($player['created_at'])) ?>
                            </p>
                            <p class="text-light mb-0">
                                <strong>Registration ID:</strong> MPCL<?= str_pad($player['id'], 4, '0', STR_PAD_LEFT) ?>
                            </p>
                        </div>

                        <div class="text-center mt-4">
                            <a href="<?= base_url('league-status') ?>" class="btn btn-golden me-3">
                                <i class="fas fa-search me-2"></i>Check Another
                            </a>
                            <a href="<?= base_url('league-registration') ?>" class="btn btn-outline-warning">
                                <i class="fas fa-plus me-2"></i>New Registration
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
```

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>League Status - MPCL</title>
    <link href="https://cdn.jsdelivr.net```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>League Status - MPCL</title>
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
        .status-paid {
            color: #28a745;
        }
        .status-unpaid {
            color: #dc3545;
        }
        .info-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #ffd700;
        }
    </style>
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
        <div class="row justify-content-center w-100">
            <div class="col-md-8 col-lg-6">
                <div class="card status-card">
                    <div class="card-header text-center border-0 pt-4">
                        <h2 class="text-golden mb-0">
                            <i class="fas fa-user-check me-2"></i>League Registration Status
                        </h2>
                    </div>

                    <div class="card-body px-4 pb-4">
                        <div class="info-item">
                            <h5 class="text-golden mb-3">Player Information</h5>
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="text-light mb-2">
                                        <strong>Name:</strong> <?= esc($player['name']) ?>
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Mobile:</strong> <?= esc($player['mobile']) ?>
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Email:</strong> <?= esc($player['email']) ?>
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-light mb-2">
                                        <strong>Age:</strong> <?= esc($player['age']) ?> years
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Cricketer Type:</strong> <?= esc(ucfirst(str_replace('-', ' ', $player['cricketer_type']))) ?>
                                    </p>
                                    <p class="text-light mb-2">
                                        <strong>Age Group:</strong> <?= esc(ucfirst(str_replace('_', ' ', $player['age_group']))) ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <h5 class="text-golden mb-3">Payment Status</h5>
                            
                                    
                                        <?= $player['payment_status'] == 'paid' ? 'Paid' : 'Unpaid' ?>
                                    
                                
                        </div>

                        <?php if ($grade): ?>
                        <div class="info-item">
                            <h5 class="text-golden mb-3">Grade Assignment</h5>
                            <p class="text-light">
                                <i class="fas fa-award me-2"></i>
                                <strong>Assigned Grade:</strong> <?= esc($grade['grade_name']) ?>
                            </p>
                            <?php if (!empty($grade['description'])): ?>
                                <p class="text-light small">
                                    <?= esc($grade['description']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="info-item">
                            <h5 class="text-golden mb-3">Registration Details</h5>
                            <p class="text-light mb-2">
                                <strong>Registered On:</strong> <?= date('d M Y, h:i A', strtotime($player['created_at'])) ?>
                            </p>
                            <p class="text-light mb-0">
                                <strong>Registration ID:</strong> MPCL<?= str_pad($player['id'], 4, '0', STR_PAD_LEFT) ?>
                            </p>
                        </div>

                        <div class="text-center mt-4">
                            <a href="<?= base_url('league-status') ?>" class="btn btn-golden me-3">
                                <i class="fas fa-search me-2"></i>Check Another
                            </a>
                            <a href="<?= base_url('league-registration') ?>" class="btn btn-outline-warning">
                                <i class="fas fa-plus me-2"></i>New Registration
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
```
Analysis: The payment status display and message have been updated to use 'paid' and 'unpaid' instead of 'full', 'partial', and 'no_payment'.