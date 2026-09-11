<div class="admin-head">
    <div>
        <span class="kicker">About page</span>
        <h1><?= Security::e($definition['label']) ?>s</h1>
        <p class="admin-head-note">Add, edit, or remove each <?= Security::e(strtolower($definition['label'])) ?> independently.</p>
    </div>
    <div class="button-row">
        <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/admin/pages/about/<?= Security::e($sectionKey) ?>/item-new">+ Add <?= Security::e($definition['label']) ?></a>
        <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/admin/pages/about">&larr; About sections</a>
    </div>
</div>

<?php if ($items === []): ?>
    <div class="admin-empty">
        <h3>No <?= Security::e(strtolower($definition['label'])) ?>s yet</h3>
        <p>Add the first one and it will appear on the About page.</p>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Photo</th>
                    <th>Name / Title</th>
                    <th>Details</th>
                    <th class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $index => $item): ?>
                    <?php $title = $item['name'] ?? $item['title'] ?? ''; $detail = $item['role'] ?? $item['description'] ?? $item['text'] ?? ''; ?>
                    <tr>
                        <td><?= (int) $index + 1 ?></td>
                        <td><?php if (($item['image'] ?? '') !== ''): ?><img class="thumb" src="<?= Security::e($item['image']) ?>" alt="" loading="lazy"><?php else: ?><span class="muted">No photo</span><?php endif; ?></td>
                        <td><strong><?= Security::e($title) ?></strong></td>
                        <td><?= Security::e(mb_strimwidth((string) $detail, 0, 100, '…')) ?></td>
                        <td class="col-actions">
                            <a class="btn btn-outline btn-xs" href="<?= BASE_URL ?>/admin/pages/about/<?= Security::e($sectionKey) ?>/item-edit?index=<?= (int) $index ?>">Edit</a>
                            <form action="<?= BASE_URL ?>/admin/pages/about/<?= Security::e($sectionKey) ?>/item-delete" method="post" class="row-form delete-form">
                                <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
                                <input type="hidden" name="index" value="<?= (int) $index ?>">
                                <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
