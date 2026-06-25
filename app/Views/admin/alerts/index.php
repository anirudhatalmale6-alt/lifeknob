<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Alerts</h1>
    <p>Monitor and manage all system alerts</p>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="get" action="/admin/alerts" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Type</label>
                <select class="form-select" name="type">
                    <option value="">All Types</option>
                    <option value="missed_checkin" <?= ($type ?? '') === 'missed_checkin' ? 'selected' : '' ?>>Missed Check-in</option>
                    <option value="help" <?= ($type ?? '') === 'help' ? 'selected' : '' ?>>Help Request</option>
                    <option value="emergency" <?= ($type ?? '') === 'emergency' ? 'selected' : '' ?>>Emergency</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Statuses</option>
                    <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="resolved" <?= ($status ?? '') === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Search Elder</label>
                <input type="text" class="form-control" name="search" placeholder="Elder name..."
                       value="<?= esc($search ?? '') ?>">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-green flex-grow-1">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                <a href="/admin/alerts" class="btn btn-outline-secondary" title="Reset">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Alerts Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($alerts)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-check-circle fa-3x mb-3 text-green" style="opacity: 0.3;"></i>
                <p class="mb-0 fw-medium">No alerts found</p>
                <small>Try adjusting your filters or check back later</small>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Elder</th>
                            <th>Group</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Resolved</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alerts as $alert): ?>
                            <tr class="<?= empty($alert->is_resolved) ? '' : '' ?>">
                                <td class="text-muted">#<?= esc($alert->id) ?></td>
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
                                <td>
                                    <?php if (!empty($alert->elder_id)): ?>
                                        <a href="/admin/users/<?= esc($alert->elder_id) ?>" class="text-decoration-none fw-medium">
                                            <?= esc($alert->elder_name ?? 'Unknown') ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Unknown</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($alert->group_id)): ?>
                                        <a href="/admin/groups/<?= esc($alert->group_id) ?>" class="text-decoration-none">
                                            <?= esc($alert->group_name ?? 'Group #' . $alert->group_id) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($alert->message)): ?>
                                        <span title="<?= esc($alert->message) ?>"><?= esc(mb_strimwidth($alert->message, 0, 35, '...')) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($alert->is_resolved)): ?>
                                        <span class="badge badge-resolved"><i class="fas fa-check me-1"></i>Resolved</span>
                                    <?php else: ?>
                                        <span class="badge badge-active">
                                            <i class="fas fa-circle me-1" style="font-size: 0.4rem; vertical-align: middle; animation: pulse 1.5s infinite;"></i>Active
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><span data-time="<?= esc($alert->created_at ?? '') ?>"></span></td>
                                <td>
                                    <?php if (!empty($alert->is_resolved)): ?>
                                        <div>
                                            <?php if (!empty($alert->resolved_by_name)): ?>
                                                <small class="text-muted">by <?= esc($alert->resolved_by_name) ?></small><br>
                                            <?php endif; ?>
                                            <span data-time="<?= esc($alert->resolved_at ?? '') ?>"></span>
                                        </div>
                                    <?php else: ?>
                                        <form action="/admin/alerts/<?= esc($alert->id) ?>/resolve" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-green" onclick="return confirm('Mark this alert as resolved?')">
                                                <i class="fas fa-check me-1"></i>Resolve
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($pager)): ?>
        <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing <?= count($alerts) ?> alert<?= count($alerts) !== 1 ? 's' : '' ?>
            </small>
            <?= $pager->links('default', 'bootstrap_5') ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }
</style>
<?= $this->endSection() ?>
