<?php
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/core/Security.php';
require_once __DIR__ . '/app/core/Controller.php';
require_once __DIR__ . '/app/core/Router.php';
require_once __DIR__ . '/app/core/Mailer.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/controllers/PageController.php';
require_once __DIR__ . '/app/controllers/AppointmentController.php';
require_once __DIR__ . '/app/controllers/AdminController.php';
require_once __DIR__ . '/app/models/AppointmentModel.php';
require_once __DIR__ . '/app/models/TherapyModel.php';
require_once __DIR__ . '/app/models/PostModel.php';
require_once __DIR__ . '/app/models/GalleryModel.php';
require_once __DIR__ . '/app/models/TestimonialModel.php';
require_once __DIR__ . '/app/models/TeamModel.php';
require_once __DIR__ . '/app/models/TariffModel.php';
require_once __DIR__ . '/app/models/FeatureModel.php';
require_once __DIR__ . '/app/models/OfferModel.php';
require_once __DIR__ . '/app/models/PageSectionModel.php';
require_once __DIR__ . '/app/models/AdminUserModel.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');
header('Permissions-Policy: camera=(), microphone=(), geolocation=(), interest-cohort=()');
header('Cross-Origin-Opener-Policy: same-origin');
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; script-src 'self'; img-src 'self' data:; frame-src https://maps.google.com https://www.google.com;");

// ============ Auto cache-clear ============
// While AUTO_CLEAR_CACHE is on, tell browsers (and any proxy in between) to
// always re-fetch HTML pages, so edits show up immediately. Combined with the
// versioned asset URLs (?v=...) from asset_url() this fully solves stale cache.
if (defined('AUTO_CLEAR_CACHE') && AUTO_CLEAR_CACHE) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$router = new Router();
$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
