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

  <!-- Font Awesome Icons -->
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
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
  <nav id="sidebar" class="sidebar" style="overflow-y: auto;">
    <div class="sidebar-header">
      <h3><i class="fas fa-trophy text-warning"></i> Cricket Admin</h3>
    </div>
    <ul class="list-unstyled components">


      <li>
        <a href="#dashboard" data-page="dashboard">
          <i class="fas fa-chart-pie"></i> Dashboard
        </a>
      </li>


      <li>
        <a href="#player-submenu" data-bs-toggle="collapse" class="dropdown-toggle d-flex align-items-center justify-content-between w-100">
          <i class="fas fa-users"></i> Player Management
        </a>
        <ul class="collapse list-unstyled" id="player-submenu">
          <li><a href="<?= base_url('admin/players') ?>">All Players</a></li>
          <li><a href="<?= base_url('admin/add') ?>" data-page="add-player">Add Player</a></li>
          <li><a href="<?= base_url('admin/grades/assign') ?>" data-page="assign-grades">Assign Grades</a></li>
          <li><a href="<?= base_url('admin/performance-notes') ?>" data-page="performance-notes">Performance Notes</a></li>
          <li><a href="<?= base_url('admin/verify-payments') ?>" data-page="verify-payments">Verify Payments</a></li>
        </ul>
      </li>

      <!-- 🏟️ Trial Management -->
      <li>
        <a href="#trial-submenu" data-bs-toggle="collapse" class="dropdown-toggle d-flex align-items-center justify-content-between w-100">
          <i class="fas fa-map-marker-alt"></i> Trial Management
        </a>
        <ul class="collapse list-unstyled" id="trial-submenu">
          <li><a href="<?= base_url('admin/manage-trial-cities') ?>" data-page="trial-cities">Manage Trial Cities</a></li>
          <li><a href="<?= base_url('admin/trial-registration') ?>" data-page="trial-cities">Manage Trial Registration</a></li>
          <li><a href="#schedule-trials" data-page="schedule-trials">Schedule Trials</a></li>
          <li><a href="#trial-attendance" data-page="trial-attendance">Trial Attendance</a></li>

        </ul>
      </li>

      <!-- 🎓 Grade Management -->
      <li>
        <a href="#grade-submenu" data-bs-toggle="collapse" class="dropdown-toggle d-flex align-items-center justify-content-between w-100">
          <i class="fas fa-graduation-cap"></i> Grade Management
        </a>
        <ul class="collapse list-unstyled" id="grade-submenu">
          <li><a href="<?= base_url('admin/grades') ?>" data-page="manage-grades">Create/Edit Grades</a></li>
          <li><a href="#assign-grades-players" data-page="assign-grades-players">Assign Grades to Players</a></li>
        </ul>
      </li>

      <!-- 🏏 League Management -->
      <li>
        <a href="#league-submenu" data-bs-toggle="collapse" class="dropdown-toggle d-flex align-items-center justify-content-between w-100">
          <i class="fas fa-baseball-ball"></i> League Management
        </a>
        <ul class="collapse list-unstyled" id="league-submenu">
          <li><a href="#league-registrations" data-page="league-registrations">League Registrations</a></li>
          <li><a href="#league-payments" data-page="league-payments">League Payments</a></li>
          <li><a href="#team-allocation" data-page="team-allocation">Team Allocation</a></li>
          <li><a href="#fixture-generator" data-page="fixture-generator">Fixture Generator</a></li>
          <li><a href="#match-results" data-page="match-results">Match Results</a></li>
          <li><a href="#final-winner" data-page="final-winner">Final Winner Declaration</a></li>
        </ul>
      </li>

      <!-- 💳 Payment & QR -->
      <li>
        <a href="#payment-submenu" data-bs-toggle="collapse" class="dropdown-toggle d-flex align-items-center justify-content-between w-100">
          <i class="fas fa-qrcode"></i> Payment & QR
        </a>
        <ul class="collapse list-unstyled" id="payment-submenu">
          <li><a href="<?= base_url('admin/qr-code-setting') ?>" data-page="qr-settings">QR Code Settings</a></li>
          <li><a href="#payment-report" data-page="payment-report">Payment Report</a></li>
          <li><a href="#partial-payment" data-page="partial-payment">Partial/Full Payment Tracker</a></li>
        </ul>
      </li>

      <!-- 📈 Reports & Exports -->
      <li>
        <a href="#reports-submenu" data-bs-toggle="collapse" class="dropdown-toggle d-flex align-items-center justify-content-between w-100">
          <i class="fas fa-file-export"></i> Reports & Exports
        </a>
        <ul class="collapse list-unstyled" id="reports-submenu">
          <li><a href="#export-players" data-page="export-players">Export Player List</a></li>
          <li><a href="#export-grades" data-page="export-grades">Export Grades Report</a></li>
          <li><a href="#export-payments" data-page="export-payments">Export Payment Report</a></li>
        </ul>
      </li>

      <!-- 📢 Communication -->
      <li>
        <a href="#communication-submenu" data-bs-toggle="collapse" class="dropdown-toggle d-flex align-items-center justify-content-between w-100">
          <i class="fas fa-bullhorn"></i> Communication
        </a>
        <ul class="collapse list-unstyled" id="communication-submenu">
          <li><a href="#send-email" data-page="send-email">Send Email</a></li>
          <li><a href="#send-sms" data-page="send-sms">Send SMS</a></li>
          <li><a href="#whatsapp-broadcast" data-page="whatsapp-broadcast">WhatsApp Broadcast</a></li>
        </ul>
      </li>


      <li>
        <a href="#marketing-submenu" data-bs-toggle="collapse" class="dropdown-toggle d-flex align-items-center justify-content-between w-100">
          <i class="fas fa-bullseye"></i> Marketing & Revenue
        </a>
        <ul class="collapse list-unstyled" id="marketing-submenu">
          <li><a href="#sponsors" data-page="sponsors">Sponsors</a></li>
          <li><a href="#store" data-page="store">Merchandise Store</a></li>
          <li><a href="#donations" data-page="donations">Fundraising & Donations</a></li>
        </ul>
      </li>

      <!-- 🧑‍💻 Admin Settings -->
      <li>
        <a href="#admin-submenu" data-bs-toggle="collapse" class="dropdown-toggle d-flex align-items-center justify-content-between w-100">
          <i class="fas fa-user-cog"></i> Admin Settings
        </a>
        <ul class="collapse list-unstyled" id="admin-submenu">
          <li><a href="#manage-admins" data-page="manage-admins">Manage Admins</a></li>
          <li><a href="#branding" data-page="branding">Site Branding</a></li>
          <li><a href="#api-settings" data-page="api-settings">API Key Settings</a></li>
          <li><a href="#change-password" data-page="change-password">Change Password</a></li>
        </ul>
      </li>



      <!-- 📬 Feedback & Support -->
      <li>
        <a href="#feedback" data-page="feedback">
          <i class="fas fa-envelope-open-text"></i> View Contact Submissions
        </a>
      </li>

      <!-- 🚪 Logout -->
      <li>
        <a href="#" onclick="logout()">
          <i class="fas fa-sign-out-alt text-danger"></i> Logout
        </a>
      </li>
    </ul>
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
          <i class="fas fa-bars"></i>
        </button>

        <div class="navbar-nav ms-auto">
          <div class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle text-warning"
              href="#"
              role="button"
              data-bs-toggle="dropdown">
              <i class="fas fa-user-circle"></i> Admin
            </a>
            <ul class="dropdown-menu dropdown-menu-dark">
              <li>
                <a class="dropdown-item" href="#"><i class="fas fa-user"></i> Profile</a>
              </li>
              <li>
                <a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Settings</a>
              </li>
              <li>
                <hr class="dropdown-divider" />
              </li>
              <li>
                <a class="dropdown-item" href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
