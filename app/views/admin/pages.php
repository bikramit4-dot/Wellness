<div class="admin-head">
    <div>
        <span class="kicker">Admin Panel</span>
        <h1>Edit Pages</h1>
        <p class="admin-head-note">Every public page is listed here with its sections separated. Open a page, then edit each section individually — headings, text, photos, buttons and more.</p>
    </div>
    <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/">View Site</a>
</div>

<div class="content-grid">
    <?php foreach ($pages as $key => $p): ?>
        <a class="content-card" href="<?= BASE_URL ?>/admin/pages/<?= Security::e($key) ?>">
            <span class="content-card-icon"><svg class="icon"><use href="#icon-layout"/></svg></span>
            <div>
                <h3><?= Security::e($p['label']) ?></h3>
                <p><?= (int) $p['sectionCount'] ?> section<?= (int) $p['sectionCount'] === 1 ? '' : 's' ?></p>
            </div>
            <svg class="icon content-card-arrow"><use href="#icon-arrow"/></svg>
        </a>
    <?php endforeach; ?>
</div>
