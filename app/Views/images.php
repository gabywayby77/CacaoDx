<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Images</title>

  <!-- CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/styles/popup.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/dashboardstyles.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/imagestyles.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/sidebar.css'); ?>">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<?php
  $userName = $userName ?? (session()->get('first_name') . ' ' . session()->get('last_name'));

  // FINAL DATASET CATEGORIES
  $folders = [
    'healthy',
    'black_pod_disease',
    'frosty_pod_rot',
    'mirid_bug'
  ];
?>

<div class="page-wrapper">

  <!-- Sidebar -->
  <?= $this->include('layouts/sidebar'); ?>

  <!-- Overlay -->
  <div id="overlay" class="overlay" tabindex="-1" aria-hidden="true"></div>

  <!-- Main -->
  <main id="mainContent" class="main-content">
    <header class="header">
      <button id="sidebarToggle" class="sidebar-toggle">
        <i class="fas fa-bars"></i>
      </button>

      <h1 class="page-title">Image Uploads</h1>

      <div class="header-right">
        <div class="icons">
          <button class="icon-btn"><i class="fas fa-search"></i></button>
          <button class="icon-btn"><i class="fas fa-bell"></i></button>
        </div>

        <div class="profile-inline">
          <img src="<?= base_url('assets/images/cacao-logo.png'); ?>" class="profile-pic" alt="CacaoDx">
          <span class="username"><?= esc($userName) ?></span>
        </div>
      </div>
    </header>

    <!-- CONTENT -->
    <section class="content images-grid">

      <?php foreach ($folders as $folder):

        $path = FCPATH . "uploads/{$folder}/";

        if (!is_dir($path)) {
          mkdir($path, 0777, true);
        }

        $images = array_values(array_diff(scandir($path), ['.', '..']));
        $preview = array_slice($images, 0, 3);
      ?>

      <div class="folder-card">
        <h3><?= ucwords(str_replace('_', ' ', $folder)) ?></h3>

        <div class="folder-preview">
          <?php if (!empty($preview)): ?>
            <?php foreach ($preview as $img): ?>
              <img src="<?= base_url("uploads/{$folder}/{$img}") ?>" alt="<?= esc($img) ?>">
            <?php endforeach; ?>
          <?php else: ?>
            <p class="empty">No images uploaded</p>
          <?php endif; ?>
        </div>

        <form action="<?= base_url('images/upload/' . $folder) ?>" method="post" enctype="multipart/form-data">
          <input type="file" name="image[]" multiple accept="image/*" required>
          <button type="submit" class="add-btn">
            <i class="fas fa-upload"></i> Upload
          </button>
        </form>
      </div>

      <?php endforeach; ?>

    </section>
  </main>
</div>

<!-- Sidebar Script -->
<script>
(function () {
  const sidebar = document.getElementById('sidebar');
  const toggle = document.getElementById('sidebarToggle');
  const overlay = document.getElementById('overlay');

  function isMobile() { return window.innerWidth <= 900; }

  toggle.addEventListener('click', () => {
    if (isMobile()) {
      sidebar.classList.toggle('open');
      overlay.classList.toggle('show');
      document.body.classList.toggle('no-scroll');
    } else {
      sidebar.classList.toggle('collapsed');
    }
  });

  overlay.addEventListener('click', () => {
    sidebar.classList.remove('open');
    overlay.classList.remove('show');
    document.body.classList.remove('no-scroll');
  });
})();
</script>

</body>
</html>
