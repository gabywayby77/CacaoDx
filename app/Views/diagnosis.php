<?php
$current_page = service('uri')->getSegment(1);
$userName = session()->get('first_name') . ' ' . session()->get('last_name');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Diagnosis Records</title>
  <link rel="stylesheet" href="<?= base_url('assets/styles/popup.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/logsstyles.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/diagnosisstyles.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/sidebar.css'); ?>">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="page-wrapper">

  <?= $this->include('layouts/sidebar'); ?>

  <div id="overlay" class="overlay"></div>

  <main id="mainContent" class="main-content">
    <header class="header">
      <button id="sidebarToggle" class="sidebar-toggle">
        <i class="fas fa-bars"></i>
      </button>
      <h1 class="page-title">Diagnosis Records</h1>
      <div class="header-right">
        <div class="icons">
          <button class="icon-btn"><i class="fas fa-search"></i></button>
          <button class="icon-btn"><i class="fas fa-bell"></i></button>
        </div>
        <div class="profile-inline">
          <img src="https://via.placeholder.com/40">
          <span class="username"><?= esc($userName) ?></span>
        </div>
      </div>
    </header>

    <section class="logs-section">
      <div class="section-header">
        <h2>Diagnosis History</h2>
      </div>

      <div class="diagnosis-scroll">
        <div class="diagnosis-inner">

          <div class="header-row">
            <div class="col">ID</div>
            <div class="col">User</div>
            <div class="col">Disease</div>
            <div class="col">Treatment</div>
            <div class="col">Confidence</div>
            <div class="col">Notes</div>
            <div class="col">Prevention</div>
            <div class="col">Recommended</div>
            <div class="col">Date</div>
          </div>

          <?php if (!empty($diagnosis)): ?>
            <?php $rowIndex = 0; ?>
            <?php foreach ($diagnosis as $row): ?>
              <div class="navbar-row <?= ($rowIndex++ % 2 == 1) ? 'alt-row' : '' ?>">
                <div class="col"><?= esc($row['id']) ?></div>
                <div class="col"><?= esc($row['first_name'] . ' ' . $row['last_name']) ?></div>
                <div class="col"><?= esc($row['disease_name']) ?></div>
                <div class="col"><?= esc($row['treatment_name']) ?></div>
                <div class="col"><?= esc($row['confidence']) ?>%</div>
                <div class="col"><?= esc($row['notes']) ?></div>
                <div class="col"><?= esc($row['prevention']) ?></div>
                <div class="col"><?= esc($row['recommended_action']) ?></div>
                <div class="col"><?= date('F j, Y', strtotime($row['diagnosis_date'])) ?></div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="navbar-row">
              <div class="col" style="text-align:center; color:var(--muted); font-style:italic;">
                No diagnosis records found.
              </div>
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

<script src="<?= base_url('assets/scripts/sidebar.js'); ?>"></script>

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
