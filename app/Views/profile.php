<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>My Profile - CacaoDX</title>

<!-- CSS -->
<link rel="stylesheet" href="<?= base_url('assets/styles/profilestyles.css'); ?>">
<link rel="stylesheet" href="<?= base_url('assets/styles/sidebar.css'); ?>">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<?php
helper('auth');

$current_page = 'profile';

// User data from controller
$userName = $userName ?? 'User';
$userEmail = $userEmail ?? 'N/A';
$userPhone = $userPhone ?? 'Not provided';
$userRole = is_admin() ? 'Administrator' : ($userTypeName ?? 'User');
$userAddress = $userAddress ?? 'Negros Oriental, Philippines';
$joinDate = $joinDate ?? date('F Y');
$firstName = $firstName ?? 'N/A';
$lastName = $lastName ?? 'N/A';
$userId = $userId ?? 0;
$userStatus = $userStatus ?? 'active';
$diagnosisCount = $diagnosisCount ?? 0;
$farmsCount = $farmsCount ?? 0;

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
    <h1 class="page-title">My Profile</h1>
  </div>

  <div class="header-right">
    <div class="profile-dropdown-container">
      <div class="profile-inline" onclick="toggleProfileDropdown(event)">
        <img src="<?= $avatar ?>" class="profile-pic" alt="Profile">
        <div>
          <span class="username"><?= esc($userName) ?></span>
          <small style="display: block; font-size: 11px; color: #95a5a6;">
            <?= $userRole ?>
          </small>
        </div>
        <i class="fas fa-chevron-down" style="margin-left: 8px; font-size: 12px; color: #95a5a6; transition: transform 0.3s;"></i>
      </div>

      <div id="profileDropdown" class="profile-dropdown">
        <a href="<?= base_url('profile') ?>" class="dropdown-item active">
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

<!-- ===== PROFILE CONTENT ===== -->
<div class="profile-container">
  
  <!-- Profile Header Card -->
  <div class="profile-header-card">
    <div class="profile-cover"></div>
    <div class="profile-header-content">
      <div class="profile-avatar-section">
        <img src="<?= $avatar ?>" alt="Profile Picture" class="profile-avatar">
        <button class="change-avatar-btn" title="Change Avatar">
          <i class="fas fa-camera"></i>
        </button>
      </div>
      <div class="profile-header-info">
        <h2><?= esc($userName) ?></h2>
        <p class="role-badge <?= is_admin() ? 'admin' : 'user' ?>">
          <i class="fas fa-<?= is_admin() ? 'shield-alt' : 'user' ?>"></i>
          <?= $userRole ?>
        </p>
        <p class="join-date">
          <i class="fas fa-calendar-alt"></i>
          Member since <?= $joinDate ?>
        </p>
      </div>
      <div class="profile-header-actions">
        <a href="<?= base_url('settings') ?>" class="btn-primary">
          <i class="fas fa-edit"></i>
          Edit Profile
        </a>
      </div>
    </div>
  </div>

  <!-- Profile Details Grid -->
  <div class="profile-grid">
    
    <!-- Personal Information -->
    <div class="profile-card">
      <div class="card-header">
        <h3><i class="fas fa-user-circle"></i> Personal Information</h3>
      </div>
      <div class="card-body">
        <div class="info-row">
          <span class="info-label">First Name</span>
          <span class="info-value"><?= esc($firstName) ?></span>
        </div>
        <div class="info-row">
          <span class="info-label">Last Name</span>
          <span class="info-value"><?= esc($lastName) ?></span>
        </div>
        <div class="info-row">
          <span class="info-label">Email Address</span>
          <span class="info-value"><?= esc($userEmail) ?></span>
        </div>
        <div class="info-row">
          <span class="info-label">Contact Number</span>
          <span class="info-value"><?= esc($userPhone) ?></span>
        </div>
        <div class="info-row">
          <span class="info-label">Location</span>
          <span class="info-value"><?= esc($userAddress) ?></span>
        </div>
      </div>
    </div>

    <!-- Account Information -->
    <div class="profile-card">
      <div class="card-header">
        <h3><i class="fas fa-shield-alt"></i> Account Information</h3>
      </div>
      <div class="card-body">
        <div class="info-row">
          <span class="info-label">User ID</span>
          <span class="info-value">#USR-<?= str_pad($userId, 4, '0', STR_PAD_LEFT) ?></span>
        </div>
        <div class="info-row">
          <span class="info-label">Account Type</span>
          <span class="info-value"><?= $userRole ?></span>
        </div>
        <div class="info-row">
          <span class="info-label">Status</span>
          <span class="info-value">
            <span class="status-badge <?= $userStatus === 'active' ? 'active' : 'inactive' ?>">
              <i class="fas fa-<?= $userStatus === 'active' ? 'check-circle' : 'times-circle' ?>"></i> 
              <?= ucfirst($userStatus) ?>
            </span>
          </span>
        </div>
        <div class="info-row">
          <span class="info-label">Last Login</span>
          <span class="info-value"><?= date('M d, Y h:i A') ?></span>
        </div>
        <div class="info-row">
          <span class="info-label">Joined Date</span>
          <span class="info-value"><?= $joinDate ?></span>
        </div>
      </div>
    </div>

    <!-- Activity Stats -->
    <div class="profile-card">
      <div class="card-header">
        <h3><i class="fas fa-chart-line"></i> Activity Stats</h3>
      </div>
      <div class="card-body">
        <div class="stat-item">
          <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="fas fa-camera"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Total Scans</span>
            <span class="stat-value"><?= number_format($diagnosisCount) ?></span>
          </div>
        </div>
        <div class="stat-item">
          <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <i class="fas fa-leaf"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Farms Monitored</span>
            <span class="stat-value"><?= number_format($farmsCount) ?></span>
          </div>
        </div>
        <div class="stat-item">
          <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <i class="fas fa-bell"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Alerts Received</span>
            <span class="stat-value">0</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="profile-card">
      <div class="card-header">
        <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
      </div>
      <div class="card-body">
        <a href="<?= base_url('settings') ?>" class="action-btn">
          <i class="fas fa-cog"></i>
          <span>Account Settings</span>
          <i class="fas fa-chevron-right"></i>
        </a>
        <a href="<?= base_url('dashboard') ?>" class="action-btn">
          <i class="fas fa-home"></i>
          <span>Back to Dashboard</span>
          <i class="fas fa-chevron-right"></i>
        </a>
        <a href="<?= base_url('diagnosis') ?>" class="action-btn">
          <i class="fas fa-stethoscope"></i>
          <span>View My Diagnoses</span>
          <i class="fas fa-chevron-right"></i>
        </a>
        <a href="<?= base_url('help') ?>" class="action-btn">
          <i class="fas fa-question-circle"></i>
          <span>Help & Support</span>
          <i class="fas fa-chevron-right"></i>
        </a>
      </div>
    </div>

  </div>

</div>

</main>
</div>

<!-- ===== SIDEBAR TOGGLE ===== -->
<script>
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
</script>

<!-- ===== PROFILE DROPDOWN ===== -->
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

</body>
</html>