<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark border-warning mb-4">
  <div class="card-body">
    <div class="d-flex justify-content-between">
      <h5 class="card-title text-warning">Player Details</h5>
      <a href="<?= site_url('admin/players'); ?>" class="btn btn-warning">
        <i class="fas fa-arrow-left"></i>
        <span class="d-none d-md-inline">Back to Players List</span>
      </a>
    </div>
  </div>
</div>

<div class="card bg-dark text-white border-secondary">
  <div class="card-body">
    <div class="row">
      <?php
      // Column definitions for clarity
      $fields = [
        'name' => 'Name',
        'age' => 'Age',
        'mobile_number' => 'Mobile Number',
        'email' => 'Email',
        'state' => 'State',
        'city' => 'City',
        'trial_city' => 'Trial City',
        'cricketer_type' => 'Cricketer Type',
        'reference_id' => 'Reference ID',
        'step_id' => 'Step ID',
        'payment_status' => 'Payment Status',
        'ground_status' => 'Ground Status',
        'status' => 'Overall Status',
        'accept' => 'Accepted',
        'date' => 'Registered On'
      ];
      ?>

      <?php foreach ($fields as $key => $label) : ?>
        <div class="col-md-6 mb-3">
          <strong><?= $label ?>:</strong>
          <?php if ($key == 'date') : ?>
            <div class="text-warning">
              <?= !empty($player[$key]) ? date('d M Y, h:i A', strtotime($player[$key])) : 'N/A' ?>
            </div>
          <?php else : ?>
            <div class="text-warning"><?= esc($player[$key] ?? 'N/A') ?></div>
          <?php endif; ?>

        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>
