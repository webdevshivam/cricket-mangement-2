
<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>Edit Trial Manager<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark text-light border-warning">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-edit text-warning"></i> Edit Trial Manager
                    </h4>
                </div>

                <div class="card-body">
                    <?php if (session()->get('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session()->get('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="/admin/trial-managers/update/<?= $manager['id'] ?>" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Manager Name</label>
                                    <input type="text" class="form-control bg-dark text-white border-secondary" 
                                           id="name" name="name" value="<?= old('name', $manager['name']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control bg-dark text-white border-secondary" 
                                           id="email" name="email" value="<?= old('email', $manager['email']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="trial_name" class="form-label">Trial Name</label>
                                    <input type="text" class="form-control bg-dark text-white border-secondary" 
                                           id="trial_name" name="trial_name" value="<?= old('trial_name', $manager['trial_name']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="trial_city_id" class="form-label">Trial City</label>
                                    <select class="form-select bg-dark text-white border-secondary" 
                                            id="trial_city_id" name="trial_city_id">
                                        <option value="">Select City</option>
                                        <?php foreach ($trial_cities as $city): ?>
                                            <option value="<?= $city['id'] ?>" 
                                                    <?= old('trial_city_id', $manager['trial_city_id']) == $city['id'] ? 'selected' : '' ?>>
                                                <?= esc($city['city_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select bg-dark text-white border-secondary" 
                                            id="status" name="status" required>
                                        <option value="active" <?= old('status', $manager['status']) === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= old('status', $manager['status']) === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password (Optional)</label>
                                    <input type="password" class="form-control bg-dark text-white border-secondary" 
                                           id="new_password" name="new_password" 
                                           placeholder="Leave blank to keep current password">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/admin/trial-managers" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Update Trial Manager
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
