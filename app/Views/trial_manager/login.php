<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trial Manager Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #343a40 0%, #212529 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: rgba(33, 37, 41, 0.95);
            backdrop-filter: blur(10px);
            border: 2px solid #ffc107;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(255, 193, 7, 0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }

        .login-header {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: #212529;
            padding: 2rem;
            text-align: center;
        }

        .login-header h2 {
            margin: 0;
            font-weight: 700;
        }

        .login-body {
            padding: 2rem;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #ffc107;
            border-radius: 10px;
            color: #fff;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #ffc107;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
            color: #fff;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .btn-primary {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            color: #212529;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
            color: #212529;
        }

        .form-label {
            color: #ffc107;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .text-warning {
            color: #ffc107 !important;
        }

        .input-group {
            position: relative;
        }

        .input-group-text {
            background: rgba(255, 193, 7, 0.2);
            border: 1px solid #ffc107;
            color: #ffc107;
            border-radius: 10px 0 0 10px;
        }

        .input-group .form-control {
            border-radius: 0 10px 10px 0;
            border-left: none;
        }

        .footer-link {
            text-align: center;
            margin-top: 1rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .footer-link a {
            color: #ffc107;
            text-decoration: none;
        }

        .footer-link a:hover {
            color: #fd7e14;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-clipboard-check fa-3x mb-3"></i>
                <h2>Trial Manager Portal</h2>
                <p class="mb-0">Access your trial management dashboard</p>
            </div>

            <div class="login-body">
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

                <form action="/trial-manager/authenticate" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>Email Address
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="Enter your email" required 
                                   value="<?= old('email') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-key"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Enter your password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Login to Dashboard
                    </button>
                </form>

                <div class="footer-link">
                    <small>Need help? <a href="/contact">Contact Support</a></small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
</body>
</html>
```