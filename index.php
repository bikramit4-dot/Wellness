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
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; script-src 'self'; img-src 'self' data:; frame-src https://maps.google.com https://www.google.com;");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$router = new Router();
$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
