<?php
$userName = $userName ?? (session()->get('first_name') . ' ' . session()->get('last_name'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Activity Logs</title>

  <!-- CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/styles/popup.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/logsstyles.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/sidebar.css'); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="page-wrapper">

  <!-- Sidebar -->
  <?= $this->include('layouts/sidebar'); ?>
  <div id="overlay" class="overlay"></div>

  <!-- Main Content -->
  <main id="mainContent" class="main-content">
    <header class="header">
      <button id="sidebarToggle" class="sidebar-toggle">
        <i class="fas fa-bars"></i>
      </button>
      <h1 class="page-title">Activity Logs</h1>
      <div class="header-right">
        <div class="icons">
          <button class="icon-btn"><i class="fas fa-search"></i></button>
          <button class="icon-btn"><i class="fas fa-bell"></i></button>
        </div>
        <div class="profile-inline">
          <img src="https://via.placeholder.com/40" alt="Profile" class="profile-pic">
          <span class="username"><?= esc($userName) ?></span>
        </div>
      </div>
    </header>

    <section class="logs-section">
      <div class="diagnosis-scroll">          
        <div class="section-header">
            <h2>Recent Activities</h2>
          </div>
          <br>
        <div class="diagnosis-inner">

          <!-- Table Header -->
          <div class="header-row">
            <div class="col">ID</div>
            <div class="col">User</div>
            <div class="col">Activity</div>
            <div class="col">Date</div>
          </div>

          <!-- Data Rows -->
          <?php if (!empty($logs) && is_array($logs)): ?>
            <?php $rowIndex = 0; ?>
            <?php foreach ($logs as $log): ?>
              <div class="navbar-row <?= ($rowIndex++ % 2 == 1) ? 'alt-row' : '' ?>">
                <div class="col"><?= esc($log['id']) ?></div>
                <div class="col">
                  <?= isset($log['first_name'], $log['last_name']) ? esc($log['first_name'] . ' ' . $log['last_name']) : esc($log['user_id']) ?>
                </div>
                <div class="col"><?= esc($log['activity']) ?></div>
                <div class="col"><?= date('F j, Y', strtotime($log['log_date'])) ?></div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="navbar-row empty">
              <div class="col" style="text-align:center;">No logs found.</div>
            </div>
          <?php endif; ?>

        </div>
      </div>

      <!-- Pagination -->
      <div class="pagination">
        <a href="?page=<?= max(1, $currentPage - 1) ?>">
          <button <?= ($currentPage <= 1) ? 'disabled' : '' ?>>← Prev</button>
        </a>

        <span id="pageInfo">
          Page <?= $currentPage ?> of <?= $totalPages ?>
        </span>

        <a href="?page=<?= ($currentPage >= $totalPages) ? $totalPages : $currentPage + 1 ?>">
          <button <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>>Next →</button>
        </a>
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

  function setAria(expanded) {
    toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
  }

  function isMobile() { return window.innerWidth <= 900; }

  toggle.addEventListener('click', () => {
    if (isMobile()) {
      const open = sidebar.classList.toggle('open');
      overlay.classList.toggle('show', open);
      setAria(open);
      document.body.classList.toggle('no-scroll', open);
    } else {
      const collapsed = sidebar.classList.toggle('collapsed');
      setAria(!collapsed);
    }
  });

  overlay.addEventListener('click', () => {
    sidebar.classList.remove('open');
    overlay.classList.remove('show');
    document.body.classList.remove('no-scroll');
    setAria(false);
  });
})();
</script>

</body>
</html>
