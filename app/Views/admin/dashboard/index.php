<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Dashboard</h1>
    <p>Overview of your LifeKnob system</p>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-blue-light">
                <i class="fas fa-users"></i>
            </div>
            <div class="flex-grow-1">
                <div class="stat-value"><?= number_format($stats['total_users'] ?? 0) ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <small class="text-muted"><?= $planData['free'] ?? 0 ?>F / <?= $planData['paid'] ?? 0 ?>P</small>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-green-light">
                <i class="fas fa-link"></i>
            </div>
            <div class="flex-grow-1">
                <div class="stat-value"><?= number_format($stats['active_connections'] ?? 0) ?></div>
                <div class="stat-label">Active Connections</div>
            </div>
            <?php if (($connStatusData['pending'] ?? 0) > 0): ?>
            <span class="badge bg-warning text-dark"><?= $connStatusData['pending'] ?> pending</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-green-light">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div>
                <div class="stat-value"><?= number_format($stats['checkins_today'] ?? 0) ?></div>
                <div class="stat-label">Check-ins Today</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon <?= ($stats['overdue_users'] ?? 0) > 0 ? 'bg-red-light' : 'bg-green-light' ?>">
                <i class="fas <?= ($stats['overdue_users'] ?? 0) > 0 ? 'fa-exclamation-triangle' : 'fa-check-circle' ?>"></i>
            </div>
            <div class="flex-grow-1">
                <div class="stat-value"><?= number_format($stats['overdue_users'] ?? 0) ?></div>
                <div class="stat-label">Overdue Users</div>
            </div>
            <?php if (($stats['overdue_users'] ?? 0) > 0): ?>
            <a href="/admin/alerts" class="btn btn-sm btn-outline-danger">View</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-area text-green me-2"></i>Activity (Last 7 Days)
            </div>
            <div class="card-body">
                <div id="activityChart" style="height: 280px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-pie text-green me-2"></i>Users by Plan
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div id="planChart" style="width: 100%; height: 240px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Tables -->
<div class="row g-4">
    <!-- Recent Check-ins -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-clipboard-check text-green me-2"></i>Recent Check-ins</span>
                <a href="/admin/checkins" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentCheckIns)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-clipboard fa-2x mb-2" style="opacity: 0.2;"></i>
                        <p class="mb-0">No check-ins yet</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>User</th><th>Type</th><th>Time</th></tr></thead>
                            <tbody>
                                <?php foreach ($recentCheckIns as $ci): ?>
                                    <tr>
                                        <td>
                                            <a href="/admin/users/<?= esc($ci->user_id ?? '') ?>" class="text-decoration-none fw-medium"><?= esc($ci->user_name ?? 'Unknown') ?></a>
                                            <br><small class="text-muted"><?= esc($ci->user_code ?? '') ?></small>
                                        </td>
                                        <td>
                                            <?php $ciBadge = match($ci->type ?? 'ok') { 'ok'=>'badge-ok','help'=>'badge-help','emergency'=>'badge-emergency',default=>'bg-secondary' }; ?>
                                            <span class="badge <?= $ciBadge ?>"><?= ucfirst($ci->type ?? 'ok') ?></span>
                                        </td>
                                        <td><span data-time="<?= esc($ci->created_at ?? '') ?>"></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Connections -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-link text-green me-2"></i>Recent Connections</span>
                <a href="/admin/connections" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentConnections)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-link fa-2x mb-2" style="opacity: 0.2;"></i>
                        <p class="mb-0">No connections yet</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>From</th><th>To</th><th>Status</th><th>Time</th></tr></thead>
                            <tbody>
                                <?php foreach ($recentConnections as $conn): ?>
                                    <tr>
                                        <td>
                                            <a href="/admin/users/<?= esc($conn->user_id ?? '') ?>" class="text-decoration-none fw-medium"><?= esc($conn->user_name ?? '') ?></a>
                                            <br><small class="text-muted"><?= esc($conn->user_code ?? '') ?></small>
                                        </td>
                                        <td>
                                            <a href="/admin/users/<?= esc($conn->connected_to ?? '') ?>" class="text-decoration-none fw-medium"><?= esc($conn->connected_name ?? '') ?></a>
                                            <br><small class="text-muted"><?= esc($conn->connected_code ?? '') ?></small>
                                        </td>
                                        <td>
                                            <?php $sBadge = match($conn->status ?? 'pending') { 'accepted'=>'badge-active','pending'=>'badge-help','inactive'=>'badge-inactive',default=>'bg-secondary' }; ?>
                                            <span class="badge <?= $sBadge ?>"><?= ucfirst($conn->status ?? 'pending') ?></span>
                                        </td>
                                        <td><span data-time="<?= esc($conn->created_at ?? '') ?>"></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js"></script>
<script>
// Activity chart
new ApexCharts(document.querySelector("#activityChart"), {
    chart: { type: 'area', height: 280, toolbar: { show: false }, fontFamily: 'inherit' },
    series: [
        { name: 'Check-ins', data: <?= json_encode(array_column($chartData, 'count')) ?> },
        { name: 'New Users', data: <?= json_encode($newUsersData) ?> }
    ],
    xaxis: {
        categories: <?= json_encode(array_column($chartData, 'date')) ?>,
        labels: { style: { colors: '#888', fontSize: '12px' } }
    },
    yaxis: { labels: { style: { colors: '#888' } }, min: 0, forceNiceScale: true },
    colors: ['#27ae60', '#3498db'],
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] } },
    stroke: { curve: 'smooth', width: 3 },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f0f0f0', strokeDashArray: 4 },
    tooltip: { theme: 'light' },
    legend: { position: 'top', horizontalAlign: 'right', labels: { colors: '#666' } }
}).render();

// Plan donut
new ApexCharts(document.querySelector("#planChart"), {
    chart: { type: 'donut', height: 240 },
    series: [<?= $planData['free'] ?? 0 ?>, <?= $planData['paid'] ?? 0 ?>],
    labels: ['Free', 'Paid'],
    colors: ['#95a5a6', '#27ae60'],
    plotOptions: {
        pie: { donut: { size: '65%', labels: {
            show: true,
            name: { fontSize: '14px', color: '#333' },
            value: { fontSize: '22px', fontWeight: 700, color: '#1a1a2e' },
            total: { show: true, label: 'Total', fontSize: '12px', color: '#888', formatter: () => '<?= ($planData['free'] ?? 0) + ($planData['paid'] ?? 0) ?>' }
        }}}
    },
    dataLabels: { enabled: false },
    legend: { position: 'bottom', labels: { colors: '#666' } },
    stroke: { width: 0 }
}).render();
</script>
<?= $this->endSection() ?>
