<?php
$adminName = Security::e($_SESSION['admin_username'] ?? 'Admin');
$todayLabel = date('l, F j, Y');
$newCount = (int) $counts['new'];
$delta = (int) $weekDelta;
$maxTreat = $popularTreatments !== [] ? max($popularTreatments) : 1;
?>

<!-- ============ Welcome hero ============ -->
<div class="dash-hero">
    <div class="dash-hero-deco" aria-hidden="true"><svg class="icon"><use href="#icon-leaf"/></svg></div>
    <div class="dash-hero-content">
        <span class="dash-hero-kicker">Admin Panel</span>
        <h1>Welcome back, <?= $adminName ?></h1>
        <p class="dash-hero-meta">
            <svg class="icon"><use href="#icon-calendar"/></svg>
            <?= $todayLabel ?>
            &nbsp;&middot;&nbsp;
            <span class="dash-hero-new"><?= $newCount ?> new booking<?= $newCount === 1 ? '' : 's' ?></span>
            waiting for review
        </p>
    </div>
    <div class="dash-hero-actions">
        <a class="btn btn-light btn-sm" href="<?= BASE_URL ?>/admin/appointments">All Appointments</a>
        <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/contact">New Booking Form</a>
        <a class="btn dash-hero-ghost btn-sm" href="<?= BASE_URL ?>/">View Site</a>
    </div>
</div>

<!-- ============ Status cards ============ -->
<div class="admin-stats">
    <div class="admin-stat stat-total">
        <span class="stat-icon"><svg class="icon"><use href="#icon-calendar"/></svg></span>
        <div class="stat-body">
            <strong><?= (int) $counts['total'] ?></strong>
            <span>Total Appointments</span>
        </div>
        <span class="stat-trend <?= $delta >= 0 ? 'up' : 'down' ?>">
            <?= $delta >= 0 ? '&#8593;' : '&#8595;' ?> <?= abs($delta) ?> vs prev. week
        </span>
    </div>
    <div class="admin-stat stat-new">
        <span class="stat-icon"><svg class="icon"><use href="#icon-zap"/></svg></span>
        <div class="stat-body">
            <strong><?= (int) $counts['new'] ?></strong>
            <span>New</span>
        </div>
        <span class="stat-trend">needs review</span>
    </div>
    <div class="admin-stat stat-confirmed">
        <span class="stat-icon"><svg class="icon"><use href="#icon-check"/></svg></span>
        <div class="stat-body">
            <strong><?= (int) $counts['confirmed'] ?></strong>
            <span>Confirmed</span>
        </div>
        <span class="stat-trend">booked</span>
    </div>
    <div class="admin-stat stat-completed">
        <span class="stat-icon"><svg class="icon"><use href="#icon-clock"/></svg></span>
        <div class="stat-body">
            <strong><?= (int) $counts['completed'] ?></strong>
            <span>Completed</span>
        </div>
        <span class="stat-trend">attended</span>
    </div>
</div>

