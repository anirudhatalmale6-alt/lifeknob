<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <h1><i class="fas fa-edit me-2"></i><?= esc($language['name']) ?> Translations</h1>
        <p>Edit translations for <strong><?= esc($language['name']) ?></strong> (<?= esc($language['code']) ?>)</p>
    </div>
    <a href="/admin/languages" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<form action="/admin/languages/save/<?= esc($language['code']) ?>" method="post">
    <?php foreach ($grouped as $group => $strings): ?>
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-folder me-2"></i><?= ucfirst(esc($group)) ?></span>
            <span class="badge bg-secondary"><?= count($strings) ?> keys</span>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width: 25%">Key</th>
                        <th style="width: 35%">English</th>
                        <th style="width: 40%"><?= esc($language['name']) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($strings as $s): ?>
                    <tr>
                        <td><code class="small"><?= esc($s['key']) ?></code></td>
                        <td class="small text-muted"><?= esc($s['en_value']) ?></td>
                        <td>
                            <?php if ($isDefault): ?>
                                <input type="text" name="translations[<?= esc($s['key']) ?>]"
                                    value="<?= esc($s['en_value']) ?>"
                                    class="form-control form-control-sm">
                            <?php else: ?>
                                <input type="text" name="translations[<?= esc($s['key']) ?>]"
                                    value="<?= esc($s['value']) ?>"
                                    class="form-control form-control-sm"
                                    placeholder="<?= esc($s['en_value']) ?>">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-green btn-lg">
            <i class="fas fa-save me-2"></i>Save All Translations
        </button>
        <a href="/admin/languages" class="btn btn-outline-secondary btn-lg">Cancel</a>
    </div>
</form>

<div class="card mb-4">
    <div class="card-header"><i class="fas fa-plus me-2"></i>Add New String Key</div>
    <div class="card-body">
        <form action="/admin/languages/add-key" method="post" class="row g-2 align-items-end">
            <input type="hidden" name="return_to" value="<?= esc($language['code']) ?>">
            <div class="col-md-4">
                <label class="form-label">Key</label>
                <input type="text" name="key" class="form-control" placeholder="e.g. settings_title" required>
            </div>
            <div class="col-md-5">
                <label class="form-label">English Value</label>
                <input type="text" name="value" class="form-control" placeholder="e.g. Settings" required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-green w-100"><i class="fas fa-plus me-1"></i>Add Key</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
