<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Dashboard</title>

<!-- CSS -->
<link rel="stylesheet" href="<?= base_url('assets/styles/popup.css'); ?>">
<link rel="stylesheet" href="<?= base_url('assets/styles/dashboardstyles.css'); ?>">
<link rel="stylesheet" href="<?= base_url('assets/styles/sidebar.css'); ?>">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>

<body>

<?php
$current_page = service('uri')->getSegment(1) ?? 'dashboard';

$userName = $userName ?? (session()->get('first_name').' '.session()->get('last_name'));
$totalUsers = $totalUsers ?? 0;
$totalDiagnosis = $totalDiagnosis ?? 0;
$totalDiseases = $totalDiseases ?? 0;

$avatar = 'https://ui-avatars.com/api/?name='.urlencode($userName).'&background=4CAF50&color=fff&size=200';
?>

<div class="page-wrapper">

<?= $this->include('layouts/sidebar'); ?>
<div id="overlay" class="overlay"></div>

<main id="mainContent" class="main-content">

<header class="header">
<button id="sidebarToggle" class="sidebar-toggle">
<i class="fas fa-bars"></i>
</button>

<h1 class="page-title">Dashboard</h1>

<div class="header-right">
<div class="icons">
<button class="icon-btn"><i class="fas fa-search"></i></button>
<button class="icon-btn"><i class="fas fa-bell"></i></button>
</div>

<div class="profile-inline" onclick="openProfile()">
<img src="<?= $avatar ?>" class="profile-pic">
<span class="username"><?= esc($userName) ?></span>
</div>
</div>
</header>

<!-- STATS -->
<section class="stats">
<div class="card total-users">
<div class="card-icon"><i class="fas fa-users"></i></div>
<div class="card-content">
<h3>Total Users</h3>
<p><?= esc($totalUsers) ?></p>
</div>
</div>

<div class="card total-diagnostics">
<div class="card-icon"><i class="fas fa-stethoscope"></i></div>
<div class="card-content">
<h3>Total Diagnostics</h3>
<p><?= esc($totalDiagnosis) ?></p>
</div>
</div>

<div class="card total-diseases">
<div class="card-icon"><i class="fas fa-virus"></i></div>
<div class="card-content">
<h3>Total Diseases</h3>
<p><?= esc($totalDiseases) ?></p>
</div>
</div>
</section>

<!-- CONTENT -->
<section class="content">

<!-- MAP -->
<div class="map">
<h3>Farm Locations – Negros Oriental</h3>
<div id="map" style="height:880px;border-radius:12px;"></div>
</div>

<!-- USERS -->
<div class="users">
<h3>Users</h3>
<canvas id="userChart" width="380" height="380"></canvas>
</div>

<!-- NEW USERS -->
<div class="new-users">
<h3>New Users</h3>
<ul>
<?php if (!empty($newUsers)): ?>
<?php foreach ($newUsers as $user): ?>
<li>
<img src="https://ui-avatars.com/api/?name=<?= urlencode($user['first_name'].' '.$user['last_name']) ?>&size=40">
<span class="name"><?= esc($user['first_name'].' '.$user['last_name']) ?></span>
<span class="meta"><?= date('M d, Y', strtotime($user['registered_at'])) ?></span>
</li>
<?php endforeach; ?>
<?php else: ?>
<li>No new users found</li>
<?php endif; ?>
</ul>
</div>

</section>
</main>
</div>

<!-- DATA -->
<script>
const farms = <?= json_encode($farms ?? [], JSON_HEX_TAG | JSON_HEX_APOS); ?>;
</script>

<!-- Debug - Remove after testing -->
<script>
console.log('Farmers: <?= $farmerCount ?? 0 ?>');
console.log('Regular Users: <?= $regularUserCount ?? 0 ?>');
console.log('Admins: <?= $adminCount ?? 0 ?>');
</script>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
/* USERS CHART - DYNAMIC */
new Chart(document.getElementById('userChart'), {
    type: 'pie',
    data: {
        labels: ['Farmers', 'Regular Users', 'Admins'],
        datasets: [{
            data: [
                <?= $farmerCount ?? 0 ?>,
                <?= $regularUserCount ?? 0 ?>,
                <?= $adminCount ?? 0 ?>
            ],
            backgroundColor: ['#e17055', '#00b894', '#d63031']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { 
            legend: { 
                position: 'bottom',
                labels: {
                    font: {
                        size: 14
                    },
                    padding: 15
                }
            },
            tooltip: {
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
        }
    }
});

/* MAP */
const map = L.map('map').setView([9.79, 123.4], 9);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

farms.forEach(farm => {
    if (!farm.latitude || !farm.longitude) return;

    let color = 'blue';
    if (farm.disease_status === 'healthy') color = 'green';
    if (farm.disease_status === 'mild') color = 'orange';
    if (farm.disease_status === 'severe') color = 'red';

    const popup = `
        <div style="min-width:280px">
            <h4>${farm.farm_name}</h4>
            <hr>

            <b>Location</b><br>
            Barangay: ${farm.barangay}<br>
            Municipality: ${farm.municipality}<br><br>

            <b>Farm Details</b><br>
            Size: ${farm.size_in_hectares} ha<br>
            Cacao Trees: ${farm.cacao_trees}<br>
            Average Yield: ${farm.average_yield_kg} kg<br>
            Last Harvest: ${farm.last_harvest_date}<br><br>

            <b>Health Status</b><br>
            Disease: <b>${farm.disease_status}</b><br>
            Pests: ${farm.pests_detected ?? 'None'}<br><br>

            <b>Notes</b><br>
            ${farm.notes ?? 'No notes'}<br><br>

            <small>Created: ${farm.created_date}</small>
        </div>
    `;

    L.circleMarker([farm.latitude, farm.longitude], {
        radius: 8,
        fillColor: color,
        color: '#000',
        weight: 1,
        fillOpacity: 0.85
    }).addTo(map).bindPopup(popup);
});

setTimeout(() => map.invalidateSize(), 300);
</script>

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
</script>

</body>
</html>