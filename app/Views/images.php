<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Image Uploads - CacaoDx</title>

  <!-- CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/styles/popup.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/dashboardstyles.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/imagestyles.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/sidebar.css'); ?>">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<?php
  helper('auth');
  
  $userName = $userName ?? (session()->get('first_name') . ' ' . session()->get('last_name'));
  $avatar = 'https://ui-avatars.com/api/?name='.urlencode($userName).'&background=d34c4e&color=fff&size=200&bold=true';

  // FINAL DATASET CATEGORIES with better labels and icons
  $folders = [
    'healthy' => ['label' => 'Healthy Pods', 'icon' => 'fa-heart'],
    'black_pod_disease' => ['label' => 'Black Pod Disease', 'icon' => 'fa-disease'],
    'frosty_pod_rot' => ['label' => 'Frosty Pod Rot', 'icon' => 'fa-snowflake'],
    'mirid_bug' => ['label' => 'Mirid Bug Damage', 'icon' => 'fa-bug']
  ];
?>

<div class="page-wrapper">

  <!-- Sidebar -->
  <?= $this->include('layouts/sidebar'); ?>

  <!-- Overlay -->
  <div id="overlay" class="overlay" tabindex="-1" aria-hidden="true"></div>

  <!-- Success Message -->
  <div id="successMessage" class="success-message">
    <i class="fas fa-check-circle"></i> <span id="successText">Images uploaded successfully!</span>
  </div>

  <!-- Main -->
  <main id="mainContent" class="main-content">
    <header class="header">
      <div style="display: flex; align-items: center; gap: 16px;">
        <button id="sidebarToggle" class="sidebar-toggle">
          <i class="fas fa-bars"></i>
        </button>
        <h1 class="page-title">Image Dataset Management</h1>
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

    <!-- CONTENT -->
    <section class="content images-grid">

      <?php foreach ($folders as $folder => $info):

        $path = FCPATH . "uploads/{$folder}/";

        if (!is_dir($path)) {
          mkdir($path, 0777, true);
        }

        $images = array_values(array_diff(scandir($path), ['.', '..']));
        $preview = array_slice($images, 0, 3);
        $totalCount = count($images);
      ?>

      <div class="folder-card" data-folder="<?= $folder ?>">
        <span class="image-count"><?= $totalCount ?> images</span>
        
        <h3>
          <i class="fas <?= $info['icon'] ?>"></i>
          <?= $info['label'] ?>
        </h3>

        <div class="folder-preview">
          <?php if (!empty($preview)): ?>
            <?php foreach ($preview as $img): ?>
              <img 
                src="<?= base_url("uploads/{$folder}/{$img}") ?>" 
                alt="<?= esc($img) ?>"
                onclick="viewImage('<?= base_url("uploads/{$folder}/{$img}") ?>', '<?= esc($img) ?>')"
                loading="lazy"
              >
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty">
              <i class="fas fa-image"></i>
              <p>No images uploaded yet</p>
            </div>
          <?php endif; ?>
        </div>

        <?php if ($totalCount > 3): ?>
          <a href="<?= base_url('images/view/' . $folder) ?>" class="view-all-btn">
            <i class="fas fa-eye"></i> View All (<?= $totalCount ?>)
          </a>
        <?php endif; ?>

        <form 
          action="<?= base_url('images/upload/' . $folder) ?>" 
          method="post" 
          enctype="multipart/form-data"
          class="upload-form"
          data-folder="<?= $folder ?>"
        >
          <div class="file-input-wrapper">
            <input 
              type="file" 
              name="image[]" 
              id="file-<?= $folder ?>"
              multiple 
              accept="image/jpeg,image/png,image/jpg"
              onchange="updateFileLabel(this, '<?= $folder ?>')"
            >
            <label for="file-<?= $folder ?>" class="file-input-label" id="label-<?= $folder ?>">
              <i class="fas fa-cloud-upload-alt"></i>
              <span>Choose Images</span>
            </label>
            <div class="selected-files" id="selected-<?= $folder ?>"></div>
          </div>

          <button type="submit" class="add-btn">
            <i class="fas fa-upload"></i> Upload Images
          </button>

          <div class="upload-progress" id="progress-<?= $folder ?>">
            <div class="upload-progress-bar"></div>
          </div>
        </form>
      </div>

      <?php endforeach; ?>

    </section>
  </main>
</div>

<!-- Image Modal -->
<div id="imageModal" class="image-modal" onclick="closeModal()">
  <span class="modal-close">&times;</span>
  <img class="modal-content" id="modalImage">
  <div id="modalCaption"></div>
</div>

<!-- Sidebar Script -->
<script>
(function () {
  const sidebar = document.getElementById('sidebar');
  const toggle = document.getElementById('sidebarToggle');
  const overlay = document.getElementById('overlay');

  function isMobile() { return window.innerWidth <= 900; }

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

// Profile Dropdown
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

// File input label update
function updateFileLabel(input, folder) {
  const label = document.getElementById('label-' + folder);
  const selectedDiv = document.getElementById('selected-' + folder);
  
  if (input.files.length > 0) {
    label.classList.add('has-files');
    label.querySelector('span').textContent = input.files.length + ' file(s) selected';
    
    let fileNames = Array.from(input.files).map(f => f.name).slice(0, 3).join(', ');
    if (input.files.length > 3) fileNames += '...';
    selectedDiv.textContent = fileNames;
  } else {
    label.classList.remove('has-files');
    label.querySelector('span').textContent = 'Choose Images';
    selectedDiv.textContent = '';
  }
}

// Handle form submission with progress
document.querySelectorAll('.upload-form').forEach(form => {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const folder = this.dataset.folder;
    const formData = new FormData(this);
    const progressBar = document.querySelector(`#progress-${folder} .upload-progress-bar`);
    const progressDiv = document.getElementById(`progress-${folder}`);
    
    progressDiv.classList.add('active');
    
    fetch(this.action, {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      progressBar.style.width = '100%';
      
      setTimeout(() => {
        if (data.success) {
          showSuccess(data.message || 'Images uploaded successfully!');
          setTimeout(() => location.reload(), 1500);
        } else {
          showSuccess(data.message || 'Upload failed!', true);
          progressDiv.classList.remove('active');
          progressBar.style.width = '0%';
        }
      }, 500);
    })
    .catch(error => {
      console.error('Error:', error);
      showSuccess('Upload failed. Please try again.', true);
      progressDiv.classList.remove('active');
      progressBar.style.width = '0%';
    });
  });
});

// Show success message
function showSuccess(message, isError = false) {
  const msg = document.getElementById('successMessage');
  const text = document.getElementById('successText');
  
  text.textContent = message;
  msg.style.background = isError ? '#f44336' : '#4CAF50';
  msg.classList.add('show');
  
  setTimeout(() => {
    msg.classList.remove('show');
  }, 3000);
}

// Image modal
function viewImage(src, caption) {
  const modal = document.getElementById('imageModal');
  const modalImg = document.getElementById('modalImage');
  const captionText = document.getElementById('modalCaption');
  
  modal.style.display = 'block';
  modalImg.src = src;
  captionText.textContent = caption;
}

function closeModal() {
  document.getElementById('imageModal').style.display = 'none';
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeModal();
  }
});

// Show success message if exists in URL params
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('success')) {
  showSuccess(urlParams.get('message') || 'Operation successful!');
}
</script>

</body>
</html>