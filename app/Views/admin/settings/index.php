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

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Default Check-in Settings</h6></div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Default Frequency</td>
                        <td><strong><?= $settings['default_frequency_hours'] ?> hours</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Reminder Before Deadline</td>
                        <td><strong><?= $settings['default_reminder_minutes'] ?> minutes</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Alert Delay After Miss</td>
                        <td><strong><?= $settings['default_alert_delay_minutes'] ?> minutes</strong></td>
                    </tr>
                </table>
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
                    <tr>
                        <td class="text-muted">Cron URL</td>
                        <td><code>/cron/checkins?token=<?= esc($settings['cron_token']) ?></code></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Cron Setup</h6></div>
            <div class="card-body">
                <p class="text-muted mb-2">Add this to your server's crontab to run check-in monitoring every 5 minutes:</p>
                <pre class="bg-dark text-light p-3 rounded">*/5 * * * * curl -s --max-time 60 "https://lifeknob.com/cron/checkins?token=<?= esc($settings['cron_token']) ?>" > /dev/null 2>&1</pre>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
