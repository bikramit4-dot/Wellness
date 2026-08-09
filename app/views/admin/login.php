<section class="admin-login">
    <div class="login-card">
        <div class="login-card-head">
            <span class="brand-mark" aria-hidden="true"><svg class="icon"><use href="#icon-shield"/></svg></span>
            <h1>Admin Login</h1>
            
        </div>
        <form action="<?= BASE_URL ?>/admin/login" method="post" class="contact-form">
            <input type="hidden" name="csrf_token" value="<?= Security::e(Security::csrfToken()) ?>">
            <div>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus placeholder="admin" autocomplete="username">
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter admin password" autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-dark">Sign In <svg class="icon"><use href="#icon-arrow"/></svg></button>
        </form>
            </div>
</section>
