<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1><?= esc($user->name) ?></h1>
        <p>
            <span class="badge <?= match($user->role) { 'elder' => 'badge-elder', 'family' => 'badge-family', 'admin' => 'badge-admin', default => 'bg-secondary' } ?>">
                <?= ucfirst(esc($user->role)) ?>
            </span>
            <span class="ms-2 text-muted">ID #<?= esc($user->id) ?></span>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/users" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Users
        </a>
        <form action="/admin/users/<?= esc($user->id) ?>/toggle" method="post" class="d-inline">
            <?= csrf_field() ?>
            <?php if (!empty($user->is_active)): ?>
                <button type="submit" class="btn btn-outline-warning" onclick="return confirm('Deactivate this user?')">
                    <i class="fas fa-ban me-1"></i>Deactivate
                </button>
            <?php else: ?>
                <button type="submit" class="btn btn-green" onclick="return confirm('Activate this user?')">
                    <i class="fas fa-check me-1"></i>Activate
                </button>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="row g-4">
    <!-- User Info Card -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center pt-4">
                <div class="mb-3">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: <?= !empty($user->is_active) ? '#27ae60' : '#e74c3c' ?>; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700;">
                        <?= strtoupper(substr($user->name, 0, 1)) ?>
                    </div>
                </div>
                <h5 class="mb-1"><?= esc($user->name) ?></h5>
                <p class="text-muted mb-3"><?= esc($user->email) ?></p>

                <?php if (!empty($user->is_active)): ?>
                    <span class="badge badge-active px-3 py-2"><i class="fas fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i>Active</span>
                <?php else: ?>
                    <span class="badge badge-inactive px-3 py-2"><i class="fas fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i>Inactive</span>
                <?php endif; ?>
            </div>

            <hr class="my-0">

            <div class="card-body">
                <table class="table table-borderless mb-0" style="font-size: 0.9rem;">
                    <tr>
                        <td class="text-muted fw-medium" style="width: 40%;"><i class="fas fa-phone me-2"></i>Phone</td>
                        <td><?= esc($user->phone ?? 'Not set') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium"><i class="fas fa-user-tag me-2"></i>Role</td>
                        <td><?= ucfirst(esc($user->role)) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium"><i class="fas fa-globe me-2"></i>Timezone</td>
                        <td><?= esc($user->timezone ?? 'UTC') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium"><i class="fas fa-calendar me-2"></i>Registered</td>
                        <td><span data-date="<?= esc($user->created_at ?? '') ?>"></span></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium"><i class="fas fa-clock me-2"></i>Last Seen</td>
                        <td><span data-time="<?= esc($user->last_seen_at ?? '') ?>"></span></td>
                    </tr>
                    <?php if (!empty($user->firebase_token)): ?>
                    <tr>
                        <td class="text-muted fw-medium"><i class="fas fa-mobile-alt me-2"></i>Push</td>
                        <td><span class="badge badge-active">Enabled</span></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-8">
        <?php if (($user->role ?? '') === 'elder' && !empty($checkInSettings)): ?>
        <!-- Check-in Settings -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-cog text-green me-2"></i>Check-in Settings
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: #f8f9fa;">
                            <div class="stat-icon bg-green-light" style="width: 40px; height: 40px; font-size: 0.9rem;">
                                <i class="fas fa-sync-alt"></i>
                            </div>
                            <div>
                                <div class="fw-bold"><?= esc($checkInSettings->frequency_hours ?? 12) ?> hours</div>
                                <small class="text-muted">Check-in Frequency</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: #f8f9fa;">
                            <div class="stat-icon bg-orange-light" style="width: 40px; height: 40px; font-size: 0.9rem;">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div>
                                <div class="fw-bold"><?= esc($checkInSettings->reminder_minutes ?? 30) ?> min</div>
                                <small class="text-muted">Reminder Before Alert</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: #f8f9fa;">
                            <div class="stat-icon bg-red-light" style="width: 40px; height: 40px; font-size: 0.9rem;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <div class="fw-bold"><?= esc($checkInSettings->alert_delay_minutes ?? 60) ?> min</div>
                                <small class="text-muted">Alert Delay</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: #f8f9fa;">
                            <div class="stat-icon bg-blue-light" style="width: 40px; height: 40px; font-size: 0.9rem;">
                                <i class="fas fa-moon"></i>
                            </div>
                            <div>
                                <div class="fw-bold"><?= esc($checkInSettings->quiet_hours_start ?? '22:00') ?> - <?= esc($checkInSettings->quiet_hours_end ?? '07:00') ?></div>
                                <small class="text-muted">Quiet Hours</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (($user->role ?? '') === 'elder' && !empty($recentCheckIns)): ?>
        <!-- Recent Check-ins -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-clipboard-check text-green me-2"></i>Recent Check-ins</span>
                <span class="badge bg-secondary"><?= count($recentCheckIns) ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Note</th>
                                <th>Location</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentCheckIns as $ci): ?>
                                <tr>
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
                                    <td><?= esc($ci->note ?? '-') ?></td>
                                    <td>
                                        <?php if (!empty($ci->latitude) && !empty($ci->longitude)): ?>
                                            <a href="https://maps.google.com/?q=<?= esc($ci->latitude) ?>,<?= esc($ci->longitude) ?>"
                                               target="_blank" class="text-decoration-none" title="Open in Google Maps">
                                                <i class="fas fa-map-marker-alt text-red me-1"></i>
                                                <?= number_format((float)$ci->latitude, 4) ?>, <?= number_format((float)$ci->longitude, 4) ?>
                                            </a>
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
            </div>
        </div>
        <?php elseif (($user->role ?? '') === 'elder'): ?>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-clipboard-check text-green me-2"></i>Recent Check-ins
            </div>
            <div class="card-body text-center py-4 text-muted">
                <i class="fas fa-clipboard fa-2x mb-2" style="opacity: 0.2;"></i>
                <p class="mb-0">No check-ins recorded yet</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Family Groups -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-user-friends text-orange me-2"></i>Family Groups</span>
                <span class="badge bg-secondary"><?= count($groups ?? []) ?></span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($groups)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-users fa-2x mb-2" style="opacity: 0.2;"></i>
                        <p class="mb-0">Not a member of any group</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Group Name</th>
                                    <th>Invite Code</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($groups as $group): ?>
                                    <tr>
                                        <td class="fw-medium"><?= esc($group->name) ?></td>
                                        <td><code><?= esc($group->invite_code ?? '-') ?></code></td>
                                        <td><span data-date="<?= esc($group->created_at ?? '') ?>"></span></td>
                                        <td>
                                            <a href="/admin/groups/<?= esc($group->id) ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>View
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
    </div>
</div>

<?= $this->endSection() ?>
