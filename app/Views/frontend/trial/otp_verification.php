
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - MPCL Trial Registration</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets-frontend/trial/') ?>trial.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .verification-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .verification-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .verification-header {
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
            color: white;
            text-align: center;
            padding: 2rem;
            position: relative;
        }

        .verification-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20" fill="white" opacity="0.1"><polygon points="0,20 100,0 100,20"/></svg>');
            background-size: 100px 20px;
        }

        .verification-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .verification-icon i {
            font-size: 2.5rem;
        }

        .verification-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .verification-subtitle {
            opacity: 0.9;
            font-size: 1rem;
        }

        .verification-body {
            padding: 2.5rem;
        }

        .otp-container {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 2rem 0;
            flex-wrap: wrap;
        }

        .otp-input {
            width: 60px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid #e0e6ed;
            border-radius: 12px;
            background: #f8f9fa;
            transition: all 0.3s ease;
            outline: none;
        }

        .otp-input:focus {
            border-color: #ff6b35;
            background: white;
            box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.1);
            transform: scale(1.05);
        }

        .otp-input:valid {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.05);
        }

        .verify-btn {
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
            border: none;
            border-radius: 12px;
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 1rem;
        }

        .verify-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 107, 53, 0.3);
            color: white;
        }

        .resend-section {
            text-align: center;
            padding: 1.5rem 0;
            border-top: 1px solid #e0e6ed;
            margin-top: 1.5rem;
        }

        .resend-btn {
            background: none;
            border: 2px solid #6c757d;
            border-radius: 8px;
            padding: 10px 25px;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .resend-btn:hover {
            border-color: #ff6b35;
            color: #ff6b35;
            background: rgba(255, 107, 53, 0.05);
        }

        .back-link {
            color: #6c757d;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #ff6b35;
        }

        .alert-modern {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .email-display {
            background: rgba(255, 107, 53, 0.1);
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            margin-bottom: 1rem;
            font-weight: 600;
            color: #ff6b35;
        }

        @media (max-width: 480px) {
            .otp-container {
                gap: 8px;
            }
            
            .otp-input {
                width: 45px;
                height: 45px;
                font-size: 18px;
            }
            
            .verification-header {
                padding: 1.5rem;
            }
            
            .verification-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-card">
            <div class="verification-header">
                <div class="verification-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1 class="verification-title">Email Verification</h1>
                <p class="verification-subtitle">Please verify your email to continue</p>
            </div>

            <div class="verification-body">
                <div class="email-display">
                    <i class="fas fa-envelope me-2"></i>
                    OTP sent to: <?= esc($email) ?>
                </div>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-modern">
                        <i class="fas fa-check-circle"></i>
                        <div><?= session()->getFlashdata('success') ?></div>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-modern">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div><?= session()->getFlashdata('error') ?></div>
                    </div>
                <?php endif; ?>

                <form id="otpForm" action="<?= base_url('trial-verify-otp') ?>" method="post">
                    <div class="text-center mb-3">
                        <h6 class="text-muted">Enter 6-digit OTP</h6>
                    </div>
                    
                    <div class="otp-container">
                        <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required autocomplete="off">
                        <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required autocomplete="off">
                        <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required autocomplete="off">
                        <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required autocomplete="off">
                        <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required autocomplete="off">
                        <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required autocomplete="off">
                    </div>
                    <input type="hidden" name="otp" id="otpValue">
                    
                    <button type="submit" class="verify-btn">
                        <i class="fas fa-check me-2"></i>Verify OTP
                    </button>
                </form>

                <div class="resend-section">
                    <p class="text-muted mb-3">Didn't receive the OTP?</p>
                    <button id="resendBtn" class="resend-btn">
                        <i class="fas fa-redo me-2"></i>Resend OTP
                    </button>
                </div>

                <div class="text-center mt-4">
                    <a href="<?= base_url('trial-registration') ?>" class="back-link">
                        <i class="fas fa-arrow-left"></i>
                        Back to Registration
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // OTP Input handling
        const otpInputs = document.querySelectorAll('.otp-input');
        const otpForm = document.getElementById('otpForm');
        const otpValue = document.getElementById('otpValue');

        otpInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                // Only allow numbers
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
                
                if (e.target.value.length === 1) {
                    if (index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                }
                updateOTPValue();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                    otpInputs[index - 1].focus();
                }
                
                // Allow navigation with arrow keys
                if (e.key === 'ArrowLeft' && index > 0) {
                    otpInputs[index - 1].focus();
                }
                if (e.key === 'ArrowRight' && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '');
                if (pastedData.length === 6) {
                    pastedData.split('').forEach((char, i) => {
                        if (i < otpInputs.length) {
                            otpInputs[i].value = char;
                        }
                    });
                    updateOTPValue();
                    otpInputs[5].focus();
                }
            });
        });

        function updateOTPValue() {
            const otp = Array.from(otpInputs).map(input => input.value).join('');
            otpValue.value = otp;
        }

        // Resend OTP
        document.getElementById('resendBtn').addEventListener('click', function() {
            const button = this;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
            
            fetch('<?= base_url('trial-resend-otp') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-modern';
                    alertDiv.innerHTML = '<i class="fas fa-check-circle"></i><div>' + data.message + '</div>';
                    document.querySelector('.verification-body').insertBefore(alertDiv, document.querySelector('form'));
                    
                    // Clear OTP inputs
                    otpInputs.forEach(input => input.value = '');
                    otpInputs[0].focus();
                    
                    // Remove alert after 5 seconds
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 5000);
                } else {
                    // Show error message
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger alert-modern';
                    alertDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i><div>' + data.message + '</div>';
                    document.querySelector('.verification-body').insertBefore(alertDiv, document.querySelector('form'));
                    
                    // Remove alert after 5 seconds
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 5000);
                }
            })
            .catch(error => {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-modern';
                alertDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i><div>Failed to resend OTP. Please try again.</div>';
                document.querySelector('.verification-body').insertBefore(alertDiv, document.querySelector('form'));
                
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-redo me-2"></i>Resend OTP';
            });
        });

        // Auto-focus first input on page load
        window.addEventListener('load', () => {
            otpInputs[0].focus();
        });

        // Form submission validation
        otpForm.addEventListener('submit', (e) => {
            const otp = Array.from(otpInputs).map(input => input.value).join('');
            if (otp.length !== 6) {
                e.preventDefault();
                alert('Please enter complete 6-digit OTP');
                return false;
            }
        });
    </script>
</body>
</html>
