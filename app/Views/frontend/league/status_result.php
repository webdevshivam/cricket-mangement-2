<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>League Status - MPCL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gold: #d4af37;
            --dark-gold: #b8941f;
            --light-gold: #f4e197;
            --primary-black: #0a0a0a;
            --secondary-black: #1a1a1a;
            --accent-black: #2a2a2a;
            --text-light: #ffffff;
            --text-muted: #b3b3b3;
            --border-color: #333333;
            --shadow-color: rgba(212, 175, 55, 0.2);
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--primary-black) 0%, var(--secondary-black) 50%, var(--accent-black) 100%);
            min-height: 100vh;
            color: var(--text-light);
            font-weight: 400;
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container-fluid {
            position: relative;
            background: 
                radial-gradient(circle at 20% 20%, rgba(212, 175, 55, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(212, 175, 55, 0.05) 0%, transparent 50%),
                linear-gradient(135deg, transparent 0%, rgba(212, 175, 55, 0.02) 100%);
        }

        .status-card {
            background: linear-gradient(145deg, 
                rgba(26, 26, 26, 0.95) 0%, 
                rgba(42, 42, 42, 0.9) 50%, 
                rgba(26, 26, 26, 0.95) 100%
            );
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 20px;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(212, 175, 55, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
        }

        .status-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--primary-gold), transparent);
            opacity: 0.6;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
            padding: 2rem 2rem 1.5rem;
            text-align: center;
        }

        .card-header h2 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 2rem;
            background: linear-gradient(135deg, var(--primary-gold), var(--light-gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .card-header .subtitle {
            color: var(--text-muted);
            font-size: 1rem;
            font-weight: 400;
            margin-top: 0.5rem;
        }

        .card-body {
            padding: 2rem;
        }

        .info-section {
            background: rgba(212, 175, 55, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            position: relative;
        }

        .info-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--primary-gold), transparent);
            opacity: 0.3;
        }

        .section-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1.25rem;
            color: var(--primary-gold);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            font-size: 1.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .info-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(212, 175, 55, 0.1);
            border-radius: 12px;
            padding: 1.25rem;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(212, 175, 55, 0.2);
            transform: translateY(-2px);
        }

        .info-item-label {
            color: var(--text-muted);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .info-item-value {
            color: var(--text-light);
            font-size: 1.1rem;
            font-weight: 600;
            line-height: 1.4;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .status-paid {
            background: linear-gradient(135deg, var(--success-color), #20c997);
            color: white;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .status-unpaid {
            background: linear-gradient(135deg, var(--danger-color), #e74c3c);
            color: white;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .grade-badge {
            background: linear-gradient(135deg, var(--primary-gold), var(--light-gold));
            color: var(--primary-black);
            padding: 0.75rem 1.5rem;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1.2rem;
            letter-spacing: 0.1em;
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
        }

        .alert-modern {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            color: var(--text-light);
        }

        .alert-modern .alert-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--warning-color);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-golden {
            background: linear-gradient(135deg, var(--primary-gold) 0%, var(--light-gold) 100%);
            border: none;
            color: var(--primary-black);
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 12px;
            padding: 0.875rem 2rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            letter-spacing: 0.02em;
            text-transform: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-golden::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-golden:hover {
            background: linear-gradient(135deg, var(--light-gold) 0%, #fff5b7 100%);
            color: var(--primary-black);
            transform: translateY(-2px);
            box-shadow: 
                0 12px 25px rgba(212, 175, 55, 0.3),
                0 0 0 1px rgba(212, 175, 55, 0.2);
            text-decoration: none;
        }

        .btn-golden:hover::before {
            left: 100%;
        }

        .btn-outline-golden {
            background: transparent;
            border: 1.5px solid var(--primary-gold);
            color: var(--primary-gold);
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            border-radius: 12px;
            padding: 0.875rem 2rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-outline-golden::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: var(--primary-gold);
            transition: width 0.3s ease;
            z-index: -1;
        }

        .btn-outline-golden:hover {
            color: var(--primary-black);
            border-color: var(--primary-gold);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.2);
            text-decoration: none;
        }

        .btn-outline-golden:hover::before {
            width: 100%;
        }

        .icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 3rem;
            height: 3rem;
            background: linear-gradient(135deg, var(--primary-gold), var(--light-gold));
            border-radius: 12px;
            margin-bottom: 1rem;
        }

        .icon-wrapper i {
            color: var(--primary-black);
            font-size: 1.25rem;
        }

        .registration-id {
            background: rgba(212, 175, 55, 0.1);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: var(--primary-gold);
            letter-spacing: 0.1em;
            text-align: center;
            margin-top: 1rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .card-header h2 {
                font-size: 1.75rem;
            }

            .card-body,
            .info-section {
                padding: 1.5rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .btn-golden,
            .btn-outline-golden {
                width: 100%;
                margin-bottom: 1rem;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .card-header {
                padding: 1.5rem 1rem 1rem;
            }

            .card-header h2 {
                font-size: 1.5rem;
            }

            .card-body,
            .info-section {
                padding: 1rem;
            }
        }

        /* Selection styling */
        ::selection {
            background: var(--primary-gold);
            color: var(--primary-black);
        }

        ::-moz-selection {
            background: var(--primary-gold);
            color: var(--primary-black);
        }
    </style>
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="row justify-content-center w-100">
            <div class="col-xl-6 col-lg-8 col-md-10 col-sm-12">
                <div class="card status-card">
                    <div class="card-header">
                        <div class="icon-wrapper mx-auto">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <h2>League Registration Status</h2>
                        <p class="subtitle">Your registration details and current status</p>
                    </div>

                    <div class="card-body">
                        <!-- Player Information Section -->
                        <div class="info-section">
                            <h3 class="section-title">
                                <i class="fas fa-user"></i>
                                Player Information
                            </h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-item-label">Full Name</div>
                                    <div class="info-item-value"><?= esc($player['name']) ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-item-label">Mobile Number</div>
                                    <div class="info-item-value"><?= esc($player['mobile']) ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-item-label">Email Address</div>
                                    <div class="info-item-value"><?= esc($player['email']) ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-item-label">Age</div>
                                    <div class="info-item-value"><?= esc($player['age']) ?> years</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-item-label">Player Type</div>
                                    <div class="info-item-value"><?= esc(ucfirst(str_replace('-', ' ', $player['cricketer_type']))) ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-item-label">Age Category</div>
                                    <div class="info-item-value"><?= esc(ucfirst(str_replace('_', ' ', $player['age_group']))) ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Status Section -->
                        <div class="info-section">
                            <h3 class="section-title">
                                <i class="fas fa-credit-card"></i>
                                Payment Status
                            </h3>
                            <div class="text-center">
                                <div class="status-badge <?= $player['payment_status'] == 'paid' ? 'status-paid' : 'status-unpaid' ?>">
                                    <i class="fas <?= $player['payment_status'] == 'paid' ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                                    <?= $player['payment_status'] == 'paid' ? 'Payment Completed' : 'Payment Pending' ?>
                                </div>

                                <?php if ($player['payment_status'] != 'paid' && $grade): ?>
                                    <div class="alert-modern">
                                        <div class="alert-title">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Payment Required
                                        </div>
                                        <p class="mb-2">
                                            <strong>League Fee:</strong> ₹<?= esc($grade['league_fee']) ?>
                                        </p>
                                        <p class="mb-0">
                                            Please complete your payment of <strong>₹<?= esc($grade['league_fee']) ?></strong> to finalize your league registration.
                                        </p>
                                    </div>
                                <?php elseif ($player['payment_status'] != 'paid' && !$grade): ?>
                                    <div class="alert-modern">
                                        <div class="alert-title">
                                            <i class="fas fa-info-circle"></i>
                                            Grade Assignment Pending
                                        </div>
                                        <p class="mb-0">
                                            Your grade is currently being assigned. Fee details will be available once your grade is confirmed.
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Grade Assignment Section -->
                        <?php if ($grade): ?>
                        <div class="info-section">
                            <h3 class="section-title">
                                <i class="fas fa-award"></i>
                                Grade Assignment
                            </h3>
                            <div class="text-center">
                                <div class="grade-badge">
                                    Grade <?= esc($grade['title']) ?>
                                </div>
                                <?php if (!empty($grade['description'])): ?>
                                    <p class="text-muted mt-3 mb-0">
                                        <?= esc($grade['description']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Registration Details Section -->
                        <div class="info-section">
                            <h3 class="section-title">
                                <i class="fas fa-calendar-alt"></i>
                                Registration Details
                            </h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-item-label">Registration Date</div>
                                    <div class="info-item-value"><?= date('d M Y, h:i A', strtotime($player['created_at'])) ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-item-label">Registration ID</div>
                                    <div class="registration-id">
                                        MPCL<?= str_pad($player['id'], 4, '0', STR_PAD_LEFT) ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="text-center mt-4">
                            <a href="<?= base_url('league-status') ?>" class="btn btn-golden me-3">
                                <i class="fas fa-search"></i>
                                Check Another Status
                            </a>
                            <a href="<?= base_url('league-registration') ?>" class="btn btn-outline-golden">
                                <i class="fas fa-plus"></i>
                                New Registration
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add smooth animations on page load
        document.addEventListener('DOMContentLoaded', function() {
            const infoSections = document.querySelectorAll('.info-section');

            infoSections.forEach((section, index) => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    section.style.opacity = '1';
                    section.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
    </script>
</body>
</html>