<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Settings - CacaoDX</title>

<!-- CSS -->
<link rel="stylesheet" href="<?= base_url('assets/styles/settingsstyles.css'); ?>">
<link rel="stylesheet" href="<?= base_url('assets/styles/sidebar.css'); ?>">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<?php
helper('auth');

$current_page = 'settings';

// Get user data from controller
$user = $user ?? [];
$userName = $userName ?? 'User';
$avatar = 'https://ui-avatars.com/api/?name='.urlencode($userName).'&background=d34c4e&color=fff&size=200&bold=true';
?>

<div class="page-wrapper">

<?= $this->include('layouts/sidebar'); ?>
<div id="overlay" class="overlay"></div>

<main id="mainContent" class="main-content">

<!-- ===== HEADER ===== -->
<header class="header">
  <div style="display: flex; align-items: center; gap: 16px;">
    <button id="sidebarToggle" class="sidebar-toggle">
      <i class="fas fa-bars"></i>
    </button>
    <h1 class="page-title">Settings</h1>
  </div>

  <div class="header-right">
    <div class="profile-dropdown-container">
      <div class="profile-inline" onclick="toggleProfileDropdown(event)">
        <img src="<?= $avatar ?>" class="profile-pic" alt="Profile">
        <div>
          <span class="username"><?= esc($userName) ?></span>
          <small style="display: block; font-size: 11px; color: #95a5a6;">
            <?= is_admin() ? 'Administrator' : ($user['user_type_name'] ?? 'User') ?>
          </small>
        </div>
        <i class="fas fa-chevron-down" style="margin-left: 8px; font-size: 12px; color: #95a5a6; transition: transform 0.3s;"></i>
      </div>

      <div id="profileDropdown" class="profile-dropdown">
        <a href="<?= base_url('profile') ?>" class="dropdown-item">
          <i class="fas fa-user"></i>
          <span>View Profile</span>
        </a>
        <a href="<?= base_url('settings') ?>" class="dropdown-item active">
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

