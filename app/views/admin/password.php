<div class="admin-head">
    <div>
        <span class="kicker">Security</span>
        <h1>Passwords & Users</h1>
        <p class="admin-head-note">Change your own password, or add / manage other admin users who can sign in to the panel.</p>
    </div>
    <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/admin">&larr; Back to dashboard</a>
</div>

<div class="admin-two-col">
    <div class="card">
        <div class="card-block">
            <span class="kicker">My Account</span>
            <h3>Change My Password</h3>
        </div>
        <form action="<?= BASE_URL ?>/admin/password" method="post" class="contact-form admin-form">
            <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
            <div>
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
            </div>
            <div>
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required minlength="8" autocomplete="new-password">
                <span class="form-hint">At least 8 characters.</span>
            </div>
            <div>
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-dark">Update Password <svg class="icon"><use href="#icon-check"/></svg></button>
        </form>
    </div>

    <div class="card">
        <div class="card-block">
            <span class="kicker">Team Access</span>
            <h3>Add a New Admin User</h3>
            <p>They can sign in at <code><?= Security::e(BASE_URL) ?>/admin/login</code> with these credentials.</p>
        </div>
        <form action="<?= BASE_URL ?>/admin/users/save" method="post" class="contact-form admin-form">
            <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
            <div>
                <label for="user_username">Username</label>
                <input type="text" id="user_username" name="username" required minlength="3" maxlength="40" pattern="[A-Za-z0-9_\-\.]+" autocomplete="off" placeholder="e.g. doctor">
            </div>
            <div>
                <label for="user_display_name">Display name</label>
                <input type="text" id="user_display_name" name="display_name" autocomplete="off" placeholder="e.g. Dr. Sharma">
            </div>
            <div>
                <label for="user_password">Password</label>
                <input type="password" id="user_password" name="password" required minlength="8" autocomplete="new-password">
                <span class="form-hint">At least 8 characters.</span>
            </div>
            <div>
                <label for="user_confirm">Confirm Password</label>
                <input type="password" id="user_confirm" name="confirm_password" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary">Add User <svg class="icon"><use href="#icon-check"/></svg></button>
        </form>

        <?php if (!empty($users)): ?>
            <hr class="admin-divider">
            <div class="card-block">
                <h3>Current Users</h3>
            </div>
            <div class="table-wrap">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Display name</th>
                            <th class="col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <?php $userId = (int) ($user['id'] ?? 0); ?>
                            <?php $isYou = strtolower((string) ($user['username'] ?? '')) === strtolower((string) ($_SESSION['admin_username'] ?? '')); ?>
                            <tr>
                                <td><strong><?= Security::e($user['username'] ?? '') ?></strong><?php if ($isYou): ?> <span class="badge badge-confirmed">you</span><?php endif; ?></td>
                                <td><?= Security::e($user['display_name'] ?? '') ?></td>
                                <td class="col-actions">
                                    <a class="btn btn-outline btn-xs" href="<?= BASE_URL ?>/admin/users/edit?id=<?= $userId ?>">Edit</a>
                                    <?php if (!$isYou): ?>
                                        <form action="<?= BASE_URL ?>/admin/users/delete" method="post" class="row-form delete-form">
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
    </div>
</div>
