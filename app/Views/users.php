<?php
$userName = $userName ?? (session()->get('first_name').' '.session()->get('last_name'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Users</title>

  <!-- CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/styles/popup.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/userstyles.css'); ?>">
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

      <h1 class="page-title">Users</h1>

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

    <!-- USERS LIST -->
    <section class="logs-section">
      <div class="diagnosis-scroll">

        <div class="section-header">
          <h2>User List</h2>
          <button class="add-btn" onclick="openAddUserModal()">
            <i class="fas fa-user-plus"></i> Add User
          </button>
        </div>

        <div class="users-inner">

          <div class="header-row">
            <div class="col">ID</div>
            <div class="col">Full Name</div>
            <div class="col">Email</div>
            <div class="col">Role</div>
            <div class="col">Status</div>
            <div class="col">Actions</div>
          </div>

          <?php foreach ($users as $user): ?>
          <div class="navbar-row">
            <div class="col"><?= $user['id'] ?></div>
            <div class="col"><?= esc($user['first_name'].' '.$user['last_name']) ?></div>
            <div class="col"><?= esc($user['email']) ?></div>
            <div class="col"><?= ucfirst($user['role']) ?></div>
            <div class="col"><?= ucfirst($user['status']) ?></div>

            <div class="col actions">
              <button class="action-btn edit-btn"
                onclick="openEditUserModal(
                  '<?= $user['id'] ?>',
                  '<?= esc($user['first_name']) ?>',
                  '<?= esc($user['last_name']) ?>',
                  '<?= esc($user['email']) ?>',
                  '<?= esc($user['status']) ?>'
                )">
                <i class="fas fa-edit"></i>
              </button>

              <button class="action-btn delete-btn"
                onclick="openDeleteUserModal('<?= $user['id'] ?>')">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
          <?php endforeach; ?>

        </div>
      </div>
    </section>

  </main>
</div>

<!-- ================= ADD USER ================= -->
<div id="addUserModal" class="popup-overlay">
  <div class="form-card">
    <div class="popup-header">Add User</div>

    <form method="post" action="<?= site_url('users/store') ?>">
      <?= csrf_field() ?>

      <div class="form-grid">
        <div class="form-group">
          <label>First Name</label>
          <input type="text" name="first_name" required>
        </div>

        <div class="form-group">
          <label>Last Name</label>
          <input type="text" name="last_name" required>
        </div>

        <div class="form-group full">
          <label>Email</label>
          <input type="email" name="email" required>
        </div>

        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" required>
        </div>

        <div class="form-group">
          <label>Role</label>
          <select name="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="btn cancel" onclick="closeAddUserModal()">Cancel</button>
        <button type="submit" class="btn add-btn">
          <i class="fas fa-user-plus"></i> Create User
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ================= EDIT USER ================= -->
<div id="editUserModal" class="popup-overlay">
  <div class="form-card">
    <div class="popup-header">Edit User</div>

    <form method="post" action="<?= site_url('users/update') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="id" id="edit_id">

      <div class="form-grid">
        <div class="form-group">
          <label>First Name</label>
          <input type="text" name="first_name" id="edit_first" required>
        </div>

        <div class="form-group">
          <label>Last Name</label>
          <input type="text" name="last_name" id="edit_last" required>
        </div>

        <div class="form-group full">
          <label>Email</label>
          <input type="email" name="email" id="edit_email" required>
        </div>

        <div class="form-group">
          <label>Status</label>
          <select name="status" id="edit_status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="btn cancel" onclick="closeEditUserModal()">Cancel</button>
        <button type="submit" class="btn add-btn">
          <i class="fas fa-save"></i> Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ================= DELETE USER ================= -->
<div id="deleteUserModal" class="popup-overlay">
  <div class="form-card">
    <div class="popup-header">Delete User</div>

    <form method="post" action="<?= site_url('users/delete') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="id" id="delete_id">

      <p style="text-align:center">Are you sure?</p>

      <div class="form-actions">
        <button type="button" class="btn cancel" onclick="closeDeleteUserModal()">Cancel</button>
        <button type="submit" class="btn danger">Delete</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= JS ================= -->

<script>
const addUserModal    = document.getElementById('addUserModal');
const editUserModal   = document.getElementById('editUserModal');
const deleteUserModal = document.getElementById('deleteUserModal');

const edit_id     = document.getElementById('edit_id');
const edit_first  = document.getElementById('edit_first');
const edit_last   = document.getElementById('edit_last');
const edit_email  = document.getElementById('edit_email');
const edit_status = document.getElementById('edit_status');

const delete_id = document.getElementById('delete_id');

function openAddUserModal(){ addUserModal.classList.add('show'); }
function closeAddUserModal(){ addUserModal.classList.remove('show'); }

function openEditUserModal(id,f,l,e,s){
  edit_id.value = id;
  edit_first.value = f;
  edit_last.value = l;
  edit_email.value = e;
  edit_status.value = s;
  editUserModal.classList.add('show');
}
function closeEditUserModal(){ editUserModal.classList.remove('show'); }

function openDeleteUserModal(id){
  delete_id.value = id;
  deleteUserModal.classList.add('show');
}
function closeDeleteUserModal(){ deleteUserModal.classList.remove('show'); }
</script>

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
</script>

</body>
</html>
