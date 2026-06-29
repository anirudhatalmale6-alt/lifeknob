<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><i class="fas fa-image me-2"></i>Logo Management</h1>
    <p>Upload and manage app logos (SVG, PNG, JPG, WebP - max 2MB)</p>
</div>

<div class="row g-4">
    <?php foreach ($logos as $logo): ?>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-image me-2"></i><?= esc($logo['label']) ?>
            </div>
            <div class="card-body text-center">
                <?php if ($logo['file_path']): ?>
                    <div style="background: #003049; border-radius: 12px; padding: 20px; margin-bottom: 12px;">
                        <img src="<?= esc($logo['file_path']) ?>?v=<?= strtotime($logo['updated_at']) ?>"
                            style="max-width: 100%; max-height: 150px; object-fit: contain;" alt="<?= esc($logo['logo_key']) ?>">
                    </div>
                    <p class="small text-muted mb-2"><?= esc(basename($logo['file_path'])) ?></p>
                    <form action="/admin/logos/delete/<?= esc($logo['logo_key']) ?>" method="post" class="d-inline">
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this logo?')">
                            <i class="fas fa-trash me-1"></i>Remove
                        </button>
                    </form>
                <?php else: ?>
                    <div style="background: #f0f0f0; border-radius: 12px; padding: 40px; margin-bottom: 12px;">
                        <i class="fas fa-image fa-3x text-muted"></i>
                        <p class="text-muted mt-2 mb-0">No logo uploaded</p>
                    </div>
                <?php endif; ?>

                <hr>
                <form action="/admin/logos/upload/<?= esc($logo['logo_key']) ?>" method="post" enctype="multipart/form-data">
                    <div class="input-group">
                        <input type="file" name="logo_file" class="form-control form-control-sm" accept=".svg,.png,.jpg,.jpeg,.webp" required>
                        <button type="submit" class="btn btn-sm btn-green">
                            <i class="fas fa-upload"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?= $this->endSection() ?>
