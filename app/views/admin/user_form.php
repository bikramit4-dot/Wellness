<div class="admin-head">
    <div>
        <span class="kicker">Access</span>
        <h1><?= Security::e($isEdit ? 'Edit ' : 'Add ') ?>User</h1>
        <p class="admin-head-note">Users sign in with their email address and password at <code><?= Security::e(BASE_URL) ?>/admin/login</code>.</p>
    </div>
</div>

<form action="<?= BASE_URL ?>/admin/users/save" method="post" class="contact-form admin-form">
    <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int) ($user['id'] ?? 0) ?>">
    <?php endif; ?>

    <div>
        <label for="email">Email (Gmail) <span class="req">*</span></label>
        <input type="email" id="email" name="email" value="<?= Security::e($user['email'] ?? '') ?>" required maxlength="190" placeholder="e.g. doctor@chitrawannaturecure.com" autocomplete="off">
        <span class="form-hint">The user's Gmail address — they'll use this to sign in.</span>
    </div>

    <div>
        <label for="display_name">Display name</label>
        <input type="text" id="display_name" name="display_name" value="<?= Security::e($user['display_name'] ?? '') ?>" autocomplete="off" placeholder="e.g. Dr. Sharma">
        <span class="form-hint">Shown in the admin header when this user is signed in.</span>
    </div>

    <div>
        <label for="role">Access Level <span class="req">*</span></label>
        <select id="role" name="role" required>
            <option value="staff" <?= ($user['role'] ?? 'admin') === 'staff' ? 'selected' : '' ?>>Booking Staff — Appointments & Payments only</option>
            <option value="admin" <?= ($user['role'] ?? 'admin') === 'admin' ? 'selected' : '' ?>>Full Admin — Everything (content, pages, settings, users)</option>
        </select>
        <span class="form-hint">Staff can only view bookings. Admins can edit content, pages, and manage users.</span>
    </div>

    <div>
        <label for="password"><?= $isEdit ? 'New password' : 'Password' ?> <?= $isEdit ? '' : '<span class="req">*</span>' ?></label>                <div class="pw-wrap">
                    <input type="password" id="password" name="password" <?= $isEdit ? '' : 'required' ?> minlength="8" autocomplete="new-password" placeholder="Min 8 characters">
                    <button type="button" class="pw-toggle" data-pw-target="password" title="Show/hide password">
                        <svg class="icon"><use href="#icon-eye"/></svg>
                    </button>
                </div>
        <div class="pw-hint" id="pwHint">
            <div class="pw-strength-bar"><i id="pwBar"></i></div>
            <span class="pw-strength-text" id="pwText"></span>
        </div>
        <span class="form-hint"><?= $isEdit ? 'Leave blank to keep the current password.' : 'Minimum 8 characters. Use letters, numbers & symbols for a strong password.' ?></span>
    </div>

    <div>
        <label for="confirm_password"><?= $isEdit ? 'Confirm new password' : 'Confirm password' ?> <?= $isEdit ? '' : '<span class="req">*</span>' ?></label>                <div class="pw-wrap">
                    <input type="password" id="confirm_password" name="confirm_password" <?= $isEdit ? '' : 'required' ?> minlength="8" autocomplete="new-password" placeholder="Re-enter password">
                    <button type="button" class="pw-toggle" data-pw-target="confirm_password" title="Show/hide password">
                        <svg class="icon"><use href="#icon-eye"/></svg>
                    </button>
                </div>
        <span class="form-hint" id="pwMatch"></span>
    </div>

    <div style="display:flex;gap:.8rem;align-items:center;">
        <button type="submit" class="btn btn-dark"><?= Security::e($isEdit ? 'Save Changes' : 'Add User') ?> <svg class="icon"><use href="#icon-check"/></svg></button>
        <a href="<?= BASE_URL ?>/admin/users/new" class="btn btn-outline">+ Add Another</a>
    </div>
</form>

<!-- ============ Users List Below ============ -->
<section style="margin-top:2.5rem;">
    <div class="admin-head" style="margin-bottom:1rem;">
        <div>
            <span class="kicker">Team</span>
            <h2>All Users <span style="font-size:.9rem;font-weight:400;color:var(--muted);">(<?= count($users ?? []) ?>)</span></h2>
        </div>
    </div>

    <?php if (empty($users)): ?>
        <div class="admin-empty">
            <svg class="icon"><use href="#icon-users"/></svg>
            <h3>No users yet</h3>
            <p>Fill in the form above to add your first user.</p>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Email</th>
                        <th>Display Name</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; foreach ($users as $u): ?>
                        <?php $userId = (int) ($u['id'] ?? 0); ?>
                        <?php $isYou = strtolower((string) ($u['username'] ?? '')) === strtolower((string) ($_SESSION['admin_username'] ?? '')); ?>
                        <?php $uRole = (string) ($u['role'] ?? 'admin'); ?>
                        <tr>
                            <td><?= ++$i ?></td>
                            <td>
                                <strong><?= Security::e($u['email'] ?: $u['username']) ?></strong>
                                <?php if ($isYou): ?> <span class="badge badge-confirmed">you</span><?php endif; ?>
                            </td>
                            <td><?= Security::e($u['display_name'] ?? '') ?></td>
                            <td>
                                <?php if ($uRole === 'admin'): ?>
                                    <span style="background:#16422f;color:#fff;padding:.15rem .5rem;border-radius:6px;font-size:.75rem;font-weight:600;">Admin</span>
                                <?php else: ?>
                                    <span style="background:#dbeafe;color:#1e40af;padding:.15rem .5rem;border-radius:6px;font-size:.75rem;font-weight:600;">Staff</span>
                                <?php endif; ?>
                            </td>
                            <td><?= Security::e((string) ($u['created_at'] ?? '')) ?></td>
                            <td class="col-actions">
                                <a class="btn btn-outline btn-xs" href="<?= BASE_URL ?>/admin/users/edit?id=<?= $userId ?>">Edit</a>
                                <?php if (!$isYou): ?>
                                    <form action="<?= BASE_URL ?>/admin/users/delete" method="post" class="row-form delete-form" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
                                        <input type="hidden" name="id" value="<?= $userId ?>">
                                        <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
