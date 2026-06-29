<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <h1><i class="fas fa-language me-2"></i>Languages</h1>
        <p>Manage app languages and translations</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-blue-light"><i class="fas fa-globe"></i></div>
                <div>
                    <div class="stat-value"><?= count($languages) ?></div>
                    <div class="stat-label">Languages</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-green-light"><i class="fas fa-key"></i></div>
                <div>
                    <div class="stat-value"><?= $totalKeys ?></div>
                    <div class="stat-label">String Keys</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list me-2"></i>All Languages</span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Language</th>
                            <th>Code</th>
                            <th>Translations</th>
                            <th>Coverage</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($languages as $lang): ?>
                        <tr>
                            <td>
                                <strong><?= esc($lang['name']) ?></strong>
                                <?php if ($lang['is_default']): ?>
                                    <span class="badge bg-primary ms-1">Default</span>
                                <?php endif; ?>
                            </td>
                            <td><code><?= esc($lang['code']) ?></code></td>
                            <td><?= $lang['translation_count'] ?> / <?= $totalKeys ?></td>
                            <td>
                                <?php
                                    $pct = $totalKeys > 0 ? round(($lang['translation_count'] / $totalKeys) * 100) : 0;
                                    $color = $pct >= 80 ? 'success' : ($pct >= 40 ? 'warning' : 'danger');
                                ?>
                                <div class="progress" style="height: 6px; width: 100px;">
                                    <div class="progress-bar bg-<?= $color ?>" style="width: <?= $pct ?>%"></div>
                                </div>
                                <small class="text-muted"><?= $pct ?>%</small>
                            </td>
                            <td>
                                <?php if ($lang['is_active']): ?>
                                    <span class="badge badge-active">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/admin/languages/edit/<?= $lang['code'] ?>" class="btn btn-sm btn-outline-primary" title="Edit translations">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($lang['code'] !== 'en'): ?>
                                <form action="/admin/languages/toggle/<?= $lang['code'] ?>" method="post" class="d-inline">
                                    <button type="submit" class="btn btn-sm btn-outline-<?= $lang['is_active'] ? 'warning' : 'success' ?>" title="<?= $lang['is_active'] ? 'Deactivate' : 'Activate' ?>">
                                        <i class="fas fa-<?= $lang['is_active'] ? 'pause' : 'play' ?>"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="fas fa-plus me-2"></i>Add Language</div>
            <div class="card-body">
                <form action="/admin/languages/add" method="post">
                    <div class="mb-3">
                        <label class="form-label">Language Code</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. ja, zh, ar" maxlength="10" required>
                        <small class="text-muted">ISO 639-1 code (2 letters)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Language Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Japanese" maxlength="50" required>
                    </div>
                    <button type="submit" class="btn btn-green w-100">
                        <i class="fas fa-plus me-2"></i>Add Language
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
