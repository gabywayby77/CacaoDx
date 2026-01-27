<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pests - CacaoDX</title>

  <!-- CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/styles/popup.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/pestsstyles.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/sidebar.css'); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<?php
  helper('auth');
  
  $userName = $userName ?? (session()->get('first_name') . ' ' . session()->get('last_name'));
  $avatar = 'https://ui-avatars.com/api/?name='.urlencode($userName).'&background=d34c4e&color=fff&size=200&bold=true';
?>

<div class="page-wrapper">

  <!-- Sidebar -->
  <?= $this->include('layouts/sidebar'); ?>

  <!-- Overlay -->
  <div id="overlay" class="overlay"></div>

  <!-- Main Content -->
  <main id="mainContent" class="main-content">

    <!-- Header -->
    <header class="header">
      <div style="display: flex; align-items: center; gap: 16px;">
        <button id="sidebarToggle" class="sidebar-toggle">
          <i class="fas fa-bars"></i>
        </button>
        <h1 class="page-title">Pests Management</h1>
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
          placeholder="Search by pest name or scientific name..." 
          onkeyup="filterPests()"
        >
        <button class="clear-search" id="clearSearch" onclick="clearSearch()" style="display: none;">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="filter-group">
        <label for="familyFilter">
          <i class="fas fa-sitemap"></i> Family:
        </label>
        <select id="familyFilter" onchange="filterPests()">
          <option value="">All Families</option>
          <?php 
          // Get unique pest families
          $families = array_unique(array_column($pests ?? [], 'family'));
          foreach ($families as $family): 
            if (!empty($family)):
          ?>
            <option value="<?= strtolower(esc($family)) ?>"><?= esc($family) ?></option>
          <?php 
            endif;
          endforeach; 
          ?>
        </select>
      </div>

      <button class="reset-filters" onclick="resetFilters()">
        <i class="fas fa-redo"></i> Reset
      </button>

      <button class="add-btn" onclick="openPestModal()">
        <i class="fas fa-plus"></i> Add Pest
      </button>
    </div>

    <!-- Pests Section -->
    <section class="logs-section">
      <div class="diagnosis-scroll">

        <!-- Section Header -->
        <div class="section-header">
          <h2>Pest List</h2>
        </div>

        <!-- Results counter -->
        <div class="results-info">
          Showing <strong id="visibleCount"><?= !empty($pests) ? count($pests) : 0 ?></strong> of <strong id="totalCount"><?= !empty($pests) ? count($pests) : 0 ?></strong> pests
        </div>

        <!-- Table -->
        <div class="users-inner">

          <div class="header-row">
            <div class="col">ID</div>
            <div class="col">Name</div>
            <div class="col">Scientific Name</div>
            <div class="col">Family</div>
            <div class="col">Description</div>
            <div class="col">Damage</div>
            <div class="col">Plant Part</div>
            <div class="col">Actions</div>
          </div>

          <?php if (!empty($pests)): ?>
            <?php foreach ($pests as $pest): ?>
              <div class="navbar-row pest-row"
                   data-name="<?= strtolower(esc($pest['name'])) ?>"
                   data-scientific="<?= strtolower(esc($pest['scientific_name'])) ?>"
                   data-family="<?= strtolower(esc($pest['family'])) ?>">
                <div class="col"><?= esc($pest['id']) ?></div>
                <div class="col"><?= esc($pest['name']) ?></div>
                <div class="col"><?= esc($pest['scientific_name']) ?></div>
                <div class="col"><?= esc($pest['family']) ?></div>
                <div class="col"><?= esc($pest['description']) ?></div>
                <div class="col"><?= esc($pest['damage']) ?></div>
                <div class="col"><?= esc($pest['plant_part_id']) ?></div>

                <div class="col actions">
                  <button class="action-btn edit-btn"
                    onclick="openEditPestModal(
                      '<?= $pest['id'] ?>',
                      '<?= esc($pest['name']) ?>',
                      '<?= esc($pest['scientific_name']) ?>',
                      '<?= esc($pest['family']) ?>',
                      '<?= esc($pest['description']) ?>',
                      '<?= esc($pest['damage']) ?>',
                      '<?= $pest['plant_part_id'] ?>'
                    )">
                    <i class="fas fa-edit"></i>
                  </button>

                  <button class="action-btn delete-btn"
                    onclick="openDeletePestModal('<?= $pest['id'] ?>')">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="navbar-row empty">
              <div class="col" style="text-align:center;">No pests found.</div>
            </div>
          <?php endif; ?>

          <!-- No results message -->
          <div class="navbar-row empty no-results" style="display: none;">
            <div class="col">
              <i class="fas fa-search" style="font-size: 48px; opacity: 0.3; margin-bottom: 10px;"></i>
              <p>No pests found matching your search criteria</p>
            </div>
          </div>

        </div>
      </div>
    </section>

  </main>
</div>

