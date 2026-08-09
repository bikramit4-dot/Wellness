<div class="admin-head">
    <div>
        <span class="kicker">Edit Pages</span>
        <h1><?= Security::e($page['label']) ?></h1>
        <p class="admin-head-note">Each section below is edited separately. Changes save straight to the database and appear on <a href="<?= BASE_URL ?><?= Security::e($page['url']) ?>">the live page</a>.</p>
    </div>
    <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/admin/pages">&larr; All pages</a>
</div>

<div class="section-grid">
    <?php foreach ($page['sections'] as $sectionKey => $section): ?>
        <?php $prev = $previews[$sectionKey] ?? []; ?>
        <article class="section-card">
            <div class="section-card-body">
                <span class="kicker"><?= Security::e($section['label']) ?></span>
                <?php if (!empty($prev['heading'])): ?>
                    <h3><?= Security::e($prev['heading']) ?></h3>
                <?php endif; ?>
                <?php if (!empty($prev['kicker'])): ?>
                    <p class="section-preview-kicker"><?= Security::e($prev['kicker']) ?></p>
                <?php endif; ?>
                <?php if (!empty($prev['content'])): ?>
                    <p class="section-preview-text"><?= Security::e(mb_strimwidth($prev['content'], 0, 140, '…')) ?></p>
                <?php endif; ?>
                <?php if (!empty($prev['image'])): ?>
                    <img class="thumb" src="<?= Security::e($prev['image']) ?>" alt="" loading="lazy">
                <?php endif; ?>
            </div>
            <div class="section-card-actions">
                <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/admin/pages/<?= Security::e($pageKey) ?>/<?= Security::e($sectionKey) ?>/edit">Edit Section</a>
                <form action="<?= BASE_URL ?>/admin/pages/<?= Security::e($pageKey) ?>/<?= Security::e($sectionKey) ?>/reset" method="post" class="row-form">
                    <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
                    <button type="submit" class="btn btn-outline btn-xs">Reset to default</button>
                </form>
            </div>
        </article>
    <?php endforeach; ?>
</div>
