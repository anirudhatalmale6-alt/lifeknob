<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1>Users</h1>
        <p>Manage all registered users</p>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="get" action="/admin/users" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-semibold text-muted">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control" name="q" placeholder="Name, email, phone, or user code..."
                           value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Plan</label>
                <select class="form-select" name="plan">
                    <option value="">All Plans</option>
                    <option value="free" <?= ($planFilter ?? '') === 'free' ? 'selected' : '' ?>>Free</option>
                    <option value="paid" <?= ($planFilter ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-green flex-grow-1">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                <a href="/admin/users" class="btn btn-outline-secondary" title="Reset">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($users)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-users fa-3x mb-3" style="opacity: 0.15;"></i>
                <p class="mb-0 fw-medium">No users found</p>
                <small>Try adjusting your filters</small>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Email</th>
                            <th>Plan</th>
                            <th>Connections</th>
                            <th>Last Check-in</th>
                            <th>Last Seen</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="text-muted">#<?= esc($user->id) ?></td>
                                <td class="fw-medium"><?= esc($user->name) ?></td>
                                <td><code><?= esc($user->user_code ?? '-') ?></code></td>
                                <td>
                                    <a href="mailto:<?= esc($user->email) ?>" class="text-decoration-none text-muted">
                                        <?= esc($user->email) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                        $plan = $user->plan ?? 'free';
                                        $planBadge = $plan === 'paid' ? 'badge-active' : 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $planBadge ?>"><?= ucfirst($plan) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= $user->connection_count ?? 0 ?></span>
                                </td>
                                <td><span data-time="<?= esc($user->last_checkin_at ?? '') ?>"></span></td>
                                <td><span data-time="<?= esc($user->last_seen_at ?? '') ?>"></span></td>
                                <td>
                                    <?php if (!empty($user->is_active)): ?>
                                        <span class="badge badge-active"><i class="fas fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i>Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-inactive"><i class="fas fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i>Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="/admin/users/<?= esc($user->id) ?>" class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="/admin/users/<?= esc($user->id) ?>/toggle" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <?php if (!empty($user->is_active)): ?>
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Deactivate"
                                                        onclick="return confirm('Deactivate this user?')">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Activate"
                                                        onclick="return confirm('Activate this user?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
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
                Showing <?= count($users) ?> user<?= count($users) !== 1 ? 's' : '' ?>
            </small>
            <?= $pager->links('default', 'default_full') ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
