<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Dashboard - CacaoDX</title>

<!-- CSS -->
<link rel="stylesheet" href="<?= base_url('assets/styles/dashboardstyles.css'); ?>">
<link rel="stylesheet" href="<?= base_url('assets/styles/sidebar.css'); ?>">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>

<body>

<?php
helper('auth'); // Load auth helper for role checks

$current_page = service('uri')->getSegment(1) ?? 'dashboard';

$userName = $userName ?? user_name();
$totalUsers = $totalUsers ?? 0;
$totalDiagnosis = $totalDiagnosis ?? 0;
$totalDiseases = $totalDiseases ?? 0;
$uniqueScanUsers = $uniqueScanUsers ?? 0;

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
    <h1 class="page-title">Dashboard</h1>
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

    <div class="profile-inline" onclick="openProfile()">
      <img src="<?= $avatar ?>" class="profile-pic" alt="Profile">
      <div>
        <span class="username"><?= esc($userName) ?></span>
        <small style="display: block; font-size: 11px; color: #95a5a6;">
          <?= is_admin() ? 'Administrator' : 'User' ?>
        </small>
      </div>
    </div>
  </div>
</header>

<!-- ===== STATS CARDS ===== -->
<section class="stats">
  <div class="card total-users">
    <div class="card-icon">
      <i class="fas fa-users"></i>
    </div>
    <div class="card-content">
      <h3>Total Users</h3>
      <p><?= number_format($totalUsers) ?></p>
      <small style="font-size: 12px; color: #95a5a6; margin-top: 4px; display: block;">
        Registered accounts
      </small>
    </div>
  </div>

  <div class="card total-diagnostics">
    <div class="card-icon">
      <i class="fas fa-stethoscope"></i>
    </div>
    <div class="card-content">
      <h3>Total Scans</h3>
      <p><?= number_format($totalDiagnosis) ?></p>
      <small style="font-size: 12px; color: #95a5a6; margin-top: 4px; display: block;">
        <?= number_format($uniqueScanUsers) ?> users scanned
      </small>
    </div>
  </div>

  <div class="card total-diseases">
    <div class="card-icon">
      <i class="fas fa-virus"></i>
    </div>
    <div class="card-content">
      <h3>Disease Types</h3>
      <p><?= number_format($totalDiseases) ?></p>
      <small style="font-size: 12px; color: #95a5a6; margin-top: 4px; display: block;">
        In database
      </small>
    </div>
  </div>
</section>

<!-- ===== CONTENT GRID ===== -->
<section class="content">

  <!-- MAP -->
  <div class="map">
    <h3>Farm Locations – Negros Oriental</h3>
    <div id="map" style="height:100%;"></div>
  </div>

  <!-- USERS CHART -->
  <div class="users">
    <h3>User Distribution</h3>
    <canvas id="userChart"></canvas>
  </div>

  <!-- NEW USERS -->
  <div class="new-users">
    <h3>Recent Users</h3>
    <ul>
      <?php if (!empty($newUsers)): ?>
        <?php foreach ($newUsers as $user): ?>
          <li>
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['first_name'].' '.$user['last_name']) ?>&size=80&background=d34c4e&color=fff&bold=true" alt="User avatar">
            <span class="name"><?= esc($user['first_name'].' '.$user['last_name']) ?></span>
            <span class="meta"><?= date('M d, Y', strtotime($user['registered_at'])) ?></span>
          </li>
        <?php endforeach; ?>
      <?php else: ?>
        <li style="justify-content: center; color: #95a5a6;">
          <i class="fas fa-user-slash"></i>
          <span style="margin-left: 8px;">No new users</span>
        </li>
      <?php endif; ?>
    </ul>
  </div>

</section>
</main>
</div>

<!-- ===== DATA FOR SCRIPTS ===== -->
<script>
const farms = <?= json_encode($farms ?? [], JSON_HEX_TAG | JSON_HEX_APOS); ?>;
const farmerCount = <?= $farmerCount ?? 0 ?>;
const regularUserCount = <?= $regularUserCount ?? 0 ?>;
const adminCount = <?= $adminCount ?? 0 ?>;
</script>

