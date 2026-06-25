<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1><?= esc($group->name) ?></h1>
        <p>
            <span class="text-muted">Group #<?= esc($group->id) ?></span>
            <span class="mx-2">|</span>
            <code class="bg-light px-2 py-1 rounded"><?= esc($group->invite_code ?? '') ?></code>
        </p>
    </div>
    <a href="/admin/groups" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Groups
    </a>
</div>

<div class="row g-4">
    <!-- Group Info -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle text-muted me-2"></i>Group Info
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0" style="font-size: 0.9rem;">
                    <tr>
                        <td class="text-muted fw-medium" style="width: 45%;"><i class="fas fa-tag me-2"></i>Name</td>
                        <td class="fw-bold"><?= esc($group->name) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium"><i class="fas fa-key me-2"></i>Invite Code</td>
                        <td><code><?= esc($group->invite_code ?? '-') ?></code></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium"><i class="fas fa-user me-2"></i>Created By</td>
                        <td><?= esc($creator->name ?? 'Unknown') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium"><i class="fas fa-calendar me-2"></i>Created</td>
                        <td><span data-date="<?= esc($group->created_at ?? '') ?>"></span></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium"><i class="fas fa-users me-2"></i>Members</td>
                        <td><span class="badge bg-light text-dark"><?= count($members ?? []) ?></span></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3 p-3 rounded" style="background: rgba(39,174,96,0.06);">
                    <div class="stat-icon bg-green-light" style="width: 40px; height: 40px; font-size: 0.9rem;">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div>
                        <div class="fw-bold"><?= esc($elderCount ?? 0) ?></div>
                        <small class="text-muted">Elders</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: rgba(52,152,219,0.06);">
                    <div class="stat-icon bg-blue-light" style="width: 40px; height: 40px; font-size: 0.9rem;">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div>
                        <div class="fw-bold"><?= esc($familyCount ?? 0) ?></div>
                        <small class="text-muted">Family Members</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-8">
        <!-- Members Table -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-users text-green me-2"></i>Members</span>
                <span class="badge bg-secondary"><?= count($members ?? []) ?></span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($members)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-users fa-2x mb-2" style="opacity: 0.2;"></i>
                        <p class="mb-0">No members in this group</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Group Role</th>
                                    <th>Status</th>
                                    <th>Last Seen</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($members as $member): ?>
                                    <tr>
                                        <td class="fw-medium"><?= esc($member->name) ?></td>
                                        <td class="text-muted"><?= esc($member->email) ?></td>
                                        <td>
                                            <?php
                                                $roleBadge = match($member->user_role ?? '') {
                                                    'elder' => 'badge-elder',
                                                    'family' => 'badge-family',
                                                    'admin' => 'badge-admin',
                                                    default => 'bg-secondary',
                                                };
                                            ?>
                                            <span class="badge <?= $roleBadge ?>"><?= ucfirst(esc($member->user_role ?? 'unknown')) ?></span>
                                        </td>
                                        <td>
                                            <?php
                                                $groupRole = $member->role ?? 'member';
                                                $grBadge = match($groupRole) {
                                                    'admin', 'owner' => 'badge-admin',
                                                    'caregiver' => 'badge-family',
                                                    default => 'bg-light text-dark',
                                                };
                                            ?>
                                            <span class="badge <?= $grBadge ?>"><?= ucfirst(esc($groupRole)) ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($member->is_active)): ?>
                                                <span class="badge badge-active">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-inactive">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><span data-time="<?= esc($member->last_seen_at ?? '') ?>"></span></td>
                                        <td>
                                            <a href="/admin/users/<?= esc($member->user_id) ?>" class="btn btn-sm btn-outline-primary" title="View User">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Alerts for this Group -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-bell text-red me-2"></i>Recent Alerts</span>
                <span class="badge bg-secondary"><?= count($alerts ?? []) ?></span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($alerts)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-check-circle fa-2x mb-2 text-green"></i>
                        <p class="mb-0">No alerts for this group</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Elder</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($alerts as $alert): ?>
                                    <tr class="<?= empty($alert->is_resolved) ? 'table-warning' : '' ?>">
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
                                        <td class="fw-medium"><?= esc($alert->elder_name ?? 'Unknown') ?></td>
                                        <td>
                                            <?php if (!empty($alert->message)): ?>
                                                <span title="<?= esc($alert->message) ?>"><?= esc(mb_strimwidth($alert->message, 0, 40, '...')) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($alert->is_resolved)): ?>
                                                <span class="badge badge-resolved">Resolved</span>
                                            <?php else: ?>
                                                <span class="badge badge-active">
                                                    <i class="fas fa-circle me-1" style="font-size: 0.4rem; vertical-align: middle; animation: pulse 1.5s infinite;"></i>Active
                                                </span>
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
