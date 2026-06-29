<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><i class="fas fa-file-contract me-2"></i>Terms & Conditions</h1>
    <p>Manage Terms & Conditions (including Privacy Policy) for each language</p>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-file-contract me-2"></i>Terms & Conditions per Language
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Language</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($languages as $lang): ?>
                <tr>
                    <td>
                        <strong><?= esc($lang['name']) ?></strong>
                        <code class="ms-1 small"><?= esc($lang['code']) ?></code>
                    </td>
                    <td>
                        <?php if (isset($grouped['tcs'][$lang['code']])): ?>
                            <span class="badge badge-active">Done</span>
                        <?php else: ?>
                            <span class="badge badge-missed">Missing</span>
                        <?php endif; ?>
                    </td>
                    <td class="small text-muted">
                        <?php if (isset($grouped['tcs'][$lang['code']])): ?>
                            <?= date('M j, Y', strtotime($grouped['tcs'][$lang['code']]['updated_at'])) ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="/admin/legal/edit/tcs/<?= $lang['code'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
