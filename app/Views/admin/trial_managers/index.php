
<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Trial Managers<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark text-light border-primary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-tie text-primary"></i> Trial Managers
                    </h4>
                    <a href="/admin/trial-managers/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Trial Manager
                    </a>
                </div>

                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <?php if (session()->getFlashdata('generated_password')): ?>
                                <hr>
                                <strong>Login Credentials:</strong><br>
                                Email: <?= session()->getFlashdata('manager_email') ?><br>
                                Password: <span class="text-warning"><?= session()->getFlashdata('generated_password') ?></span>
                                <br><small class="text-muted">Please save these credentials securely.</small>
                            <?php endif; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-dark table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Trial Name</th>
                                    <th>City</th>
                                    <th>Players</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($managers)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No trial managers found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($managers as $manager): ?>
                                        <tr>
                                            <td><?= $manager['id'] ?></td>
                                            <td><?= esc($manager['name']) ?></td>
                                            <td><?= esc($manager['email']) ?></td>
                                            <td><?= esc($manager['trial_name']) ?></td>
                                            <td><?= esc($manager['city_name'] ?? 'Not Set') ?></td>
                                            <td>
                                                <span class="badge bg-info"><?= $manager['total_players'] ?></span>
                                            </td>
                                            <td>
                                                <?php if ($manager['status'] === 'active'): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($manager['created_at'])) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="/admin/trial-managers/view/<?= $manager['id'] ?>" 
                                                       class="btn btn-sm btn-outline-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="/admin/trial-managers/edit/<?= $manager['id'] ?>" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button onclick="deleteManager(<?= $manager['id'] ?>)" 
                                                            class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteManager(id) {
    if (confirm('Are you sure you want to delete this trial manager? This action cannot be undone.')) {
        fetch('/admin/trial-managers/delete/' + id, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the trial manager.');
        });
    }
}
</script>
<?= $this->endSection() ?>
