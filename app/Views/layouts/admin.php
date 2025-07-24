<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cricket League Management Dashboard</title>

  <!-- Bootstrap 5 CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet" />

  <!-- Google Material Icons -->
  <link
    href="https://fonts.googleapis.com/icon?family=Material+Icons"
    rel="stylesheet" />

  <!-- Bootstrap Icons -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css"
    rel="stylesheet" />

  <!-- Select2 CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
    rel="stylesheet" />

  <!-- DataTables CSS -->
  <link
    href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css"
    rel="stylesheet" />

  <!-- Animate.css -->
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
    rel="stylesheet" />

  <!-- SweetAlert2 CSS -->

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">


  <!-- Custom CSS -->
  <link href="<?= base_url() ?>/assets/css/style.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

</head>

<body>
  <!-- Sidebar -->
  <nav id="sidebar" class="sidebar">
    <div class="sidebar-header">
      <h3><i class="material-icons text-warning">emoji_events</i> Cricket Admin</h3>
    </div>
    <div class="sidebar-content">
      <ul class="list-unstyled components">

        <li class="nav-item">
          <a href="<?= base_url('admin/dashboard') ?>" class="nav-link">
            <i class="material-icons">dashboard</i> Dashboard
          </a>
        </li>

        <!-- 🏟️ Trial Management -->
        <li class="nav-item">
          <a href="#trial-submenu" class="nav-link dropdown-toggle" aria-expanded="false">
            <i class="material-icons">location_on</i> Trial Management
          </a>
          <ul class="collapse list-unstyled" id="trial-submenu">
            <li><a href="<?= base_url('admin/trial-registration') ?>" class="nav-link">Manage Trial Registration</a></li>
            <li><a href="<?= base_url('admin/trial-verification') ?>" class="nav-link">Trial Verification</a></li>
            <li><a href="<?= base_url('admin/payment-tracking') ?>" class="nav-link">Payment Tracking</a></li>
            <li><a href="<?= base_url('admin/schedule-trials') ?>" class="nav-link">Schedule Trials</a></li>
            <li><a href="<?= base_url('admin/trial-attendance') ?>" class="nav-link">Trial Attendance</a></li>
          </ul>
        </li>

        <!-- 🎓 Grade Management -->
        <li class="nav-item">
          <a href="#grade-submenu" class="nav-link dropdown-toggle" aria-expanded="false">
            <i class="material-icons">school</i> Grade Management
          </a>
          <ul class="collapse list-unstyled" id="grade-submenu">
            <li><a href="<?= base_url('admin/grades') ?>" class="nav-link">Create/Edit Grades</a></li>
            <li><a href="<?= base_url('admin/grades/assignments') ?>" class="nav-link">Assign Grades to Players</a></li>
          </ul>
        </li>

        <!-- 🏏 League Management -->
        <li class="nav-item">
          <a href="#league-submenu" class="nav-link dropdown-toggle" aria-expanded="false">
            <i class="material-icons">sports_cricket</i> League Management
          </a>
          <ul class="collapse list-unstyled" id="league-submenu">
            <li><a href="<?= base_url('admin/league-registration') ?>" class="nav-link">League Registrations</a></li>
            <li><a href="<?= base_url('admin/league-payments') ?>" class="nav-link">League Payments</a></li>
            <li><a href="<?= base_url('admin/team-allocation') ?>" class="nav-link">Team Allocation</a></li>
            <li><a href="<?= base_url('admin/fixture-generator') ?>" class="nav-link">Fixture Generator</a></li>
            <li><a href="<?= base_url('admin/match-results') ?>" class="nav-link">Match Results</a></li>
            <li><a href="<?= base_url('admin/final-winner') ?>" class="nav-link">Final Winner Declaration</a></li>
          </ul>
        </li>

        <!-- 💳 Payment & QR -->
        <li class="nav-item">
          <a href="#payment-submenu" class="nav-link dropdown-toggle" aria-expanded="false">
            <i class="material-icons">qr_code</i> Payment & QR
          </a>
          <ul class="collapse list-unstyled" id="payment-submenu">
            <li><a href="<?= base_url('admin/qr-code-setting') ?>" class="nav-link">QR Code Settings</a></li>
            <li><a href="<?= base_url('admin/payment-report') ?>" class="nav-link">Payment Report</a></li>
            <li><a href="<?= base_url('admin/partial-payment') ?>" class="nav-link">Partial/Full Payment Tracker</a></li>
          </ul>
        </li>

        <!-- 📈 Reports & Exports -->
        <li class="nav-item">
          <a href="#reports-submenu" class="nav-link dropdown-toggle" aria-expanded="false">
            <i class="material-icons">file_download</i> Reports & Exports
          </a>
          <ul class="collapse list-unstyled" id="reports-submenu">
            <li><a href="<?= base_url('admin/export-players') ?>" class="nav-link">Export Player List</a></li>
            <li><a href="<?= base_url('admin/export-grades') ?>" class="nav-link">Export Grades Report</a></li>
            <li><a href="<?= base_url('admin/export-payments') ?>" class="nav-link">Export Payment Report</a></li>
          </ul>
        </li>

        <!-- 📢 Communication -->
        <li class="nav-item">
          <a href="#communication-submenu" class="nav-link dropdown-toggle" aria-expanded="false">
            <i class="material-icons">campaign</i> Communication
          </a>
          <ul class="collapse list-unstyled" id="communication-submenu">
            <li><a href="<?= base_url('admin/send-email') ?>" class="nav-link">Send Email</a></li>
            <li><a href="<?= base_url('admin/send-sms') ?>" class="nav-link">Send SMS</a></li>
            <li><a href="<?= base_url('admin/whatsapp-broadcast') ?>" class="nav-link">WhatsApp Broadcast</a></li>
          </ul>
        </li>

        <li class="nav-item">
          <a href="#marketing-submenu" class="nav-link dropdown-toggle" aria-expanded="false">
            <i class="material-icons">trending_up</i> Marketing & Revenue
          </a>
          <ul class="collapse list-unstyled" id="marketing-submenu">
            <li><a href="<?= base_url('admin/sponsors') ?>" class="nav-link">Sponsors</a></li>
            <li><a href="<?= base_url('admin/store') ?>" class="nav-link">Merchandise Store</a></li>
            <li><a href="<?= base_url('admin/donations') ?>" class="nav-link">Fundraising & Donations</a></li>
          </ul>
        </li>

        <!-- 🧑‍💻 Admin Settings -->
        <li class="nav-item">
          <a href="#admin-submenu" class="nav-link dropdown-toggle" aria-expanded="false">
            <i class="material-icons">settings</i> Admin Settings
          </a>
          <ul class="collapse list-unstyled" id="admin-submenu">
            <li><a href="<?= base_url('admin/manage-admins') ?>" class="nav-link">Manage Admins</a></li>
            <li><a href="<?= base_url('admin/otp-settings') ?>" class="nav-link">OTP Settings</a></li>
            <li><a href="<?= base_url('admin/tournaments') ?>" class="nav-link">Tournaments</a></li>
            <li><a href="<?= base_url('admin/branding') ?>" class="nav-link">Site Branding</a></li>
            <li><a href="<?= base_url('admin/api-settings') ?>" class="nav-link">API Key Settings</a></li>
            <li><a href="<?= base_url('admin/change-password') ?>" class="nav-link">Change Password</a></li>
          </ul>
        </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('admin/teams') ?>">
                <i class="material-icons">groups</i>
                <p>Team Management</p>
              </a>
            </li>

        <!-- 📬 Feedback & Support -->
        <li class="nav-item">
          <a href="<?= base_url('admin/feedback') ?>" class="nav-link">
            <i class="material-icons">feedback</i> View Contact Submissions
          </a>
        </li>

        <!-- 🚪 Logout -->
        <li class="nav-item">
          <a href="#" onclick="logout()" class="nav-link">
            <i class="material-icons text-danger">logout</i> Logout
          </a>
        </li>
      </ul>
    </div>
  </nav>


  <!-- Page Content -->
  <div id="content">
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <button
          type="button"
          id="sidebarCollapse"
          class="btn btn-outline-warning">
          <i class="material-icons">menu</i>
        </button>

        <div class="navbar-nav ms-auto">
          <div class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle text-warning"
              href="#"
              role="button"
              data-bs-toggle="dropdown">
              <i class="material-icons">account_circle</i> Admin
            </a>
            <ul class="dropdown-menu dropdown-menu-dark">
              <li>
                <a class="dropdown-item" href="#"><i class="material-icons">person</i> Profile</a>
              </li>
              <li>
                <a class="dropdown-item" href="#"><i class="material-icons">settings</i> Settings</a>
              </li>
              <li>
                <hr class="dropdown-divider" />
              </li>
              <li>
                <a class="dropdown-item" href="#" onclick="logout()"><i class="material-icons">logout</i> Logout</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content Area -->
    <div class="container-fluid p-4">
      <!-- Dashboard Content -->
      <div id="dashboard-content" class="page-content active">
        <?= $this->renderSection('content'); ?>
      </div>

      <!-- Other page contents will be loaded dynamically -->
      <div id="dynamic-content"></div>
    </div>
  </div>

  <!-- Loading Spinner -->
  <div id="loading-spinner" class="loading-spinner">
    <div class="spinner-border text-warning" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>

  <!-- Success Celebration Animation -->
  <div id="celebration" class="celebration-container">
    <div class="confetti"></div>
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

  <!-- Bootstrap 5 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- SweetAlert2 -->



  <!-- Moment.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

  <!-- Custom Scripts -->
  <script src="data/sample-data.js"></script>
  <script src="<?= base_url() ?>/assets/js/main.js"></script>
  <script src="<?= base_url() ?>/assets/js/dashboard.js"></script>


  <script>
    let notyf = new Notyf({
      duration: 3000,
      ripple: true,
      position: {
        x: 'right',
        y: 'bottom'
      }
    });

    <?php if (session()->getFlashdata('success')): ?>
      notyf.success("<?= esc(session()->getFlashdata('success')) ?>");
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
      notyf.error("<?= esc(session()->getFlashdata('error')) ?>");
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
      <?php foreach ((array)session()->getFlashdata('errors') as $err): ?>
        notyf.error("<?= esc($err) ?>");
      <?php endforeach; ?>
    <?php endif; ?>
  </script>


</body>



</html>