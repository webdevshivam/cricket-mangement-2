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
      <div class="col-md-12">
        <!-- show errors all -->
        <?php if (session()->getFlashdata('errors')): ?>
          <div class="alert alert-danger bg-light border border-danger text-danger fw-bold">
            <ul class="mb-0 list-unstyled">
              <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li>
                  <i class="fas fa-exclamation-circle text-danger me-2"></i>
                  <?= esc($error) ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>




        <form action="<?= base_url('admin/players/save') ?>" method="post">
          <div class="row">

            <!-- Player Name -->
            <div class="col-md-6 mb-3">
              <label for="full_name" class="form-label">Player Name</label>
              <input type="text" class="form-control" id="full_name" name="name" placeholder="Enter player name" required>
            </div>

            <!-- Player Age -->
            <div class="col-md-6 mb-3">
              <label for="age" class="form-label">Player Age</label>
              <input type="number" class="form-control" id="age" name="age" placeholder="Enter player age" required>
            </div>

            <!-- Mobile Number -->
            <div class="col-md-6 mb-3">
              <label for="mobile_number" class="form-label">Player Mobile Number</label>
              <input type="tel" class="form-control" id="mobile_number" name="mobile_number" placeholder="Enter mobile number" required>
            </div>

            <!-- Email -->
            <div class="col-md-6 mb-3">
              <label for="email" class="form-label">Player Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
            </div>

            <!-- Cricketer Type -->
            <div class="col-md-12 mb-3">
              <label for="cricketer_type" class="form-label">Cricketer Type</label>
              <select class="form-select" id="cricketer_type" name="cricketer_type" required onchange="generatePaymentLink()">
                <option selected disabled>Select Cricketer Type</option>
                <option value="bowler">Bowler - ₹999</option>
                <option value="batsman">Batsman - ₹999</option>
                <option value="all rounder">All Rounder - ₹1199</option>
                <option value="wicket keeper">Wicket Keeper - ₹1199</option>
              </select>
            </div>

            <!-- State -->
            <div class="col-md-6 mb-3">
              <label for="state" class="form-label">Player State</label>
              <input type="text" class="form-control" id="state" name="state" placeholder="Enter state" required>
            </div>

            <!-- City -->
            <div class="col-md-6 mb-3">
              <label for="city" class="form-label">Player City</label>
              <input type="text" class="form-control" id="city" name="city" placeholder="Enter city" required>
            </div>

            <!-- Trial City -->
            <div class="col-md-12 mb-3">
              <label for="trial_city" class="form-label">Choose Player Trial City</label>
              <select class="form-select" id="trial_city" name="trial_city" required>
                <option value="">Select Trial City</option>
                <?php foreach ($trialCities as $city): ?>
                  <option value="<?= esc($city['id']) ?>"><?= esc($city['city_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

          </div>

          <button type="submit" class="btn btn-warning">Save</button>
        </form>
      </div>
    </div>

  </div>
</div>

</div>
</div>

<?= $this->endSection(); ?>
