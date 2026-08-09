<div class="admin-head">
    <div>
        <span class="kicker">Access</span>
        <h1><?= Security::e($isEdit ? 'Edit ' : 'Add ') ?>Admin User</h1>
        <p class="admin-head-note">The user can sign in at <code><?= Security::e(BASE_URL) ?>/admin/login</code>.</p>
    </div>
    <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/admin/users">&larr; All users</a>
</div>

<form action="<?= BASE_URL ?>/admin/users/save" method="post" class="contact-form admin-form">
    <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int) ($user['id'] ?? 0) ?>">
    <?php endif; ?>

    <div>
        <label for="username">Username <span class="req">*</span></label>
        <input type="text" id="username" name="username" value="<?= Security::e($user['username'] ?? '') ?>" required minlength="3" maxlength="40" pattern="[A-Za-z0-9_\-\.]+" autocomplete="off" placeholder="e.g. doctor, manager">
        <span class="form-hint">Letters, numbers, dashes, underscores or dots (3–40 characters). Lowercase is fine.</span>
    </div>

    <div>
        <label for="display_name">Display name</label>
        <input type="text" id="display_name" name="display_name" value="<?= Security::e($user['display_name'] ?? '') ?>" autocomplete="off" placeholder="e.g. Dr. Sharma">
        <span class="form-hint">Shown in the admin header when this user is signed in.</span>
    </div>

    <div>
        <label for="password"><?= $isEdit ? 'New password' : 'Password' ?> <?= $isEdit ? '' : '<span class="req">*</span>' ?></label>
        <input type="password" id="password" name="password" <?= $isEdit ? '' : 'required' ?> minlength="8" autocomplete="new-password">
        <span class="form-hint"><?= $isEdit ? 'Leave blank to keep the current password.' : 'At least 8 characters.' ?></span>
    </div>

    <div>
        <label for="confirm_password"><?= $isEdit ? 'Confirm new password' : 'Confirm password' ?> <?= $isEdit ? '' : '<span class="req">*</span>' ?></label>
        <input type="password" id="confirm_password" name="confirm_password" <?= $isEdit ? '' : 'required' ?> minlength="8" autocomplete="new-password">
    </div>

    <button type="submit" class="btn btn-dark"><?= Security::e($isEdit ? 'Save Changes' : 'Add User') ?> <svg class="icon"><use href="#icon-check"/></svg></button>
</form>
