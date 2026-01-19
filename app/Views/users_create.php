<?php
$userName = $userName ?? (session()->get('first_name') . ' ' . session()->get('last_name'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Add User</title>

  <!-- CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/styles/popup.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/sidebar.css'); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="page-wrapper">

  <!-- Sidebar -->
  <?= $this->include('layouts/sidebar'); ?>

  <!-- Overlay -->
  <div id="overlay" class="overlay"></div>

  <!-- Main Content -->
  <main id="mainContent" class="main-content">

    <!-- Header -->
    <header class="header">
      <button id="sidebarToggle" class="sidebar-toggle">
        <i class="fas fa-bars"></i>
      </button>

      <h1 class="page-title">Add User</h1>

      <div class="header-right">
        <div class="profile-inline">
          <img src="https://via.placeholder.com/40" class="profile-pic">
          <span class="username"><?= esc($userName) ?></span>
        </div>
      </div>
    </header>

    <!-- Form Section -->
    <section class="logs-section">

      <div class="form-card">

        <form action="<?= site_url('users/store'); ?>" method="post">
          <?= csrf_field(); ?>

          <div class="form-grid">

            <div class="form-group">
              <label>First Name</label>
              <input type="text" name="first_name" required>
            </div>

            <div class="form-group">
              <label>Last Name</label>
              <input type="text" name="last_name" required>
            </div>

            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" required>
            </div>

            <div class="form-group">
              <label>Password</label>
              <input type="password" name="password" required>
            </div>

            <div class="form-group full">
              <label>Role</label>
              <select name="role" required>
                <option value="admin">Admin</option>
                <option value="user">User</option>
              </select>
            </div>

          </div>

          <!-- Buttons -->
          <div class="form-actions">
            <a href="<?= site_url('users'); ?>" class="btn cancel">Cancel</a>
            <button type="submit" class="btn add-btn">
              <i class="fas fa-save"></i> Save User
            </button>
          </div>

        </form>

      </div>

    </section>

  </main>

</div>

<!-- Sidebar Toggle JS -->
<script>
(function() {
  const sidebar = document.getElementById('sidebar');
  const toggle = document.getElementById('sidebarToggle');
  const overlay = document.getElementById('overlay');

  toggle.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    overlay.classList.toggle('show');
  });

  overlay.addEventListener('click', () => {
    sidebar.classList.remove('open');
    overlay.classList.remove('show');
  });
})();
</script>

<script>
function openPopup(message) {
  document.querySelector('.popup-body').innerText = message;
  document.getElementById('popupOverlay').classList.add('show');
}

function closePopup() {
  document.getElementById('popupOverlay').classList.remove('show');
}
</script>

<!-- Popup -->
<div id="popupOverlay" class="popup-overlay">
  <div class="popup-box">
    <div class="popup-header">Success</div>
    <div class="popup-body">
      User saved successfully.
    </div>
    <div class="popup-actions">
      <button class="close-btn" onclick="closePopup()">OK</button>
    </div>
  </div>
</div>

</body>
</html>
