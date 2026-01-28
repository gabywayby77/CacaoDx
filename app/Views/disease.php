<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Diseases - CacaoDX</title>

  <!-- CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/styles/popup.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/diseasestyles.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/sidebar.css'); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <style>
    /* Select dropdown styling for modals */
    .form-group select {
      width: 100%;
      padding: 12px 14px;
      border: 2px solid var(--border);
      border-radius: var(--radius-md);
      font-size: 14px;
      font-weight: 500;
      background: white;
      color: var(--text);
      cursor: pointer;
      transition: all var(--transition);
      outline: none;
    }

    .form-group select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(211, 76, 78, 0.1);
    }

    .form-group select:hover {
      border-color: var(--primary-light);
    }

    /* Updated column widths for disease table with plant part */
    .header-row .col:nth-child(1), .navbar-row .col:nth-child(1) { flex: 1 1 180px; } /* Name */
    .header-row .col:nth-child(2), .navbar-row .col:nth-child(2) { flex: 0 0 120px; } /* Type */
    .header-row .col:nth-child(3), .navbar-row .col:nth-child(3) { flex: 2 1 280px; } /* Cause */
    .header-row .col:nth-child(4), .navbar-row .col:nth-child(4) { flex: 0 0 120px; } /* Plant Part */
    .header-row .col:nth-child(5), .navbar-row .col:nth-child(5) { flex: 0 0 150px; } /* Actions */
  </style>
</head>

<body>
<?php
  helper('auth');
  
  $userName = $userName ?? (session()->get('first_name') . ' ' . session()->get('last_name'));
  $avatar = 'https://ui-avatars.com/api/?name='.urlencode($userName).'&background=d34c4e&color=fff&size=200&bold=true';
?>

<div class="page-wrapper">

  <?= $this->include('layouts/sidebar'); ?>
  <div id="overlay" class="overlay"></div>

  <main id="mainContent" class="main-content">

    <!-- HEADER -->
    <header class="header">
      <div style="display: flex; align-items: center; gap: 16px;">
        <button id="sidebarToggle" class="sidebar-toggle">
          <i class="fas fa-bars"></i>
        </button>
        <h1 class="page-title">Diseases Management</h1>
      </div>

      <div class="header-right">
        <div class="icons">
          <button class="icon-btn" title="Search">
            <i class="fas fa-search"></i>
          </button>
          <button class="icon-btn" title="Notifications">
            <i class="fas fa-bell"></i>
          </button>
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

    <!-- Success/Error Messages -->
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?= session()->getFlashdata('success') ?>
      </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <?= session()->getFlashdata('error') ?>
      </div>
    <?php endif; ?>

    <!-- SEARCH & FILTER BAR -->
    <div class="search-filter-bar">
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input 
          type="text" 
          id="searchInput" 
          placeholder="Search by disease name or cause..." 
          onkeyup="filterDiseases()"
        >
        <button class="clear-search" id="clearSearch" onclick="clearSearch()" style="display: none;">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="filter-group">
        <label for="typeFilter">
          <i class="fas fa-tag"></i> Type:
        </label>
        <select id="typeFilter" onchange="filterDiseases()">
          <option value="">All Types</option>
          <?php 
          // Get unique disease types
          $types = array_unique(array_column($diseases ?? [], 'type'));
          foreach ($types as $type): 
          ?>
            <option value="<?= strtolower(esc($type)) ?>"><?= esc($type) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <button class="reset-filters" onclick="resetFilters()">
        <i class="fas fa-redo"></i> Reset
      </button>

      <?php if (is_admin()): ?>
      <button class="add-btn" onclick="openAddDiseaseModal()">
        <i class="fas fa-plus"></i> Add Disease
      </button>
      <?php endif; ?>
    </div>

    <!-- DISEASES -->
    <section class="logs-section">
      <div class="diagnosis-scroll">

        <div class="section-header">
          <h2>Disease List</h2>
        </div>

        <!-- Results counter -->
        <div class="results-info">
          Showing <strong id="visibleCount"><?= !empty($diseases) ? count($diseases) : 0 ?></strong> of <strong id="totalCount"><?= !empty($diseases) ? count($diseases) : 0 ?></strong> diseases
        </div>

        <div class="disease-inner">

          <div class="header-row">
            <div class="col">Name</div>
            <div class="col">Type</div>
            <div class="col">Cause</div>
            <div class="col">Plant Part</div>
            <?php if (is_admin()): ?>
            <div class="col">Actions</div>
            <?php endif; ?>
          </div>

          <?php if (!empty($diseases)): ?>
            <?php foreach ($diseases as $disease): ?>
              <div class="navbar-row disease-row"
                   data-name="<?= strtolower(esc($disease['name'])) ?>"
                   data-type="<?= strtolower(esc($disease['type'])) ?>"
                   data-cause="<?= strtolower(esc($disease['cause'])) ?>">
                <div class="col"><?= esc($disease['name']) ?></div>
                <div class="col"><?= esc($disease['type']) ?></div>
                <div class="col"><?= esc($disease['cause']) ?></div>
                <div class="col"><?= esc($disease['plant_part_name'] ?? 'N/A') ?></div>

                <?php if (is_admin()): ?>
                <div class="col actions">
                  <button class="action-btn edit-btn"
                    onclick="openEditDiseaseModal(
                      '<?= $disease['id'] ?>',
                      '<?= esc($disease['name']) ?>',
                      '<?= esc($disease['type']) ?>',
                      '<?= esc($disease['cause']) ?>',
                      '<?= esc($disease['plant_part_id']) ?>'
                    )">
                    <i class="fas fa-edit"></i>
                  </button>

                  <button class="action-btn delete-btn"
                    onclick="openDeleteDiseaseModal('<?= $disease['id'] ?>')">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="navbar-row empty">
              <div class="col" style="text-align:center;">No diseases found.</div>
            </div>
          <?php endif; ?>

          <!-- No results message -->
          <div class="navbar-row empty no-results" style="display: none;">
            <div class="col">
              <i class="fas fa-search" style="font-size: 48px; opacity: 0.3; margin-bottom: 10px;"></i>
              <p>No diseases found matching your search criteria</p>
            </div>
          </div>

        </div>
      </div>
    </section>
  </main>
