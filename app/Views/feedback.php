<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Feedback - CacaoDX</title>

  <!-- CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/styles/popup.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/feedbackstyles.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/sidebar.css'); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<?php
  helper('auth');
  
  $userName = $userName ?? (session()->get('first_name') . ' ' . session()->get('last_name'));
  $avatar = 'https://ui-avatars.com/api/?name='.urlencode($userName).'&background=d34c4e&color=fff&size=200&bold=true';
  $isAdmin = is_admin();
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
        <h1 class="page-title"><?= $isAdmin ? 'User Feedback' : 'Send Feedback' ?></h1>
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
                <?= $isAdmin ? 'Administrator' : 'User' ?>
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

    <?php if ($isAdmin): ?>
      <!-- ================= ADMIN VIEW - ALL FEEDBACK ================= -->
      
      <!-- SEARCH & FILTER BAR -->
      <div class="search-filter-bar">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input 
            type="text" 
            id="searchInput" 
            placeholder="Search by user or comments..." 
            onkeyup="filterFeedback()"
          >
          <button class="clear-search" id="clearSearch" onclick="clearSearch()" style="display: none;">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div class="filter-group">
          <label for="ratingFilter">
            <i class="fas fa-star"></i> Rating:
          </label>
          <select id="ratingFilter" onchange="filterFeedback()">
            <option value="">All Ratings</option>
            <option value="5">5 Stars</option>
            <option value="4">4 Stars</option>
            <option value="3">3 Stars</option>
            <option value="2">2 Stars</option>
            <option value="1">1 Star</option>
          </select>
        </div>

        <div class="filter-group">
          <label for="dateFromFilter">
            <i class="fas fa-calendar-alt"></i> From:
          </label>
          <input type="date" id="dateFromFilter" onchange="filterFeedback()">
        </div>

        <div class="filter-group">
          <label for="dateToFilter">
            <i class="fas fa-calendar-check"></i> To:
          </label>
          <input type="date" id="dateToFilter" onchange="filterFeedback()">
        </div>

        <button class="reset-filters" onclick="resetFilters()">
          <i class="fas fa-redo"></i> Reset
        </button>
      </div>

      <!-- FEEDBACK LIST -->
      <section class="logs-section">
        <div class="diagnosis-scroll">

          <div class="section-header">
            <h2>All Feedback</h2>
          </div>

          <!-- Results counter -->
          <div class="results-info">
            Showing <strong id="visibleCount"><?= !empty($feedbacks) ? count($feedbacks) : 0 ?></strong> of <strong id="totalCount"><?= !empty($feedbacks) ? count($feedbacks) : 0 ?></strong> feedback entries
          </div>

          <div class="feedback-inner">

            <div class="header-row">
              <div class="col">ID</div>
              <div class="col">User</div>
              <div class="col">Rating</div>
              <div class="col">Comments</div>
              <div class="col">Date</div>
              <div class="col">Actions</div>
            </div>

            <?php if (!empty($feedbacks)): ?>
              <?php foreach ($feedbacks as $feedback): ?>
                <div class="navbar-row feedback-row"
                     data-user="<?= strtolower(esc($feedback['user_name'] ?? 'Unknown')) ?>"
                     data-rating="<?= esc($feedback['rating']) ?>"
                     data-comments="<?= strtolower(esc($feedback['comments'])) ?>"
                     data-date="<?= date('Y-m-d', strtotime($feedback['created_at'])) ?>">
                  <div class="col"><?= esc($feedback['id']) ?></div>
                  <div class="col"><?= esc($feedback['user_name'] ?? 'Unknown User') ?></div>
                  <div class="col">
                    <div class="star-rating">
                      <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star <?= $i <= $feedback['rating'] ? 'filled' : '' ?>"></i>
                      <?php endfor; ?>
                    </div>
                  </div>
                  <div class="col"><?= esc($feedback['comments']) ?></div>
                  <div class="col"><?= date('M d, Y', strtotime($feedback['created_at'])) ?></div>
                  <div class="col actions">
                    <button class="action-btn delete-btn" onclick="openDeleteFeedbackModal('<?= $feedback['id'] ?>')">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="navbar-row empty">
                <div class="col">
                  <i class="fas fa-comment-slash" style="font-size: 48px; opacity: 0.3; margin-bottom: 10px;"></i>
                  <p>No feedback received yet.</p>
                </div>
              </div>
            <?php endif; ?>

            <!-- No results message -->
            <div class="navbar-row empty no-results" style="display: none;">
              <div class="col">
                <i class="fas fa-search" style="font-size: 48px; opacity: 0.3; margin-bottom: 10px;"></i>
                <p>No feedback found matching your search criteria</p>
              </div>
            </div>

          </div>
        </div>
      </section>

    <?php else: ?>
      <!-- ================= USER VIEW - SUBMIT FEEDBACK ================= -->
      
      <section class="feedback-form-section">
        <div class="feedback-form-card">
          <div class="form-header">
            <i class="fas fa-comment-dots"></i>
            <h2>We'd Love Your Feedback!</h2>
            <p>Help us improve CacaoDX by sharing your thoughts and experiences.</p>
          </div>

          <form method="post" action="<?= site_url('feedback/submit') ?>">
            <?= csrf_field() ?>

            <!-- Rating Section -->
            <div class="form-group">
              <label>Rate Your Experience <span class="required">*</span></label>
              <div class="star-input" id="starInput">
                <i class="fas fa-star" data-rating="1"></i>
                <i class="fas fa-star" data-rating="2"></i>
                <i class="fas fa-star" data-rating="3"></i>
                <i class="fas fa-star" data-rating="4"></i>
                <i class="fas fa-star" data-rating="5"></i>
              </div>
              <input type="hidden" name="rating" id="ratingValue" required>
              <div class="rating-text" id="ratingText">Click to rate</div>
            </div>

            <!-- Comments Section -->
            <div class="form-group">
              <label for="comments">Your Comments <span class="required">*</span></label>
              <textarea 
                name="comments" 
                id="comments" 
                rows="6" 
                placeholder="Tell us what you think about CacaoDX..."
                required
              ></textarea>
              <div class="char-counter">
                <span id="charCount">0</span> / 500 characters
              </div>
            </div>

            <!-- Submit Button -->
            <div class="form-actions">
              <button type="submit" class="btn submit-btn">
                <i class="fas fa-paper-plane"></i> Submit Feedback
              </button>
            </div>
          </form>
        </div>

        <!-- User's Previous Feedback -->
        <?php if (!empty($userFeedbacks)): ?>
        <div class="user-feedback-history">
          <h3><i class="fas fa-history"></i> Your Previous Feedback</h3>
          
          <?php foreach ($userFeedbacks as $fb): ?>
          <div class="feedback-card">
            <div class="feedback-header">
              <div class="star-rating">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <i class="fas fa-star <?= $i <= $fb['rating'] ? 'filled' : '' ?>"></i>
                <?php endfor; ?>
              </div>
              <span class="feedback-date"><?= date('M d, Y', strtotime($fb['created_at'])) ?></span>
            </div>
            <p class="feedback-comment"><?= esc($fb['comments']) ?></p>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </section>

    <?php endif; ?>
  </main>
