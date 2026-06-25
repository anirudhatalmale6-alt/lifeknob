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
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Name, email, or phone..."
                           value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Role</label>
                <select class="form-select" name="role">
                    <option value="">All Roles</option>
                    <option value="elder" <?= ($role ?? '') === 'elder' ? 'selected' : '' ?>>Elder</option>
                    <option value="family" <?= ($role ?? '') === 'family' ? 'selected' : '' ?>>Family Member</option>
                    <option value="admin" <?= ($role ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Statuses</option>
                    <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($status ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
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
                            <th>Email</th>
                            <th>Role</th>
                            <th>Phone</th>
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
                                <td>
                                    <a href="mailto:<?= esc($user->email) ?>" class="text-decoration-none text-muted">
                                        <?= esc($user->email) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                        $roleBadge = match($user->role) {
                                            'elder' => 'badge-elder',
                                            'family' => 'badge-family',
                                            'admin' => 'badge-admin',
                                            default => 'bg-secondary',
                                        };
                                        $roleIcon = match($user->role) {
                                            'elder' => 'fa-user-clock',
                                            'family' => 'fa-user-friends',
                                            'admin' => 'fa-user-shield',
                                            default => 'fa-user',
                                        };
                                    ?>
                                    <span class="badge <?= $roleBadge ?>">
                                        <i class="fas <?= $roleIcon ?> me-1"></i><?= ucfirst(esc($user->role)) ?>
                                    </span>
                                </td>
                                <td><?= esc($user->phone ?? '-') ?></td>
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
