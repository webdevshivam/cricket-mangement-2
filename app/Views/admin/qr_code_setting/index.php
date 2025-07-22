<?= $this->extend('layouts/admin'); ?>
<?= $this->section('content'); ?>



<div class="card bg-dark text-white border-secondary">
  <div class="card-body">


    <div class="row">
      <div class="col-md-12">
        <?php
        helper('form');
        ?>
        <?= form_open_multipart('admin/qr-code-setting/update-setting') ?>

        <input type="hidden" name="id" value="<?= esc($setting['id']) ?>">

        <div class="mb-3">
          <label>UPI Handler Name</label>
          <input type="text" name="upi_handler_name" class="form-control" value="<?= esc($setting['upi_handler_name']) ?>" required>
        </div>

        <div class="mb-3">
          <label>UPI Number</label>
          <input type="text" name="upi_number" class="form-control" value="<?= esc($setting['upi_number']) ?>" required>
        </div>

        <div class="mb-3">
          <label>UPI ID</label>
          <input type="text" name="upi_id" class="form-control" value="<?= esc($setting['upi_id']) ?>" required>
        </div>

        <div class="mb-3">
          <label>QR Code</label><br>
          <?php if (!empty($setting['qr_code'])): ?>
            <img src="<?= base_url('uploads/qr_codes/' . $setting['qr_code']) ?>" alt="QR Code" height="100"><br>
          <?php endif; ?>
          <input type="file" name="qr_code" class="form-control mt-2">
        </div>

        <button type="submit" class="btn btn-warning text-black">Update</button>

        <?= form_close() ?>




      </div>
    </div>

  </div>
</div>

</div>
</div>

<?= $this->endSection(); ?>
