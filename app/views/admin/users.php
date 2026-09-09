<div class="admin-head">
    <div>
        <span class="kicker">Access</span>
        <h1>Users</h1>
        <p class="admin-head-note">Team members who can sign in to the admin panel. <strong>Admins</strong> have full access. <strong>Staff</strong> can only view bookings.</p>
    </div>
    <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/admin/users/new">+ Add User</a>
</div>

<?php if (empty($users)): ?>
    <div class="admin-empty">
        <svg class="icon"><use href="#icon-users"/></svg>
        <h3>No users yet</h3>
        <p>Add your first admin user and they can sign in right away.</p>
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
                <?php $i = 0; foreach ($users as $user): ?>
                    <?php $userId = (int) ($user['id'] ?? 0); ?>
                    <?php $isYou = strtolower((string) ($user['username'] ?? '')) === strtolower((string) ($_SESSION['admin_username'] ?? '')); ?>
                    <?php $userRole = (string) ($user['role'] ?? 'admin'); ?>
                    <tr>
                        <td><?= ++$i ?></td>
                        <td>
                            <strong><?= Security::e($user['email'] ?: $user['username']) ?></strong>
                            <?php if ($isYou): ?> <span class="badge badge-confirmed">you</span><?php endif; ?>
                        </td>
                        <td><?= Security::e($user['display_name'] ?? '') ?></td>
                        <td>
                            <?php if ($userRole === 'admin'): ?>
                                <span class="badge badge-admin" style="background:#16422f;color:#fff;padding:.15rem .5rem;border-radius:6px;font-size:.75rem;font-weight:600;">Admin</span>
                            <?php else: ?>
                                <span class="badge badge-staff" style="background:#dbeafe;color:#1e40af;padding:.15rem .5rem;border-radius:6px;font-size:.75rem;font-weight:600;">Staff</span>
                            <?php endif; ?>
                        </td>
                        <td><?= Security::e((string) ($user['created_at'] ?? '')) ?></td>
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
