<?php
$userName = $userName ?? (session()->get('first_name') . ' ' . session()->get('last_name'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Diseases</title>

  <!-- CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/styles/popup.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/diseasestyles.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/sidebar.css'); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="page-wrapper">

  <?= $this->include('layouts/sidebar'); ?>
  <div id="overlay" class="overlay"></div>

  <main id="mainContent" class="main-content">

    <!-- HEADER -->
    <header class="header">
      <button id="sidebarToggle" class="sidebar-toggle">
        <i class="fas fa-bars"></i>
      </button>

      <h1 class="page-title">Diseases</h1>

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

      <button class="add-btn" onclick="openAddDiseaseModal()">
        <i class="fas fa-plus"></i> Add Disease
      </button>
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
            <div class="col">ID</div>
            <div class="col">Name</div>
            <div class="col">Type</div>
            <div class="col">Cause</div>
            <div class="col">Plant Part</div>
            <div class="col">Actions</div>
          </div>

          <?php if (!empty($diseases)): ?>
            <?php foreach ($diseases as $disease): ?>
              <div class="navbar-row disease-row"
                   data-name="<?= strtolower(esc($disease['name'])) ?>"
                   data-type="<?= strtolower(esc($disease['type'])) ?>"
                   data-cause="<?= strtolower(esc($disease['cause'])) ?>">
                <div class="col"><?= esc($disease['id']) ?></div>
                <div class="col"><?= esc($disease['name']) ?></div>
                <div class="col"><?= esc($disease['type']) ?></div>
                <div class="col"><?= esc($disease['cause']) ?></div>
                <div class="col"><?= esc($disease['plant_part_id']) ?></div>

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
          <input type="text" name="plant_part_id" required>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="btn cancel" onclick="closeAddDiseaseModal()">Cancel</button>
        <button type="submit" class="btn add-btn">Add Disease</button>
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
          <input type="text" name="plant_part_id" id="edit_plant_part" required>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="btn cancel" onclick="closeEditDiseaseModal()">Cancel</button>
        <button type="submit" class="btn add-btn">Save</button>
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

      <p style="text-align:center;">Are you sure you want to delete this disease?</p>

      <div class="form-actions">
        <button type="button" class="btn cancel" onclick="closeDeleteDiseaseModal()">Cancel</button>
        <button type="submit" class="btn danger">Delete</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= JS ================= -->
<script>
(function(){
  const sidebar = document.getElementById('sidebar');
  const toggle = document.getElementById('sidebarToggle');
  const overlay = document.getElementById('overlay');

  toggle.onclick = () => {
    if (window.innerWidth <= 900) {
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

// ================= SEARCH & FILTER ================= 

function filterDiseases() {
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  const typeFilter = document.getElementById('typeFilter').value.toLowerCase();
  const clearBtn = document.getElementById('clearSearch');
  
  const rows = document.querySelectorAll('.disease-row');
  const noResults = document.querySelector('.no-results');
  let visibleCount = 0;
  const totalCount = rows.length;
  
  // Show/hide clear button
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
  filterDiseases();
  document.getElementById('searchInput').focus();
}

function resetFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('typeFilter').value = '';
  filterDiseases();
}

// Real-time search feedback
document.getElementById('searchInput').addEventListener('input', function() {
  filterDiseases();
});

// Modal functions
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
</script>

</body>
</html>