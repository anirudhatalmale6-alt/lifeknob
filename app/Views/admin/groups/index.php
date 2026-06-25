<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Family Groups</h1>
    <p>All registered family groups and their members</p>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($groups)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-user-friends fa-3x mb-3" style="opacity: 0.15;"></i>
                <p class="mb-0 fw-medium">No family groups yet</p>
                <small>Groups are created when users set up their family connections</small>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Group Name</th>
                            <th>Invite Code</th>
                            <th>Members</th>
                            <th>Elders</th>
                            <th>Created By</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($groups as $group): ?>
                            <tr>
                                <td class="text-muted">#<?= esc($group->id) ?></td>
                                <td class="fw-medium">
                                    <i class="fas fa-users text-muted me-1"></i>
                                    <?= esc($group->name) ?>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded"><?= esc($group->invite_code ?? '-') ?></code>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-user me-1"></i><?= esc($group->members_count ?? 0) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-elder">
                                        <i class="fas fa-user-clock me-1"></i><?= esc($group->elders_count ?? 0) ?>
                                    </span>
                                </td>
                                <td><?= esc($group->creator_name ?? 'Unknown') ?></td>
                                <td><span data-date="<?= esc($group->created_at ?? '') ?>"></span></td>
                                <td>
                                    <a href="/admin/groups/<?= esc($group->id) ?>" class="btn btn-sm btn-outline-primary" title="View Details">
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

    <?php if (!empty($pager)): ?>
        <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing <?= count($groups) ?> group<?= count($groups) !== 1 ? 's' : '' ?>
            </small>
            <?= $pager->links('default', 'default_full') ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
