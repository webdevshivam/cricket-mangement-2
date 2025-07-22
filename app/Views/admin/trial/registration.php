<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark border-warning stats-card mb-4">
  <div class="card-body d-flex justify-content-between align-items-center">
    <h5 class="card-title text-warning mb-0">Trial Registrations</h5>
  </div>
</div>

<div class="container mt-3">
  <form id="playerForm" method="post" action="<?= site_url('admin/players/bulk_action') ?>">
    <div class="table-responsive">
      <table class="table table-dark table-striped table-bordered">
        <thead>
          <tr>
            <th><input class="form-check-input" type="checkbox" id="selectAll" /></th>
            <th>#</th>
            <th>Name</th>
            <th>Mobile</th>
            <th>City</th>
            <th>Role</th>
            <th>Payment Status</th>
            <th>Registered On</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($registrations)) : ?>
            <?php
            $currentPage = $pager->getCurrentPage() ?? 1;
            $perPage = $pager->getPerPage() ?? 10;
            $i = 1 + ($currentPage - 1) * $perPage;
            ?>
            <?php foreach ($registrations as $reg) : ?>
              <tr>
                <td><input type="checkbox" class="form-check-input" name="ids[]" value="<?= $reg['id'] ?>" /></td>
                <td><?= $i++ ?></td>
                <td><?= esc($reg['name']) ?></td>
                <td><?= esc($reg['mobile']) ?></td>
                <td><?= esc($reg['city']) ?></td>
                <td><?= esc($reg['cricket_type']) ?></td>
                <td>
                  <select name="payment_status[<?= $reg['id'] ?>]" class="form-select form-select-sm bg-dark text-white">
                    <option value="pending">Pending</option>
                    <option value="partial">Partial</option>
                    <option value="full">Full</option>
                  </select>
                </td>
                <td><?= date('d M Y', strtotime($reg['created_at'] ?? '')) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else : ?>
            <tr>
              <td colspan="8" class="text-center">No registrations found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="mt-3 d-flex gap-2">
      <button type="submit" name="action" value="delete" class="btn btn-danger">Delete Selected</button>
      <button type="submit" name="action" value="update" class="btn btn-primary">Update Payment Status</button>
    </div>
  </form>

  <!-- Pagination Links -->
  <div class="d-flex justify-content-center mt-4">
    <?= $pager->links() ?>
  </div>
</div>

<script>
  // Select All checkbox
  document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('input[name="ids[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
  });
</script>

<?= $this->endSection(); ?>
