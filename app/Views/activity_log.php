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
          <img src="https://ui-avatars.com/api/?name=<?= urlencode($userName) ?>&size=40" alt="Profile" class="profile-pic">
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
          placeholder="Search by user or activity..." 
          onkeyup="filterLogs()"
        >
        <button class="clear-search" id="clearSearch" onclick="clearSearch()" style="display: none;">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="filter-group">
        <label for="dateFilter">
          <i class="fas fa-calendar"></i> Date:
        </label>
        <select id="dateFilter" onchange="filterLogs()">
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
      <div class="diagnosis-scroll">          
        <div class="section-header">
          <h2>Recent Activities</h2>
        </div>

        <!-- Results counter -->
        <div class="results-info">
          Showing <strong id="visibleCount"><?= !empty($logs) ? count($logs) : 0 ?></strong> of <strong id="totalCount"><?= !empty($logs) ? count($logs) : 0 ?></strong> logs
        </div>

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
            <?php foreach ($logs as $log): ?>
              <div class="navbar-row log-row" 
                   data-user="<?= strtolower(isset($log['first_name'], $log['last_name']) ? esc($log['first_name'] . ' ' . $log['last_name']) : esc($log['user_id'])) ?>"
                   data-activity="<?= strtolower(esc($log['activity'])) ?>"
                   data-date="<?= esc($log['log_date']) ?>">
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

          <!-- No results message -->
          <div class="navbar-row empty no-results" style="display: none;">
            <div class="col">
              <i class="fas fa-search" style="font-size: 48px; opacity: 0.3; margin-bottom: 10px;"></i>
              <p>No activity logs found matching your search criteria</p>
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

function filterLogs() {
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  const dateFilter = document.getElementById('dateFilter').value;
  const clearBtn = document.getElementById('clearSearch');
  
  const rows = document.querySelectorAll('.log-row');
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
    const activity = row.dataset.activity;
    const dateStr = row.dataset.date;
    const logDate = new Date(dateStr);
    
    const matchesSearch = user.includes(searchInput) || activity.includes(searchInput);
    
    let matchesDate = true;
    if (dateFilter === 'today') {
      matchesDate = logDate >= today;
    } else if (dateFilter === 'week') {
      matchesDate = logDate >= weekAgo;
    } else if (dateFilter === 'month') {
      matchesDate = logDate >= monthAgo;
    }
    
    if (matchesSearch && matchesDate) {
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
  filterLogs();
  document.getElementById('searchInput').focus();
}

function resetFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('dateFilter').value = '';
  filterLogs();
}

// Real-time search feedback
document.getElementById('searchInput').addEventListener('input', function() {
  filterLogs();
});
</script>

</body>
</html>