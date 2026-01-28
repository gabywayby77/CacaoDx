<?php
  helper('auth'); // Load our custom helper
  $current_page = service('uri')->getSegment(1);
  $userRole = get_user_role();
?>

<aside id="sidebar" class="sidebar">
  <ul class="menu">

    <!-- ✅ AVAILABLE TO ALL USERS -->
    <li class="<?= ($current_page === 'dashboard') ? 'active' : '' ?>">
      <a href="<?= base_url('dashboard'); ?>" data-spa>
        <i class="fa-solid fa-house"></i>
        <span class="label">Home</span>
      </a>
    </li>

    <!-- ✅ ADMIN ONLY - Uploads/Images -->
    <?php if (is_admin()): ?>
      <li class="<?= ($current_page === 'images') ? 'active' : '' ?>">
        <a href="<?= base_url('images'); ?>" data-spa>
          <i class="fas fa-image"></i>
          <span class="label">Uploads</span>
        </a>
      </li>
    <?php endif; ?>

    <!-- ✅ ADMIN ONLY - Users -->
    <?php if (is_admin()): ?>
      <li class="<?= ($current_page === 'users') ? 'active' : '' ?>">
        <a href="<?= base_url('users'); ?>" data-spa>
          <i class="fas fa-users"></i>
          <span class="label">Users</span>
        </a>
      </li>
    <?php endif; ?>

    <!-- ✅ AVAILABLE TO ALL USERS - Disease (Read-only for users) -->
    <li class="<?= ($current_page === 'disease') ? 'active' : '' ?>">
      <a href="<?= base_url('disease'); ?>" data-spa>
        <i class="fas fa-virus"></i>
        <span class="label">Disease</span>
      </a>
    </li>

    <!-- ✅ ADMIN ONLY - Pests -->
    <?php if (is_admin()): ?>
      <li class="<?= ($current_page === 'pests') ? 'active' : '' ?>">
        <a href="<?= base_url('pests'); ?>" data-spa>
          <i class="fas fa-bug"></i>
          <span class="label">Pests</span>
        </a>
      </li>
    <?php endif; ?>

    <!-- ✅ AVAILABLE TO ALL USERS - Diagnosis (Read-only for users) -->
    <li class="<?= ($current_page === 'diagnosis') ? 'active' : '' ?>">
      <a href="<?= base_url('diagnosis'); ?>">
        <i class="fas fa-stethoscope"></i>
        <span class="label">Diagnosis</span>
      </a>
    </li>

    <!-- ✅ ADMIN ONLY - Activity Logs -->
    <?php if (is_admin()): ?>
      <li class="<?= ($current_page === 'activity_log') ? 'active' : '' ?>">
        <a href="<?= base_url('activity_log'); ?>" data-spa>
          <i class="fas fa-list"></i>
          <span class="label">Logs</span>
        </a>
      </li>
    <?php endif; ?>

    <!-- Add this in your sidebar.php -->
<li class="<?= ($current_page === 'feedback') ? 'active' : '' ?>">
  <a href="<?= base_url('feedback'); ?>">
    <i class="fas fa-comment-dots"></i>
    <span class="label">Feedback</span>
  </a>
</li>

  </ul>

  <!-- Logout (always visible) -->
  <a href="<?= base_url('logout'); ?>" class="logout">
    <i class="fas fa-arrow-right-from-bracket"></i>
    <span class="label">Logout</span>
  </a>
</aside>