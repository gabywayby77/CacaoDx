<?php
$current_page = service('uri')->getSegment(1);
$userName = session()->get('first_name') . ' ' . session()->get('last_name');
$avatar = 'https://ui-avatars.com/api/?name='.urlencode($userName).'&background=d34c4e&color=fff&size=200&bold=true';
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
      <div style="display: flex; align-items: center; gap: 16px;">
        <button id="sidebarToggle" class="sidebar-toggle">
          <i class="fas fa-bars"></i>
        </button>
        <h1 class="page-title">Diagnosis Records</h1>
      </div>
      <div class="header-right">
        <div class="icons">
          <button class="icon-btn"><i class="fas fa-search"></i></button>
          <button class="icon-btn"><i class="fas fa-bell"></i></button>
        </div>
        
        <!-- PROFILE DROPDOWN -->
        <div class="profile-dropdown-container">
          <div class="profile-inline" onclick="toggleProfileDropdown(event)">
            <img src="<?= $avatar ?>" class="profile-pic" alt="Profile">
            <div>
              <span class="username"><?= esc($userName) ?></span>
              <small style="display: block; font-size: 11px; color: #95a5a6;">
                <?= is_admin() ? 'Administrator' : 'User' ?>
              </small>
            </div>
            <i class="fas fa-chevron-down" style="margin-left: 8px; font-size: 12px; color: #95a5a6; transition: transform 0.3s;"></i>
          </div>

          <div id="profileDropdown" class="profile-dropdown">
            <a href="<?= base_url('profile') ?>" class="dropdown-item">
              <i class="fas fa-user"></i>
              <span>View Profile</span>
            </a>
            <a href="<?= base_url('settings') ?>" class="dropdown-item">
              <i class="fas fa-cog"></i>
              <span>Settings</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="<?= base_url('logout') ?>" class="dropdown-item logout">
              <i class="fas fa-sign-out-alt"></i>
              <span>Sign Out</span>
            </a>
          </div>
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
        <label for="startDate">
          <i class="fas fa-calendar"></i> From:
        </label>
        <input type="date" id="startDate" onchange="filterDiagnosis()" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
      </div>

      <div class="filter-group">
        <label for="endDate">
          <i class="fas fa-calendar"></i> To:
        </label>
        <input type="date" id="endDate" onchange="filterDiagnosis()" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
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
        Showing <strong id="visibleCount"><?= !empty($diagnosis) ? count($diagnosis) : 0 ?></strong> of <strong id="totalCount"><?= $totalRecords ?? 0 ?></strong> records
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

// ================= PROFILE DROPDOWN ================= 

function toggleProfileDropdown(event) {
  event.stopPropagation();
  const dropdown = document.getElementById('profileDropdown');
  const chevron = event.currentTarget.querySelector('.fa-chevron-down');
  
  dropdown.classList.toggle('show');
  
  if (dropdown.classList.contains('show')) {
    chevron.style.transform = 'rotate(180deg)';
  } else {
    chevron.style.transform = 'rotate(0deg)';
  }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
  const dropdown = document.getElementById('profileDropdown');
  const container = event.target.closest('.profile-dropdown-container');
  const chevron = document.querySelector('.profile-inline .fa-chevron-down');
  
  if (!container && dropdown.classList.contains('show')) {
    dropdown.classList.remove('show');
    if (chevron) {
      chevron.style.transform = 'rotate(0deg)';
    }
  }
});

// Close dropdown on escape key
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    const dropdown = document.getElementById('profileDropdown');
    const chevron = document.querySelector('.profile-inline .fa-chevron-down');
    
    if (dropdown.classList.contains('show')) {
      dropdown.classList.remove('show');
      if (chevron) {
        chevron.style.transform = 'rotate(0deg)';
      }
    }
  }
});

// ================= SEARCH & FILTER ================= 

// Store the original total from server
const originalTotal = <?= $totalRecords ?? 0 ?>;

function filterDiagnosis() {
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  const diseaseFilter = document.getElementById('diseaseFilter').value.toLowerCase();
  const startDate = document.getElementById('startDate').value;
  const endDate = document.getElementById('endDate').value;
  const clearBtn = document.getElementById('clearSearch');
  
  const rows = document.querySelectorAll('.diagnosis-row');
  const noResults = document.querySelector('.no-results');
  let visibleCount = 0;
  
  // Show/hide clear button
  clearBtn.style.display = searchInput ? 'flex' : 'none';
  
  rows.forEach(row => {
    const user = row.dataset.user;
    const disease = row.dataset.disease;
    const dateStr = row.dataset.date;
    const diagnosisDate = new Date(dateStr);
    
    const matchesSearch = user.includes(searchInput) || disease.includes(searchInput);
    const matchesDisease = !diseaseFilter || disease === diseaseFilter;
    
    // Date range filtering
    let matchesDate = true;
    if (startDate) {
      const start = new Date(startDate);
      start.setHours(0, 0, 0, 0);
      if (diagnosisDate < start) {
        matchesDate = false;
      }
    }
    if (endDate && matchesDate) {
      const end = new Date(endDate);
      end.setHours(23, 59, 59, 999);
      if (diagnosisDate > end) {
        matchesDate = false;
      }
    }
    
    if (matchesSearch && matchesDisease && matchesDate) {
      row.style.display = 'flex';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });
  
  // Update counters - only update visible count, keep original total
  document.getElementById('visibleCount').textContent = visibleCount;
  document.getElementById('totalCount').textContent = originalTotal;
  
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
  document.getElementById('startDate').value = '';
  document.getElementById('endDate').value = '';
  filterDiagnosis();
}

// Real-time search feedback
document.getElementById('searchInput').addEventListener('input', function() {
  filterDiagnosis();
});
</script>

</body>
</html>