<div class="admin-head">
    <div>
        <span class="kicker">Edit Pages</span>
        <h1><?= Security::e($page['label']) ?> Page Sections</h1>
        <p class="admin-head-note">Every section of the <a href="<?= BASE_URL ?><?= Security::e($page['url']) ?>" target="_blank" rel="noopener"><?= Security::e($page['label']) ?> page</a> is listed here. Click <strong>Edit</strong> to change section settings, or use <strong>Manage items</strong> to add, edit, or delete individual content.</p>
    </div>
    <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/admin/pages">&larr; Back to Edit Pages</a>
</div>

<?php $customs = $customs ?? []; $hasCustoms = $customs !== []; ?>
<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Section</th>
                <th>Heading</th>
                <th>Preview</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0; foreach ($page['sections'] as $sectionKey => $section): ?>
                <?php $prev = $previews[$sectionKey] ?? []; ?>
                <tr>
                    <td><?= ++$i ?></td>
                    <td><strong><?= Security::e($section['label']) ?></strong></td>
                    <td>
                        <?php if (($prev['heading'] ?? '') !== ''): ?><?= Security::e($prev['heading']) ?>
                        <?php elseif (($prev['kicker'] ?? '') !== ''): ?><span class="muted"><?= Security::e($prev['kicker']) ?></span>
                        <?php else: ?><span class="muted">—</span><?php endif; ?>
                    </td>
                    <td>
                        <?php if (($prev['content'] ?? '') !== ''): ?>
                            <?= Security::e(mb_strimwidth((string) $prev['content'], 0, 90, '…')) ?>
                        <?php elseif (($prev['image'] ?? '') !== ''): ?>
                            <img class="thumb" src="<?= Security::e($prev['image']) ?>" alt="" loading="lazy">
                        <?php else: ?><span class="muted">—</span><?php endif; ?>
                    </td>
                    <td class="col-actions">
                        <a class="btn btn-outline btn-xs" href="<?= BASE_URL ?>/admin/pages/<?= Security::e($pageKey) ?>/<?= Security::e($sectionKey) ?>/edit">Edit</a>
                        <?php if ($pageKey === 'about' && in_array($sectionKey, ['founder', 'approach', 'doctors', 'vision'], true)): ?>
                            <a class="btn btn-outline btn-xs" href="<?= BASE_URL ?>/admin/pages/about/<?= Security::e($sectionKey) ?>/items">Manage items</a>
                        <?php elseif ($pageKey === 'about' && $sectionKey === 'group'): ?>
                            <a class="btn btn-outline btn-xs" href="<?= BASE_URL ?>/admin/content/team">Manage people</a>
                        <?php endif; ?>
                        <form action="<?= BASE_URL ?>/admin/pages/<?= Security::e($pageKey) ?>/<?= Security::e($sectionKey) ?>/reset" method="post" class="row-form">
                            <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
                            <button type="submit" class="btn btn-outline btn-xs">Reset</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php foreach ($customs as $custom): ?>
                <tr>
                    <td><?= ++$i ?></td>
                    <td>
                        <strong><?= $custom['kicker'] !== '' ? Security::e($custom['kicker']) : 'Custom section' ?></strong>
                        <span class="badge badge-confirmed" style="margin-left:.4rem;">Added by you · <?= Security::e($custom['typeLabel']) ?></span>
                    </td>
                    <td>
                        <strong><?= Security::e($custom['heading']) ?></strong>
                    </td>
                    <td>
                        <?php if ($custom['content'] !== ''): ?><?= Security::e(mb_strimwidth($custom['content'], 0, 90, '…')) ?> · <?php endif; ?>
                        <?= (int) $custom['boxCount'] ?> box<?= (int) $custom['boxCount'] === 1 ? '' : 'es' ?>
                    </td>
                    <td class="col-actions">
                        <a class="btn btn-outline btn-xs" href="<?= BASE_URL ?>/admin/pages/<?= Security::e($pageKey) ?>/custom-<?= (int) $custom['id'] ?>/edit">Edit</a>
                        <form action="<?= BASE_URL ?>/admin/pages/<?= Security::e($pageKey) ?>/custom-<?= (int) $custom['id'] ?>/delete" method="post" class="row-form delete-form" onsubmit="return confirm('Delete this section permanently? It will also disappear from the live page.');">
                            <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
                            <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php if ($hasCustoms): ?>
    <p class="admin-head-note" style="margin-top:.6rem;">Sections marked <span class="badge badge-confirmed">Added by you</span> were created from this panel and can be deleted. Built-in sections can be edited or reset to their default content.</p>
<?php endif; ?>
