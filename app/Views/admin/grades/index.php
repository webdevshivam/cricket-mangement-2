<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark border-warning mb-3">
  <div class="card-body d-flex justify-content-between">
    <h5 class="text-warning">Grade List</h5>
    <a href="<?= site_url('admin/grades/add') ?>" class="btn btn-warning">
      <i class="fas fa-plus"></i> Add Grade
    </a>
  </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<div class="card bg-dark border-warning">
  <div class="card-body table-responsive">
    <table class="table table-bordered table-dark text-white">
      <thead class="table-warning text-dark">
        <tr>
          <th>#</th>
          <th>Title</th>
          <th>Fee</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($grades as $i => $grade): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= esc($grade['title']) ?></td>
            <td>₹<?= esc($grade['league_fee']) ?></td>
            <td><span class="badge bg-<?= $grade['status'] == 'active' ? 'success' : 'secondary' ?>">
                <?= ucfirst($grade['status']) ?>
              </span></td>
            <td>
              <a href="<?= site_url('admin/grades/edit/' . $grade['id']) ?>" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i>
              </a>
              <a href="<?= site_url('admin/grades/delete/' . $grade['id']) ?>"
                class="btn btn-danger btn-sm"
                onclick="return confirm('Delete this grade?')">
                <i class="fas fa-trash"></i>
              </a>
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>

    <div class="d-flex justify-content-center">
      <?= $pager->links() ?>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>
