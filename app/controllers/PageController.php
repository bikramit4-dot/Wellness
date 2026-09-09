<?php
class PageController extends Controller
{
    /**
     * Category routes that contain individual therapy pages: /{category}/{slug}
     */
    private const THERAPY_CATEGORIES = [
        'treatments' => ['label' => 'Treatments', 'url' => '/treatments'],
        'physiotherapy' => ['label' => 'Physiotherapy', 'url' => '/physiotherapy'],
        'diet-therapy' => ['label' => 'Diet Therapy', 'url' => '/diet-therapy'],
        'special-therapies' => ['label' => 'Special Therapies', 'url' => '/special-therapies'],
    ];

    public function show(string $path): void
    {
        $pages = [
            '/' => ['view' => 'pages/home', 'title' => 'Home'],
            '/about' => ['view' => 'pages/about', 'title' => 'About Us', 'intro' => 'Meet the people and philosophy behind Chitrawan Nature Cure Hospital — where natural healing is a way of life.'],
            '/treatments' => ['view' => 'pages/treatments', 'title' => 'Treatments', 'intro' => 'Explore our signature natural therapies, each designed to help your body heal itself.'],
            '/physiotherapy' => ['view' => 'pages/physiotherapy', 'title' => 'Physiotherapy', 'intro' => 'Rebuild strength, restore mobility, and live pain-free with expert-guided therapy.'],
            '/diet-therapy' => ['view' => 'pages/diet', 'title' => 'Diet Therapy', 'intro' => 'Food is medicine. Discover nutritional programs tailored to your body and goals.'],
            '/special-therapies' => ['view' => 'pages/special', 'title' => 'Special Therapies', 'intro' => 'Unique, time-honored therapies for relaxation, detoxification, and disease management.'],
            '/tariff' => ['view' => 'pages/tariff', 'title' => 'Tariff', 'intro' => 'Transparent pricing and flexible packages for every wellness journey.'],
            '/gallery' => ['view' => 'pages/gallery', 'title' => 'Gallery', 'intro' => 'A glimpse into our healing spaces, therapies, and community.'],
            '/blog' => ['view' => 'pages/blog', 'title' => 'Blog', 'intro' => 'Insights, tips, and stories to support your path to better health.'],
            '/contact' => ['view' => 'pages/contact', 'title' => 'Contact Us', 'intro' => 'We would love to hear from you. Reach out or book your appointment today.'],
        ];

        if (isset($pages[$path])) {
            // Map each public URL to its page_sections key.
            $pageKeys = [
                '/' => 'home',
                '/about' => 'about',
                '/treatments' => 'treatments',
                '/physiotherapy' => 'physiotherapy',
                '/diet-therapy' => 'diet',
                '/special-therapies' => 'special',
                '/tariff' => 'tariff',
                '/gallery' => 'gallery',
                '/blog' => 'blog',
                '/contact' => 'contact',
            ];
            $pageKey = $pageKeys[$path] ?? ltrim($path, '/');
            $data = [
                'title' => $pages[$path]['title'],
                'pageIntro' => $pages[$path]['intro'] ?? '',
                'sections' => PageSectionModel::forPage($pageKey),
            ];

            // The page heading (hero) section can override the default title/intro.
            // Skipped on the home page, which has its own dedicated hero section
            // (and must keep the 'Home' title so the automatic page-hero in the
            // layout stays hidden).
            if ($path !== '/') {
                $hero = $data['sections']['hero'] ?? [];
                if (trim((string) ($hero['heading'] ?? '')) !== '') {
                    $data['title'] = (string) $hero['heading'];
                }
                if (trim((string) ($hero['content'] ?? '')) !== '') {
                    $data['pageIntro'] = (string) $hero['content'];
                }
            }

            // Category index pages receive their items from the database-backed models.
            if (in_array($path, ['/treatments', '/physiotherapy', '/diet-therapy', '/special-therapies'], true)) {
                $data['therapies'] = self::therapies();
            }
            if ($path === '/blog') {
                $data['posts'] = self::posts();
            }
            if ($path === '/') {
                $data['testimonials'] = TestimonialModel::all();
                $data['features'] = FeatureModel::all();
                $data['offers'] = OfferModel::all();
                // Server-side timestamp for the review form.
                $_SESSION['review_form_ts'] = min(
                    (int) ($_SESSION['review_form_ts'] ?? PHP_INT_MAX),
                    time()
                );
            }
            if ($path === '/about') {
                $data['team'] = TeamModel::all();
            }
            if ($path === '/tariff') {
                $data['tariff'] = TariffModel::all();
                // Server-side timestamp for the QR advance-payment form. We keep
                // the EARLIEST render time of the session so a user with several
                // tabs open can submit from any of them without being flagged.
                $_SESSION['tariff_form_ts'] = min(
                    (int) ($_SESSION['tariff_form_ts'] ?? PHP_INT_MAX),
                    time()
                );
            }
            if ($path === '/gallery') {
                $data['galleryItems'] = GalleryModel::all();
            }
            if ($path === '/contact') {
                // Server-side timestamp for the appointment form. We keep the
                // EARLIEST render time of the session so a user with several
                // tabs open can submit from any of them without being flagged.
                $_SESSION['contact_form_ts'] = min(
                    (int) ($_SESSION['contact_form_ts'] ?? PHP_INT_MAX),
                    time()
                );
            }

            $this->render($pages[$path]['view'], $data);
            return;
        }

        // Individual therapy pages: /treatments/naturopathy, /physiotherapy/electrotherapy, ...
        $therapies = self::therapies();
        foreach (self::THERAPY_CATEGORIES as $cat => $catInfo) {
            if (str_starts_with($path, '/' . $cat . '/')) {
                $slug = trim(substr($path, strlen($cat) + 2), '/');
                if (isset($therapies[$slug]) && $therapies[$slug]['category'] === '/' . $cat) {
                    $therapy = $therapies[$slug];
                    $this->render('pages/therapy', [
                        'title' => $therapy['title'],
                        'pageIntro' => $therapy['intro'] ?? '',
                        'therapy' => $therapy,
                        'sections' => PageSectionModel::forPage('therapy'),
                        'breadcrumbParents' => [$catInfo],
                    ]);
                    return;
                }
            }
        }

        // Individual blog articles: /blog/{slug}
        if (str_starts_with($path, '/blog/')) {
            $slug = trim(substr($path, 6), '/');
            $posts = self::posts();
            if (isset($posts[$slug])) {
                $post = $posts[$slug];
                $this->render('pages/post', [
                    'title' => $post['title'],
                    'post' => $post,
                    'sections' => PageSectionModel::forPage('post'),
                    'hidePageHero' => true,
                ]);
                return;
            }
        }

        // Individual plan detail pages: /tariff/plan/{index}
        if (str_starts_with($path, '/tariff/plan/')) {
            $index = (int) trim(substr($path, strlen('/tariff/plan/')), '/');
            $tariff = TariffModel::all();
            $plans = $tariff['plans'] ?? [];
            if (isset($plans[$index])) {
                $plan = $plans[$index];
                $this->render('pages/plan', [
                    'title' => $plan['name'] ?? 'Plan Details',
                    'plan' => $plan,
                    'sections' => PageSectionModel::forPage('tariff'),
                    'breadcrumbParents' => [
                        ['label' => 'Tariff', 'url' => '/tariff'],
                    ],
                ]);
                return;
            }
        }

        http_response_code(404);
        $this->render('errors/404', ['title' => 'Page Not Found']);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private static function therapies(): array
    {
        static $therapies = null;
        if ($therapies === null) {
            $therapies = TherapyModel::all();
        }

        return $therapies;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private static function posts(): array
    {
        static $posts = null;
        if ($posts === null) {
            $posts = PostModel::all();
        }

        return $posts;
    }
}
