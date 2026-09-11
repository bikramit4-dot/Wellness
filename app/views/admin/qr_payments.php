<div class="qr-payments-page">
<div class="admin-head">
    <div>
        <span class="kicker">Admin Panel</span>
        <h1>QR Advance Payments</h1>
    </div>
    <div class="admin-head-actions">
        <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/admin">Dashboard</a>
        <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/tariff#qr-payment">Public Payment Form</a>
    </div>
</div>

<!-- Stats -->
<div class="admin-stats">
    <div class="admin-stat">
        <span class="stat-icon"><svg class="icon"><use href="#icon-zap"/></svg></span>
        <div class="stat-body">
            <strong><?= (int) $counts['total'] ?></strong>
            <span>Total</span>
        </div>
    </div>
    <div class="admin-stat stat-new">
        <span class="stat-icon"><svg class="icon"><use href="#icon-mail"/></svg></span>
        <div class="stat-body">
            <strong><?= (int) $counts['new'] ?></strong>
            <span>New</span>
        </div>
    </div>
    <div class="admin-stat stat-confirmed">
        <span class="stat-icon"><svg class="icon"><use href="#icon-check"/></svg></span>
        <div class="stat-body">
            <strong><?= (int) $counts['verified'] ?></strong>
            <span>Verified</span>
        </div>
    </div>
    <div class="admin-stat stat-completed">
        <span class="stat-icon"><svg class="icon"><use href="#icon-close"/></svg></span>
        <div class="stat-body">
            <strong><?= (int) $counts['rejected'] ?></strong>
            <span>Rejected</span>
        </div>
    </div>
</div>

<!-- Filter tabs -->
<div class="admin-tabs">
    <a class="admin-tab <?= $filter === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/qr-payments">All</a>
    <a class="admin-tab <?= $filter === 'new' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/qr-payments?status=new">New</a>
    <a class="admin-tab <?= $filter === 'verified' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/qr-payments?status=verified">Verified</a>
    <a class="admin-tab <?= $filter === 'rejected' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/qr-payments?status=rejected">Rejected</a>
</div>

<?php if (empty($payments)): ?>
    <div class="admin-empty">
        <svg class="icon"><use href="#icon-zap"/></svg>
        <h3><?= $filter === '' ? 'No advance payments yet' : 'No ' . Security::e($filter) . ' payments' ?></h3>
        <p>New QR-code advance payments made through the tariff page will appear here with the customer's screenshot.</p>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Screenshot</th>
                    <th>Name</th>
                    <th>Package</th>
                    <th>Contact / Address</th>
                    <th>Amount</th>
                    <th>Transaction</th>
                    <th>Status</th>
                    <th>Received</th>
                    <th class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $p): ?>
                    <?php $pId = Security::e($p['id'] ?? ''); ?>
                    <?php $pShot = (string) ($p['screenshot'] ?? ''); ?>
                    <?php $pMsg = (string) ($p['message'] ?? ''); ?>
                    <tr>
                        <td data-label="Screenshot">
                            <?php if ($pShot !== ''): ?>
                                <a class="qr-thumb" href="<?= Security::e($pShot) ?>" target="_blank" rel="noopener" title="Open payment screenshot">
                                    <img src="<?= Security::e($pShot) ?>" alt="Payment screenshot" loading="lazy">
                                </a>
                            <?php else: ?>
                                <span class="row-note">—</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Name">
                            <strong><?= Security::e($p['name'] ?? '') ?></strong>
                            <?php if ($pMsg !== ''): ?>
                                <span class="row-note" title="<?= Security::e($pMsg) ?>"><?= Security::e(strlen($pMsg) > 60 ? substr($pMsg, 0, 57) . '…' : $pMsg) ?></span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Package"><strong><?= Security::e($p['package'] ?? '') ?: '—' ?></strong></td>
                        <td data-label="Contact / Address">
                            <a href="mailto:<?= Security::e($p['email'] ?? '') ?>"><?= Security::e($p['email'] ?? '') ?></a>
                            <?php if (!empty($p['phone'])): ?>
                                <span class="row-note"><?= Security::e($p['phone']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($p['address'])): ?>
                                <span class="row-note"><?= Security::e($p['address']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Amount"><strong><?= Security::e($p['amount'] ?? '') ?></strong></td>
                        <td data-label="Transaction"><?= Security::e($p['transaction_id'] ?? '') ?></td>
                        <td data-label="Status"><span class="badge badge-<?= Security::e($p['status'] ?? 'new') ?>"><?= Security::e(ucfirst($p['status'] ?? 'new')) ?></span></td>
                        <td data-label="Received"><span class="row-note"><?= Security::e(date('M j, Y g:i A', strtotime($p['created_at'] ?? 'now'))) ?></span></td>
                        <td data-label="Actions" class="col-actions">
                            <form action="<?= BASE_URL ?>/admin/qr-status" method="post" class="row-form">
                                <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
                                <input type="hidden" name="id" value="<?= $pId ?>">
                                <?php if ($filter !== ''): ?><input type="hidden" name="filter" value="<?= Security::e($filter) ?>"><?php endif; ?>
                                <select name="status" class="status-select" aria-label="Change status">
                                    <?php foreach (['new', 'verified', 'rejected'] as $s): ?>
                                        <option value="<?= $s ?>" <?= ($p['status'] ?? 'new') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-dark btn-xs" title="Save status">Save</button>
                            </form>
                            <form action="<?= BASE_URL ?>/admin/qr-delete" method="post" class="row-form delete-form">
                                <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
                                <input type="hidden" name="id" value="<?= $pId ?>">
                                <?php if ($filter !== ''): ?><input type="hidden" name="filter" value="<?= Security::e($filter) ?>"><?php endif; ?>
                                <button type="submit" class="btn btn-danger btn-xs" title="Delete payment record">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
</div>
