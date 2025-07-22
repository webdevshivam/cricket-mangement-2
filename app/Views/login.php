<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Cricket League Admin</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link href="<?= base_url() ?>/assets/css/login.css" rel="stylesheet">

  <!-- Animate.css -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
</head>

<body>
  <div class="login-container">
    <div class="login-background">
      <div class="cricket-field"></div>
      <div class="cricket-stumps"></div>
      <div class="cricket-ball"></div>
    </div>

    <div class="container-fluid h-100">
      <div class="row h-100 justify-content-center align-items-center">
        <div class="col-md-6 col-lg-4">
          <div class="login-card animate__animated animate__fadeInUp">
            <div class="login-header text-center mb-4">
              <div class="logo mb-3">
                <i class="fas fa-trophy"></i>
              </div>
              <h2 class="text-warning">Cricket League Admin</h2>
              <p class="text-muted">Sign in to your dashboard</p>
            </div>
            <?php if (session()->getFlashdata('error')): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle me-2"></i>
                <strong>Error!</strong> <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            <?php endif; ?>

            <form action="<?= site_url('login') ?>" method="post" id="login-form">
              <div class="form-floating mb-3">
                <input type="email" name="identity" class="form-control" id="email" placeholder="name@example.com" required>
                <label for="email">
                  <i class="fas fa-envelope me-2"></i>Email address
                </label>

              </div>

              <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" placeholder="Password" required name="password">
                <label for="password">
                  <i class="fas fa-lock me-2"></i>Password
                </label>

                <button type="button" class="password-toggle" onclick="togglePassword()">
                  <i class="fas fa-eye" id="password-eye"></i>
                </button>
              </div>

              <div class="row mb-3">
                <div class="col-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember-me">
                    <label class="form-check-label" for="remember-me">
                      Remember me
                    </label>
                  </div>
                </div>
                <div class="col-6 text-end">
                  <a href="#" class="forgot-password" data-bs-toggle="modal" data-bs-target="#forgot-password-modal">
                    Forgot password?
                  </a>
                </div>
              </div>

              <button type="submit" class="btn btn-warning w-100 mb-3">
                <i class="fas fa-sign-in-alt me-2"></i>Sign In
              </button>





            </form>
          </div>
        </div>
      </div>
    </div>
  </div>





  <!-- Loading Spinner -->
  <div id="loading-spinner" class="loading-overlay d-none">
    <div class="spinner-border text-warning" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Custom JS -->
  <script src="<?= base_url() ?>/assets/js/login.js"></script>
</body>

</html>