</div>

<!-- ================= DELETE FEEDBACK MODAL (ADMIN ONLY) ================= -->
<?php if ($isAdmin): ?>
<div id="deleteFeedbackModal" class="popup-overlay">
  <div class="form-card">
    <div class="popup-header">Delete Feedback</div>

    <form method="post" action="<?= site_url('feedback/delete') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="id" id="delete_feedback_id">

      <p style="text-align:center; padding: 20px;">Are you sure you want to delete this feedback? This action cannot be undone.</p>

      <div class="form-actions">
        <button type="button" class="btn cancel" onclick="closeDeleteFeedbackModal()">Cancel</button>
        <button type="submit" class="btn danger">
          <i class="fas fa-trash"></i> Delete Feedback
        </button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

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
</script>

<?php if ($isAdmin): ?>
<!-- Admin: Search & Filter -->
<script>
function filterFeedback() {
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  const ratingFilter = document.getElementById('ratingFilter').value;
  const dateFromFilter = document.getElementById('dateFromFilter').value;
  const dateToFilter = document.getElementById('dateToFilter').value;
  const clearBtn = document.getElementById('clearSearch');
  
  const rows = document.querySelectorAll('.feedback-row');
  const noResults = document.querySelector('.no-results');
  let visibleCount = 0;
  const totalCount = rows.length;
  
  clearBtn.style.display = searchInput ? 'flex' : 'none';
  
  rows.forEach(row => {
    const user = row.dataset.user;
    const rating = row.dataset.rating;
    const comments = row.dataset.comments;
    const feedbackDate = row.dataset.date;
    
    const matchesSearch = user.includes(searchInput) || comments.includes(searchInput);
    const matchesRating = !ratingFilter || rating === ratingFilter;
    
    // Date filtering
    let matchesDateFrom = true;
    let matchesDateTo = true;
    
    if (dateFromFilter) {
      matchesDateFrom = feedbackDate >= dateFromFilter;
    }
    
    if (dateToFilter) {
      matchesDateTo = feedbackDate <= dateToFilter;
    }
    
    if (matchesSearch && matchesRating && matchesDateFrom && matchesDateTo) {
      row.style.display = 'flex';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });
  
  document.getElementById('visibleCount').textContent = visibleCount;
  document.getElementById('totalCount').textContent = totalCount;
  
  noResults.style.display = visibleCount === 0 ? 'flex' : 'none';
}

function clearSearch() {
  document.getElementById('searchInput').value = '';
  filterFeedback();
  document.getElementById('searchInput').focus();
}

function resetFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('ratingFilter').value = '';
  document.getElementById('dateFromFilter').value = '';
  document.getElementById('dateToFilter').value = '';
  filterFeedback();
}

