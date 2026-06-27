<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Overdue Users</h1>
    <p>Users with accepted connections who haven't checked in within <?= esc($thresholdDays) ?> day<?= $thresholdDays != 1 ? 's' : '' ?></p>
</div>

<!-- Info Banner -->
<div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
    <i class="fas fa-info-circle me-3 fa-lg"></i>
    <div>
        <strong>Threshold: <?= esc($thresholdDays) ?> day<?= $thresholdDays != 1 ? 's' : '' ?></strong> &mdash;
        Users shown here have at least one accepted connection and their most recent check-in is older than <?= esc($thresholdDays) ?> day<?= $thresholdDays != 1 ? 's' : '' ?>.
        <a href="/admin/settings" class="alert-link">Change threshold</a>
    </div>
</div>

<!-- Overdue Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-exclamation-triangle text-orange me-2"></i>Overdue Users</span>
        <span class="badge <?= count($overdueUsers ?? []) > 0 ? 'bg-danger' : 'bg-success' ?>"><?= count($overdueUsers ?? []) ?></span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($overdueUsers)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-check-circle fa-3x mb-3 text-green" style="opacity: 0.3;"></i>
                <p class="mb-0 fw-medium">No overdue users</p>
                <small>All connected users have checked in within the last <?= esc($thresholdDays) ?> day<?= $thresholdDays != 1 ? 's' : '' ?></small>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Code</th>
                            <th>Last Check-in</th>
                            <th>Days Overdue</th>
                            <th>Connected To</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($overdueUsers as $ou): ?>
                            <tr>
                                <td>
                                    <a href="/admin/users/<?= esc($ou->id) ?>" class="text-decoration-none fw-medium">
                                        <?= esc($ou->name) ?>
                                    </a>
                                    <br><small class="text-muted"><?= esc($ou->email) ?></small>
                                </td>
                                <td><code><?= esc($ou->user_code ?? '-') ?></code></td>
                                <td><span data-time="<?= esc($ou->last_checkin_at ?? '') ?>"></span></td>
                                <td>
                                    <?php
                                        $days = $ou->days_overdue ?? 0;
                                        $badgeClass = $days >= 7 ? 'badge-emergency' : ($days >= 3 ? 'badge-help' : 'badge-missed');
                                    ?>
                                    <span class="badge <?= $badgeClass ?>">
                                        <i class="fas fa-clock me-1"></i><?= $days ?> day<?= $days != 1 ? 's' : '' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($ou->connected_to)): ?>
                                        <?php foreach ($ou->connected_to as $ct): ?>
                                            <span class="badge bg-light text-dark me-1 mb-1">
                                                <?= esc($ct->name) ?>
                                                <small class="text-muted">(<?= esc($ct->code) ?>)</small>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($ou->is_active)): ?>
                                        <span class="badge badge-active"><i class="fas fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i>Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-inactive"><i class="fas fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i>Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
