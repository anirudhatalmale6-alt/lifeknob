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

<form method="post" action="/admin/settings/save" enctype="multipart/form-data">
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

    <!-- Advertising -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Advertising</h6>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="ads_enabled" value="1" <?= ($settings['ads_enabled'] ?? '1') == '1' ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold">Enable ads for free users</label>
                    </div>
                </div>

                <div class="row">
                    <!-- Banner Ads -->
                    <div class="col-md-6">
                        <div class="border rounded p-3 mb-3">
                            <h6 class="text-primary mb-3">Banner Ads <small class="text-muted fw-normal">(Home, People, Setup pages)</small></h6>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Option 1: Paste Ad Code</label>
                                <textarea name="adsense_banner_code" class="form-control" rows="3" placeholder="Paste AdSense or other ad network code here..."><?= esc($settings['adsense_banner_code'] ?? '') ?></textarea>
                                <small class="text-muted">If ad code is provided, it takes priority over custom image</small>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Option 2: Upload Your Banner</label>
                                <small class="text-muted d-block mb-2">Use your own ad image with a click-through URL (e.g. promote resesnews)</small>

                                <?php if (!empty($settings['banner_ad_image'])): ?>
                                    <div class="mb-2">
                                        <img src="<?= esc($settings['banner_ad_image']) ?>" class="img-fluid rounded border" style="max-height: 80px;">
                                        <div class="form-check mt-1">
                                            <input class="form-check-input" type="checkbox" name="remove_banner_image" value="1">
                                            <label class="form-check-label text-danger small">Remove current image</label>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <input type="file" name="banner_ad_file" class="form-control mb-2" accept=".png,.jpg,.jpeg,.gif,.webp">
                                <small class="text-muted">Recommended: 320x100 (3.2:1 ratio), PNG/JPG/GIF/WebP</small>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Click URL</label>
                                <input type="url" name="banner_ad_url" class="form-control" value="<?= esc($settings['banner_ad_url'] ?? '') ?>" placeholder="https://resesnews.com">
                                <small class="text-muted">Where to go when user taps the banner</small>
                            </div>
                        </div>
                    </div>

                    <!-- Bumper Ads -->
                    <div class="col-md-6">
                        <div class="border rounded p-3 mb-3">
                            <h6 class="text-primary mb-3">Bumper Ads <small class="text-muted fw-normal">(6-sec full-screen after actions)</small></h6>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Option 1: Paste Ad Code</label>
                                <textarea name="adsense_bumper_code" class="form-control" rows="3" placeholder="Paste interstitial ad code here..."><?= esc($settings['adsense_bumper_code'] ?? '') ?></textarea>
                                <small class="text-muted">If ad code is provided, it takes priority over custom image</small>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Option 2: Upload Your Banner</label>
                                <small class="text-muted d-block mb-2">Full-screen ad image with click-through URL</small>

                                <?php if (!empty($settings['bumper_ad_image'])): ?>
                                    <div class="mb-2">
                                        <img src="<?= esc($settings['bumper_ad_image']) ?>" class="img-fluid rounded border" style="max-height: 120px;">
                                        <div class="form-check mt-1">
                                            <input class="form-check-input" type="checkbox" name="remove_bumper_image" value="1">
                                            <label class="form-check-label text-danger small">Remove current image</label>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <input type="file" name="bumper_ad_file" class="form-control mb-2" accept=".png,.jpg,.jpeg,.gif,.webp">
                                <small class="text-muted">Recommended: 500x300 or similar, PNG/JPG/GIF/WebP</small>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Click URL</label>
                                <input type="url" name="bumper_ad_url" class="form-control" value="<?= esc($settings['bumper_ad_url'] ?? '') ?>" placeholder="https://resesnews.com">
                                <small class="text-muted">Where to go when user taps the bumper ad</small>
                            </div>

                            <hr>

                            <div class="mb-2">
                                <label class="form-label">Trigger delay on People page (seconds)</label>
                                <input type="number" name="bumper_delay_seconds" class="form-control" value="<?= esc($settings['bumper_delay_seconds'] ?? '30') ?>" min="10" max="300">
                                <small class="text-muted">Show bumper after this many seconds on People page</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-2">
                    <small class="text-muted">
                        <strong>How it works:</strong> Bumper ads trigger after knob turn success, after saving settings, or after spending time on the People page.
                        If ad code is provided, it is used. Otherwise, the uploaded image + URL is shown. If neither is set, a placeholder is displayed.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </div>
</div>
</form>

<?= $this->endSection() ?>
