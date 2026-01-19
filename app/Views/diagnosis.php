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
          <img src="https://ui-avatars.com/api/?name=<?= urlencode($userName) ?>&size=40">
          <span class="username"><?= esc($userName) ?></span>
        </div>
      </div>
    </header>

    <!-- SEARCH & FILTER BAR -->
    <div class="search-filter-bar">
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input 
          type="text" 
          id="searchInput" 
          placeholder="Search by user or disease..." 
          onkeyup="filterDiagnosis()"
        >
        <button class="clear-search" id="clearSearch" onclick="clearSearch()" style="display: none;">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="filter-group">
        <label for="diseaseFilter">
          <i class="fas fa-disease"></i> Disease:
        </label>
        <select id="diseaseFilter" onchange="filterDiagnosis()">
          <option value="">All Diseases</option>
          <?php 
          // Get unique diseases
          $diseases = array_unique(array_column($diagnosis ?? [], 'disease_name'));
          foreach ($diseases as $disease): 
            if (!empty($disease)):
          ?>
            <option value="<?= strtolower(esc($disease)) ?>"><?= esc($disease) ?></option>
          <?php 
            endif;
          endforeach; 
          ?>
        </select>
      </div>

      <div class="filter-group">
        <label for="dateFilter">
          <i class="fas fa-calendar"></i> Date:
        </label>
        <select id="dateFilter" onchange="filterDiagnosis()">
          <option value="">All Time</option>
          <option value="today">Today</option>
          <option value="week">This Week</option>
          <option value="month">This Month</option>
        </select>
      </div>

      <button class="reset-filters" onclick="resetFilters()">
        <i class="fas fa-redo"></i> Reset
      </button>
    </div>

    <section class="logs-section">
      <div class="section-header">
        <h2>Diagnosis History</h2>
      </div>

      <!-- Results counter -->
      <div class="results-info">
        Showing <strong id="visibleCount"><?= !empty($diagnosis) ? count($diagnosis) : 0 ?></strong> of <strong id="totalCount"><?= !empty($diagnosis) ? count($diagnosis) : 0 ?></strong> records
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
            <?php foreach ($diagnosis as $row): ?>
              <div class="navbar-row diagnosis-row"
                   data-user="<?= strtolower(esc($row['first_name'] . ' ' . $row['last_name'])) ?>"
                   data-disease="<?= strtolower(esc($row['disease_name'])) ?>"
                   data-date="<?= esc($row['diagnosis_date']) ?>">
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
            <div class="navbar-row empty">
              <div class="col" style="text-align:center; color:var(--muted); font-style:italic;">
                No diagnosis records found.
              </div>
            </div>
          <?php endif; ?>

          <!-- No results message -->
          <div class="navbar-row empty no-results" style="display: none;">
            <div class="col">
              <i class="fas fa-search" style="font-size: 48px; opacity: 0.3; margin-bottom: 10px;"></i>
              <p>No diagnosis records found matching your search criteria</p>
            </div>
          </div>

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

// ================= SEARCH & FILTER ================= 

function filterDiagnosis() {
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  const diseaseFilter = document.getElementById('diseaseFilter').value.toLowerCase();
  const dateFilter = document.getElementById('dateFilter').value;
  const clearBtn = document.getElementById('clearSearch');
  
  const rows = document.querySelectorAll('.diagnosis-row');
  const noResults = document.querySelector('.no-results');
  let visibleCount = 0;
  const totalCount = rows.length;
  
  // Show/hide clear button
  clearBtn.style.display = searchInput ? 'flex' : 'none';
  
  const now = new Date();
  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
  const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
  const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
  
  rows.forEach(row => {
    const user = row.dataset.user;
    const disease = row.dataset.disease;
    const dateStr = row.dataset.date;
    const diagnosisDate = new Date(dateStr);
    
    const matchesSearch = user.includes(searchInput) || disease.includes(searchInput);
    const matchesDisease = !diseaseFilter || disease === diseaseFilter;
    
    let matchesDate = true;
    if (dateFilter === 'today') {
      matchesDate = diagnosisDate >= today;
    } else if (dateFilter === 'week') {
      matchesDate = diagnosisDate >= weekAgo;
    } else if (dateFilter === 'month') {
      matchesDate = diagnosisDate >= monthAgo;
    }
    
    if (matchesSearch && matchesDisease && matchesDate) {
      row.style.display = 'flex';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });
  
  // Update counters
  document.getElementById('visibleCount').textContent = visibleCount;
  document.getElementById('totalCount').textContent = totalCount;
  
  // Show/hide no results message
  if (visibleCount === 0) {
    noResults.style.display = 'flex';
  } else {
    noResults.style.display = 'none';
  }
}

function clearSearch() {
  document.getElementById('searchInput').value = '';
  filterDiagnosis();
  document.getElementById('searchInput').focus();
}

function resetFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('diseaseFilter').value = '';
  document.getElementById('dateFilter').value = '';
  filterDiagnosis();
}

// Real-time search feedback
document.getElementById('searchInput').addEventListener('input', function() {
  filterDiagnosis();
});
</script>

</body>
</html>