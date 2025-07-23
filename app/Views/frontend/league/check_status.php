
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check League Status - MPCL</title>
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

        .search-section {
            background: rgba(212, 175, 55, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            position: relative;
        }

        .search-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--primary-gold), transparent);
            opacity: 0.3;
        }

        .form-label {
            color: var(--primary-gold);
            font-weight: 500;
            font-size: 0.95rem;
            margin-bottom: 0.75rem;
            letter-spacing: 0.02em;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: var(--text-light);
            border-radius: 12px;
            padding: 0.875rem 1.25rem;
            font-size: 1rem;
            font-weight: 400;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: var(--primary-gold);
            box-shadow: 
                0 0 0 0.2rem rgba(212, 175, 55, 0.15),
                0 8px 25px rgba(212, 175, 55, 0.1);
            color: var(--text-light);
            transform: translateY(-1px);
        }

        .form-control::placeholder {
            color: var(--text-muted);
            font-weight: 400;
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
        }

        .btn-golden:hover::before {
            left: 100%;
        }

        .btn-golden:active {
            transform: translateY(0);
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

        .info-section {
            text-align: center;
            margin-top: 3rem;
        }

        .info-section h3 {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            color: var(--text-light);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .info-section p {
            color: var(--text-muted);
            font-size: 1rem;
            line-height: 1.6;
            max-width: 500px;
            margin: 0 auto;
        }

        .feature-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .feature-item {
            background: rgba(212, 175, 55, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            background: rgba(212, 175, 55, 0.08);
            border-color: rgba(212, 175, 55, 0.2);
            transform: translateY(-2px);
        }

        .feature-item i {
            color: var(--primary-gold);
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .feature-item h5 {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }

        .feature-item p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .card-header h2 {
                font-size: 1.75rem;
            }
            
            .card-body,
            .search-section {
                padding: 1.5rem;
            }
            
            .btn-golden,
            .btn-outline-golden {
                width: 100%;
                margin-bottom: 1rem;
            }
            
            .feature-list {
                grid-template-columns: 1fr;
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
            .search-section {
                padding: 1rem;
            }
        }

        /* Loading Animation */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 24px;
            height: 24px;
            border: 2px solid var(--primary-gold);
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            transform: translate(-50%, -50%);
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
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
            <div class="col-xl-5 col-lg-6 col-md-8 col-sm-10">
                <div class="card status-card">
                    <div class="card-header">
                        <div class="icon-wrapper mx-auto">
                            <i class="fas fa-search"></i>
                        </div>
                        <h2>League Status Check</h2>
                        <p class="subtitle">Enter your mobile number to check registration status</p>
                    </div>

                    <div class="card-body">
                        <div class="search-section">
                            <form method="POST" action="<?= base_url('league-status-check') ?>" id="statusForm">
                                <div class="mb-4">
                                    <label for="mobile" class="form-label">
                                        <i class="fas fa-mobile-alt me-2"></i>Mobile Number
                                    </label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="mobile" 
                                        name="mobile" 
                                        placeholder="Enter your 10-digit mobile number"
                                        pattern="[0-9]{10}" 
                                        maxlength="10"
                                        required
                                    >
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-golden">
                                        <i class="fas fa-search me-2"></i>Check Status
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="info-section">
                            <h3>What you can check</h3>
                            <div class="feature-list">
                                <div class="feature-item">
                                    <i class="fas fa-user-check"></i>
                                    <h5>Registration Status</h5>
                                    <p>Check if your league registration is complete</p>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-credit-card"></i>
                                    <h5>Payment Status</h5>
                                    <p>View your payment status and pending fees</p>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-award"></i>
                                    <h5>Grade Assignment</h5>
                                    <p>See your assigned grade and league details</p>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <p class="text-muted mb-3">Don't have an account yet?</p>
                            <a href="<?= base_url('league-registration') ?>" class="btn btn-outline-golden">
                                <i class="fas fa-plus me-2"></i>Register for League
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation and enhancement
        document.getElementById('statusForm').addEventListener('submit', function(e) {
            const mobileInput = document.getElementById('mobile');
            const mobileValue = mobileInput.value.trim();
            
            // Mobile number validation
            if (!/^[0-9]{10}$/.test(mobileValue)) {
                e.preventDefault();
                mobileInput.focus();
                mobileInput.classList.add('is-invalid');
                
                // Remove invalid class after user starts typing
                mobileInput.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                }, { once: true });
                
                return false;
            }
            
            // Add loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Checking...';
            submitBtn.disabled = true;
            
            // Re-enable button after 5 seconds (failsafe)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });

        // Mobile number input formatting
        document.getElementById('mobile').addEventListener('input', function(e) {
            // Remove any non-digit characters
            this.value = this.value.replace(/\D/g, '');
            
            // Limit to 10 digits
            if (this.value.length > 10) {
                this.value = this.value.slice(0, 10);
            }
        });

        // Auto-focus on mobile input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('mobile').focus();
        });
    </script>
</body>
</html>
