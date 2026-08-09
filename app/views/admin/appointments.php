<div class="admin-head">
    <div>
        <span class="kicker">Admin Panel</span>
        <h1>Appointment Bookings</h1>
    </div>
    <div class="admin-head-actions">
        <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/admin">Dashboard</a>
        <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/contact">New Booking Form</a>
    </div>
</div>

<!-- Stats -->
<div class="admin-stats">
    <div class="admin-stat">
        <span class="stat-icon"><svg class="icon"><use href="#icon-calendar"/></svg></span>
        <div class="stat-body">
            <strong><?= (int) $counts['total'] ?></strong>
            <span>Total</span>
        </div>
    </div>
    <div class="admin-stat stat-new">
        <span class="stat-icon"><svg class="icon"><use href="#icon-zap"/></svg></span>
        <div class="stat-body">
            <strong><?= (int) $counts['new'] ?></strong>
            <span>New</span>
        </div>
    </div>
    <div class="admin-stat stat-confirmed">
        <span class="stat-icon"><svg class="icon"><use href="#icon-check"/></svg></span>
        <div class="stat-body">
            <strong><?= (int) $counts['confirmed'] ?></strong>
            <span>Confirmed</span>
        </div>
    </div>
    <div class="admin-stat stat-completed">
        <span class="stat-icon"><svg class="icon"><use href="#icon-clock"/></svg></span>
        <div class="stat-body">
            <strong><?= (int) $counts['completed'] ?></strong>
            <span>Completed</span>
        </div>
    </div>
</div>

<!-- Filter tabs -->
<div class="admin-tabs">
    <a class="admin-tab <?= $filter === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/appointments">All</a>
    <a class="admin-tab <?= $filter === 'new' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/appointments?status=new">New</a>
    <a class="admin-tab <?= $filter === 'confirmed' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/appointments?status=confirmed">Confirmed</a>
    <a class="admin-tab <?= $filter === 'completed' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/appointments?status=completed">Completed</a>
</div>

<?php if (empty($appointments)): ?>
    <div class="admin-empty">
        <svg class="icon"><use href="#icon-calendar"/></svg>
        <h3><?= $filter === '' ? 'No appointments yet' : 'No ' . Security::e($filter) . ' appointments' ?></h3>
        <p>New bookings made through the contact page will appear here.</p>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Contact</th>
                    <th>Treatment</th>
                    <th>Preferred</th>
                    <th>Status</th>
                    <th>Received</th>
                    <th class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $a): ?>
                    <?php $aId = Security::e($a['id'] ?? ''); ?>
                    <?php $aDate = strtotime($a['date'] ?? ''); ?>
                    <?php $aMsg = (string) ($a['message'] ?? ''); ?>
                    <tr>
                        <td>
                            <strong><?= Security::e($a['name'] ?? '') ?></strong>
                            <?php if ($aMsg !== ''): ?>
                                <span class="row-note" title="<?= Security::e($aMsg) ?>"><?= Security::e(strlen($aMsg) > 60 ? substr($aMsg, 0, 57) . '…' : $aMsg) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="mailto:<?= Security::e($a['email'] ?? '') ?>"><?= Security::e($a['email'] ?? '') ?></a>
                            <?php if (!empty($a['phone'])): ?>
                                <span class="row-note"><?= Security::e($a['phone']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= Security::e($a['treatment'] ?? '') ?></td>
                        <td>
                            <?php if (!empty($a['date'])): ?>
                                <?= $aDate ? Security::e(date('M j, Y', $aDate)) : Security::e($a['date']) ?>
                                <?php if (!empty($a['time'])): ?><span class="row-note"><?= Security::e($a['time']) ?></span><?php endif; ?>
                            <?php else: ?>
                                <span class="row-note">—</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge badge-<?= Security::e($a['status'] ?? 'new') ?>"><?= Security::e(ucfirst($a['status'] ?? 'new')) ?></span></td>
                        <td><span class="row-note"><?= Security::e(date('M j, Y g:i A', strtotime($a['created_at'] ?? 'now'))) ?></span></td>
                        <td class="col-actions">
                            <form action="<?= BASE_URL ?>/admin/status" method="post" class="row-form">
                                <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
                                <input type="hidden" name="id" value="<?= $aId ?>">
                                <?php if ($filter !== ''): ?><input type="hidden" name="filter" value="<?= Security::e($filter) ?>"><?php endif; ?>
                                <select name="status" class="status-select" aria-label="Change status">
                                    <?php foreach (['new', 'confirmed', 'completed'] as $s): ?>
                                        <option value="<?= $s ?>" <?= ($a['status'] ?? 'new') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-dark btn-xs" title="Save status">Save</button>
                            </form>
                            <form action="<?= BASE_URL ?>/admin/delete" method="post" class="row-form delete-form">
                                <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
                                <input type="hidden" name="id" value="<?= $aId ?>">
                                <?php if ($filter !== ''): ?><input type="hidden" name="filter" value="<?= Security::e($filter) ?>"><?php endif; ?>
                                <button type="submit" class="btn btn-danger btn-xs" title="Delete appointment">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
