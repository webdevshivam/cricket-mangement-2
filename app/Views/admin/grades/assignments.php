
<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>

<div class="card bg-dark text-white border-warning mb-4">
  <div class="card-body">
    <div class="d-flex justify-content-between">
      <h5 class="card-title text-warning">Grade Assignments</h5>
      <div>
        <a href="<?= site_url('admin/grades/assign'); ?>" class="btn btn-warning me-2">
          <i class="fas fa-plus"></i> Assign New Grade
        </a>
        <a href="<?= site_url('admin/grades'); ?>" class="btn btn-outline-warning">
          <i class="fas fa-arrow-left"></i> Back to Grades
        </a>
      </div>
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

    <!-- Assignments Table -->
    <div class="table-responsive">
      <table class="table table-dark table-striped">
        <thead>
          <tr>
            <th>Player Name</th>
            <th>Mobile</th>
            <th>Current Grade</th>
            <th>Assigned Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($assignments)): ?>
            <tr>
              <td colspan="5" class="text-center">No grade assignments found</td>
            </tr>
          <?php else: ?>
            <?php foreach ($assignments as $assignment): ?>
              <tr>
                <td><?= esc($assignment['player_name']) ?></td>
                <td><?= esc($assignment['mobile']) ?></td>
                <td>
                  <span class="badge bg-warning text-dark"><?= esc($assignment['grade_title']) ?></span>
                </td>
                <td><?= date('d M Y', strtotime($assignment['assigned_at'])) ?></td>
                <td>
                  <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#updateModal<?= $assignment['id'] ?>">
                    <i class="fas fa-edit"></i> Update
                  </button>
                  <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $assignment['id'] ?>">
                    <i class="fas fa-trash"></i> Remove
                  </button>
                </td>
              </tr>

              <!-- Update Modal -->
              <div class="modal fade" id="updateModal<?= $assignment['id'] ?>" tabindex="-1" aria-labelledby="updateModalLabel<?= $assignment['id'] ?>" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content bg-dark text-white border border-warning">
                    <div class="modal-header border-bottom border-warning">
                      <h5 class="modal-title text-warning" id="updateModalLabel<?= $assignment['id'] ?>">Update Grade Assignment</h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="<?= site_url('admin/grades/updateAssignment/' . $assignment['id']) ?>" method="post">
                      <?= csrf_field(); ?>
                      <div class="modal-body">
                        <div class="mb-3">
                          <p class="mb-2"><strong>Player:</strong> <?= esc($assignment['player_name']) ?></p>
                        </div>
                        <div class="mb-3">
                          <label for="grade_id<?= $assignment['id'] ?>" class="form-label">Select New Grade</label>
                          <select class="form-select bg-dark text-white border-secondary" id="grade_id<?= $assignment['id'] ?>" name="grade_id" required>
                            <option value="">-- Select Grade --</option>
                            <?php 
                            $gradeModel = new \App\Models\GradeModel();
                            $grades = $gradeModel->where('status', 'active')->findAll();
                            foreach ($grades as $grade): ?>
                              <option value="<?= $grade['id'] ?>" <?= $grade['id'] == $assignment['grade_id'] ? 'selected' : '' ?>>
                                <?= esc($grade['title']) ?> - ₹<?= esc($grade['league_fee']) ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                      <div class="modal-footer border-top border-warning">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Update Assignment</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>v>

              <!-- Delete Modal -->
              <div class="modal fade" id="deleteModal<?= $assignment['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $assignment['id'] ?>" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content bg-dark text-white border border-danger">
                    <div class="modal-header border-bottom border-danger">
                      <h5 class="modal-title text-danger" id="deleteModalLabel<?= $assignment['id'] ?>">Remove Grade Assignment</h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <p>Are you sure you want to remove the grade assignment for <strong><?= esc($assignment['player_name']) ?></strong>?</p>
                      <p class="text-warning mb-0">This action will remove the player from the assigned grade.</p>
                    </div>
                    <div class="modal-footer border-top border-danger">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <form action="<?= site_url('admin/grades/deleteAssignment/' . $assignment['id']) ?>" method="post" class="d-inline">
                        <?= csrf_field(); ?>
                        <button type="submit" class="btn btn-danger">Remove Assignment</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>v>

            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<?= $this->endSection(); ?>