<!-- ADD PEST MODAL -->
<div id="pestModal" class="popup-overlay">
  <div class="form-card">

    <div class="popup-header">Add Pest</div>

    <form action="<?= site_url('pests/store'); ?>" method="post">
      <?= csrf_field(); ?>

      <div class="form-grid">
        <div class="form-group">
          <label>Pest Name</label>
          <input type="text" name="name" required>
        </div>

        <div class="form-group">
          <label>Scientific Name</label>
          <input type="text" name="scientific_name" required>
        </div>

        <div class="form-group">
          <label>Family</label>
          <input type="text" name="family">
        </div>

        <div class="form-group">
          <label>Plant Part ID</label>
          <input type="number" name="plant_part_id" required>
        </div>

        <div class="form-group full">
          <label>Description</label>
          <textarea name="description" required></textarea>
        </div>

        <div class="form-group full">
          <label>Damage</label>
          <textarea name="damage" required></textarea>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="btn cancel" onclick="closePestModal()">Cancel</button>
        <button type="submit" class="btn add-btn">
          <i class="fas fa-save"></i> Save Pest
        </button>
      </div>
    </form>

  </div>
</div>

<!-- EDIT PEST MODAL -->
<div id="editPestModal" class="popup-overlay">
  <div class="form-card">

    <div class="popup-header">Edit Pest</div>

    <form action="<?= site_url('pests/update'); ?>" method="post">
      <?= csrf_field(); ?>
      <input type="hidden" name="id" id="edit_id">

      <div class="form-grid">
        <div class="form-group">
          <label>Pest Name</label>
          <input type="text" name="name" id="edit_name" required>
        </div>

        <div class="form-group">
          <label>Scientific Name</label>
          <input type="text" name="scientific_name" id="edit_scientific" required>
        </div>

        <div class="form-group">
          <label>Family</label>
          <input type="text" name="family" id="edit_family">
        </div>

        <div class="form-group">
          <label>Plant Part ID</label>
          <input type="number" name="plant_part_id" id="edit_plant_part" required>
        </div>

        <div class="form-group full">
          <label>Description</label>
          <textarea name="description" id="edit_description" required></textarea>
        </div>

        <div class="form-group full">
          <label>Damage</label>
          <textarea name="damage" id="edit_damage" required></textarea>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="btn cancel" onclick="closeEditPestModal()">Cancel</button>
        <button type="submit" class="btn add-btn">
          <i class="fas fa-save"></i> Update Pest
        </button>
      </div>
    </form>

  </div>
</div>

<!-- DELETE PEST MODAL -->
<div id="deletePestModal" class="popup-overlay">
  <div class="form-card">

    <div class="popup-header">Delete Pest</div>

    <p style="text-align:center; padding: 20px;">Are you sure you want to delete this pest? This action cannot be undone.</p>

    <form action="<?= site_url('pests/delete'); ?>" method="post">
      <?= csrf_field(); ?>
      <input type="hidden" name="id" id="delete_pest_id">

      <div class="form-actions">
        <button type="button" class="btn cancel" onclick="closeDeletePestModal()">Cancel</button>
        <button type="submit" class="btn danger">
          <i class="fas fa-trash"></i> Delete Pest
        </button>
      </div>
    </form>

  </div>
</div>

<!-- ================= SCRIPTS ================= -->

<!-- Sidebar Toggle -->
<script>
(function () {
  const sidebar = document.getElementById('sidebar');
  const toggle = document.getElementById('sidebarToggle');
  const overlay = document.getElementById('overlay');

  function isMobile() {
    return window.innerWidth <= 900;
  }

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
function filterPests() {
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  const familyFilter = document.getElementById('familyFilter').value.toLowerCase();
  const clearBtn = document.getElementById('clearSearch');
  
  const rows = document.querySelectorAll('.pest-row');
  const noResults = document.querySelector('.no-results');
  let visibleCount = 0;
  const totalCount = rows.length;
  
  clearBtn.style.display = searchInput ? 'flex' : 'none';
  
  rows.forEach(row => {
    const name = row.dataset.name;
    const scientific = row.dataset.scientific;
    const family = row.dataset.family;
    
    const matchesSearch = name.includes(searchInput) || scientific.includes(searchInput);
    const matchesFamily = !familyFilter || family === familyFilter;
    
    if (matchesSearch && matchesFamily) {
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
  filterPests();
  document.getElementById('searchInput').focus();
}

function resetFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('familyFilter').value = '';
  filterPests();
}

document.getElementById('searchInput').addEventListener('input', function() {
  filterPests();
});
</script>

<!-- Modal Functions -->
<script>
function openPestModal() {
  document.getElementById('pestModal').classList.add('show');
}

function closePestModal() {
  document.getElementById('pestModal').classList.remove('show');
}

function openEditPestModal(id, name, sci, family, desc, damage, plant) {
  document.getElementById('edit_id').value = id;
  document.getElementById('edit_name').value = name;
  document.getElementById('edit_scientific').value = sci;
  document.getElementById('edit_family').value = family;
  document.getElementById('edit_description').value = desc;
  document.getElementById('edit_damage').value = damage;
  document.getElementById('edit_plant_part').value = plant;
  document.getElementById('editPestModal').classList.add('show');
}

function closeEditPestModal() {
  document.getElementById('editPestModal').classList.remove('show');
}

function openDeletePestModal(id) {
  document.getElementById('delete_pest_id').value = id;
  document.getElementById('deletePestModal').classList.add('show');
}

function closeDeletePestModal() {
  document.getElementById('deletePestModal').classList.remove('show');
}

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closePestModal();
    closeEditPestModal();
    closeDeletePestModal();
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