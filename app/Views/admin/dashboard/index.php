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
                <i class="fas fa-link"></i>
            </div>
            <div>
                <div class="stat-value"><?= number_format($stats['active_connections'] ?? 0) ?></div>
                <div class="stat-label">Active Connections</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-blue-light">
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
            <div class="stat-icon bg-red-light">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <div class="stat-value"><?= number_format($stats['overdue_users'] ?? 0) ?></div>
                <div class="stat-label">Overdue Users</div>
            </div>
        </div>
    </div>
</div>

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
                                        <td>
                                            <a href="/admin/users/<?= esc($ci->user_id ?? '') ?>" class="text-decoration-none fw-medium">
                                                <?= esc($ci->user_name ?? 'Unknown') ?>
                                            </a>
                                        </td>
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
                            <thead>
                                <tr>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentConnections as $conn): ?>
                                    <tr>
                                        <td>
                                            <a href="/admin/users/<?= esc($conn->user_id ?? '') ?>" class="text-decoration-none fw-medium">
                                                <?= esc($conn->user_name ?? 'Unknown') ?>
                                            </a>
                                            <br><small class="text-muted"><?= esc($conn->user_code ?? '') ?></small>
                                        </td>
                                        <td>
                                            <a href="/admin/users/<?= esc($conn->connected_to ?? '') ?>" class="text-decoration-none fw-medium">
                                                <?= esc($conn->connected_name ?? 'Unknown') ?>
                                            </a>
                                            <br><small class="text-muted"><?= esc($conn->connected_code ?? '') ?></small>
                                        </td>
                                        <td>
                                            <?php
                                                $status = $conn->status ?? 'pending';
                                                $statusBadge = match($status) {
                                                    'accepted' => 'badge-active',
                                                    'pending' => 'badge-help',
                                                    'rejected' => 'badge-emergency',
                                                    'inactive' => 'badge-inactive',
                                                    default => 'bg-secondary',
                                                };
                                            ?>
                                            <span class="badge <?= $statusBadge ?>"><?= ucfirst($status) ?></span>
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