function openDeleteFeedbackModal(id) {
  document.getElementById('delete_feedback_id').value = id;
  document.getElementById('deleteFeedbackModal').classList.add('show');
}

function closeDeleteFeedbackModal() {
  document.getElementById('deleteFeedbackModal').classList.remove('show');
}
</script>
<?php else: ?>
<!-- User: Star Rating & Character Counter -->
<script>
const stars = document.querySelectorAll('.star-input i');
const ratingValue = document.getElementById('ratingValue');
const ratingText = document.getElementById('ratingText');
const comments = document.getElementById('comments');
const charCount = document.getElementById('charCount');

const ratingLabels = ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];

stars.forEach(star => {
  star.addEventListener('click', function() {
    const rating = this.dataset.rating;
    ratingValue.value = rating;
    
    stars.forEach((s, index) => {
      if (index < rating) {
        s.classList.add('filled');
      } else {
        s.classList.remove('filled');
      }
    });
    
    ratingText.textContent = ratingLabels[rating - 1];
    ratingText.style.color = 'var(--primary)';
  });
  
  star.addEventListener('mouseenter', function() {
    const rating = this.dataset.rating;
    stars.forEach((s, index) => {
      if (index < rating) {
        s.classList.add('hover');
      } else {
        s.classList.remove('hover');
      }
    });
  });
});

document.querySelector('.star-input').addEventListener('mouseleave', function() {
  stars.forEach(s => s.classList.remove('hover'));
});

comments.addEventListener('input', function() {
  const length = this.value.length;
  charCount.textContent = length;
  
  if (length > 500) {
    this.value = this.value.substring(0, 500);
    charCount.textContent = 500;
  }
});
</script>
<?php endif; ?>

<!-- Auto-hide alerts -->
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