<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />

  <title><?= esc($title ?? 'CacaoDX') ?></title>

  <!-- Global CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/styles/sidebar.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/dashboardstyles.css'); ?>">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<div class="page-wrapper">

  <?= $this->include('layouts/sidebar'); ?>

  <div id="overlay" class="overlay"></div>

  <!-- 🔥 SPA CONTENT TARGET -->
  <main id="app-content" class="main-content">
    <?= $this->renderSection('content') ?>
  </main>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- SPA Navigation -->
<script src="<?= base_url('assets/js/spa-navigation.js') ?>"></script>

</body>
</html>
