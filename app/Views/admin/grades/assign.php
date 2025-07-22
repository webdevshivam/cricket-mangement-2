<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark text-white border-warning mb-4">
  <div class="card-body">
    <div class="d-flex justify-content-between">
      <h5 class="card-title text-warning">Assign Grade</h5>
      <a href="<?= site_url('admin/grades'); ?>" class="btn btn-warning">
        <i class="fas fa-arrow-left"></i> <span class="d-none d-md-inline">Back to Grades List</span>
      </a>
    </div>
  </div>
</div>

<div class="card bg-dark text-white border-secondary">
  <div class="card-body">

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- Assign Form -->
    <form action="<?= site_url('admin/grades/assignSave') ?>" method="post">
      <?= csrf_field(); ?>

      <div class="row">

        <!-- Select Player -->
        <div class="col-md-6 mb-3">
          <label for="player_id" class="form-label">Select Player</label>
          <select class="form-select" id="player_id" name="player_id" required>
            <option value="">-- Select Player --</option>
            <?php foreach ($players as $player): ?>
              <option value="<?= $player['id'] ?>">
                <?= esc($player['name']) ?> (<?= esc($player['mobile_number']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Select Grade -->
        <div class="col-md-6 mb-3">
          <label for="grade_id" class="form-label">Select Grade</label>
          <select class="form-select" id="grade_id" name="grade_id" required>
            <option value="">-- Select Grade --</option>
            <?php foreach ($grades as $grade): ?>
              <option value="<?= $grade['id'] ?>">
                <?= esc($grade['title']) ?> - ₹<?= esc($grade['league_fee']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

      </div>

      <button type="submit" class="btn btn-warning">Assign Grade</button>
    </form>

  </div>
</div>

<?= $this->endSection(); ?>
