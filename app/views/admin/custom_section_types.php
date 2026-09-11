<div class="admin-head">
    <div>
        <span class="kicker"><?= Security::e($pageLabel) ?> Page</span>
        <h1>Add Section — choose the type</h1>
        <p class="admin-head-note">What kind of section do you want to add? Each type has its own layout and fields on the website.</p>
    </div>
    <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/admin/pages/<?= Security::e($pageKey) ?>">&larr; Back to sections</a>
</div>

<div class="content-grid">
    <?php foreach ($types as $typeKey => $type): ?>
        <a class="content-card" href="<?= BASE_URL ?>/admin/pages/<?= Security::e($pageKey) ?>/custom-new?type=<?= Security::e($typeKey) ?>">
            <span class="content-card-icon"><svg class="icon"><use href="#<?= Security::e($type['icon']) ?>"/></svg></span>
            <div>
                <h3><?= Security::e($type['label']) ?></h3>
                <p><?= Security::e($type['hint']) ?></p>
            </div>
            <svg class="icon content-card-arrow"><use href="#icon-arrow"/></svg>
        </a>
    <?php endforeach; ?>
</div>
