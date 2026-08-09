<div class="admin-head">
    <div>
        <span class="kicker">Admin Panel</span>
        <h1>Website Content</h1>
        <p class="admin-head-note">Manage the content shown across the public website. Changes are saved straight to the MySQL database.</p>
    </div>
    <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/admin/appointments">Appointment Bookings</a>
</div>

<div class="content-grid">
    <a class="content-card content-card-accent" href="<?= BASE_URL ?>/admin/pages">
        <span class="content-card-icon"><svg class="icon"><use href="#icon-layout"/></svg></span>
        <div>
            <h3>Edit Pages</h3>
            <p>Edit every page section by section</p>
        </div>
        <svg class="icon content-card-arrow"><use href="#icon-arrow"/></svg>
    </a>
    <?php foreach ($sections as $key => $s): ?>
        <a class="content-card" href="<?= BASE_URL ?>/admin/content/<?= Security::e($key) ?>">
            <span class="content-card-icon"><svg class="icon"><use href="#<?= Security::e($s['icon']) ?>"/></svg></span>
            <div>
                <h3><?= Security::e($s['label']) ?></h3>
                <p><?= (int) $s['count'] ?> item<?= (int) $s['count'] === 1 ? '' : 's' ?></p>
            </div>
            <svg class="icon content-card-arrow"><use href="#icon-arrow"/></svg>
        </a>
    <?php endforeach; ?>
</div>
