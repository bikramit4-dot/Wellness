<?php
/**
 * Generate a new ADMIN_PASSWORD_HASH line for app/config/config.php.
 *
 * Usage:
 *   php app/scripts/admin-password.php                # prompts for the password
 *   php app/scripts/admin-password.php "MyNewPassword"  # password as argument
 *
 * Then paste the printed line into app/config/config.php, replacing the
 * existing ADMIN_PASSWORD_HASH definition. The old password stops working
 * immediately after you save the file.
 */

$password = trim((string) ($argv[1] ?? ''));
if ($password === '') {
    fwrite(STDOUT, 'New admin password: ');
    $password = trim((string) fgets(STDIN));
}

if (strlen($password) < 8) {
    fwrite(STDERR, "ERROR: password must be at least 8 characters.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

echo "\nReplace the ADMIN_PASSWORD_HASH line in app/config/config.php with:\n\n";
echo "    define('ADMIN_PASSWORD_HASH', '" . $hash . "');\n\n";
echo "Credentials: username = ADMIN_USERNAME in config, password = the one you just set.\n";
