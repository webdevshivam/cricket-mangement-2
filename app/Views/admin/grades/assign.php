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
    <form action="<?= site_url('admin/grades/assignGrade') ?>" method="post">
      <?= csrf_field(); ?>

      <div class="row">

        <!-- Select Players (Multiple) -->
        <div class="col-md-6 mb-3">
          <label class="form-label">Select Verified Trial Players</label>
          <div class="card bg-secondary p-3" style="max-height: 300px; overflow-y: auto;">
            <?php if (empty($players)): ?>
              <p class="text-muted">No verified trial players found.</p>
            <?php else: ?>
              <?php foreach ($players as $player): ?>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="selected[]" value="<?= $player['id'] ?>" id="player_<?= $player['id'] ?>">
                  <label class="form-check-label" for="player_<?= $player['id'] ?>">
                    <?= esc($player['name']) ?> (<?= esc($player['mobile']) ?>) - <?= esc($player['cricket_type']) ?>
                  </label>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
          <small class="text-muted">Only verified trial players with full payment are shown.</small>
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

      <div class="mb-3">
        <button type="button" class="btn btn-sm btn-outline-light" onclick="selectAll()">Select All</button>
        <button type="button" class="btn btn-sm btn-outline-light" onclick="deselectAll()">Deselect All</button>
      </div>

      <button type="submit" class="btn btn-warning">Assign Grade to Selected Players</button>
    </form>

    <script>
      function selectAll() {
        const checkboxes = document.querySelectorAll('input[name="selected[]"]');
        checkboxes.forEach(cb => cb.checked = true);
      }

      function deselectAll() {
        const checkboxes = document.querySelectorAll('input[name="selected[]"]');
        checkboxes.forEach(cb => cb.checked = false);
      }
    </script>

  </div>
</div>

<?= $this->endSection(); ?>
