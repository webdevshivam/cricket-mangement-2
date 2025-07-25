
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trial Status Found - MPCL</title>
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
            --info-color: #17a2b8;
            --warning-color: #ffc107;
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
            background: linear-gradient(135deg, var(--success-color), #20c997);
            border-bottom: none;
            padding: 2rem;
            text-align: center;
            border-radius: 20px 20px 0 0;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .card-header h2 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 2rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .card-header .subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            font-weight: 400;
        }

        .card-body {
            padding: 2rem;
        }

        .status-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 4rem;
            height: 4rem;
            background: linear-gradient(135deg, var(--success-color), #20c997);
            border-radius: 50%;
            margin-bottom: 1rem;
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
        }

        .status-icon i {
            color: var(--text-light);
            font-size: 1.5rem;
        }

        .info-section {
            background: rgba(212, 175, 55, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .info-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-group {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .info-group:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(212, 175, 55, 0.2);
            transform: translateY(-2px);
        }

        .info-label {
            color: var(--primary-gold);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-value {
            color: var(--text-light);
            font-size: 1.1rem;
            font-weight: 500;
            margin: 0;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .status-paid {
            background: linear-gradient(135deg, var(--success-color), #20c997);
            color: var(--text-light);
        }

        .status-partial {
            background: linear-gradient(135deg, var(--warning-color), #ffca2c);
            color: var(--primary-black);
        }

        .status-pending {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: var(--text-light);
        }

        .grade-section {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1), rgba(212, 175, 55, 0.05));
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .grade-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 5rem;
            height: 5rem;
            background: linear-gradient(135deg, var(--primary-gold), var(--light-gold));
            border-radius: 50%;
            margin-bottom: 1rem;
            box-shadow: 0 12px 25px rgba(212, 175, 55, 0.3);
        }

        .grade-icon i {
            color: var(--primary-black);
            font-size: 2rem;
        }

        .grade-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1.5rem;
            color: var(--primary-gold);
            margin-bottom: 0.5rem;
        }

        .grade-name {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 2rem;
            background: linear-gradient(135deg, var(--primary-gold), var(--light-gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }

        .motivation-section {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(32, 201, 151, 0.05));
            border: 1px solid rgba(40, 167, 69, 0.3);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .motivation-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--success-color), transparent);
            opacity: 0.6;
        }

        .motivation-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 4rem;
            height: 4rem;
            background: linear-gradient(135deg, var(--success-color), #20c997);
            border-radius: 50%;
            margin-bottom: 1rem;
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
        }

        .motivation-icon i {
            color: var(--text-light);
            font-size: 1.25rem;
        }

        .motivation-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1.25rem;
            color: var(--success-color);
            margin-bottom: 1rem;
        }

        .motivation-text {
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--text-light);
            line-height: 1.6;
            font-style: italic;
            position: relative;
        }

        .motivation-text::before,
        .motivation-text::after {
            content: '"';
            font-size: 2rem;
            color: var(--success-color);
            font-weight: 700;
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

        .btn-outline-success {
            background: transparent;
            border: 1.5px solid var(--success-color);
            color: var(--success-color);
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

        .btn-outline-success::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: var(--success-color);
            transition: width 0.3s ease;
            z-index: -1;
        }

        .btn-outline-success:hover {
            color: var(--text-light);
            border-color: var(--success-color);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.2);
            text-decoration: none;
        }

        .btn-outline-success:hover::before {
            width: 100%;
        }

        .actions-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .no-grade-message {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            color: var(--warning-color);
        }

        .no-grade-message i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.7;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .card-header h2 {
                font-size: 1.75rem;
            }

            .card-body,
            .info-section,
            .grade-section,
            .motivation-section {
                padding: 1.5rem;
            }

            .info-row {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .actions-section {
                grid-template-columns: 1fr;
            }

            .grade-name {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .card-header {
                padding: 1.5rem 1rem;
            }

            .card-header h2 {
                font-size: 1.5rem;
            }

            .card-body,
            .info-section,
            .grade-section,
            .motivation-section {
                padding: 1rem;
            }

            .info-group {
                padding: 1rem;
            }
        }

        /* Animation for status found */
        .fade-in {
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
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
            <div class="col-xl-8 col-lg-10 col-md-11">
                <div class="card status-card fade-in">
                    <div class="card-header">
                        <div class="status-icon mx-auto">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2>Trial Status Found</h2>
                        <p class="subtitle">Registration details retrieved successfully</p>
                    </div>

                    <div class="card-body">
                        <!-- Player Information Section -->
                        <div class="info-section">
                            <div class="info-row">
                                <div class="info-group">
                                    <div class="info-label">
                                        <i class="fas fa-user"></i>
                                        Player Name
                                    </div>
                                    <p class="info-value"><?= esc($player['name']) ?></p>
                                </div>

                                <div class="info-group">
                                    <div class="info-label">
                                        <i class="fas fa-mobile-alt"></i>
                                        Mobile Number
                                    </div>
                                    <p class="info-value"><?= esc($player['mobile']) ?></p>
                                </div>

                                <div class="info-group">
                                    <div class="info-label">
                                        <i class="fas fa-envelope"></i>
                                        Email Address
                                    </div>
                                    <p class="info-value"><?= esc($player['email']) ?></p>
                                </div>

                                <div class="info-group">
                                    <div class="info-label">
                                        <i class="fas fa-birthday-cake"></i>
                                        Age
                                    </div>
                                    <p class="info-value"><?= esc($player['age']) ?> years</p>
                                </div>

                                <div class="info-group">
                                    <div class="info-label">
                                        <i class="fas fa-map-marker-alt"></i>
                                        City
                                    </div>
                                    <p class="info-value"><?= esc($player['city']) ?></p>
                                </div>

                                <div class="info-group">
                                    <div class="info-label">
                                        <i class="fas fa-cricket"></i>
                                        Cricket Type
                                    </div>
                                    <p class="info-value"><?= esc(ucfirst($player['cricket_type'])) ?></p>
                                </div>

                                <div class="info-group">
                                    <div class="info-label">
                                        <i class="fas fa-credit-card"></i>
                                        Payment Status
                                    </div>
                                    <p class="info-value">
                                        <?php
                                        $paymentStatus = $player['payment_status'];
                                        $statusClass = '';
                                        $statusText = '';
                                        $statusIcon = '';

                                        switch($paymentStatus) {
                                            case 'paid':
                                            case 'full':
                                                $statusClass = 'status-paid';
                                                $statusText = 'Fully Paid';
                                                $statusIcon = 'fas fa-check-circle';
                                                break;
                                            case 'partial':
                                                $statusClass = 'status-partial';
                                                $statusText = 'Partially Paid';
                                                $statusIcon = 'fas fa-clock';
                                                break;
                                            default:
                                                $statusClass = 'status-pending';
                                                $statusText = 'Payment Pending';
                                                $statusIcon = 'fas fa-exclamation-circle';
                                        }
                                        ?>
                                        <span class="status-badge <?= $statusClass ?>">
                                            <i class="<?= $statusIcon ?>"></i>
                                            <?= $statusText ?>
                                        </span>
                                    </p>
                                </div>

                                <div class="info-group">
                                    <div class="info-label">
                                        <i class="fas fa-calendar-alt"></i>
                                        Registration Date
                                    </div>
                                    <p class="info-value"><?= date('d M Y', strtotime($player['created_at'])) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Grade Assignment Section -->
                        <?php if (isset($grade) && $grade): ?>
                            <div class="grade-section">
                                <div class="grade-icon mx-auto">
                                    <i class="fas fa-award"></i>
                                </div>
                                <h3 class="grade-title">Congratulations!</h3>
                                <div class="grade-name"><?= esc($grade['grade_name']) ?></div>
                                <p class="text-muted mb-0">You have been successfully assigned to this grade. Check your email for further instructions.</p>
                            </div>
                        <?php else: ?>
                            <div class="no-grade-message">
                                <i class="fas fa-hourglass-half"></i>
                                <h5 class="mb-2">Grade Assignment Pending</h5>
                                <p class="mb-0">Your grade assignment is under review. You will be notified once the evaluation is complete.</p>
                            </div>
                        <?php endif; ?>

                        <!-- Motivational Section -->
                        <div class="motivation-section">
                            <div class="motivation-icon mx-auto">
                                <i class="fas fa-heart"></i>
                            </div>
                            <h4 class="motivation-title">Stay Motivated</h4>
                            <p class="motivation-text"><?= esc($motivation) ?></p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="actions-section">
                            <a href="<?= base_url('trial-status') ?>" class="btn btn-outline-success">
                                <i class="fas fa-search"></i>
                                Check Another Status
                            </a>
                            <a href="<?= base_url('league-status') ?>" class="btn btn-golden">
                                <i class="fas fa-trophy"></i>
                                Check League Status
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add some interactive animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate info groups on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animation = 'fadeIn 0.6s ease-in-out';
                    }
                });
            }, observerOptions);

            // Observe all info groups
            document.querySelectorAll('.info-group').forEach(group => {
                observer.observe(group);
            });

            // Add hover effect to status badge
            const statusBadge = document.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.05)';
                });
                statusBadge.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            }
        });

        // Print functionality
        function printStatus() {
            window.print();
        }

        // Copy to clipboard functionality
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Could add a toast notification here
                console.log('Copied to clipboard: ' + text);
            });
        }
    </script>
</body>
</html>
