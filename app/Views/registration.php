<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="utf-8">
  <title>CacaoDx Registration</title>

  <link rel="stylesheet" href="<?= base_url('assets/styles/registrationstyles.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/loginstyles.css'); ?>">
</head>
<body>

<section class="vh-100 gradient-custom d-flex align-items-center">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-9 col-xl-7">
        <div class="card shadow-lg card-registration">
          <div class="card-body p-5">

            <h3 class="text-center mb-5">Registration Form</h3>

            <?php if (session()->getFlashdata('error')): ?>
              <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
              </div>
            <?php endif; ?>

            <form action="<?= site_url('registration'); ?>" method="post">

              <div class="row">
                <div class="col-md-6 mb-4">
                  <label class="form-label">First Name</label>
                  <input type="text" name="first_name" class="form-control form-control-lg" required>
                </div>

                <div class="col-md-6 mb-4">
                  <label class="form-label">Last Name</label>
                  <input type="text" name="last_name" class="form-control form-control-lg" required>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-4">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" class="form-control form-control-lg" required>
                </div>

                <div class="col-md-6 mb-4">
                  <label class="form-label">Phone Number</label>
                  <input type="tel" name="contact_number" class="form-control form-control-lg" required>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-4">
                  <label class="form-label">Password</label>
                  <input type="password" name="password" class="form-control form-control-lg" required>
                </div>

                <!-- ✅ NEW: Role Selection -->
                <div class="col-md-6 mb-4">
                  <label class="form-label" for="role">Account Type</label>
                  <select name="role" id="role" class="form-control form-control-lg" required style="appearance: auto; -webkit-appearance: menulist; -moz-appearance: menulist; background-color: white; color: #000;">
                    <option value="" style="color: #000;">Select Role</option>
                    <option value="user" style="color: #000;">Regular User</option>
                    <option value="admin" style="color: #000;">Administrator</option>
                  </select>
                  <small class="text-muted d-block mt-1">
                    Regular users have read-only access. Admins can manage content.
                  </small>
                </div>
              </div>

              <div class="mt-4 text-center">
                <button class="btn btn-primary btn-lg px-5" type="submit">
                  Register
                </button>
              </div>

            </form>

            <!-- 🔗 Login Link -->
            <div class="text-center mt-4">
              <p>Already have an account?</p>
              <a href="<?= site_url('login'); ?>" class="btn btn-outline-secondary">
                Go to Login
              </a>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

</body>
</html>