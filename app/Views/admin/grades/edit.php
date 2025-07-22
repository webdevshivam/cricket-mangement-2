<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark border-warning">
  <div class="card-body">
    <h5 class="text-warning mb-3">Add Grade</h5>
    <form action="<?= site_url('admin/grades/update/' . $grade['id']) ?>" method="post">
      <?= csrf_field() ?>
      <div class="mb-3">
        <label class="form-label text-white">Title</label>
        <input type="text" name="title" class="form-control" value="<?= esc($grade['title']) ?>" required>

      </div>
      <div class="mb-3">
        <label class="form-label text-white">Description</label>
        <textarea name="description" class="form-control"><?= esc($grade['description']) ?></textarea>

      </div>
      <div class="mb-3">
        <label class="form-label text-white">League Fee</label>
        <input type="number" name="league_fee" class="form-control" step="0.01" value="<?= esc($grade['league_fee']) ?>" required>

      </div>
      <div class="mb-3">
        <label class="form-label text-white">Status</label>
        <select name="status" class="form-select">
          <option value="active" <?= $grade['status'] == 'active' ? 'selected' : '' ?>>Active</option>
          <option value="inactive" <?= $grade['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
      </div>
      <div class="text-end">
        <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Save</button>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection(); ?>
