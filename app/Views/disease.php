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
  <link rel="stylesheet" href="<?= base_url('assets/styles/userstyles.css'); ?>">
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
          <img src="https://via.placeholder.com/40">
          <span class="username"><?= esc($userName) ?></span>
        </div>
      </div>
    </header>

    <!-- DISEASES -->
    <section class="logs-section">
      <div class="diagnosis-scroll">

        <div class="section-header">
          <h2>Disease List</h2>
          <button class="add-btn" onclick="openAddDiseaseModal()">
            <i class="fas fa-plus"></i> Add Disease
          </button>
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
              <div class="navbar-row">
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
