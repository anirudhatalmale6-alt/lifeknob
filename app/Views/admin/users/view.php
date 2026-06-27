<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1><?= esc($user->name) ?></h1>
        <p>
            <code><?= esc($user->user_code ?? 'No code') ?></code>
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
                        <td class="text-muted fw-medium" style="width: 40%;"><i class="fas fa-fingerprint me-2"></i>Code</td>
                        <td><code><?= esc($user->user_code ?? 'None') ?></code></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium"><i class="fas fa-phone me-2"></i>Phone</td>
                        <td><?= esc($user->phone ?? 'Not set') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium"><i class="fas fa-crown me-2"></i>Plan</td>
                        <td>
                            <?php $plan = $user->plan ?? 'free'; ?>
                            <span class="badge <?= $plan === 'paid' ? 'badge-active' : 'bg-secondary' ?>"><?= ucfirst($plan) ?></span>
                            <small class="text-muted ms-1">(max <?= esc($user->max_connections ?? 3) ?> conn.)</small>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium"><i class="fas fa-user-tag me-2"></i>Role</td>
                        <td><?= ucfirst(esc($user->role)) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium"><i class="fas fa-phone-alt me-2"></i>SOS</td>
                        <td>
                            <?php if (!empty($user->sos_number)): ?>
                                <?= esc($user->sos_name ?? '') ?> - <?= esc($user->sos_number) ?>
                            <?php else: ?>
                                <span class="text-muted">Not set</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium"><i class="fas fa-ambulance me-2"></i>Ambulance</td>
                        <td><?= esc($user->ambulance_number ?? 'Not set') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium"><i class="fas fa-mobile-alt me-2"></i>Device</td>
                        <td>
                            <?php if (!empty($user->device_id)): ?>
                                <span title="<?= esc($user->device_id) ?>"><?= esc(mb_strimwidth($user->device_id, 0, 20, '...')) ?></span>
                            <?php else: ?>
                                <span class="text-muted">Not set</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium"><i class="fas fa-calendar me-2"></i>Registered</td>
                        <td><span data-date="<?= esc($user->created_at ?? '') ?>"></span></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-medium"><i class="fas fa-clock me-2"></i>Last Seen</td>
                        <td><span data-time="<?= esc($user->last_seen_at ?? '') ?>"></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-8">
        <!-- Connections -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-link text-green me-2"></i>Connections</span>
                <span class="badge bg-secondary"><?= count($connections ?? []) ?></span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($connections)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-link fa-2x mb-2" style="opacity: 0.2;"></i>
                        <p class="mb-0">No connections</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Connected To</th>
                                    <th>Code</th>
                                    <th>Display Name</th>
                                    <th>Status</th>
                                    <th>Their Last Check-in</th>
                                    <th>Connected</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($connections as $conn): ?>
                                    <tr>
                                        <td>
                                            <a href="/admin/users/<?= esc($conn->other_id ?? '') ?>" class="text-decoration-none fw-medium">
                                                <?= esc($conn->other_name ?? 'Unknown') ?>
                                            </a>
                                        </td>
                                        <td><code><?= esc($conn->other_code ?? '-') ?></code></td>
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
                                        <td><span data-time="<?= esc($conn->other_last_checkin ?? '') ?>"></span></td>
                                        <td><span data-time="<?= esc($conn->created_at ?? '') ?>"></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Check-in History -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-clipboard-check text-green me-2"></i>Check-in History</span>
                <span class="badge bg-secondary"><?= count($checkIns ?? []) ?></span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($checkIns)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-clipboard fa-2x mb-2" style="opacity: 0.2;"></i>
                        <p class="mb-0">No check-ins recorded yet</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Note</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($checkIns as $ci): ?>
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

<?= $this->endSection() ?>
