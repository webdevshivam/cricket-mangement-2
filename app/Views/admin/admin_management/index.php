
<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Manage Admins<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-light">
            <i class="fas fa-users-cog me-2"></i>Manage Admins
        </h1>
        <a href="/admin/manage-admins/create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Admin
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card bg-dark border-secondary">
        <div class="card-header bg-secondary text-light">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Admin Users
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($admins)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-users-cog fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No admins found</h5>
                    <p class="text-muted">Add your first admin to get started.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-dark table-striped table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admins as $admin): ?>
                                <tr>
                                    <td><?= $admin['id'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <span class="text-light"><?= esc($admin['name']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-info">
                                            <i class="fas fa-envelope me-1"></i>
                                            <?= esc($admin['email']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-success">
                                            <i class="fas fa-phone me-1"></i>
                                            <?= esc($admin['mobile']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('d M Y', strtotime($admin['created_at'] ?? 'now')) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/admin/manage-admins/edit/<?= $admin['id'] ?>" 
                                               class="btn btn-sm btn-outline-warning" 
                                               data-bs-toggle="tooltip" 
                                               title="Edit Admin">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($admin['id'] != session()->get('user_id')): ?>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="confirmDelete(<?= $admin['id'] ?>, '<?= esc($admin['name']) ?>')"
                                                        data-bs-toggle="tooltip" 
                                                        title="Delete Admin">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-light">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-light">
                <p>Are you sure you want to delete admin <strong id="adminName"></strong>?</p>
                <p class="text-warning mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="deleteLink" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>Delete
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(adminId, adminName) {
    document.getElementById('adminName').textContent = adminName;
    document.getElementById('deleteLink').href = '/admin/manage-admins/delete/' + adminId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});
</script>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}
</style>
<?= $this->endSection() ?>
