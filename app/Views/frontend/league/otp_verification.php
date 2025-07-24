
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - MPCL League Registration</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets-frontend/trial/') ?>trial.css">
    
    <style>
        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid #ddd;
            border-radius: 8px;
            margin: 0 5px;
        }
        .otp-input:focus {
            border-color: #ff6b35;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
        }
        .otp-container {
            display: flex;
            justify-content: center;
            margin: 30px 0;
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
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h2>Email Verification</h2>
                        <p class="subtitle">Enter the OTP sent to <strong><?= esc($email) ?></strong></p>
                    </div>

                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success alert-modern">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <div class="alert-title">Success!</div>
                                    <div class="alert-content"><?= session()->getFlashdata('success') ?></div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-modern">
                                <i class="fas fa-exclamation-triangle"></i>
                                <div>
                                    <div class="alert-title">Error!</div>
                                    <div class="alert-content"><?= session()->getFlashdata('error') ?></div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form id="otpForm" action="<?= base_url('league-verify-otp') ?>" method="post">
                            <div class="otp-container">
                                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required>
                                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required>
                                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required>
                                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required>
                                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required>
                                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required>
                            </div>
                            <input type="hidden" name="otp" id="otpValue">
                            
                            <div class="text-center mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-check me-2"></i>Verify OTP
                                </button>
                            </div>
                        </form>

                        <div class="text-center">
                            <p class="text-muted mb-2">Didn't receive the OTP?</p>
                            <button id="resendBtn" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-2"></i>Resend OTP
                            </button>
                        </div>

                        <div class="text-center mt-4">
                            <a href="<?= base_url('league-registration') ?>" class="btn btn-link">
                                <i class="fas fa-arrow-left me-2"></i>Back to Registration
                            </a>
                        </div>
                    </div>
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
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text');
                if (pastedData.length === 6 && /^\d{6}$/.test(pastedData)) {
                    pastedData.split('').forEach((char, i) => {
                        if (i < otpInputs.length) {
                            otpInputs[i].value = char;
                        }
                    });
                    updateOTPValue();
                }
            });
        });

        function updateOTPValue() {
            const otp = Array.from(otpInputs).map(input => input.value).join('');
            otpValue.value = otp;
        }

        // Resend OTP
        document.getElementById('resendBtn').addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
            
            fetch('<?= base_url('league-resend-otp') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    // Clear OTP inputs
                    otpInputs.forEach(input => input.value = '');
                    otpInputs[0].focus();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                alert('Failed to resend OTP. Please try again.');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-redo me-2"></i>Resend OTP';
            });
        });

        // Auto-focus first input
        otpInputs[0].focus();
    </script>
</body>
</html>
