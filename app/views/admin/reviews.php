<div class="admin-head">
    <div>
        <span class="kicker">Admin Panel</span>
        <h1>Customer Reviews</h1>
    </div>
    <div class="admin-head-actions">
        <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/admin">Dashboard</a>
        <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/#reviews">Public Reviews</a>
    </div>
</div>

<!-- Stats -->
<div class="admin-stats">
    <div class="admin-stat">
        <span class="stat-icon"><svg class="icon"><use href="#icon-star"/></svg></span>
        <div class="stat-body">
            <strong><?= (int) $counts['total'] ?></strong>
            <span>Total</span>
        </div>
    </div>
    <div class="admin-stat stat-new">
        <span class="stat-icon"><svg class="icon"><use href="#icon-mail"/></svg></span>
        <div class="stat-body">
            <strong><?= (int) $counts['pending'] ?></strong>
            <span>Pending</span>
        </div>
    </div>
    <div class="admin-stat stat-confirmed">
        <span class="stat-icon"><svg class="icon"><use href="#icon-check"/></svg></span>
        <div class="stat-body">
            <strong><?= (int) $counts['approved'] ?></strong>
            <span>Approved</span>
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
    <a class="admin-tab <?= $filter === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/reviews">All</a>
    <a class="admin-tab <?= $filter === 'pending' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/reviews?status=pending">Pending <?= $counts['pending'] > 0 ? '<span class="tab-badge">' . $counts['pending'] . '</span>' : '' ?></a>
    <a class="admin-tab <?= $filter === 'approved' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/reviews?status=approved">Approved</a>
    <a class="admin-tab <?= $filter === 'rejected' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/reviews?status=rejected">Rejected</a>
</div>

<?php if (empty($reviews)): ?>
    <div class="admin-empty">
        <svg class="icon"><use href="#icon-star"/></svg>
        <h3><?= $filter === '' ? 'No reviews yet' : 'No ' . Security::e($filter) . ' reviews' ?></h3>
        <p>Customer reviews submitted through the home page will appear here for your approval.</p>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Rating</th>
                    <th>Reviewer</th>
                    <th>Review</th>
                    <th>Status</th>
                    <th class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $r): ?>
                    <?php $rId = (int) ($r['id'] ?? 0); ?>
                    <?php $rRating = (int) ($r['rating'] ?? 5); ?>
                    <?php $rStatus = Security::e($r['status'] ?? 'pending'); ?>
                    <tr class="<?= $rStatus === 'pending' ? 'row-pending' : '' ?>">
                        <td>
                            <div class="stars" aria-label="<?= $rRating ?> out of 5">
                                <?php for ($s = 0; $s < $rRating; $s++): ?>
                                    <svg class="icon icon-sm"><use href="#icon-star"/></svg>
                                <?php endfor; ?>
                            </div>
                        </td>
                        <td>
                            <div class="reviewer-info">
                                <span class="avatar avatar-sm <?= Security::e($r['avatar'] ?? 'a1') ?>"><?= Security::e($r['initials'] ?? '') ?></span>
                                <div>
                                    <strong><?= Security::e($r['name'] ?? '') ?></strong>
                                    <?php if (!empty($r['role'])): ?>
                                        <span class="row-note"><?= Security::e($r['role']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <blockquote class="review-quote">&ldquo;<?= Security::e($r['quote'] ?? '') ?>&rdquo;</blockquote>
                        </td>
                        <td>
                            <span class="badge badge-<?= $rStatus ?>"><?= ucfirst($rStatus) ?></span>
                        </td>
                        <td class="col-actions">
                            <?php if ($rStatus === 'pending'): ?>
                                <!-- Approve -->
                                <form action="<?= BASE_URL ?>/admin/review-status" method="post" class="row-form inline-form">
                                    <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
                                    <input type="hidden" name="id" value="<?= $rId ?>">
                                    <input type="hidden" name="status" value="approved">
                                    <?php if ($filter !== ''): ?><input type="hidden" name="filter" value="<?= Security::e($filter) ?>"><?php endif; ?>
                                    <button type="submit" class="btn btn-success btn-xs" title="Approve review">Approve</button>
                                </form>
                                <!-- Reject -->
                                <form action="<?= BASE_URL ?>/admin/review-status" method="post" class="row-form inline-form">
                                    <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
                                    <input type="hidden" name="id" value="<?= $rId ?>">
                                    <input type="hidden" name="status" value="rejected">
                                    <?php if ($filter !== ''): ?><input type="hidden" name="filter" value="<?= Security::e($filter) ?>"><?php endif; ?>
                                    <button type="submit" class="btn btn-outline btn-xs" title="Reject review">Reject</button>
                                </form>
                            <?php elseif ($rStatus === 'approved'): ?>
                                <!-- Move to pending -->
                                <form action="<?= BASE_URL ?>/admin/review-status" method="post" class="row-form inline-form">
                                    <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
                                    <input type="hidden" name="id" value="<?= $rId ?>">
                                    <input type="hidden" name="status" value="pending">
                                    <?php if ($filter !== ''): ?><input type="hidden" name="filter" value="<?= Security::e($filter) ?>"><?php endif; ?>
                                    <button type="submit" class="btn btn-outline btn-xs" title="Unapprove">Unapprove</button>
                                </form>
                            <?php elseif ($rStatus === 'rejected'): ?>
                                <!-- Re-approve -->
                                <form action="<?= BASE_URL ?>/admin/review-status" method="post" class="row-form inline-form">
                                    <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
                                    <input type="hidden" name="id" value="<?= $rId ?>">
                                    <input type="hidden" name="status" value="approved">
                                    <?php if ($filter !== ''): ?><input type="hidden" name="filter" value="<?= Security::e($filter) ?>"><?php endif; ?>
                                    <button type="submit" class="btn btn-success btn-xs" title="Approve review">Approve</button>
                                </form>
                            <?php endif; ?>
                            <!-- Delete -->
                            <form action="<?= BASE_URL ?>/admin/review-delete" method="post" class="row-form inline-form delete-form">
                                <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
                                <input type="hidden" name="id" value="<?= $rId ?>">
                                <?php if ($filter !== ''): ?><input type="hidden" name="filter" value="<?= Security::e($filter) ?>"><?php endif; ?>
                                <button type="submit" class="btn btn-danger btn-xs" title="Delete review">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