<div class="dash-grid">
    <!-- ============ Recent appointments ============ -->
    <section class="dash-panel">
        <div class="dash-panel-head">
            <h2>Recent Appointments</h2>
            <a href="<?= BASE_URL ?>/admin/appointments">View all &rarr;</a>
        </div>
        <?php if (empty($recentAppointments)): ?>
            <p class="dash-empty">No bookings yet. New bookings made through the contact page will appear here.</p>
        <?php else: ?>
            <ul class="dash-recent">
                <?php foreach ($recentAppointments as $i => $a): ?>
                    <li>
                        <span class="avatar a<?= 1 + ($i % 6) ?>"><?= Security::e(strtoupper(substr((string) ($a['name'] ?? ' '), 0, 2))) ?></span>
                        <div class="dash-recent-info">
                            <strong><?= Security::e($a['name'] ?? '') ?></strong>
                            <span><?= Security::e(trim((string) ($a['treatment'] ?? '')) ?: 'No treatment') ?> &middot; <?= Security::e(date('M j, g:i A', strtotime($a['created_at'] ?? 'now'))) ?></span>
                        </div>
                        <span class="badge badge-<?= Security::e($a['status'] ?? 'new') ?>"><i class="badge-dot"></i><?= Security::e(ucfirst($a['status'] ?? 'new')) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <div class="dash-side">
        <!-- ============ Activity ============ -->
        <section class="dash-panel">
            <div class="dash-panel-head">
                <h2>Last 7 Days</h2>
                <span class="dash-week"><strong><?= (int) $week ?></strong> bookings</span>
            </div>
            <?php if ((int) $week === 0): ?>
                <p class="dash-empty">No bookings in the last 7 days.</p>
            <?php else: ?>
                <div class="dash-bars">
                    <?php foreach ($activity as $d): ?>
                        <div class="dash-bar">
                            <span class="dash-bar-val"><?= (int) $d['count'] ?></span>
                            <div class="dash-bar-track" title="<?= Security::e($d['label']) ?>: <?= (int) $d['count'] ?> booking<?= (int) $d['count'] === 1 ? '' : 's' ?>">
                                <i style="height: <?= (int) $d['pct'] ?>%"></i>
                            </div>
                            <span class="dash-bar-label"><?= Security::e($d['label']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="dash-week-delta <?= $delta >= 0 ? 'up' : 'down' ?>">
                    <?= $delta >= 0 ? '&#8593;' : '&#8595;' ?> <?= abs($delta) ?> booking<?= abs($delta) === 1 ? '' : 's' ?> compared with the previous week
                </p>
            <?php endif; ?>
        </section>

        <!-- ============ Popular treatments ============ -->
        <section class="dash-panel">
            <div class="dash-panel-head"><h2>Popular Treatments</h2></div>
            <?php if (empty($popularTreatments)): ?>
                <p class="dash-empty">No treatment data yet.</p>
            <?php else: ?>
                <ul class="dash-treat">
                    <?php foreach ($popularTreatments as $treatment => $n): ?>
                        <li>
                            <div class="dash-treat-top">
                                <span><?= Security::e($treatment) ?></span>
                                <strong><?= (int) $n ?></strong>
                            </div>
                            <div class="dash-treat-track"><i style="width: <?= (int) round($n / $maxTreat * 100) ?>%"></i></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </div>
</div>

<!-- ============ Quick actions ============ -->
<div class="dash-quick">
    <a class="quick-link" href="<?= BASE_URL ?>/contact"><span class="quick-link-icon"><svg class="icon"><use href="#icon-mail"/></svg></span> New Booking</a>
    <a class="quick-link" href="<?= BASE_URL ?>/admin/appointments"><span class="quick-link-icon"><svg class="icon"><use href="#icon-calendar"/></svg></span> Appointments</a>
    <a class="quick-link" href="<?= BASE_URL ?>/admin/content"><span class="quick-link-icon"><svg class="icon"><use href="#icon-users"/></svg></span> Manage Content</a>
    <a class="quick-link" href="<?= BASE_URL ?>/"><span class="quick-link-icon"><svg class="icon"><use href="#icon-leaf"/></svg></span> View Website</a>
</div>

<!-- ============ Manage content ============ -->
<section class="dash-panel">
    <div class="dash-panel-head">
        <h2>Manage Content</h2>
        <a href="<?= BASE_URL ?>/admin/content">Open content manager &rarr;</a>
    </div>
    <div class="content-grid">
        <?php foreach ($sections as $key => $s): ?>
            <a class="content-card" href="<?= BASE_URL ?>/admin/content/<?= Security::e($key) ?>">
                <span class="content-card-icon"><svg class="icon"><use href="#<?= Security::e($s['icon']) ?>"/></svg></span>
                <div class="content-card-body">
                    <h3><?= Security::e($s['label']) ?></h3>
                    <p><?= (int) $s['count'] ?> item<?= (int) $s['count'] === 1 ? '' : 's' ?></p>
                </div>
                <span class="content-card-count"><?= (int) $s['count'] ?></span>
                <svg class="icon content-card-arrow"><use href="#icon-arrow"/></svg>
            </a>
        <?php endforeach; ?>
    </div>
</section>
