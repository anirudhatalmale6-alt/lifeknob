<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">System Settings</h4>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form method="post" action="/admin/settings/save">
<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Alert Settings</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Alert Threshold (days without OK button)</label>
                    <select name="alert_threshold_days" class="form-select">
                        <option value="1" <?= $settings['alert_threshold_days'] == '1' ? 'selected' : '' ?>>1 day</option>
                        <option value="2" <?= $settings['alert_threshold_days'] == '2' ? 'selected' : '' ?>>2 days</option>
                        <option value="3" <?= $settings['alert_threshold_days'] == '3' ? 'selected' : '' ?>>3 days</option>
                        <option value="5" <?= $settings['alert_threshold_days'] == '5' ? 'selected' : '' ?>>5 days</option>
                        <option value="7" <?= $settings['alert_threshold_days'] == '7' ? 'selected' : '' ?>>7 days</option>
                    </select>
                    <small class="text-muted">Connections turn red after this many days without pressing OK</small>
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="alert_email_enabled" value="1" <?= $settings['alert_email_enabled'] == '1' ? 'checked' : '' ?>>
                        <label class="form-check-label">Send alert emails when overdue</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="reminder_enabled" value="1" <?= $settings['reminder_enabled'] == '1' ? 'checked' : '' ?>>
                        <label class="form-check-label">Daily reminder push notifications</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">System Status</h6></div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Firebase Push</td>
                        <td>
                            <?php if ($settings['firebase_configured']): ?>
                                <span class="badge bg-success">Configured</span>
                            <?php else: ?>
                                <span class="badge bg-warning">Not configured</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Cron Token</td>
                        <td><code><?= esc($settings['cron_token']) ?></code></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </div>
</div>
</form>

<?= $this->endSection() ?>
