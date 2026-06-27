<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Connections</h1>
    <p>All user-to-user connections</p>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="get" action="/admin/connections" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-semibold text-muted">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control" name="q" placeholder="User name or code..."
                           value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Statuses</option>
                    <option value="accepted" <?= ($statusFilter ?? '') === 'accepted' ? 'selected' : '' ?>>Accepted</option>
                    <option value="pending" <?= ($statusFilter ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="rejected" <?= ($statusFilter ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    <option value="inactive" <?= ($statusFilter ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-green flex-grow-1">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                <a href="/admin/connections" class="btn btn-outline-secondary" title="Reset">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Connections Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($connections)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-link fa-3x mb-3" style="opacity: 0.15;"></i>
                <p class="mb-0 fw-medium">No connections found</p>
                <small>Try adjusting your filters</small>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User A</th>
                            <th></th>
                            <th>User B</th>
                            <th>Display Name</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($connections as $conn): ?>
                            <tr>
                                <td class="text-muted">#<?= esc($conn->id) ?></td>
                                <td>
                                    <a href="/admin/users/<?= esc($conn->user_id) ?>" class="text-decoration-none fw-medium">
                                        <?= esc($conn->user_name ?? 'Unknown') ?>
                                    </a>
                                    <br><small class="text-muted"><code><?= esc($conn->user_code ?? '') ?></code></small>
                                </td>
                                <td class="text-center text-muted">
                                    <i class="fas fa-arrows-alt-h"></i>
                                </td>
                                <td>
                                    <a href="/admin/users/<?= esc($conn->connected_to) ?>" class="text-decoration-none fw-medium">
                                        <?= esc($conn->connected_name ?? 'Unknown') ?>
                                    </a>
                                    <br><small class="text-muted"><code><?= esc($conn->connected_code ?? '') ?></code></small>
                                </td>
                                <td><?= esc($conn->display_name ?? '-') ?></td>
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

    <div class="card-footer bg-white border-top">
        <small class="text-muted">
            Showing <?= count($connections) ?> connection<?= count($connections) !== 1 ? 's' : '' ?>
        </small>
    </div>
</div>

<?= $this->endSection() ?>