<!-- ===== EXTERNAL SCRIPTS ===== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- ===== USER CHART ===== -->
<script>
const ctx = document.getElementById('userChart');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Farmers', 'Regular Users', 'Admins'],
        datasets: [{
            data: [farmerCount, regularUserCount, adminCount],
            backgroundColor: [
                'rgba(102, 126, 234, 0.8)',
                'rgba(245, 87, 108, 0.8)',
                'rgba(79, 172, 254, 0.8)'
            ],
            borderColor: [
                'rgba(102, 126, 234, 1)',
                'rgba(245, 87, 108, 1)',
                'rgba(79, 172, 254, 1)'
            ],
            borderWidth: 2,
            hoverOffset: 12
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: { size: 13, weight: '600' },
                    padding: 16,
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(45, 52, 54, 0.95)',
                padding: 12,
                cornerRadius: 8,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 13 },
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        },
        cutout: '65%'
    }
});
</script>

<!-- ===== FARM MAP ===== -->
<script>
const map = L.map('map', {
    zoomControl: true,
    scrollWheelZoom: true
}).setView([9.79, 123.4], 9);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 18
}).addTo(map);

// Custom marker icon
const customIcon = (color) => L.divIcon({
    className: 'custom-marker',
    html: `<div style="
        background: ${color};
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    "></div>`,
    iconSize: [24, 24]
});

farms.forEach(farm => {
    if (!farm.latitude || !farm.longitude) return;

    let color = '#3498db';
    if (farm.disease_status === 'healthy') color = '#27ae60';
    if (farm.disease_status === 'mild') color = '#f39c12';
    if (farm.disease_status === 'severe') color = '#e74c3c';

    const popup = `
        <div style="font-family: Inter, sans-serif; min-width: 280px;">
            <h4 style="margin: 0 0 12px 0; color: #2d3436; font-size: 16px; border-bottom: 2px solid ${color}; padding-bottom: 8px;">
                ${farm.farm_name}
            </h4>

            <div style="margin-bottom: 12px;">
                <strong style="color: #636e72;">📍 Location</strong><br>
                <span style="color: #2d3436;">Barangay ${farm.barangay}, ${farm.municipality}</span>
            </div>

            <div style="margin-bottom: 12px;">
                <strong style="color: #636e72;">🌱 Farm Details</strong><br>
                <span style="color: #2d3436;">
                    Size: <strong>${farm.size_in_hectares} ha</strong><br>
                    Trees: <strong>${farm.cacao_trees}</strong><br>
                    Yield: <strong>${farm.average_yield_kg} kg</strong><br>
                    Last Harvest: ${farm.last_harvest_date}
                </span>
            </div>

            <div style="margin-bottom: 12px;">
                <strong style="color: #636e72;">🏥 Health Status</strong><br>
                <span style="padding: 4px 12px; background: ${color}; color: white; border-radius: 6px; font-weight: 600; font-size: 12px;">
                    ${farm.disease_status.toUpperCase()}
                </span><br>
                <span style="color: #2d3436; font-size: 13px;">
                    Pests: ${farm.pests_detected ?? 'None detected'}
                </span>
            </div>

            ${farm.notes ? `
                <div style="margin-top: 12px; padding: 8px; background: #f8f9fa; border-radius: 6px;">
                    <strong style="color: #636e72;">📝 Notes:</strong><br>
                    <span style="color: #2d3436; font-size: 13px;">${farm.notes}</span>
                </div>
            ` : ''}

            <div style="margin-top: 12px; color: #95a5a6; font-size: 11px;">
                Created: ${farm.created_date}
            </div>
        </div>
    `;

    L.marker([farm.latitude, farm.longitude], { icon: customIcon(color) })
        .addTo(map)
        .bindPopup(popup, { maxWidth: 320 });
});

setTimeout(() => map.invalidateSize(), 300);
</script>

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

</body>
</html>