<!-- ===== SETTINGS CONTENT ===== -->
<div class="settings-container">

  <!-- Settings Navigation Tabs -->
  <div class="settings-tabs">
    <button class="tab-btn active" data-tab="personal">
      <i class="fas fa-user"></i>
      Personal Info
    </button>
    <button class="tab-btn" data-tab="security">
      <i class="fas fa-lock"></i>
      Security
    </button>
    <button class="tab-btn" data-tab="notifications">
      <i class="fas fa-bell"></i>
      Notifications
    </button>
    <button class="tab-btn" data-tab="preferences">
      <i class="fas fa-sliders-h"></i>
      Preferences
    </button>
  </div>

  <!-- Tab Content -->
  <div class="settings-content">

    <!-- PERSONAL INFO TAB -->
    <div class="tab-content active" id="personal-tab">
      <div class="settings-card">
        <div class="card-header">
          <h3><i class="fas fa-user-edit"></i> Personal Information</h3>
          <p>Update your personal details and contact information</p>
        </div>
        <div class="card-body">
          <form action="<?= base_url('settings/update-personal') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div class="form-row">
              <div class="form-group">
                <label for="first_name">
                  <i class="fas fa-user"></i> First Name
                </label>
                <input type="text" id="first_name" name="first_name" value="<?= esc($user['first_name'] ?? '') ?>" required>
              </div>

              <div class="form-group">
                <label for="last_name">
                  <i class="fas fa-user"></i> Last Name
                </label>
                <input type="text" id="last_name" name="last_name" value="<?= esc($user['last_name'] ?? '') ?>" required>
              </div>
            </div>

            <div class="form-group">
              <label for="email">
                <i class="fas fa-envelope"></i> Email Address
              </label>
              <input type="email" id="email" name="email" value="<?= esc($user['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
              <label for="contact_number">
                <i class="fas fa-phone"></i> Contact Number
              </label>
              <input type="text" id="contact_number" name="contact_number" value="<?= esc($user['contact_number'] ?? '') ?>" placeholder="+63 912 345 6789">
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Save Changes
              </button>
              <button type="button" class="btn-secondary" onclick="window.location.reload()">
                <i class="fas fa-undo"></i> Reset
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- SECURITY TAB -->
    <div class="tab-content" id="security-tab">
      <div class="settings-card">
        <div class="card-header">
          <h3><i class="fas fa-shield-alt"></i> Change Password</h3>
          <p>Keep your account secure by using a strong password</p>
        </div>
        <div class="card-body">
          <form action="<?= base_url('settings/update-password') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div class="form-group">
              <label for="current_password">
                <i class="fas fa-lock"></i> Current Password
              </label>
              <input type="password" id="current_password" name="current_password" required>
            </div>

            <div class="form-group">
              <label for="new_password">
                <i class="fas fa-key"></i> New Password
              </label>
              <input type="password" id="new_password" name="new_password" required minlength="8">
              <small class="form-hint">At least 8 characters</small>
            </div>

            <div class="form-group">
              <label for="confirm_password">
                <i class="fas fa-key"></i> Confirm New Password
              </label>
              <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-primary">
                <i class="fas fa-key"></i> Update Password
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="settings-card">
        <div class="card-header">
          <h3><i class="fas fa-user-shield"></i> Account Status</h3>
          <p>Your account information and status</p>
        </div>
        <div class="card-body">
          <div class="info-item">
            <span class="info-label">Account Status</span>
            <span class="status-badge <?= ($user['status'] ?? 'active') === 'active' ? 'active' : 'inactive' ?>">
              <i class="fas fa-circle"></i> <?= ucfirst($user['status'] ?? 'Active') ?>
            </span>
          </div>
          <div class="info-item">
            <span class="info-label">Account Created</span>
            <span class="info-value"><?= date('F d, Y', strtotime($user['registered_at'] ?? 'now')) ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">User ID</span>
            <span class="info-value">#USR-<?= str_pad($user['id'] ?? 0, 4, '0', STR_PAD_LEFT) ?></span>
          </div>
        </div>
      </div>
    </div>

    <!-- NOTIFICATIONS TAB -->
    <div class="tab-content" id="notifications-tab">
      <div class="settings-card">
        <div class="card-header">
          <h3><i class="fas fa-bell"></i> Notification Preferences</h3>
          <p>Choose what notifications you want to receive</p>
        </div>
        <div class="card-body">
          <form action="<?= base_url('settings/update-notifications') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div class="notification-item">
              <div class="notification-info">
                <h4><i class="fas fa-envelope"></i> Email Notifications</h4>
                <p>Receive updates and alerts via email</p>
              </div>
              <label class="switch">
                <input type="checkbox" name="email_notifications" checked>
                <span class="slider"></span>
              </label>
            </div>

            <div class="notification-item">
              <div class="notification-info">
                <h4><i class="fas fa-exclamation-triangle"></i> Disease Alerts</h4>
                <p>Get notified when diseases are detected</p>
              </div>
              <label class="switch">
                <input type="checkbox" name="disease_alerts" checked>
                <span class="slider"></span>
              </label>
            </div>

            <div class="notification-item">
              <div class="notification-info">
                <h4><i class="fas fa-clipboard-list"></i> Diagnosis Updates</h4>
                <p>Receive updates on your diagnosis results</p>
              </div>
              <label class="switch">
                <input type="checkbox" name="diagnosis_updates" checked>
                <span class="slider"></span>
              </label>
            </div>

            <div class="notification-item">
              <div class="notification-info">
                <h4><i class="fas fa-chart-line"></i> Weekly Reports</h4>
                <p>Get weekly summaries of your activity</p>
              </div>
              <label class="switch">
                <input type="checkbox" name="weekly_reports">
                <span class="slider"></span>
              </label>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Save Preferences
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- PREFERENCES TAB -->
    <div class="tab-content" id="preferences-tab">
      <div class="settings-card">
        <div class="card-header">
          <h3><i class="fas fa-palette"></i> Appearance</h3>
          <p>Customize how CacaoDX looks for you</p>
        </div>
        <div class="card-body">
          <form action="<?= base_url('settings/update-preferences') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div class="preference-item">
              <label for="theme">
                <i class="fas fa-moon"></i> Theme
              </label>
              <select id="theme" name="theme" class="form-select">
                <option value="light" selected>Light Mode</option>
                <option value="dark">Dark Mode</option>
                <option value="auto">Auto (System)</option>
              </select>
            </div>

            <div class="preference-item">
              <label for="language">
                <i class="fas fa-language"></i> Language
              </label>
              <select id="language" name="language" class="form-select">
                <option value="en" selected>English</option>
                <option value="fil">Filipino</option>
              </select>
            </div>

            <div class="preference-item">
              <label for="timezone">
                <i class="fas fa-clock"></i> Timezone
              </label>
              <select id="timezone" name="timezone" class="form-select">
                <option value="Asia/Manila" selected>Asia/Manila (PHT)</option>
                <option value="UTC">UTC</option>
              </select>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Save Preferences
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="settings-card danger-zone">
        <div class="card-header">
          <h3><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
          <p>Irreversible actions - proceed with caution</p>
        </div>
        <div class="card-body">
          <div class="danger-item">
            <div>
              <h4>Delete Account</h4>
              <p>Permanently delete your account and all associated data</p>
            </div>
            <button type="button" class="btn-danger" onclick="confirmDelete()">
              <i class="fas fa-trash-alt"></i> Delete Account
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>

</div>

</main>
</div>

<!-- ===== SCRIPTS ===== -->
<script>
// Sidebar Toggle
(function() {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('overlay');

    function isMobile() { return window.innerWidth <= 900; }

    toggle.addEventListener('click', () => {
        if (isMobile()) {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
            document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
        } else {
            sidebar.classList.toggle('collapsed');
        }
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    });
})();

// Profile Dropdown
function toggleProfileDropdown(event) {
  event.stopPropagation();
  const dropdown = document.getElementById('profileDropdown');
  const chevron = event.currentTarget.querySelector('.fa-chevron-down');
  
  dropdown.classList.toggle('show');
  chevron.style.transform = dropdown.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
}

document.addEventListener('click', function(event) {
  const dropdown = document.getElementById('profileDropdown');
  const container = event.target.closest('.profile-dropdown-container');
  const chevron = document.querySelector('.profile-inline .fa-chevron-down');
  
  if (!container && dropdown.classList.contains('show')) {
    dropdown.classList.remove('show');
    if (chevron) chevron.style.transform = 'rotate(0deg)';
  }
});

// Tab Switching
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const tabName = this.dataset.tab;
    
    // Remove active class from all tabs and content
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    
    // Add active class to clicked tab and corresponding content
    this.classList.add('active');
    document.getElementById(tabName + '-tab').classList.add('active');
  });
});

// Confirm Delete Account
function confirmDelete() {
  if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
    if (confirm('This will permanently delete all your data. Are you absolutely sure?')) {
      window.location.href = '<?= base_url('settings/delete-account') ?>';
    }
  }
}

// Auto-hide alerts after 5 seconds
setTimeout(() => {
  document.querySelectorAll('.alert').forEach(alert => {
    alert.style.opacity = '0';
    setTimeout(() => alert.remove(), 300);
  });
}, 5000);
</script>

</body>
</html>