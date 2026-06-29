<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><i class="fas fa-file-contract me-2"></i>Legal Pages</h1>
    <p>Manage Terms & Conditions and Privacy Policy for each language</p>
</div>

<?php
$types = [
    'tcs' => ['label' => 'Terms & Conditions', 'icon' => 'fa-file-contract', 'color' => 'blue'],
    'pp'  => ['label' => 'Privacy Policy', 'icon' => 'fa-shield-alt', 'color' => 'green'],
];
?>

<div class="row g-4">
<?php foreach ($types as $type => $info): ?>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="fas <?= $info['icon'] ?> me-2"></i><?= $info['label'] ?>
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
                                <?php if (isset($grouped[$type][$lang['code']])): ?>
                                    <span class="badge badge-active">Done</span>
                                <?php else: ?>
                                    <span class="badge badge-missed">Missing</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted">
                                <?php if (isset($grouped[$type][$lang['code']])): ?>
                                    <?= date('M j, Y', strtotime($grouped[$type][$lang['code']]['updated_at'])) ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/admin/legal/edit/<?= $type ?>/<?= $lang['code'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?= $this->endSection() ?>
