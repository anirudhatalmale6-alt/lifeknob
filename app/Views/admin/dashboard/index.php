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
            <div>
                <div class="stat-value"><?= number_format($stats['total_users'] ?? 0) ?></div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-green-light">
                <i class="fas fa-user-clock"></i>
            </div>
            <div>
                <div class="stat-value"><?= number_format($stats['active_elders'] ?? 0) ?></div>
                <div class="stat-label">Active Elders</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-blue-light">
                <i class="fas fa-user-friends"></i>
            </div>
            <div>
                <div class="stat-value"><?= number_format($stats['family_members'] ?? 0) ?></div>
                <div class="stat-label">Family Members</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-red-light">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <div class="stat-value"><?= number_format($stats['active_alerts'] ?? 0) ?></div>
                <div class="stat-label">Active Alerts</div>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
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
    <div class="col-md-6">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-orange-light">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <div class="stat-value"><?= number_format($stats['total_groups'] ?? 0) ?></div>
                <div class="stat-label">Family Groups</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Alerts -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-bell text-red me-2"></i>Recent Alerts</span>
                <a href="/admin/alerts" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentAlerts)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-check-circle fa-2x mb-2 text-green"></i>
                        <p class="mb-0">No alerts at this time</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Elder</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentAlerts as $alert): ?>
                                    <tr>
                                        <td>
                                            <?php
                                                $type = $alert->type ?? 'unknown';
                                                $badgeClass = match($type) {
                                                    'emergency' => 'badge-emergency',
                                                    'help' => 'badge-help',
                                                    'missed_checkin' => 'badge-missed',
                                                    default => 'bg-secondary',
                                                };
                                                $icon = match($type) {
                                                    'emergency' => 'fa-exclamation-circle',
                                                    'help' => 'fa-hand-paper',
                                                    'missed_checkin' => 'fa-clock',
                                                    default => 'fa-info-circle',
                                                };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>">
                                                <i class="fas <?= $icon ?> me-1"></i><?= ucfirst(str_replace('_', ' ', $type)) ?>
                                            </span>
                                        </td>
                                        <td><?= esc($alert->elder_name ?? 'Unknown') ?></td>
                                        <td>
                                            <?php if (!empty($alert->is_resolved)): ?>
                                                <span class="badge badge-resolved">Resolved</span>
                                            <?php else: ?>
                                                <span class="badge badge-active">Active</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><span data-time="<?= esc($alert->created_at ?? '') ?>"></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

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
                        <i class="fas fa-clipboard fa-2x mb-2"></i>
                        <p class="mb-0">No check-ins recorded yet</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Note</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentCheckIns as $ci): ?>
                                    <tr>
                                        <td class="fw-medium"><?= esc($ci->user_name ?? 'Unknown') ?></td>
                                        <td>
                                            <?php
                                                $ciType = $ci->type ?? 'ok';
                                                $ciBadge = match($ciType) {
                                                    'ok' => 'badge-ok',
                                                    'help' => 'badge-help',
                                                    'emergency' => 'badge-emergency',
                                                    default => 'bg-secondary',
                                                };
                                            ?>
                                            <span class="badge <?= $ciBadge ?>"><?= ucfirst($ciType) ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($ci->note)): ?>
                                                <span class="text-muted" title="<?= esc($ci->note) ?>"><?= esc(mb_strimwidth($ci->note, 0, 30, '...')) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
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
</div>

<!-- Check-in Activity Chart Placeholder -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chart-area text-green me-2"></i>Check-in Activity (Last 7 Days)</span>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary active">7 Days</button>
                    <button type="button" class="btn btn-outline-secondary">30 Days</button>
                </div>
            </div>
            <div class="card-body">
                <div style="height: 250px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, rgba(39,174,96,0.03) 0%, rgba(52,152,219,0.03) 100%); border-radius: 8px; border: 2px dashed #e0e0e0;">
                    <div class="text-center text-muted">
                        <i class="fas fa-chart-line fa-3x mb-3" style="opacity: 0.2;"></i>
                        <p class="mb-1 fw-medium">Activity Chart</p>
                        <small>Integrate Chart.js or ApexCharts here to visualize daily check-in counts</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
