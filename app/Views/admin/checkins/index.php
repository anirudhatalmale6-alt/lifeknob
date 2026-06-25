<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Check-in Log</h1>
    <p>All check-in records across the system</p>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="get" action="/admin/checkins" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted">Type</label>
                <select class="form-select" name="type">
                    <option value="">All Types</option>
                    <option value="ok" <?= ($type ?? '') === 'ok' ? 'selected' : '' ?>>OK</option>
                    <option value="help" <?= ($type ?? '') === 'help' ? 'selected' : '' ?>>Help</option>
                    <option value="emergency" <?= ($type ?? '') === 'emergency' ? 'selected' : '' ?>>Emergency</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Search User</label>
                <input type="text" class="form-control" name="search" placeholder="User name..."
                       value="<?= esc($search ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted">From</label>
                <input type="date" class="form-control" name="date_from" value="<?= esc($dateFrom ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted">To</label>
                <input type="date" class="form-control" name="date_to" value="<?= esc($dateTo ?? '') ?>">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-green flex-grow-1">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                <a href="/admin/checkins" class="btn btn-outline-secondary" title="Reset">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Check-ins Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($checkIns)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-clipboard-check fa-3x mb-3" style="opacity: 0.15;"></i>
                <p class="mb-0 fw-medium">No check-ins found</p>
                <small>Try adjusting your filters or check back later</small>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Note</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($checkIns as $ci): ?>
                            <tr>
                                <td class="text-muted">#<?= esc($ci->id) ?></td>
                                <td>
                                    <?php if (!empty($ci->user_id)): ?>
                                        <a href="/admin/users/<?= esc($ci->user_id) ?>" class="text-decoration-none fw-medium">
                                            <?= esc($ci->user_name ?? 'Unknown') ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Unknown</span>
                                    <?php endif; ?>
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
                                        $ciIcon = match($ciType) {
                                            'ok' => 'fa-check-circle',
                                            'help' => 'fa-hand-paper',
                                            'emergency' => 'fa-exclamation-circle',
                                            default => 'fa-info-circle',
                                        };
                                    ?>
                                    <span class="badge <?= $ciBadge ?>">
                                        <i class="fas <?= $ciIcon ?> me-1"></i><?= ucfirst($ciType) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($ci->latitude) && !empty($ci->longitude)): ?>
                                        <a href="https://maps.google.com/?q=<?= esc($ci->latitude) ?>,<?= esc($ci->longitude) ?>"
                                           target="_blank" class="text-decoration-none" title="Open in Google Maps">
                                            <i class="fas fa-map-marker-alt text-red me-1"></i>
                                            <?= number_format((float)$ci->latitude, 4) ?>, <?= number_format((float)$ci->longitude, 4) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>Not available</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($ci->note)): ?>
                                        <span title="<?= esc($ci->note) ?>"><?= esc(mb_strimwidth($ci->note, 0, 40, '...')) ?></span>
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

    <?php if (!empty($pager)): ?>
        <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing <?= count($checkIns) ?> check-in<?= count($checkIns) !== 1 ? 's' : '' ?>
            </small>
            <?= $pager->links('default', 'default_full') ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
