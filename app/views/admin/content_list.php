<div class="admin-head">
    <div>
        <span class="kicker">Content</span>
        <h1><?= Security::e($section['label']) ?></h1>
    </div>
    <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/admin/content/<?= Security::e($sectionKey) ?>/new">+ Add <?= Security::e($section['label']) ?></a>
</div>

<?php if (empty($items)): ?>
    <div class="admin-empty">
        <svg class="icon"><use href="#<?= Security::e($section['icon']) ?>"/></svg>
        <h3>No <?= Security::e(strtolower($section['plural'])) ?> yet</h3>
        <p>Add your first item and it will appear on the website right away.</p>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <?php foreach ($section['listColumns'] as $col): ?>
                        <th><?= Security::e($col['label']) ?></th>
                    <?php endforeach; ?>
                    <th class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 0; foreach ($items as $item): ?>
                    <?php $itemId = (int) ($item['id'] ?? 0); ?>
                    <tr>
                        <td><?= ++$i ?></td>
                        <?php foreach ($section['listColumns'] as $col): ?>
                            <?php
                            $value = $item[$col['key']] ?? '';
                            $colType = $col['type'] ?? 'text';
                            ?>
                            <td>
                                <?php if ($colType === 'image' && $value !== ''): ?>
                                    <img class="thumb" src="<?= Security::e($value) ?>" alt="" loading="lazy">
                                <?php elseif ($colType === 'rating'): ?>
                                    <span class="row-rating"><?= (int) $value ?> <span class="stars" aria-hidden="true">★</span></span>
                                <?php elseif ($colType === 'bool'): ?>
                                    <span class="badge <?= $value ? 'badge-confirmed' : 'badge-completed' ?>"><?= $value ? 'Yes' : 'No' ?></span>
                                <?php else: ?>
                                    <?= Security::e((string) $value) ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        <td class="col-actions">
                            <a class="btn btn-outline btn-xs" href="<?= BASE_URL ?>/admin/content/<?= Security::e($sectionKey) ?>/edit?id=<?= $itemId ?>">Edit</a>
                            <form action="<?= BASE_URL ?>/admin/content/<?= Security::e($sectionKey) ?>/delete" method="post" class="row-form delete-form">
                                <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
                                <input type="hidden" name="id" value="<?= $itemId ?>">
                                <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
