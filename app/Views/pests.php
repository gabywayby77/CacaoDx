<?php
$userName = $userName ?? (session()->get('first_name') . ' ' . session()->get('last_name'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pests</title>

  <!-- CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/styles/popup.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/pestsstyles.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/sidebar.css'); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="page-wrapper">

  <!-- Sidebar -->
  <?= $this->include('layouts/sidebar'); ?>

  <!-- Overlay -->
  <div id="overlay" class="overlay"></div>

  <!-- Main Content -->
  <main id="mainContent" class="main-content">

    <!-- Header -->
    <header class="header">
      <button id="sidebarToggle" class="sidebar-toggle" aria-expanded="true">
        <i class="fas fa-bars"></i>
      </button>

      <h1 class="page-title">Pests</h1>

      <div class="header-right">
        <div class="icons">
          <button class="icon-btn"><i class="fas fa-search"></i></button>
          <button class="icon-btn"><i class="fas fa-bell"></i></button>
        </div>
        <div class="profile-inline">
          <img src="https://via.placeholder.com/40" class="profile-pic">
          <span class="username"><?= esc($userName) ?></span>
        </div>
      </div>
    </header>

    <!-- Pests Section -->
    <section class="logs-section">
      <div class="diagnosis-scroll">

        <!-- Section Header -->
        <div class="section-header">
          <h2>Pest List</h2>
          <button class="btn add-btn" onclick="openPestModal()">
            <i class="fas fa-bug"></i> Add Pest
          </button>
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
            <?php foreach ($pests as $index => $pest): ?>
              <div class="navbar-row <?= ($index % 2) ? 'alt-row' : '' ?>">
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
    <i class="fas fa-pen"></i>
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

        </div>
      </div>
    </section>

  </main>
</div>

<!-- SIDEBAR TOGGLE (UPDATED / DASHBOARD STYLE) -->
<script>
(function () {
  const sidebar = document.getElementById('sidebar');
  const toggle = document.getElementById('sidebarToggle');
  const overlay = document.getElementById('overlay');

  function setAria(expanded) {
    toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
  }

  function isMobile() {
    return window.innerWidth <= 900;
  }

  toggle.addEventListener('click', () => {
    if (isMobile()) {
      const open = sidebar.classList.toggle('open');
      overlay.classList.toggle('show', open);
      document.body.classList.toggle('no-scroll', open);
      setAria(open);
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

  window.addEventListener('resize', () => {
    if (!isMobile()) {
      sidebar.classList.remove('open');
      overlay.classList.remove('show');
      document.body.classList.remove('no-scroll');
    }
  });
})();
</script>

<!-- ADD PEST MODAL -->
<div id="pestModal" class="modal-overlay">
  <div class="modal-card">

    <div class="modal-header">
      <h2>Add Pest</h2>
      <button class="modal-close" onclick="closePestModal()">&times;</button>
    </div>

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
<div id="editPestModal" class="modal-overlay">
  <div class="modal-card">

    <div class="modal-header">
      <h2>Edit Pest</h2>
      <button class="modal-close" onclick="closeEditPestModal()">&times;</button>
    </div>

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
        <button type="submit" class="btn add-btn">Update Pest</button>
      </div>
    </form>

  </div>
</div>

<!-- DELETE PEST MODAL -->
<div id="deletePestModal" class="modal-overlay">
  <div class="modal-card">

    <div class="modal-header">
      <h2>Delete Pest</h2>
      <button class="modal-close" onclick="closeDeletePestModal()">&times;</button>
    </div>

    <p>Are you sure you want to delete this pest?</p>

    <form action="<?= site_url('pests/delete'); ?>" method="post">
      <?= csrf_field(); ?>
      <input type="hidden" name="id" id="delete_pest_id">

      <div class="form-actions">
        <button type="button" class="btn cancel" onclick="closeDeletePestModal()">Cancel</button>
        <button type="submit" class="btn add-btn" style="background:#dc3545;">
          Delete
        </button>
      </div>
    </form>

  </div>
</div>


<script>
function openPestModal() {
  document.getElementById('pestModal').classList.add('show');
}
function closePestModal() {
  document.getElementById('pestModal').classList.remove('show');
}
</script>

<script>
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
</script>


</body>
</html>
