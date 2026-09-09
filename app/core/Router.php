<?php
class Router
{
    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        // Strip the configured base path (e.g. '/wellness') so routes always
        // match from the application root — regardless of where it is deployed
        // (sub-directory, domain root, renamed folder, etc.).
        $base = defined('BASE_URL') ? rtrim((string) BASE_URL, '/') : '';
        if ($base !== '' && $base !== '/') {
            if ($path === $base) {
                $path = '/';
            } elseif (str_starts_with($path, $base . '/')) {
                $path = substr($path, strlen($base));
            }
        }

        $path = '/' . trim($path, '/');
        if ($path === '') {
            $path = '/';
        }

        if ($method === 'POST' && $path === '/appointment') {
            $controller = new AppointmentController();
            $controller->store();
            return;
        }

        if ($method === 'POST' && $path === '/qr-payment') {
            $controller = new QrPaymentController();
            $controller->store();
            return;
        }

        if ($method === 'POST' && $path === '/review') {
            $controller = new ReviewController();
            $controller->store();
            return;
        }

        if (str_starts_with($path, '/admin')) {
            $controller = new AdminController();

            // Content management: /admin/content, /admin/content/{section}, ...
            if (str_starts_with($path, '/admin/content')) {
                $controller->content($method, $path);
                return;
            }

            // Page section editor: /admin/pages, /admin/pages/{page}, ...
            if (str_starts_with($path, '/admin/pages')) {
                $controller->pages($method, $path);
                return;
            }

            // Admin user management: /admin/users, /admin/users/new, ...
            if (str_starts_with($path, '/admin/users')) {
                $controller->users($method, $path);
                return;
            }

            // Notification actions
            if ($path === '/admin/notifications/updates') {
                $controller->notificationsUpdates();
                return;
            }
            if ($method === 'POST' && $path === '/admin/notifications/read-all') {
                $controller->notificationsMarkAllRead();
                return;
            }
            if ($method === 'POST' && preg_match('#^/admin/notifications/read/(.+)$#', $path, $m)) {
                $controller->notificationsMarkRead($m[1]);
                return;
            }

            switch ($path) {
                case '/admin/login':
                    if ($method === 'POST') {
                        $controller->login();
                    } else {
                        $controller->showLogin();
                    }
                    break;
                case '/admin/google-login':
                    $controller->googleLogin();
                    break;
                case '/admin/google-callback':
                    $controller->googleCallback();
                    break;
                case '/admin/logout':
                    $controller->logout();
                    break;
                case '/admin/delete':
                    $controller->delete();
                    break;
                case '/admin/status':
                    $controller->updateStatus();
                    break;
                case '/admin/password':
                    if ($method === 'POST') {
                        $controller->updatePassword();
                    } else {
                        $controller->showPassword();
                    }
                    break;
                case '/admin/clear-cache':
                    if ($method === 'POST') {
                        $controller->clearCache();
                    } else {
                        $controller->dashboard();
                    }
                    break;
                case '/admin/unlock':
                    $controller->unlock();
                    break;
                case '/admin/appointments':
                    $controller->appointments();
                    break;
                case '/admin/qr-payments':
                    $controller->qrPayments();
                    break;
                case '/admin/qr-status':
                    $controller->qrStatus();
                    break;
                case '/admin/qr-delete':
                    $controller->qrDelete();
                    break;
                case '/admin/reviews':
                    $controller->reviews();
                    break;
                case '/admin/review-status':
                    $controller->reviewStatus();
                    break;
                case '/admin/review-delete':
                    $controller->reviewDelete();
                    break;
                default:
                    $controller->dashboard();
            }
            return;
        }

        $controller = new PageController();
        $controller->show($path);
    }
}