</div>

<!-- ================= ADD DISEASE ================= -->
<div id="addDiseaseModal" class="popup-overlay">
  <div class="form-card">
    <div class="popup-header">Add Disease</div>

    <form method="post" action="<?= site_url('diseases/store') ?>">
      <?= csrf_field() ?>

      <div class="form-grid">
        <div class="form-group">
          <label>Name</label>
          <input type="text" name="name" required>
        </div>

        <div class="form-group">
          <label>Type</label>
          <input type="text" name="type" required>
        </div>

        <div class="form-group full">
          <label>Cause</label>
          <input type="text" name="cause" required>
        </div>

        <div class="form-group">
          <label>Plant Part</label>
          <select name="plant_part_id" required>
            <option value="">Select Plant Part</option>
            <?php foreach ($plantParts ?? [] as $plantPart): ?>
              <option value="<?= esc($plantPart['id']) ?>"><?= esc($plantPart['part']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="btn cancel" onclick="closeAddDiseaseModal()">Cancel</button>
        <button type="submit" class="btn add-btn">
          <i class="fas fa-plus"></i> Add Disease
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ================= EDIT DISEASE ================= -->
<div id="editDiseaseModal" class="popup-overlay">
  <div class="form-card">
    <div class="popup-header">Edit Disease</div>

    <form method="post" action="<?= site_url('diseases/update') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="id" id="edit_disease_id">

      <div class="form-grid">
        <div class="form-group">
          <label>Name</label>
          <input type="text" name="name" id="edit_name" required>
        </div>

        <div class="form-group">
          <label>Type</label>
          <input type="text" name="type" id="edit_type" required>
        </div>

        <div class="form-group full">
          <label>Cause</label>
          <input type="text" name="cause" id="edit_cause" required>
        </div>

        <div class="form-group">
          <label>Plant Part</label>
          <select name="plant_part_id" id="edit_plant_part" required>
            <option value="">Select Plant Part</option>
            <?php foreach ($plantParts ?? [] as $plantPart): ?>
              <option value="<?= esc($plantPart['id']) ?>"><?= esc($plantPart['part']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="btn cancel" onclick="closeEditDiseaseModal()">Cancel</button>
        <button type="submit" class="btn add-btn">
          <i class="fas fa-save"></i> Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ================= DELETE DISEASE ================= -->
<div id="deleteDiseaseModal" class="popup-overlay">
  <div class="form-card">
    <div class="popup-header">Delete Disease</div>

    <form method="post" action="<?= site_url('diseases/delete') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="id" id="delete_disease_id">

      <p style="text-align:center; padding: 20px;">Are you sure you want to delete this disease? This action cannot be undone.</p>

      <div class="form-actions">
        <button type="button" class="btn cancel" onclick="closeDeleteDiseaseModal()">Cancel</button>
        <button type="submit" class="btn danger">
          <i class="fas fa-trash"></i> Delete Disease
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ================= JS ================= -->

<!-- Sidebar Toggle -->
<script>
(function(){
  const sidebar = document.getElementById('sidebar');
  const toggle = document.getElementById('sidebarToggle');
  const overlay = document.getElementById('overlay');

  function isMobile() { return window.innerWidth <= 900; }

  toggle.onclick = () => {
    if (isMobile()) {
      sidebar.classList.toggle('open');
      overlay.classList.toggle('show');
      document.body.classList.toggle('no-scroll');
    } else {
      sidebar.classList.toggle('collapsed');
    }
  };

  overlay.onclick = () => {
    sidebar.classList.remove('open');
    overlay.classList.remove('show');
    document.body.classList.remove('no-scroll');
  };
})();
</script>

<!-- Profile Dropdown -->
<script>
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
</script>

<!-- Search & Filter -->
<script>
function filterDiseases() {
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  const typeFilter = document.getElementById('typeFilter').value.toLowerCase();
  const clearBtn = document.getElementById('clearSearch');
  
  const rows = document.querySelectorAll('.disease-row');
  const noResults = document.querySelector('.no-results');
  let visibleCount = 0;
  const totalCount = rows.length;
  
  clearBtn.style.display = searchInput ? 'flex' : 'none';
  
  rows.forEach(row => {
    const name = row.dataset.name;
    const type = row.dataset.type;
    const cause = row.dataset.cause;
    
    const matchesSearch = name.includes(searchInput) || cause.includes(searchInput);
    const matchesType = !typeFilter || type === typeFilter;
    
    if (matchesSearch && matchesType) {
      row.style.display = 'flex';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });
  
  document.getElementById('visibleCount').textContent = visibleCount;
  document.getElementById('totalCount').textContent = totalCount;
  
  if (visibleCount === 0) {
    noResults.style.display = 'flex';
  } else {
    noResults.style.display = 'none';
  }
}

function clearSearch() {
  document.getElementById('searchInput').value = '';
  filterDiseases();
  document.getElementById('searchInput').focus();
}

function resetFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('typeFilter').value = '';
  filterDiseases();
}

document.getElementById('searchInput').addEventListener('input', function() {
  filterDiseases();
});
</script>

<!-- Modal Functions -->
<script>
const addDiseaseModal = document.getElementById('addDiseaseModal');
const editDiseaseModal = document.getElementById('editDiseaseModal');
const deleteDiseaseModal = document.getElementById('deleteDiseaseModal');

const edit_disease_id = document.getElementById('edit_disease_id');
const edit_name = document.getElementById('edit_name');
const edit_type = document.getElementById('edit_type');
const edit_cause = document.getElementById('edit_cause');
const edit_plant_part = document.getElementById('edit_plant_part');
const delete_disease_id = document.getElementById('delete_disease_id');

function openAddDiseaseModal() {
  addDiseaseModal.classList.add('show');
}

function closeAddDiseaseModal() {
  addDiseaseModal.classList.remove('show');
}

function openEditDiseaseModal(id, n, t, c, p) {
  edit_disease_id.value = id;
  edit_name.value = n;
  edit_type.value = t;
  edit_cause.value = c;
  edit_plant_part.value = p;
  editDiseaseModal.classList.add('show');
}

function closeEditDiseaseModal() {
  editDiseaseModal.classList.remove('show');
}

function openDeleteDiseaseModal(id) {
  delete_disease_id.value = id;
  deleteDiseaseModal.classList.add('show');
}

function closeDeleteDiseaseModal() {
  deleteDiseaseModal.classList.remove('show');
}

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeAddDiseaseModal();
    closeEditDiseaseModal();
    closeDeleteDiseaseModal();
  }
});
</script>

<!-- Auto-hide alerts after 5 seconds -->
<script>
setTimeout(() => {
  document.querySelectorAll('.alert').forEach(alert => {
    alert.style.opacity = '0';
    setTimeout(() => alert.remove(), 300);
  });
}, 5000);
</script>

</body>
</html>