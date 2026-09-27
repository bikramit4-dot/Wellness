<?php
class AdminController extends Controller
{
    private const ALLOWED_STATUSES = ['new', 'confirmed', 'rejected', 'completed'];

    /** Statuses for QR advance payments. */
    private const QR_STATUSES = ['new', 'verified', 'rejected'];

    /**
     * Content sections manageable from /admin/content/*.
     * Each section maps a URL key to a model + CRUD methods + admin UI
     * configuration (list columns and form fields).
     */
    private const SECTIONS = [
        'gallery' => [
            'label' => 'Gallery',
            'plural' => 'Gallery Items',
            'icon' => 'icon-sun',
            'model' => GalleryModel::class,
            'all' => 'all',
            'find' => 'find',
            'save' => 'save',
            'delete' => 'delete',
            'listColumns' => [
                ['key' => 'title', 'label' => 'Title'],
                ['key' => 'image', 'label' => 'Image', 'type' => 'image'],
                ['key' => 'sort_order', 'label' => 'Order'],
            ],
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'required' => true],
                ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                ['name' => 'image', 'label' => 'Image', 'type' => 'image', 'hint' => 'Paste an image URL, or upload a file from your computer. Images are auto-optimized to WebP for faster loading.'],
                ['name' => 'alt', 'label' => 'Alt text', 'type' => 'text', 'hint' => 'Accessible description of the image.'],
                ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'hint' => 'Lower numbers appear first.'],
            ],
        ],
        'testimonials' => [
            'label' => 'Testimonials',
            'plural' => 'Testimonials',
            'icon' => 'icon-star',
            'model' => TestimonialModel::class,
            'all' => 'all',
            'find' => 'find',
            'save' => 'save',
            'delete' => 'delete',
            'listColumns' => [
                ['key' => 'name', 'label' => 'Name'],
                ['key' => 'role', 'label' => 'Role'],
                ['key' => 'rating', 'label' => 'Rating', 'type' => 'rating'],
            ],
            'fields' => [
                ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                ['name' => 'role', 'label' => 'Role / Program', 'type' => 'text', 'hint' => 'e.g. Physiotherapy Patient'],
                ['name' => 'initials', 'label' => 'Initials (avatar)', 'type' => 'text', 'hint' => 'e.g. SP'],
                ['name' => 'avatar', 'label' => 'Avatar color', 'type' => 'select', 'options' => ['a1', 'a2', 'a3', 'a4', 'a5', 'a6']],
                ['name' => 'rating', 'label' => 'Rating (1–5)', 'type' => 'number'],
                ['name' => 'quote', 'label' => 'Quote', 'type' => 'textarea', 'required' => true],
                ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number'],
            ],
        ],
        'team' => [
            'label' => 'Team',
            'plural' => 'Team Members',
            'icon' => 'icon-users',
            'model' => TeamModel::class,
            'all' => 'all',
            'find' => 'find',
            'save' => 'save',
            'delete' => 'delete',
            'listColumns' => [
                ['key' => 'name', 'label' => 'Name'],
                ['key' => 'role', 'label' => 'Role'],
                ['key' => 'image', 'label' => 'Photo', 'type' => 'image'],
            ],
            'fields' => [
                ['name' => 'name', 'label' => 'Full name', 'type' => 'text', 'required' => true],
                ['name' => 'role', 'label' => 'Role / Title', 'type' => 'text', 'required' => true],
                ['name' => 'image', 'label' => 'Photo', 'type' => 'image', 'hint' => 'Paste a photo URL, or upload a file from your computer.'],
                ['name' => 'initials', 'label' => 'Initials (avatar)', 'type' => 'text', 'hint' => 'e.g. RS'],
                ['name' => 'avatar', 'label' => 'Avatar color', 'type' => 'select', 'options' => ['a1', 'a2', 'a3', 'a4', 'a5', 'a6']],
                ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number'],
            ],
        ],
        'plans' => [
            'label' => 'Tariff Plans',
            'plural' => 'Plans',
            'icon' => 'icon-heart',
            'model' => TariffModel::class,
            'all' => 'allPlans',
            'find' => 'findPlan',
            'save' => 'savePlan',
            'delete' => 'deletePlan',
            'listColumns' => [
                ['key' => 'name', 'label' => 'Plan'],
                ['key' => 'price', 'label' => 'Price'],
                ['key' => 'featured', 'label' => 'Featured', 'type' => 'bool'],
            ],
            'fields' => [
                ['name' => 'name', 'label' => 'Plan name', 'type' => 'text', 'required' => true],
                ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                ['name' => 'price', 'label' => 'Price', 'type' => 'text', 'hint' => 'e.g. $140'],
                ['name' => 'period', 'label' => 'Period', 'type' => 'text', 'hint' => 'e.g. session, week, month'],
                ['name' => 'features', 'label' => 'Features', 'type' => 'list', 'hint' => 'One feature per line.'],
                ['name' => 'featured', 'label' => 'Featured plan', 'type' => 'checkbox', 'hint' => 'Highlighted card with accent styling.'],
                ['name' => 'badge', 'label' => 'Badge text', 'type' => 'text', 'hint' => 'e.g. Most Popular (leave empty for none).'],
                ['name' => 'cta_label', 'label' => 'Button label', 'type' => 'text', 'hint' => 'e.g. Book the Retreat'],
                ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number'],
            ],
        ],
        'services' => [
            'label' => 'Service Prices',
            'plural' => 'Services',
            'icon' => 'icon-clock',
            'model' => TariffModel::class,
            'all' => 'allServices',
            'find' => 'findService',
            'save' => 'saveService',
            'delete' => 'deleteService',
            'listColumns' => [
                ['key' => 'service', 'label' => 'Service'],
                ['key' => 'duration', 'label' => 'Duration'],
                ['key' => 'price', 'label' => 'Price'],
            ],
            'fields' => [
                ['name' => 'service', 'label' => 'Service name', 'type' => 'text', 'required' => true],
                ['name' => 'duration', 'label' => 'Duration', 'type' => 'text', 'hint' => 'e.g. 1 Hour'],
                ['name' => 'price', 'label' => 'Price', 'type' => 'text', 'hint' => 'e.g. $25'],
                ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number'],
            ],
        ],
        'features' => [
            'label' => 'Why Choose Us',
            'plural' => 'Feature Cards',
            'icon' => 'icon-heart',
            'model' => FeatureModel::class,
            'all' => 'all',
            'find' => 'find',
            'save' => 'save',
            'delete' => 'delete',
            'listColumns' => [
                ['key' => 'title', 'label' => 'Title'],
                ['key' => 'image', 'label' => 'Image', 'type' => 'image'],
                ['key' => 'sort_order', 'label' => 'Order'],
            ],
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'required' => true],
                ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                ['name' => 'image', 'label' => 'Image', 'type' => 'image', 'hint' => 'Paste an image URL, or upload a file from your computer. Images are auto-optimized to WebP for faster loading.'],
                ['name' => 'icon', 'label' => 'Icon', 'type' => 'select', 'options' => ['icon-users', 'icon-heart', 'icon-leaf', 'icon-shield', 'icon-sun', 'icon-moon', 'icon-activity', 'icon-droplet', 'icon-zap', 'icon-clock']],
                ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number'],
            ],
        ],
        'offers' => [
            'label' => 'What We Offer',
            'plural' => 'Offer Cards',
            'icon' => 'icon-zap',
            'model' => OfferModel::class,
            'all' => 'all',
            'find' => 'find',
            'save' => 'save',
            'delete' => 'delete',
            'listColumns' => [
                ['key' => 'title', 'label' => 'Title'],
                ['key' => 'image', 'label' => 'Image', 'type' => 'image'],
                ['key' => 'sort_order', 'label' => 'Order'],
            ],
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'required' => true],
                ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                ['name' => 'image', 'label' => 'Image', 'type' => 'image', 'hint' => 'Paste an image URL, or upload a file from your computer. Images are auto-optimized to WebP for faster loading.'],
                ['name' => 'icon', 'label' => 'Icon', 'type' => 'select', 'options' => ['icon-users', 'icon-heart', 'icon-leaf', 'icon-shield', 'icon-sun', 'icon-moon', 'icon-activity', 'icon-droplet', 'icon-zap', 'icon-clock']],
                ['name' => 'link', 'label' => 'Page link', 'type' => 'text', 'hint' => 'Where the card links to, e.g. /treatments/naturopathy (defaults to /treatments).'],
                ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number'],
            ],
        ],
        'therapies' => [
            'label' => 'Therapies',
            'plural' => 'Therapies',
            'icon' => 'icon-activity',
            'model' => TherapyModel::class,
            'all' => 'allAdmin',
            'find' => 'findAdmin',
            'save' => 'saveAdmin',
            'delete' => 'deleteAdmin',
            'listColumns' => [
                ['key' => 'title', 'label' => 'Therapy'],
                ['key' => 'categoryLabel', 'label' => 'Category'],
                ['key' => 'methodsCount', 'label' => 'Techniques'],
                ['key' => 'image', 'label' => 'Photo', 'type' => 'image'],
            ],
            'fields' => [
                ['name' => 'title', 'label' => 'Therapy name', 'type' => 'text', 'required' => true],
                ['name' => 'category', 'label' => 'Category', 'type' => 'select', 'options' => ['Treatments | /treatments', 'Physiotherapy | /physiotherapy', 'Diet Therapy | /diet-therapy', 'Special Therapies | /special-therapies']],
                ['name' => 'icon', 'label' => 'Icon', 'type' => 'select', 'options' => ['icon-leaf', 'icon-activity', 'icon-moon', 'icon-zap', 'icon-heart', 'icon-shield', 'icon-sun', 'icon-droplet', 'icon-wind', 'icon-clock', 'icon-users', 'icon-star']],
                ['name' => 'image', 'label' => 'Photo', 'type' => 'image', 'hint' => 'Paste an image URL, or upload a file from your computer.'],
                ['name' => 'intro', 'label' => 'Intro (short)', 'type' => 'textarea', 'hint' => 'One line shown on the category cards.'],
                ['name' => 'about', 'label' => 'About paragraphs', 'type' => 'list', 'hint' => 'One paragraph per line.'],
                ['name' => 'methods', 'label' => 'Techniques & Methods', 'type' => 'methods', 'hint' => 'One per line, format: Title | Definition (the definition expands when clicked on the website).'],
                ['name' => 'benefits', 'label' => 'Key Benefits', 'type' => 'list', 'hint' => 'One benefit per line.'],
            ],
        ],
    ];

    /**
     * Editable page sections, grouped by page. Each page lists its sections
     * and each section lists its editable fields. Field names map directly to
     * columns of the `page_sections` table; type 'list' renders a one-per-line
     * textarea stored as a JSON array in `extras`.
     */
    private const PAGE_SECTIONS = [
        'site' => [
            'label' => 'Site Settings',
            'url' => '/',
            'sections' => [
                'brand' => [
                    'label' => 'Logo & Brand',
                    'fields' => [
                        ['name' => 'image', 'label' => 'Logo image', 'type' => 'image', 'hint' => 'Upload your logo (or paste an image URL). Auto-optimized to WebP. Leave empty to keep the leaf icon + name.'],
                        ['name' => 'heading', 'label' => 'Brand name', 'type' => 'text', 'hint' => 'Shown next to the logo, e.g. Chitrawan Nature Cure Hospital'],
                    ],
                ],
                'nav' => [
                    'label' => 'Navigation Menu',
                    'fields' => [
                        ['name' => 'extras', 'label' => 'Menu links', 'type' => 'list', 'hint' => 'One per line, format: Label | URL. Leave empty to keep the standard menu (with auto-generated therapy dropdowns). The Book Now button always appears last.'],
                    ],
                ],
                'topbar' => [
                    'label' => 'Top Bar',
                    'fields' => [
                        ['name' => 'link', 'label' => 'Phone number', 'type' => 'text', 'hint' => 'e.g. +977 56-535213'],
                        ['name' => 'sub_content', 'label' => 'Email address', 'type' => 'text', 'hint' => 'e.g. nchchitwan@gmail.com'],
                        ['name' => 'kicker', 'label' => 'Opening hours', 'type' => 'text', 'hint' => 'e.g. Sun – Fri: 8:00 AM – 7:00 PM'],
                    ],
                ],
                'footer' => [
                    'label' => 'Footer Information',
                    'fields' => [
                        ['name' => 'content', 'label' => 'Footer tagline', 'type' => 'textarea', 'hint' => 'Short description shown under the brand in the footer.'],
                        ['name' => 'heading', 'label' => 'Address', 'type' => 'text'],
                        ['name' => 'sub_content', 'label' => 'Email', 'type' => 'text'],
                        ['name' => 'link', 'label' => 'Phone', 'type' => 'text', 'hint' => 'e.g. +977 56-535213'],
                        ['name' => 'kicker', 'label' => 'Opening hours', 'type' => 'textarea', 'hint' => 'One line per period — each line appears on its own row.'],
                        ['name' => 'link_label', 'label' => 'Copyright tagline', 'type' => 'text', 'hint' => 'Small text beside the © copyright line.'],
                        ['name' => 'extras', 'label' => 'Social links', 'type' => 'list', 'hint' => 'One per line, format: Label | URL — e.g. Facebook | facebook.com/yourpage (https:// is added automatically; supported: Facebook, Instagram, TikTok, X)'],
                    ],
                ],
            ],
        ],
        'home' => [
            'label' => 'Home',
            'url' => '/',
            'sections' => [
                'hero' => [
                    'label' => 'Hero Banner',
                    'fields' => [
                        ['name' => 'kicker', 'label' => 'Eyebrow text', 'type' => 'text'],
                        ['name' => 'heading', 'label' => 'Main heading', 'type' => 'text'],
                        ['name' => 'content', 'label' => 'Subtitle paragraph', 'type' => 'textarea'],
                        ['name' => 'sub_content', 'label' => 'Rating line', 'type' => 'text', 'hint' => 'e.g. 4.9/5 rated by 1,200+ happy patients'],
                        ['name' => 'media', 'label' => 'Background video file', 'type' => 'text', 'hint' => 'File name inside public/uploads/, e.g. Olive and White Modern Spa and Wellness Banner Landscape.mp4'],
                        ['name' => 'extras', 'label' => 'Buttons', 'type' => 'list', 'hint' => 'One per line, format: Label | URL | style (primary, light, dark, outline)'],
                    ],
                ],
                'stats' => [
                    'label' => 'Stats Band',
                    'fields' => [
                        ['name' => 'extras', 'label' => 'Stats', 'type' => 'list', 'required' => true, 'hint' => 'One per line, format: Value | Label, e.g. 15+ | Years of Experience'],
                    ],
                ],
                'about_intro' => [
                    'label' => 'About Intro',
                    'fields' => [
                        ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                        ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                        ['name' => 'content', 'label' => 'Paragraph', 'type' => 'textarea'],
                        ['name' => 'sub_content', 'label' => 'Badge', 'type' => 'text', 'hint' => 'Format: Title | Text, e.g. Since 2010 | Healing with care & compassion'],
                        ['name' => 'image', 'label' => 'Photo', 'type' => 'image'],
                        ['name' => 'link', 'label' => 'Button link', 'type' => 'text'],
                        ['name' => 'link_label', 'label' => 'Button label', 'type' => 'text'],
                        ['name' => 'extras', 'label' => 'Checklist items', 'type' => 'list', 'hint' => 'One item per line.'],
                    ],
                ],
                'why' => [
                    'label' => 'Why Choose Us heading',
                    'fields' => [
                        ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                        ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                        ['name' => 'content', 'label' => 'Lede', 'type' => 'textarea'],
                    ],
                ],
                'offers' => [
                    'label' => 'What We Offer heading',
                    'fields' => [
                        ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                        ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                        ['name' => 'content', 'label' => 'Lede', 'type' => 'textarea'],
                    ],
                ],
                'testimonials' => [
                    'label' => 'Testimonials heading',
                    'fields' => [
                        ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                        ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                        ['name' => 'content', 'label' => 'Lede', 'type' => 'textarea'],
                    ],
                ],
                'story' => [
                    'label' => 'Our Story',
                    'fields' => [
                        ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                        ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                        ['name' => 'content', 'label' => 'Paragraphs', 'type' => 'textarea', 'hint' => 'Separate paragraphs with a blank line.'],
                        ['name' => 'image', 'label' => 'Photo', 'type' => 'image'],
                        ['name' => 'link', 'label' => 'Button link', 'type' => 'text'],
                        ['name' => 'link_label', 'label' => 'Button label', 'type' => 'text'],
                        ['name' => 'extras', 'label' => 'Highlight points', 'type' => 'list', 'hint' => 'One point per line.'],
                    ],
                ],
                'cta' => [
                    'label' => 'CTA Banner',
                    'fields' => [
                        ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                        ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                        ['name' => 'content', 'label' => 'Paragraph', 'type' => 'textarea'],
                        ['name' => 'extras', 'label' => 'Buttons', 'type' => 'list', 'hint' => 'One per line, format: Label | URL | style (primary, light, dark, outline)'],
                    ],
                ],
            ],
        ],
        'about' => [
            'label' => 'About',
            'url' => '/about',
            'sections' => [
                'hero' => ['label' => 'Page Heading', 'fields' => [
                    ['name' => 'heading', 'label' => 'Page title', 'type' => 'text'],
                    ['name' => 'content', 'label' => 'Intro', 'type' => 'textarea'],
                ]],
                'aboutus' => ['label' => '01 · About Us', 'fields' => [
                    ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                    ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['name' => 'content', 'label' => 'Paragraph', 'type' => 'textarea'],
                    ['name' => 'sub_content', 'label' => 'Photo badge', 'type' => 'text', 'hint' => 'Format: Title | Text — shown over the photo. Leave empty to hide the badge.'],
                    ['name' => 'image', 'label' => 'Photo', 'type' => 'image'],
                    ['name' => 'extras', 'label' => 'Check list', 'type' => 'list'],
                ]],
                'mission' => ['label' => '02 · Our Mission', 'fields' => [
                    ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                    ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['name' => 'image', 'label' => 'Photo', 'type' => 'image'],
                    ['name' => 'content', 'label' => 'Lede', 'type' => 'textarea'],
                    ['name' => 'sub_content', 'label' => 'Card title', 'type' => 'text'],
                    ['name' => 'extras', 'label' => 'Commitments list', 'type' => 'list'],
                ]],
                'vision' => ['label' => '03 · Our Vision', 'fields' => [
                    ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                    ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['name' => 'content', 'label' => 'Narrative intro', 'type' => 'textarea'],
                    ['name' => 'sub_content', 'label' => 'Quote over photo', 'type' => 'text', 'hint' => 'Short quote shown on the photo. Leave empty to hide it.'],
                    ['name' => 'image', 'label' => 'Photo', 'type' => 'image'],
                    ['name' => 'extras', 'label' => 'Vision points', 'type' => 'list', 'hint' => 'One per line, format: Title | Text | icon — each becomes a highlighted story beat.'],
                ]],
                'approach' => ['label' => '04 · Our Core Values', 'fields' => [
                    ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                    ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['name' => 'content', 'label' => 'Paragraph', 'type' => 'textarea'],
                    ['name' => 'image', 'label' => 'Photo', 'type' => 'image'],
                    ['name' => 'extras', 'label' => 'Core values', 'type' => 'list', 'hint' => 'One per line, format: Title | Description | icon | image URL — each becomes a clickable box that opens the details.'],
                ]],
                'founder' => ['label' => '05 · Our Founders', 'fields' => [
                    ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                    ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['name' => 'sub_content', 'label' => 'First founder — name + role', 'type' => 'text', 'hint' => 'Format: Name | Role'],
                    ['name' => 'content', 'label' => 'First founder — bio', 'type' => 'textarea'],
                    ['name' => 'image', 'label' => 'First founder — photo', 'type' => 'image'],
                    ['name' => 'extras', 'label' => 'More founders', 'type' => 'list', 'hint' => 'One per line, format: Name | Role | Photo URL | Short bio — photo and bio optional. Leave empty for a single founder.'],
                ]],
                'doctors' => ['label' => '06 · Our Doctors', 'fields' => [
                    ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                    ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['name' => 'content', 'label' => 'Lede', 'type' => 'textarea'],
                    ['name' => 'extras', 'label' => 'Doctors', 'type' => 'list', 'hint' => 'One per line, format: Name | Role | Photo URL — e.g. Dr. Ram Thapa | Senior Physiotherapist | /wellness/public/uploads/photos/dr-ram.jpg. Photo optional (initials shown).'],
                ]],
                'group' => ['label' => '07 · Our Team', 'fields' => [
                    ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                    ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['name' => 'image', 'label' => 'Photo', 'type' => 'image'],
                    ['name' => 'content', 'label' => 'Lede', 'type' => 'textarea'],
                ]],
                'cta' => ['label' => 'CTA Banner', 'fields' => [
                    ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                    ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['name' => 'content', 'label' => 'Paragraph', 'type' => 'textarea'],
                    ['name' => 'extras', 'label' => 'Buttons', 'type' => 'list', 'hint' => 'One per line, format: Label | URL | style'],
                ]],
            ],
        ],
        'contact' => [
            'label' => 'Contact',
            'url' => '/contact',
            'sections' => [
                'hero' => ['label' => 'Page Heading', 'fields' => [
                    ['name' => 'heading', 'label' => 'Page title', 'type' => 'text'],
                    ['name' => 'content', 'label' => 'Intro', 'type' => 'textarea'],
                ]],
                'info' => ['label' => 'Contact Information', 'fields' => [
                    ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                    ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['name' => 'extras', 'label' => 'Info items', 'type' => 'list', 'required' => true, 'hint' => 'One per line, format: Label | Value. Lines: Our Address, Phone, Email, Opening Hours.'],
                ]],
                'form' => ['label' => 'Booking Form', 'fields' => [
                    ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['name' => 'content', 'label' => 'Intro line', 'type' => 'textarea'],
                ]],
                'map' => ['label' => 'Map / Location', 'fields' => [
                    ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                    ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['name' => 'content', 'label' => 'Lede', 'type' => 'textarea'],
                    ['name' => 'link', 'label' => 'Map query', 'type' => 'text', 'hint' => 'Address used in the Google Maps embed, e.g. Chitrawan Nature Cure Hospital Center, Bharatpur-15, Nepal'],
                ]],
            ],
        ],
        'treatments' => ['label' => 'Treatments', 'url' => '/treatments', 'sections' => self::LIST_PAGE_SECTIONS],
        'physiotherapy' => ['label' => 'Physiotherapy', 'url' => '/physiotherapy', 'sections' => self::LIST_PAGE_SECTIONS],
        'diet' => ['label' => 'Diet Therapy', 'url' => '/diet-therapy', 'sections' => self::LIST_PAGE_SECTIONS],
        'special' => ['label' => 'Special Therapies', 'url' => '/special-therapies', 'sections' => self::LIST_PAGE_SECTIONS],
        'tariff' => [
            'label' => 'Tariff',
            'url' => '/tariff',
            'sections' => [
                'hero' => ['label' => 'Page Heading', 'fields' => [
                    ['name' => 'heading', 'label' => 'Page title', 'type' => 'text'],
                    ['name' => 'content', 'label' => 'Intro', 'type' => 'textarea'],
                ]],
                'packages' => ['label' => 'Packages heading', 'fields' => [
                    ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                    ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['name' => 'content', 'label' => 'Lede', 'type' => 'textarea'],
                ]],
                'services' => ['label' => 'Service prices heading', 'fields' => [
                    ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                    ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['name' => 'content', 'label' => 'Lede', 'type' => 'textarea'],
                    ['name' => 'sub_content', 'label' => 'Footnote', 'type' => 'textarea'],
                ]],
                'qr' => ['label' => 'QR Code & Advance Payment', 'fields' => [
                    ['name' => 'image', 'label' => 'QR code image', 'type' => 'image', 'hint' => 'Upload the QR code that patients scan to pay their advance. Auto-optimized to WebP.'],
                    ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['name' => 'content', 'label' => 'Intro paragraph', 'type' => 'textarea'],
                    ['name' => 'sub_content', 'label' => 'Payment details line', 'type' => 'text', 'hint' => 'Shown under the QR code, e.g. UPI ID: chitrawan naturecure@upi'],
                    ['name' => 'link_label', 'label' => 'Note below the steps', 'type' => 'text'],
                    ['name' => 'extras', 'label' => 'How to pay (steps)', 'type' => 'list', 'hint' => 'One step per line.'],
                ]],
            ],
        ],
        'gallery' => [
            'label' => 'Gallery',
            'url' => '/gallery',
            'sections' => [
                'hero' => ['label' => 'Page Heading', 'fields' => [
                    ['name' => 'heading', 'label' => 'Page title', 'type' => 'text'],
                    ['name' => 'content', 'label' => 'Intro', 'type' => 'textarea'],
                ]],
            ],
        ],
        'blog' => [
            'label' => 'Blog',
            'url' => '/blog',
            'sections' => [
                'hero' => ['label' => 'Page Heading', 'fields' => [
                    ['name' => 'heading', 'label' => 'Page title', 'type' => 'text'],
                    ['name' => 'content', 'label' => 'Intro', 'type' => 'textarea'],
                ]],
                'cta' => ['label' => 'CTA Banner', 'fields' => [
                    ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                    ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                    ['name' => 'content', 'label' => 'Paragraph', 'type' => 'textarea'],
                    ['name' => 'extras', 'label' => 'Buttons', 'type' => 'list', 'hint' => 'One per line, format: Label | URL | style'],
                ]],
            ],
        ],
        'therapy' => ['label' => 'Therapy Detail', 'url' => '/treatments', 'sections' => [
            'cta' => ['label' => 'CTA Banner', 'fields' => [
                ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                ['name' => 'heading', 'label' => 'Heading', 'type' => 'text', 'hint' => 'Use {title} to insert the therapy name, e.g. Begin Your {title} Journey'],
                ['name' => 'content', 'label' => 'Paragraph', 'type' => 'textarea'],
                ['name' => 'extras', 'label' => 'Buttons', 'type' => 'list', 'hint' => 'One per line, format: Label | URL | style'],
            ]],
        ]],
        'post' => ['label' => 'Blog Article', 'url' => '/blog', 'sections' => [
            'cta' => ['label' => 'CTA Banner', 'fields' => [
                ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
                ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
                ['name' => 'content', 'label' => 'Paragraph', 'type' => 'textarea'],
                ['name' => 'extras', 'label' => 'Buttons', 'type' => 'list', 'hint' => 'One per line, format: Label | URL | style'],
            ]],
        ]],
    ];

    private const LIST_PAGE_SECTIONS = [
        'hero' => ['label' => 'Page Heading', 'fields' => [
            ['name' => 'heading', 'label' => 'Page title', 'type' => 'text'],
            ['name' => 'content', 'label' => 'Intro', 'type' => 'textarea'],
        ]],
        'cta' => ['label' => 'CTA Banner', 'fields' => [
            ['name' => 'kicker', 'label' => 'Kicker', 'type' => 'text'],
            ['name' => 'heading', 'label' => 'Heading', 'type' => 'text'],
            ['name' => 'content', 'label' => 'Paragraph', 'type' => 'textarea'],
            ['name' => 'extras', 'label' => 'Buttons', 'type' => 'list', 'hint' => 'One per line, format: Label | URL | style'],
        ]],
    ];

    private function requireAuth(): void
    {
        if (empty($_SESSION['admin_logged_in'])) {
            $this->redirect('/admin/login');
            return;
        }

        // Hard server-side idle timeout: if the session has been inactive
        // longer than ADMIN_SESSION_TIMEOUT, sign out (backstop for the
        // client-side screen lock, or when the lock is bypassed).
        $timeout = defined('ADMIN_SESSION_TIMEOUT') ? (int) ADMIN_SESSION_TIMEOUT : 1800;
        $lastActivity = (int) ($_SESSION['admin_last_activity'] ?? 0);
        if ($lastActivity > 0 && time() - $lastActivity > $timeout) {
            unset(
            $_SESSION['admin_logged_in'],
            $_SESSION['admin_username'],
            $_SESSION['admin_display_name'],
            $_SESSION['admin_email'],
            $_SESSION['admin_avatar'],
            $_SESSION['admin_auth_method'],
            $_SESSION['admin_last_activity']
        );
            session_regenerate_id(true);
            $this->setFlash('danger', 'Your session expired after inactivity. Please sign in again.');
            $this->redirect('/admin/login');
            return;
        }

        $_SESSION['admin_last_activity'] = time();
    }

    /**
     * Re-authenticate after the client-side screen lock.
     * POST only: verifies the current user's password and resets the idle
     * clock. Returns JSON so the lock overlay can resume in place.
     */
    public function unlock(): void
    {
        if (empty($_SESSION['admin_logged_in'])) {
            $this->json(['ok' => false, 'message' => 'Session expired. Please sign in again.']);
            return;
        }

        if (!$this->requireValidCsrf()) {
            $this->json(['ok' => false, 'message' => 'Invalid security token.']);
            return;
        }

        $login = strtolower((string) ($_SESSION['admin_username'] ?? $_SESSION['admin_email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($login === '' || $this->credentialsValid($login, $password) === null) {
            $this->json(['ok' => false, 'message' => 'Incorrect password.']);
            return;
        }

        session_regenerate_id(true);
        $_SESSION['admin_last_activity'] = time();
        $this->json(['ok' => true]);
    }

    private function json(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function requireValidCsrf(): bool
    {
        return isset($_POST['csrf_token']) && Security::validateCsrf($_POST['csrf_token']);
    }

    /* ---------- Authentication ---------- */

    public function showLogin(): void
    {
        if (!empty($_SESSION['admin_logged_in'])) {
            $this->redirect('/admin');
        }

        $this->render('admin/login', [
            'title' => 'Admin Login',
            'layout' => 'admin',
        ]);
    }

    public function login(): void
    {
        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token. Please try again.');
            $this->redirect('/admin/login');
        }

        // Brute-force protection: after 5 failed attempts from one IP,
        // lock that IP out for 5 minutes.
        $ip = $this->clientIp();
        $remaining = $this->lockoutRemaining($ip);
        if ($remaining > 0) {
            $minutes = max(1, (int) ceil($remaining / 60));
            $this->setFlash('danger', 'Too many failed attempts. Try again in about ' . $minutes . ' minute(s).');
            $this->redirect('/admin/login');
        }

        // Accept email or username as login.
        $login = strtolower(Security::sanitizeText($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        error_log('[Login] Attempt: login="' . $login . '" password_len=' . strlen($password) . ' raw_post_username="' . (string)($_POST['username'] ?? '') . '"');

        $dbUser = $this->credentialsValid($login, $password);
        if ($dbUser === null) {
            error_log('[Login] FAILED for "' . $login . '"');
            $this->recordFailedAttempt($ip);
            if ($this->lockoutRemaining($ip) > 0) {
                $this->setFlash('danger', 'Too many failed attempts. Please wait a few minutes and try again.');
            } else {
                $left = max(0, 5 - (int) ($this->readAttempts()[$ip]['count'] ?? 0));
                $this->setFlash('danger', 'Invalid email or password.' . ($left > 0 ? ' ' . $left . ' attempt(s) left.' : ''));
            }
            $this->redirect('/admin/login');
        }

        $this->clearAttempts($ip);
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = (string) ($dbUser['username'] ?? $login);
        $_SESSION['admin_email'] = (string) ($dbUser['email'] ?? $login);
        $_SESSION['admin_role'] = (string) ($dbUser['role'] ?? 'admin');
        $_SESSION['admin_last_activity'] = time();

        // Show the display name (if set) in the admin header.
        $displayName = trim((string) ($dbUser['display_name'] ?? ''));
        $_SESSION['admin_display_name'] = $displayName !== '' ? $displayName : (string) ($dbUser['username'] ?? $login);

        $this->setFlash('success', 'Welcome back, ' . $_SESSION['admin_display_name'] . '!');
        $this->redirect('/admin');
    }

    /**
     * Verify the admin username + password against the `admin_users` table
     * first, then fall back to ADMIN_USERNAME / ADMIN_PASSWORD_HASH from
     * config.php (which always works, even if the database is unavailable).
     */
    /**
     * Verify credentials. Accepts email or username.
     * Returns the user row on success, null on failure.
     *
     * @return array<string, mixed>|null
     */
    private function credentialsValid(string $login, string $password): ?array
    {
        $password = trim($password);
        $login = strtolower(trim($login));

        error_log('[Auth] credentialsValid: login="' . $login . '" password_len=' . strlen($password));

        // Database users: try email first, then username.
        $dbUser = AdminUserModel::findByEmail($login);
        error_log('[Auth] findByEmail: ' . ($dbUser ? 'FOUND' : 'NULL'));
        if ($dbUser === null) {
            $dbUser = AdminUserModel::findByUsername($login);
            error_log('[Auth] findByUsername: ' . ($dbUser ? 'FOUND' : 'NULL'));
        }
        if ($dbUser !== null) {
            $hash = (string) ($dbUser['password_hash'] ?? '');
            $verify = password_verify($password, $hash);
            error_log('[Auth] password_verify: ' . ($verify ? 'PASS' : 'FAIL') . ' hash_len=' . strlen($hash));
            if ($verify) {
                return $dbUser;
            }
        }

        // Fallback: the original config-defined admin.
        $expectedUser = defined('ADMIN_USERNAME') ? strtolower(trim((string) ADMIN_USERNAME)) : '';
        $userOk = $expectedUser !== '' && hash_equals($expectedUser, $login);

        $hash = defined('ADMIN_PASSWORD_HASH') ? ADMIN_PASSWORD_HASH : '';
        $passOk = $hash !== '' && password_verify($password, $hash);

        // Always run a verify (even when the username is wrong) so response
        // timing cannot reveal which field was incorrect.
        if (!$userOk) {
            password_verify($password, $hash !== '' ? $hash : '$2y$12$rN9fY4vX1dKpQmWcT0nE7uL2sBzHj8gQaR6tY0eW5nIuM3pVbCzXaK');
        }

        if ($userOk && $passOk) {
            // Return a fake row for the config-defined admin (always 'admin' role)
            return [
                'id' => 0,
                'email' => $expectedUser,
                'username' => $expectedUser,
                'display_name' => 'Admin',
                'role' => 'admin',
            ];
        }

        return null;
    }

    /**
     * Check if the current user has the given role.
     * Config-defined admin always has 'admin' role.
     */
    private function userRole(): string
    {
        return (string) ($_SESSION['admin_role'] ?? 'admin');
    }

    private function isAdmin(): bool
    {
        return $this->userRole() === 'admin';
    }

    /**
     * Require admin role. Staff users get redirected to bookings.
     */
    private function requireAdmin(): void
    {
        $this->requireAuth();
        if (!$this->isAdmin()) {
            $this->setFlash('danger', 'You do not have permission to access that page.');
            $this->redirect('/admin/appointments');
        }
    }

    /* ---------- Google OAuth login ---------- */

    /**
     * Redirect the admin to Google's OAuth consent screen.
     */
    public function googleLogin(): void
    {
        if (!GoogleOAuth::isConfigured()) {
            $this->setFlash('danger', 'Google login is not configured. Set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in .env.');
            $this->redirect('/admin/login');
        }

        GoogleOAuth::redirect();
    }

    /**
     * Handle the callback from Google after the user signs in.
     */
    public function googleCallback(): void
    {
        $result = GoogleOAuth::handleCallback();

        if (!$result['ok']) {
            $this->setFlash('danger', $result['error'] ?? 'Google login failed.');
            $this->redirect('/admin/login');
        }

        $email = $result['email'];
        $name  = $result['name'] ?? '';
        $picture = $result['picture'] ?? '';

        // Check if this email is in the DB (for role lookup)
        $dbUser = AdminUserModel::findByEmail($email);
        $role = ($dbUser !== null && isset($dbUser['role'])) ? $dbUser['role'] : 'admin';

        // Log in the user via session
        session_regenerate_id(true);
        $_SESSION['admin_logged_in']    = true;
        $_SESSION['admin_username']     = strtolower($email);
        $_SESSION['admin_display_name'] = $name !== '' ? $name : $email;
        $_SESSION['admin_email']        = $email;
        $_SESSION['admin_avatar']       = $picture;
        $_SESSION['admin_role']         = $role;
        $_SESSION['admin_auth_method']  = 'google';
        $_SESSION['admin_last_activity'] = time();

        $this->setFlash('success', 'Welcome, ' . ($name !== '' ? $name : $email) . '!');
        $this->redirect('/admin');
    }

    /* ---------- Brute-force protection (IP-based) ---------- */

    private function clientIp(): string
    {
        return (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    }

    private function attemptLogFile(): string
    {
        return APP_ROOT . '/storage/login_attempts.json';
    }

    /**
     * @return array<string, array{count: int, locked_until: int, last_attempt: int}>
     */
    private function readAttempts(): array
    {
        $file = $this->attemptLogFile();
        if (!is_file($file)) {
            return [];
        }
        $data = json_decode((string) file_get_contents($file), true);

        return is_array($data) ? $data : [];
    }

    /**
     * Seconds remaining in the lockout for this IP, or 0 if not blocked.
     */
    private function lockoutRemaining(string $ip): int
    {
        $data = $this->readAttempts();
        $remaining = (int) ($data[$ip]['locked_until'] ?? 0) - time();

        return $remaining > 0 ? $remaining : 0;
    }

    private function recordFailedAttempt(string $ip): void
    {
        $data = $this->readAttempts();
        $entry = $data[$ip] ?? ['count' => 0, 'locked_until' => 0, 'last_attempt' => 0];
        $entry['count'] = (int) $entry['count'] + 1;
        $entry['last_attempt'] = time();
        if ($entry['count'] >= 5) {
            $entry['locked_until'] = time() + 300; // 5 minute lockout
            $entry['count'] = 0;
        }
        $data[$ip] = $entry;

        // Drop entries that have been idle for over a day AND are not
        // currently locked, so the log never grows forever.
        $now = time();
        foreach ($data as $key => $e) {
            $idle = $now - (int) ($e['last_attempt'] ?? 0);
            $lockedUntil = (int) ($e['locked_until'] ?? 0);
            if ($idle > 86400 && $lockedUntil < $now) {
                unset($data[$key]);
            }
        }

        @file_put_contents($this->attemptLogFile(), json_encode($data), LOCK_EX);
    }

    private function clearAttempts(string $ip): void
    {
        $data = $this->readAttempts();
        unset($data[$ip]);
        if ($data === []) {
            @unlink($this->attemptLogFile());
        } else {
            @file_put_contents($this->attemptLogFile(), json_encode($data), LOCK_EX);
        }
    }

    /* ---------- Change password ---------- */

    public function showPassword(): void
    {
        $this->requireAuth();

        $this->render('admin/password', [
            'title' => 'Change Password',
            'layout' => 'admin',
            'csrf' => Security::csrfToken(),
            'users' => AdminUserModel::all(),
        ]);
    }

    public function updatePassword(): void
    {
        $this->requireAuth();

        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect('/admin/password');
        }

        $current = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['confirm_password'] ?? '');
        $username = (string) ($_SESSION['admin_username'] ?? ADMIN_USERNAME);

        if (!$this->credentialsValid($username, $current)) {
            $this->setFlash('danger', 'Your current password is incorrect.');
            $this->redirect('/admin/password');
        }
        if (strlen($new) < 8) {
            $this->setFlash('danger', 'The new password must be at least 8 characters.');
            $this->redirect('/admin/password');
        }
        if (!hash_equals($new, $confirm)) {
            $this->setFlash('danger', 'The new passwords do not match.');
            $this->redirect('/admin/password');
        }

        $file = APP_ROOT . '/app/config/config.php';
        if (!is_writable($file)) {
            $this->setFlash('danger', 'The config file is not writable. Use: php app/scripts/admin-password.php "NewPassword"');
            $this->redirect('/admin/password');
        }

        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $content = (string) file_get_contents($file);

        // The pattern expects the ADMIN_PASSWORD_HASH define to stay on one line.
        if (preg_match("/define\('ADMIN_PASSWORD_HASH',\s*'[^']*'\);/", $content, $m)) {
            $newContent = str_replace($m[0], "define('ADMIN_PASSWORD_HASH', '" . $newHash . "');", $content);

            // Sanity check before touching the file: new hash present + valid PHP.
            if (strpos($newContent, $newHash) === false || !$this->isValidPhp($newContent)) {
                $this->setFlash('danger', 'Refusing to write an invalid config file. Use the CLI script instead.');
                $this->redirect('/admin/password');
            }

            // Atomic write (temp file + rename) so a crash can never leave a
            // truncated config.php, then drop any cached bytecode of the old file.
            $tmp = $file . '.tmp';
            if (@file_put_contents($tmp, $newContent, LOCK_EX) !== false && @rename($tmp, $file)) {
                if (function_exists('opcache_invalidate')) {
                    @opcache_invalidate($file, true);
                }
                $this->setFlash('success', 'Password updated. Use it on your next login.');
                $this->redirect('/admin');
                return;
            }
            @unlink($tmp);
        }

        $this->setFlash('danger', 'Could not update the password file. Use the CLI script instead.');
        $this->redirect('/admin/password');
    }

    /**
     * Best-effort PHP syntax check (skipped when exec() is unavailable).
     */
    private function isValidPhp(string $code): bool
    {
        if (!function_exists('exec')) {
            return true;
        }

        $tmp = tempnam(sys_get_temp_dir(), 'wlphp');
        if ($tmp === false) {
            return true;
        }
        @file_put_contents($tmp, $code);
        @exec('php -l ' . escapeshellarg($tmp) . ' 2>&1', $output, $exitCode);
        @unlink($tmp);

        return $exitCode === 0;
    }

    public function logout(): void
    {
        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect('/admin/login');
        }

        unset(
            $_SESSION['admin_logged_in'],
            $_SESSION['admin_username'],
            $_SESSION['admin_display_name'],
            $_SESSION['admin_email'],
            $_SESSION['admin_avatar'],
            $_SESSION['admin_auth_method'],
            $_SESSION['admin_last_activity']
        );
        session_regenerate_id(true);

        $this->setFlash('success', 'You have been logged out.');
        $this->redirect('/admin/login');
    }

    /* ---------- Dashboard overview ---------- */

    public function dashboard(): void
    {
        $this->requireAuth();

        $appointments = (new AppointmentModel())->all();
        $counts = $this->statusCounts($appointments);

        $qrPayments = (new QrPaymentModel())->all();
        $qrCounts = $this->qrCounts($qrPayments);

        $week = 0;
        $prevWeek = 0;
        $treatmentCounts = [];
        $weekStart = strtotime('-7 days');
        $prevWeekStart = strtotime('-14 days');

        // Per-day booking counts for the last 7 days (oldest first) so the
        // dashboard can draw a small activity chart.
        $activity = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = strtotime('-' . $i . ' days');
            $activity[date('Y-m-d', $day)] = [
                'label' => date('D', $day),
                'count' => 0,
                'pct' => 0,
            ];
        }

        foreach ($appointments as $a) {
            $created = strtotime((string) ($a['created_at'] ?? ''));
            if ($created !== false) {
                if ($created >= $weekStart) {
                    $week++;
                }
                if ($created >= $prevWeekStart && $created < $weekStart) {
                    $prevWeek++;
                }

                $dayKey = date('Y-m-d', $created);
                if (isset($activity[$dayKey])) {
                    $activity[$dayKey]['count']++;
                }
            }

            $treatment = trim((string) ($a['treatment'] ?? ''));
            if ($treatment !== '') {
                $treatmentCounts[$treatment] = ($treatmentCounts[$treatment] ?? 0) + 1;
            }
        }

        // Scale bars relative to the busiest day in the window.
        $maxActivity = 0;
        foreach ($activity as $day) {
            $maxActivity = max($maxActivity, (int) $day['count']);
        }
        foreach ($activity as $dayKey => $day) {
            $activity[$dayKey]['pct'] = $maxActivity > 0
                ? (int) round($day['count'] / $maxActivity * 100)
                : 0;
        }

        arsort($treatmentCounts);
        $popularTreatments = array_slice($treatmentCounts, 0, 3, true);

        $this->render('admin/dashboard', [
            'title' => 'Dashboard',
            'layout' => 'admin',
            'counts' => $counts,
            'week' => $week,
            'weekDelta' => $week - $prevWeek,
            'activity' => array_values($activity),
            'popularTreatments' => $popularTreatments,
            'recentAppointments' => array_slice($appointments, 0, 6),
            'recentQrPayments' => array_slice($qrPayments, 0, 5),
            'qrCounts' => $qrCounts,
            'sections' => $this->sectionSummaries(),
        ]);
    }

    /* ---------- Notifications ---------- */

    public function notificationsMarkAllRead(): void
    {
        $this->requireAuth();
        if (!$this->requireValidCsrf()) {
            $this->redirect('/admin');
        }
        NotificationModel::markAllRead();
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin');
    }

    public function notificationsMarkRead(string $id): void
    {
        $this->requireAuth();
        if (!$this->requireValidCsrf()) {
            $this->redirect('/admin');
        }
        NotificationModel::markRead($id);
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin');
    }

    /* ---------- Real-time notifications (AJAX) ---------- */

    /**
     * GET /admin/notifications/updates — returns new notifications as JSON.
     * The client polls this endpoint every few seconds for real-time updates.
     */
    public function notificationsUpdates(): void
    {
        if (empty($_SESSION['admin_logged_in'])) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Not authenticated']);
            exit;
        }

        $since = (int) ($_GET['since'] ?? 0);
        $all = NotificationModel::all();
        $unread = NotificationModel::unreadCount();

        // Filter notifications newer than the client's last known timestamp
        $new = [];
        if ($since > 0) {
            foreach ($all as $n) {
                $ts = strtotime((string) ($n['created_at'] ?? ''));
                if ($ts !== false && $ts > $since) {
                    $new[] = $n;
                }
            }
        }

        // Also get the current appointment/payment counts for the sidebar badges
        $apptNew = (new AppointmentModel())->countByStatus('new');
        $qrNew = self::qrNewCount();
        $reviewPending = (int) (TestimonialModel::statusCounts()['pending'] ?? 0);

        header('Content-Type: application/json');
        echo json_encode([
            'ok' => true,
            'unread' => $unread,
            'new' => array_slice($new, 0, 10),
            'appt_new' => $apptNew,
            'qr_new' => $qrNew,
            'review_pending' => $reviewPending,
            'time' => time(),
        ]);
        exit;
    }

    /* ---------- Cache control ---------- */

    /**
     * POST /admin/clear-cache — flush server-side caches so freshly edited
     * PHP/data files are served on the very next request.
     *
     * Browser-side caching is already handled automatically: HTML pages are
     * sent with no-cache headers and CSS/JS use versioned (?v=) URLs, so this
     * button mainly clears PHP opcache and the file-stat cache.
     */
    public function clearCache(): void
    {
        $this->requireAuth();

        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect('/admin');
            return;
        }

        $cleared = [];

        // Compiled PHP bytecode (opcache) — makes edited files take effect
        // immediately instead of whenever opcache next revalidates.
        if (function_exists('opcache_reset')) {
            @opcache_reset();
            $cleared[] = 'PHP opcache';
        }

        // PHP file-stat cache (filemtime, is_file, ...).
        clearstatcache(true);
        $cleared[] = 'file stat cache';

        $this->setFlash('success', 'Cache cleared (' . implode(', ', $cleared) . ').');
        $this->redirect('/admin');
    }

    /* ---------- Appointments (full list) ---------- */

    public function appointments(): void
    {
        $this->requireAuth();

        $model = new AppointmentModel();
        $appointments = $model->all();

        $filter = Security::sanitizeText($_GET['status'] ?? '');
        if (!in_array($filter, self::ALLOWED_STATUSES, true)) {
            $filter = '';
        }

        $viewAppointments = $filter === ''
            ? $appointments
            : array_values(array_filter(
                $appointments,
                static fn (array $a): bool => ($a['status'] ?? 'new') === $filter
            ));

        $this->render('admin/appointments', [
            'title' => 'Appointments',
            'layout' => 'admin',
            'appointments' => $viewAppointments,
            'counts' => $this->statusCounts($appointments),
            'filter' => $filter,
            'csrf' => Security::csrfToken(),
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $appointments
    * @return array{total: int, new: int, confirmed: int, rejected: int, completed: int}
     */
    private function statusCounts(array $appointments): array
    {
        $counts = [
            'total' => count($appointments),
            'new' => 0,
            'confirmed' => 0,
            'rejected' => 0,
            'completed' => 0,
        ];
        foreach ($appointments as $a) {
            $status = $a['status'] ?? 'new';
            if (isset($counts[$status])) {
                $counts[$status]++;
            }
        }

        return $counts;
    }

    /* ---------- QR advance payments ---------- */

    public function qrPayments(): void
    {
        $this->requireAuth();

        $model = new QrPaymentModel();
        $payments = $model->all();

        $filter = Security::sanitizeText($_GET['status'] ?? '');
        if (!in_array($filter, self::QR_STATUSES, true)) {
            $filter = '';
        }

        $viewPayments = $filter === ''
            ? $payments
            : array_values(array_filter(
                $payments,
                static fn (array $p): bool => ($p['status'] ?? 'new') === $filter
            ));

        $this->render('admin/qr_payments', [
            'title' => 'QR Advance Payments',
            'layout' => 'admin',
            'payments' => $viewPayments,
            'counts' => $this->qrCounts($payments),
            'filter' => $filter,
            'csrf' => Security::csrfToken(),
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $payments
     * @return array{total: int, new: int, verified: int, rejected: int}
     */
    private function qrCounts(array $payments): array
    {
        $counts = [
            'total' => count($payments),
            'new' => 0,
            'verified' => 0,
            'rejected' => 0,
        ];
        foreach ($payments as $p) {
            $status = $p['status'] ?? 'new';
            if (isset($counts[$status])) {
                $counts[$status]++;
            }
        }

        return $counts;
    }

    /**
     * Number of QR payments still waiting for review — used by the admin
     * navigation badge (and the dashboard).
     */
    public static function qrNewCount(): int
    {
        $pdo = Database::pdo();
        if ($pdo === null) {
            return 0;
        }

        try {
            return (int) $pdo->query("SELECT COUNT(*) FROM qr_payments WHERE status = 'new'")->fetchColumn();
        } catch (Throwable $e) {
            error_log('[AdminController] qrNewCount failed: ' . $e->getMessage());

            return 0;
        }
    }

    public function qrStatus(): void
    {
        $this->requireAuth();

        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect('/admin/qr-payments');
            return;
        }

        $id = Security::sanitizeText($_POST['id'] ?? '');
        $status = Security::sanitizeText($_POST['status'] ?? '');
        $filter = Security::sanitizeText($_POST['filter'] ?? '');

        if ($id !== '' && in_array($status, self::QR_STATUSES, true)) {
            $model = new QrPaymentModel();
            if ($model->updateStatus($id, $status)) {
                $this->setFlash('success', 'Payment status updated.');

                // Send email to customer when verified or rejected
                if (in_array($status, ['verified', 'rejected'], true)) {
                    $allPayments = $model->all();
                    foreach ($allPayments as $p) {
                        if (($p['id'] ?? '') === $id) {
                            Mailer::sendPaymentStatusEmail($p, $status);
                            break;
                        }
                    }
                }
            } else {
                $this->setFlash('danger', 'Could not update that payment.');
            }
        } else {
            $this->setFlash('danger', 'Could not update that payment.');
        }

        $this->redirect(in_array($filter, self::QR_STATUSES, true)
            ? '/admin/qr-payments?status=' . $filter
            : '/admin/qr-payments');
    }

    public function qrDelete(): void
    {
        $this->requireAuth();

        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect('/admin/qr-payments');
            return;
        }

        $id = Security::sanitizeText($_POST['id'] ?? '');
        $filter = Security::sanitizeText($_POST['filter'] ?? '');
        $deleted = false;

        if ($id !== '') {
            $model = new QrPaymentModel();

            // Remember the screenshot URL, delete the record first, and only
            // remove the file once the record is gone — so a failed delete
            // never leaves a record with a broken screenshot link.
            $screenshot = '';
            foreach ($model->all() as $p) {
                if (($p['id'] ?? '') === $id) {
                    $screenshot = (string) ($p['screenshot'] ?? '');
                    break;
                }
            }

            $deleted = $model->delete($id);
            if ($deleted && $screenshot !== '') {
                $this->deleteUploadedFile($screenshot);
            }
        }

        if ($deleted) {
            $this->setFlash('success', 'Payment record deleted.');
        } else {
            $this->setFlash('danger', 'Could not delete that payment.');
        }

        $this->redirect(in_array($filter, self::QR_STATUSES, true)
            ? '/admin/qr-payments?status=' . $filter
            : '/admin/qr-payments');
    }

    /* ---------- Customer Reviews ---------- */

    public function reviews(): void
    {
        $this->requireAuth();

        $filter = Security::sanitizeText($_GET['status'] ?? '');
        $all = TestimonialModel::allAdmin();

        // Filter by status if requested
        if ($filter !== '' && in_array($filter, ['pending', 'approved', 'rejected'], true)) {
            $all = array_filter($all, static fn (array $r): bool => ($r['status'] ?? 'pending') === $filter);
        }

        $counts = TestimonialModel::statusCounts();

        $this->render('admin/reviews', [
            'title' => 'Customer Reviews',
            'layout' => 'admin',
            'reviews' => array_values($all),
            'counts' => $counts,
            'filter' => $filter,
            'csrf' => Security::csrfToken(),
        ]);
    }

    public function reviewStatus(): void
    {
        $this->requireAuth();

        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect('/admin/reviews');
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $status = Security::sanitizeText($_POST['status'] ?? '');
        $filter = Security::sanitizeText($_POST['filter'] ?? '');

        if ($id > 0 && in_array($status, ['pending', 'approved', 'rejected'], true)) {
            if (TestimonialModel::updateStatus($id, $status)) {
                $this->setFlash('success', 'Review status updated to ' . $status . '.');
            } else {
                $this->setFlash('danger', 'Could not update review status.');
            }
        }

        $redirect = '/admin/reviews';
        if (in_array($filter, ['pending', 'approved', 'rejected'], true)) {
            $redirect .= '?status=' . $filter;
        }
        $this->redirect($redirect);
    }

    public function reviewDelete(): void
    {
        $this->requireAuth();

        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect('/admin/reviews');
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $filter = Security::sanitizeText($_POST['filter'] ?? '');

        if ($id > 0 && TestimonialModel::delete($id)) {
            $this->setFlash('success', 'Review deleted.');
        } else {
            $this->setFlash('danger', 'Could not delete that review.');
        }

        $redirect = '/admin/reviews';
        if (in_array($filter, ['pending', 'approved', 'rejected'], true)) {
            $redirect .= '?status=' . $filter;
        }
        $this->redirect($redirect);
    }

    /**
     * @return array<string, array{label: string, icon: string, count: int}>
     */
    private function sectionSummaries(): array
    {
        $sections = [];
        foreach (self::SECTIONS as $key => $section) {
            $sections[$key] = [
                'label' => $section['label'],
                'icon' => $section['icon'],
                'count' => count(call_user_func([$section['model'], $section['all']], false)),
            ];
        }

        return $sections;
    }

    /* ---------- Content management ---------- */

    /**
     * Dispatch /admin/content/* requests.
     * Routes:  /admin/content                → overview
     *          /admin/content/{section}      → list
     *          /admin/content/{section}/new  → create form
     *          /admin/content/{section}/edit → edit form (?id=N)
     *          /admin/content/{section}/save → POST create/update
     *          /admin/content/{section}/delete → POST delete
     */
    public function content(string $method, string $path): void
    {
        $this->requireAdmin();

        $rest = trim(substr($path, strlen('/admin/content')), '/');
        $segments = $rest === '' ? [] : explode('/', $rest);

        $sectionKey = $segments[0] ?? '';
        $sub = $segments[1] ?? '';

        if ($sectionKey === '') {
            $this->contentOverview();
            return;
        }

        if (!isset(self::SECTIONS[$sectionKey])) {
            $this->setFlash('danger', 'Unknown content section.');
            $this->redirect('/admin/content');
            return;
        }

        $section = self::SECTIONS[$sectionKey];

        switch ($sub) {
            case '':
                if ($method === 'GET') {
                    $this->contentList($sectionKey, $section);
                    return;
                }
                break;
            case 'new':
                if ($method === 'GET') {
                    $this->contentForm($sectionKey, $section, false);
                    return;
                }
                break;
            case 'edit':
                if ($method === 'GET') {
                    $this->contentForm($sectionKey, $section, true);
                    return;
                }
                break;
            case 'save':
                if ($method === 'POST') {
                    $this->saveContent($sectionKey, $section);
                    return;
                }
                break;
            case 'delete':
                if ($method === 'POST') {
                    $this->deleteContent($sectionKey, $section);
                    return;
                }
                break;
        }

        $this->setFlash('danger', 'Invalid request.');
        $this->redirect('/admin/content/' . $sectionKey);
    }

    private function contentOverview(): void
    {
        $this->render('admin/content', [
            'title' => 'Content',
            'layout' => 'admin',
            'sections' => $this->sectionSummaries(),
        ]);
    }

    private function contentList(string $key, array $section): void
    {
        $items = call_user_func([$section['model'], $section['all']], false);

        $this->render('admin/content_list', [
            'title' => $section['plural'],
            'layout' => 'admin',
            'sectionKey' => $key,
            'section' => $section,
            'items' => $items,
            'csrf' => Security::csrfToken(),
        ]);
    }

    private function contentForm(string $key, array $section, bool $isEdit): void
    {
        $item = null;
        if ($isEdit) {
            $id = (int) ($_GET['id'] ?? 0);
            $item = $id > 0 ? call_user_func([$section['model'], $section['find']], $id) : null;
            if ($item === null) {
                $this->setFlash('danger', 'Item not found.');
                $this->redirect('/admin/content/' . $key);
                return;
            }
        }

        $this->render('admin/content_form', [
            'title' => ($isEdit ? 'Edit ' : 'Add ') . $section['label'],
            'layout' => 'admin',
            'sectionKey' => $key,
            'section' => $section,
            'item' => $item,
            'isEdit' => $isEdit,
            'csrf' => Security::csrfToken(),
            'backUrl' => BASE_URL . '/admin/content/' . $key,
        ]);
    }

    private function saveContent(string $key, array $section): void
    {
        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token. Please try again.');
            $this->redirect('/admin/content/' . $key);
            return;
        }

        $data = [];
        $errors = [];
        $replacedImage = null;

        foreach ($section['fields'] as $field) {
            $name = $field['name'];
            $type = $field['type'] ?? 'text';

            switch ($type) {
                case 'checkbox':
                    $data[$name] = isset($_POST[$name]) ? 1 : 0;
                    break;
                case 'list':
                    $lines = preg_split('/\r\n|\r|\n/', (string) ($_POST[$name] ?? ''));
                    $lines = array_map('trim', $lines);
                    $lines = array_values(array_filter($lines, static fn (string $l): bool => $l !== ''));
                    $data[$name] = json_encode($lines, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    break;
                case 'methods':
                    $data[$name] = json_encode(
                        TherapyModel::methodsFromLines($_POST[$name] ?? ''),
                        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                    );
                    break;
                case 'number':
                    $data[$name] = max(0, (int) ($_POST[$name] ?? 0));
                    break;
                case 'image':
                    // Accept a pasted URL, or an uploaded file which takes priority.
                    $data[$name] = Security::sanitizeText($_POST[$name] ?? '');
                    if (!empty($_FILES[$name]['name'])) {
                        $data[$name] = $this->handleImageUpload($name, $key);
                        $replacedImage = $data[$name];
                    }
                    break;
                default:
                    $data[$name] = Security::sanitizeText($_POST[$name] ?? '');
            }

            if (!empty($field['required']) && $data[$name] === '') {
                $errors[] = $field['label'] . ' is required.';
            }
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $data['id'] = $id;
        }

        // Remember the previously stored image so we can clean it up from disk
        // once the replacement is saved.
        $previousImage = null;
        if ($replacedImage !== null && $id > 0) {
            $old = call_user_func([$section['model'], $section['find']], $id);
            if (is_array($old) && isset($old['image'])) {
                $previousImage = (string) $old['image'];
            }
        }

        if ($errors !== []) {
            $this->setFlash('danger', implode(' ', $errors));
            $this->render('admin/content_form', [
                'title' => ($id > 0 ? 'Edit ' : 'Add ') . $section['label'],
                'layout' => 'admin',
                'sectionKey' => $key,
                'section' => $section,
                'item' => $data,
                'isEdit' => $id > 0,
                'csrf' => Security::csrfToken(),
                'backUrl' => BASE_URL . '/admin/content/' . $key,
            ]);
            return;
        }

        if (call_user_func([$section['model'], $section['save']], $data)) {
            // The new image is saved; remove the old uploaded file (if any).
            if ($previousImage !== null) {
                $this->deleteUploadedFile($previousImage);
            }
            clearstatcache(true);
            $this->setFlash('success', $section['label'] . ' item saved.');
        } else {
            $lastErr = method_exists($section['model'], 'lastError') ? $section['model']::lastError() : '';
            $msg = 'Could not save.';
            if ($lastErr !== '') {
                $msg .= ' ' . $lastErr;
            } else {
                $msg .= ' Is the database running?';
            }
            $this->setFlash('danger', $msg);
        }

        $this->redirect('/admin/content/' . $key);
    }

    private function deleteContent(string $key, array $section): void
    {
        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect('/admin/content/' . $key);
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);

        // Remove any uploaded image from disk before deleting the row.
        $item = $id > 0 ? call_user_func([$section['model'], $section['find']], $id) : null;
        if (is_array($item) && isset($item['image'])) {
            $this->deleteUploadedFile((string) $item['image']);
        }

        if ($id > 0 && call_user_func([$section['model'], $section['delete']], $id)) {
            $this->setFlash('success', $section['label'] . ' item deleted.');
        } else {
            $this->setFlash('danger', 'Could not delete that item.');
        }

        $this->redirect('/admin/content/' . $key);
    }

    /* ---------- Page section editor (/admin/pages) ---------- */

    /**
     * Dispatch /admin/pages/* requests.
     * Routes: /admin/pages              → page overview
     *         /admin/pages/{page}       → section list for one page
     *         /admin/pages/{page}/{section}/edit → edit form
     *         /admin/pages/{page}/{section}/save → POST save
     *         /admin/pages/{page}/{section}/reset → POST reset to defaults
     */
    public function pages(string $method, string $path): void
    {
        $this->requireAdmin();

        $rest = trim(substr($path, strlen('/admin/pages')), '/');
        $segments = $rest === '' ? [] : explode('/', $rest);

        $pageKey = $segments[0] ?? '';
        $sectionKey = $segments[1] ?? '';
        $action = $segments[2] ?? '';

        if ($pageKey === '') {
            $this->pageOverview();
            return;
        }

        if (!isset(self::PAGE_SECTIONS[$pageKey])) {
            $this->setFlash('danger', 'Unknown page.');
            $this->redirect('/admin/pages');
            return;
        }

        $page = self::PAGE_SECTIONS[$pageKey];

        // About content items use the same add/edit/delete workflow as the
        // Therapies manager while remaining stored in page_sections.extras.
        if ($pageKey === 'about' && in_array($sectionKey, ['founder', 'approach', 'doctors', 'vision'], true)) {
            if ($action === 'items' && $method === 'GET') {
                $this->aboutItemList($sectionKey);
                return;
            }
            if ($action === 'item-new' && $method === 'GET') {
                $this->aboutItemForm($sectionKey, null);
                return;
            }
            if ($action === 'item-edit' && $method === 'GET') {
                $this->aboutItemForm($sectionKey, (int) ($_GET['index'] ?? -1));
                return;
            }
            if ($action === 'item-save' && $method === 'POST') {
                $this->saveAboutItem($sectionKey);
                return;
            }
            if ($action === 'item-delete' && $method === 'POST') {
                $this->deleteAboutItem($sectionKey);
                return;
            }
        }

        if ($sectionKey === '' && $method === 'GET') {
            $this->pageSectionList($pageKey, $page);
            return;
        }

        // ---- Custom sections (admin-added, stored as `custom-*` rows) ----
        // Add new:        /admin/pages/{page}/custom-new          (GET)
        // Save:           /admin/pages/{page}/custom-save         (POST)
        // Edit existing:  /admin/pages/{page}/custom-{id}/edit    (GET)
        // Save existing:  /admin/pages/{page}/custom-{id}/save    (POST)
        // Delete:         /admin/pages/{page}/custom-{id}/delete  (POST)
        if ($sectionKey === 'custom-new' && $method === 'GET') {
            $this->redirect('/admin/pages/' . $pageKey);
            return;
        }
        if ($sectionKey === 'custom-save' && $method === 'POST') {
            $this->saveCustomSection($pageKey);
            return;
        }
        if (str_starts_with($sectionKey, 'custom-')) {
            $customId = (int) substr($sectionKey, strlen('custom-'));
            $existing = $customId > 0 ? PageSectionModel::find($customId) : null;
            if ($existing === null || (string) $existing['page_key'] !== $pageKey
                || !str_starts_with((string) $existing['section_key'], 'custom-')) {
                $this->setFlash('danger', 'Unknown section.');
                $this->redirect('/admin/pages/' . $pageKey);
                return;
            }
            switch ($action) {
                case 'edit':
                    if ($method === 'GET') {
                        $this->customSectionForm($pageKey, $existing);
                        return;
                    }
                    break;
                case 'save':
                    if ($method === 'POST') {
                        $this->saveCustomSection($pageKey, $existing);
                        return;
                    }
                    break;
                case 'delete':
                    if ($method === 'POST') {
                        $this->deleteCustomSection($pageKey, $existing);
                        return;
                    }
                    break;
            }
            $this->setFlash('danger', 'Invalid request.');
            $this->redirect('/admin/pages/' . $pageKey);
            return;
        }

        if (!isset($page['sections'][$sectionKey])) {
            $this->setFlash('danger', 'Unknown section.');
            $this->redirect('/admin/pages/' . $pageKey);
            return;
        }

        switch ($action) {
            case 'edit':
                if ($method === 'GET') {
                    $this->pageSectionForm($pageKey, $sectionKey, $page['sections'][$sectionKey]);
                    return;
                }
                break;
            case 'save':
                if ($method === 'POST') {
                    $this->savePageSection($pageKey, $sectionKey, $page['sections'][$sectionKey]);
                    return;
                }
                break;
            case 'reset':
                if ($method === 'POST') {
                    $this->resetPageSection($pageKey, $sectionKey);
                    return;
                }
                break;
        }

        $this->setFlash('danger', 'Invalid request.');
        $this->redirect('/admin/pages/' . $pageKey);
    }

    private function pageOverview(): void
    {
        $pages = [];
        foreach (self::PAGE_SECTIONS as $key => $page) {
            $pages[$key] = [
                'label' => $page['label'],
                'url' => $page['url'],
                'sectionCount' => count($page['sections']),
            ];
        }

        $this->render('admin/pages', [
            'title' => 'Edit Pages',
            'layout' => 'admin',
            'pages' => $pages,
        ]);
    }

    /** @return array{label:string, fields:array<int, string>} */
    private function aboutItemDefinition(string $sectionKey): array
    {
        return match ($sectionKey) {
            'founder' => ['label' => 'Founder', 'fields' => ['name', 'role', 'image', 'bio']],
            'approach' => ['label' => 'Approach item', 'fields' => ['title', 'description', 'icon', 'image']],
            'doctors' => ['label' => 'Doctor', 'fields' => ['name', 'role', 'image']],
            'vision' => ['label' => 'Vision statement', 'fields' => ['title', 'text', 'icon']],
            default => ['label' => 'About item', 'fields' => []],
        };
    }

    /** @return array<int, array<string, string>> */
    private function aboutItems(string $sectionKey): array
    {
        $sections = PageSectionModel::forPage('about', false);
        $section = $sections[$sectionKey] ?? [];
        $items = [];
        if ($sectionKey === 'founder') {
            $first = array_pad(array_map('trim', explode('|', (string) ($section['sub_content'] ?? ''), 2)), 2, '');
            if ($first[0] !== '') {
                $items[] = ['name' => $first[0], 'role' => $first[1], 'image' => (string) ($section['image'] ?? ''), 'bio' => (string) ($section['content'] ?? '')];
            }
            foreach ((array) ($section['extras'] ?? []) as $line) {
                [$name, $role, $image, $bio] = array_pad(array_map('trim', explode('|', (string) $line, 4)), 4, '');
                $items[] = ['name' => $name, 'role' => $role, 'image' => $image, 'bio' => $bio];
            }
            return $items;
        }

        foreach ((array) ($section['extras'] ?? []) as $line) {
            $parts = array_pad(array_map('trim', explode('|', (string) $line)), 4, '');
            $items[] = match ($sectionKey) {
                'approach' => ['title' => $parts[0], 'description' => $parts[1], 'icon' => $parts[2], 'image' => $parts[3]],
                'doctors' => ['name' => $parts[0], 'role' => $parts[1], 'image' => $parts[2]],
                'vision' => ['title' => $parts[0], 'text' => $parts[1], 'icon' => $parts[2]],
                default => [],
            };
        }
        return $items;
    }

    private function aboutItemList(string $sectionKey): void
    {
        $definition = $this->aboutItemDefinition($sectionKey);
        $this->render('admin/about_items', [
            'title' => $definition['label'] . 's',
            'layout' => 'admin',
            'sectionKey' => $sectionKey,
            'definition' => $definition,
            'items' => $this->aboutItems($sectionKey),
            'csrf' => Security::csrfToken(),
        ]);
    }

    private function aboutItemForm(string $sectionKey, ?int $index): void
    {
        $definition = $this->aboutItemDefinition($sectionKey);
        $items = $this->aboutItems($sectionKey);
        $item = $index !== null && $index >= 0 ? ($items[$index] ?? null) : null;
        if ($index !== null && $index >= 0 && $item === null) {
            $this->setFlash('danger', 'About item not found.');
            $this->redirect('/admin/pages/about/' . $sectionKey . '/items');
            return;
        }
        $this->render('admin/about_item_form', [
            'title' => ($item === null ? 'Add ' : 'Edit ') . $definition['label'],
            'layout' => 'admin',
            'sectionKey' => $sectionKey,
            'definition' => $definition,
            'item' => $item ?? [],
            'index' => $index,
            'csrf' => Security::csrfToken(),
        ]);
    }

    private function saveAboutItem(string $sectionKey): void
    {
        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect('/admin/pages/about/' . $sectionKey . '/items');
            return;
        }
        $definition = $this->aboutItemDefinition($sectionKey);
        $items = $this->aboutItems($sectionKey);
        $index = (int) ($_POST['index'] ?? -1);
        $previousImage = $index >= 0 ? (string) ($items[$index]['image'] ?? '') : '';
        $item = [];
        foreach ($definition['fields'] as $field) {
            $item[$field] = Security::sanitizeText((string) ($_POST[$field] ?? ''));
        }
        if (!empty($_FILES['image']['name'])) {
            $item['image'] = $this->handleImageUpload('image', 'about-' . $sectionKey, '/admin/pages/about/' . $sectionKey . '/items');
        }
        $nameField = $sectionKey === 'doctors' || $sectionKey === 'founder' ? 'name' : 'title';
        if (trim((string) ($item[$nameField] ?? '')) === '') {
            $this->setFlash('danger', $definition['label'] . ' name is required.');
            $this->redirect('/admin/pages/about/' . $sectionKey . ($index >= 0 ? '/item-edit?index=' . $index : '/item-new'));
            return;
        }
        if ($index >= 0 && isset($items[$index])) {
            $items[$index] = $item;
        } else {
            $items[] = $item;
        }

        $sections = PageSectionModel::forPage('about', false);
        $section = $sections[$sectionKey] ?? [];
        $data = [
            'id' => (int) ($section['id'] ?? 0),
            'page_key' => 'about',
            'section_key' => $sectionKey,
            'kicker' => (string) ($section['kicker'] ?? ''),
            'heading' => (string) ($section['heading'] ?? ''),
            'content' => (string) ($section['content'] ?? ''),
            'sub_content' => (string) ($section['sub_content'] ?? ''),
            'image' => (string) ($section['image'] ?? ''),
            'extras' => [],
        ];
        if ($sectionKey === 'founder') {
            $first = array_shift($items) ?? ['name' => '', 'role' => '', 'image' => '', 'bio' => ''];
            $data['sub_content'] = trim($first['name'] . '|' . $first['role'], '|');
            $data['content'] = $first['bio'];
            $data['image'] = $first['image'];
        }
        foreach ($items as $aboutItem) {
            $data['extras'][] = match ($sectionKey) {
                'approach' => implode('|', [$aboutItem['title'], $aboutItem['description'], $aboutItem['icon'], $aboutItem['image']]),
                'doctors' => implode('|', [$aboutItem['name'], $aboutItem['role'], $aboutItem['image']]),
                'vision' => implode('|', [$aboutItem['title'], $aboutItem['text'], $aboutItem['icon']]),
                'founder' => implode('|', [$aboutItem['name'], $aboutItem['role'], $aboutItem['image'], $aboutItem['bio']]),
                default => '',
            };
        }
        if (PageSectionModel::save($data)) {
            if ($previousImage !== '' && $previousImage !== (string) ($item['image'] ?? '')) {
                $this->deleteUploadedFile($previousImage);
            }
            $this->setFlash('success', $definition['label'] . ' saved.');
        } else {
            $this->setFlash('danger', 'Could not save the ' . strtolower($definition['label']) . '.');
        }
        $this->redirect('/admin/pages/about/' . $sectionKey . '/items');
    }

    private function deleteAboutItem(string $sectionKey): void
    {
        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect('/admin/pages/about/' . $sectionKey . '/items');
            return;
        }
        $index = (int) ($_POST['index'] ?? -1);
        $items = $this->aboutItems($sectionKey);
        if (!isset($items[$index])) {
            $this->setFlash('danger', 'About item not found.');
            $this->redirect('/admin/pages/about/' . $sectionKey . '/items');
            return;
        }
        array_splice($items, $index, 1);
        $deletedImage = (string) ($this->aboutItems($sectionKey)[$index]['image'] ?? '');
        $_POST['index'] = -1;
        $_POST['name'] = $items[0]['name'] ?? '';
        $_POST['role'] = $items[0]['role'] ?? '';
        $_POST['image'] = $items[0]['image'] ?? '';
        $_POST['bio'] = $items[0]['bio'] ?? '';
        // Reuse the same normalized writer without accepting a new upload.
        $this->saveAboutItemFromItems($sectionKey, $items, $deletedImage);
    }

    /** @param array<int, array<string, string>> $items */
    private function saveAboutItemFromItems(string $sectionKey, array $items, string $deletedImage = ''): void
    {
        $sections = PageSectionModel::forPage('about', false);
        $section = $sections[$sectionKey] ?? [];
        $data = ['id' => (int) ($section['id'] ?? 0), 'page_key' => 'about', 'section_key' => $sectionKey, 'kicker' => (string) ($section['kicker'] ?? ''), 'heading' => (string) ($section['heading'] ?? ''), 'content' => (string) ($section['content'] ?? ''), 'sub_content' => (string) ($section['sub_content'] ?? ''), 'image' => (string) ($section['image'] ?? ''), 'extras' => []];
        if ($sectionKey === 'founder') {
            $first = array_shift($items) ?? ['name' => '', 'role' => '', 'image' => '', 'bio' => ''];
            $data['sub_content'] = trim($first['name'] . '|' . $first['role'], '|');
            $data['content'] = $first['bio'];
            $data['image'] = $first['image'];
        }
        foreach ($items as $aboutItem) {
            $data['extras'][] = match ($sectionKey) {
                'approach' => implode('|', [$aboutItem['title'], $aboutItem['description'], $aboutItem['icon'], $aboutItem['image']]),
                'doctors' => implode('|', [$aboutItem['name'], $aboutItem['role'], $aboutItem['image']]),
                'vision' => implode('|', [$aboutItem['title'], $aboutItem['text'], $aboutItem['icon']]),
                'founder' => implode('|', [$aboutItem['name'], $aboutItem['role'], $aboutItem['image'], $aboutItem['bio']]),
                default => '',
            };
        }
        if (PageSectionModel::save($data)) {
            if ($deletedImage !== '') {
                $this->deleteUploadedFile($deletedImage);
            }
            $this->setFlash('success', $this->aboutItemDefinition($sectionKey)['label'] . ' deleted.');
        } else {
            $this->setFlash('danger', 'Could not delete the item.');
        }
        $this->redirect('/admin/pages/about/' . $sectionKey . '/items');
    }

    private function pageSectionList(string $pageKey, array $page): void
    {
        $sections = PageSectionModel::forPage($pageKey, false);
        $previews = [];
        foreach ($page['sections'] as $sectionKey => $section) {
            $data = $sections[$sectionKey] ?? [];
            $previews[$sectionKey] = [
                'label' => $section['label'],
                'heading' => (string) ($data['heading'] ?? ''),
                'content' => (string) ($data['content'] ?? ''),
                'kicker' => (string) ($data['kicker'] ?? ''),
                'image' => (string) ($data['image'] ?? ''),
            ];
        }

        // Custom (admin-added) sections stored as custom-* rows.
        $customs = [];
        foreach ($sections as $sectionKey => $data) {
            if (!str_starts_with($sectionKey, 'custom-')) { continue; }
            $csType = (string) ($data['sub_content'] ?? 'custom');
            $customs[$sectionKey] = [
                'id' => (int) ($data['id'] ?? 0),
                'heading' => (string) ($data['heading'] ?? ''),
                'kicker' => (string) ($data['kicker'] ?? ''),
                'content' => (string) ($data['content'] ?? ''),
                'boxCount' => count((array) ($data['extras'] ?? [])),
                'typeLabel' => self::CUSTOM_SECTION_TYPES[$csType]['label'] ?? 'Custom',
            ];
        }

        $this->render('admin/page_sections', [
            'title' => 'Edit ' . $page['label'] . ' Page',
            'layout' => 'admin',
            'pageKey' => $pageKey,
            'page' => $page,
            'previews' => $previews,
            'customs' => $customs,
            'csrf' => Security::csrfToken(),
        ]);
    }

    private function pageSectionForm(string $pageKey, string $sectionKey, array $section): void
    {
        $sections = PageSectionModel::forPage($pageKey, false);
        $item = $sections[$sectionKey] ?? [];
        if (isset($item['id'])) {
            unset($item['id']);
        }

        $this->render('admin/content_form', [
            'title' => 'Edit ' . $section['label'],
            'layout' => 'admin',
            'sectionKey' => $pageKey . '/' . $sectionKey,
            'section' => $section,
            'item' => $item,
            'isEdit' => true,
            'csrf' => Security::csrfToken(),
            'formAction' => BASE_URL . '/admin/pages/' . $pageKey . '/' . $sectionKey . '/save',
            'backUrl' => BASE_URL . '/admin/pages/' . $pageKey,
        ]);
    }

    private function savePageSection(string $pageKey, string $sectionKey, array $section): void
    {
        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token. Please try again.');
            $this->redirect('/admin/pages/' . $pageKey);
            return;
        }

        $data = [];
        $errors = [];
        $replacedImage = null;

        foreach ($section['fields'] as $field) {
            $name = $field['name'];
            $type = $field['type'] ?? 'text';

            switch ($type) {
                case 'list':
                    $lines = preg_split('/\r\n|\r|\n/', (string) ($_POST[$name] ?? ''));
                    $lines = array_map('trim', $lines);
                    $lines = array_values(array_filter($lines, static fn (string $l): bool => $l !== ''));
                    $data[$name] = json_encode($lines, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    break;
                case 'image':
                    $data[$name] = Security::sanitizeText($_POST[$name] ?? '');
                    if (!empty($_FILES[$name]['name'])) {
                        $data[$name] = $this->handleImageUpload($name, $pageKey, '/admin/pages/' . $pageKey . '/' . $sectionKey . '/edit');
                        $replacedImage = $data[$name];
                    }
                    break;
                default:
                    $data[$name] = Security::sanitizeText($_POST[$name] ?? '');
            }

            if (!empty($field['required']) && trim((string) $data[$name]) === '') {
                $errors[] = $field['label'] . ' is required.';
            }
        }

        $previousImage = null;
        if ($replacedImage !== null) {
            $current = PageSectionModel::forPage($pageKey, false);
            if (isset($current[$sectionKey]['image'])) {
                $previousImage = (string) $current[$sectionKey]['image'];
            }
        }

        if ($errors !== []) {
            $this->setFlash('danger', implode(' ', $errors));
            $this->redirect('/admin/pages/' . $pageKey . '/' . $sectionKey . '/edit');
            return;
        }

        // Find the existing row id (if any) so we update rather than duplicate.
        $existing = PageSectionModel::forPage($pageKey, false);
        if (isset($existing[$sectionKey]['id'])) {
            $data['id'] = (int) $existing[$sectionKey]['id'];
        }
        $data['page_key'] = $pageKey;
        $data['section_key'] = $sectionKey;

        if (PageSectionModel::save($data)) {
            if ($previousImage !== null) {
                $this->deleteUploadedFile($previousImage);
            }
            clearstatcache(true);
            $this->setFlash('success', 'Section "' . $section['label'] . '" saved.');
        } else {
            $errorDetail = PageSectionModel::lastError();
            $msg = 'Could not save.';
            if ($errorDetail !== '') {
                $msg .= ' ' . $errorDetail;
            } else {
                $msg .= ' Is the database running?';
            }
            $this->setFlash('danger', $msg);
        }

        $this->redirect('/admin/pages/' . $pageKey);
    }

    private function resetPageSection(string $pageKey, string $sectionKey): void
    {
        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect('/admin/pages/' . $pageKey);
            return;
        }

        $existing = PageSectionModel::forPage($pageKey, false);
        $id = (int) ($existing[$sectionKey]['id'] ?? 0);
        $image = (string) ($existing[$sectionKey]['image'] ?? '');
        if ($id > 0 && PageSectionModel::delete($id)) {
            // Remove any uploaded image so it doesn't linger orphaned on disk.
            if ($image !== '' && str_starts_with($image, BASE_URL . '/public/uploads/')) {
                $this->deleteUploadedFile($image);
            }
            $this->setFlash('success', 'Section reset to its default content.');
        } else {
            $this->setFlash('danger', 'Could not reset that section.');
        }

        $this->redirect('/admin/pages/' . $pageKey);
    }

    /* ---------- Custom (admin-added) page sections ---------- */

    /**
     * The kinds of section that can be added from the page editor.
     * Each type has its own line format for the "boxes" list and its own
     * look on the live page. `sub_content` stores the chosen type slug.
     */
    private const CUSTOM_SECTION_TYPES = [
        'founder' => [
            'label' => 'Founder(s)',
            'icon' => 'icon-users',
            'headingDefault' => 'Our Founders',
            'extrasLabel' => 'Founders',
            'hint' => 'Add founders with separate name, role, photo URL or upload, and full bio fields. Clicking the box on the site opens the full bio.',
        ],
        'approach' => [
            'label' => 'Our Approach',
            'icon' => 'icon-leaf',
            'headingDefault' => 'Our Approach',
            'extrasLabel' => 'Approach items',
            'hint' => 'One item per line, format: Title | Description | Icon | Photo URL — icon and photo optional (leaf icon used by default).',
        ],
        'doctors' => [
            'label' => 'Our Doctors',
            'icon' => 'icon-star',
            'headingDefault' => 'Our Doctors',
            'extrasLabel' => 'Doctors',
            'hint' => 'One doctor per line, format: Name | Role | Photo URL — photo optional (initials avatar shown without one).',
        ],
        'group' => [
            'label' => 'Our Group (Team)',
            'icon' => 'icon-users',
            'headingDefault' => 'Our Group',
            'extrasLabel' => 'Team members',
            'hint' => 'One member per line, format: Name | Role | Photo URL | Short bio — photo and bio optional.',
        ],
        'vision' => [
            'label' => 'Vision / Statement',
            'icon' => 'icon-sun',
            'headingDefault' => 'Our Vision',
            'extrasLabel' => 'Statements',
            'hint' => 'One statement per line, format: Title | Full text | Icon — shown as a wide card; the full text opens in the popup.',
        ],
        'custom' => [
            'label' => 'Custom (free-form)',
            'icon' => 'icon-layout',
            'headingDefault' => 'Our Facilities',
            'extrasLabel' => 'Boxes',
            'hint' => 'One box per line, format: Title | Details | Photo URL — the general-purpose choice; boxes with photos show them, others show a leaf icon.',
        ],
    ];

    /**
     * "What do you want to add?" page — lets the admin pick the kind of
     * section (Founder, Our Approach, Our Doctors, …) before the form opens.
     */
    private function customSectionTypeChooser(string $pageKey): void
    {
        $this->render('admin/custom_section_types', [
            'title' => 'Add Section',
            'layout' => 'admin',
            'pageKey' => $pageKey,
            'pageLabel' => self::PAGE_SECTIONS[$pageKey]['label'],
            'types' => self::CUSTOM_SECTION_TYPES,
        ]);
    }

    /**
     * Form for creating (row === null) or editing a custom section.
     * The chosen type controls the field labels, hints and live-page look.
     */
    private function customSectionForm(string $pageKey, ?array $row, string $type = 'custom'): void
    {
        $typeDef = self::CUSTOM_SECTION_TYPES[$type] ?? self::CUSTOM_SECTION_TYPES['custom'];

        $item = [];
        $customId = 0;
        if ($row !== null) {
            $customId = (int) $row['id'];
            $type = (string) ($row['sub_content'] ?? '');
            if (!isset(self::CUSTOM_SECTION_TYPES[$type])) { $type = 'custom'; }
            $typeDef = self::CUSTOM_SECTION_TYPES[$type];
            $item = [
                'heading' => (string) $row['heading'],
                'kicker' => (string) $row['kicker'],
                'content' => (string) $row['content'],
                'extras' => PageSectionModel::decodeExtras((string) $row['extras']),
            ];
            if ($type === 'founder') {
                $item['founders'] = [];
                foreach ((array) $item['extras'] as $line) {
                    $parts = array_pad(array_map('trim', explode('|', (string) $line, 4)), 4, '');
                    $item['founders'][] = [
                        'name' => $parts[0],
                        'role' => $parts[1],
                        'image' => $parts[2],
                        'bio' => $parts[3],
                    ];
                }
            }
        }

        $fields = [
            ['name' => 'heading', 'label' => 'Section heading', 'type' => 'text', 'required' => true, 'hint' => 'The heading visitors will see, for example: Meet Our Founders.'],
            ['name' => 'kicker', 'label' => 'Small label', 'type' => 'text', 'hint' => 'Optional label above the heading, for example: 07 · Our Founders.'],
            ['name' => 'content', 'label' => 'Short introduction', 'type' => 'textarea', 'hint' => 'Optional introduction shown above the founder boxes.'],
            ['name' => $type === 'founder' ? 'founders' : 'extras', 'label' => $type === 'founder' ? 'Founder details' : $typeDef['extrasLabel'], 'type' => $type === 'founder' ? 'founders' : 'list', 'required' => true, 'hint' => $typeDef['hint']],
        ];

        $this->render('admin/content_form', [
            'title' => ($customId > 0 ? 'Edit ' : 'Add ') . $typeDef['label'],
            'layout' => 'admin',
            'section' => ['label' => $typeDef['label'] . ' Section', 'fields' => $fields],
            'item' => $item,
            'isEdit' => $customId > 0,
            'formAction' => BASE_URL . '/admin/pages/' . rawurlencode($pageKey) . '/custom-save?type=' . rawurlencode($type),
            'backUrl' => BASE_URL . '/admin/pages/' . rawurlencode($pageKey),
            'csrf' => Security::csrfToken(),
        ]);
    }

    /**
     * Validate + persist a custom section. When editing, $existing carries
     * the current row (its id and section_key are preserved).
     */
    private function saveCustomSection(string $pageKey, ?array $existing = null): void
    {
        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token. Please try again.');
            $this->redirect('/admin/pages/' . $pageKey);
            return;
        }

        // The section type is stored in `sub_content` (new: from the hidden
        // field / query, edit: from the existing row so it can never drift).
        if ($existing !== null) {
            $type = (string) ($existing['sub_content'] ?? '');
        } else {
            $type = (string) ($_POST['section_type'] ?? ($_GET['type'] ?? 'custom'));
        }
        if (!isset(self::CUSTOM_SECTION_TYPES[$type])) { $type = 'custom'; }
        $typeDef = self::CUSTOM_SECTION_TYPES[$type];

        $heading = Security::sanitizeText((string) ($_POST['heading'] ?? ''));
        $errors = [];
        if ($heading === '') {
            $errors[] = 'Section title is required.';
        }

        $lines = [];
        if ($type === 'founder') {
            $names = (array) ($_POST['founder_name'] ?? []);
            $roles = (array) ($_POST['founder_role'] ?? []);
            $imageUrls = (array) ($_POST['founder_image_url'] ?? []);
            $bios = (array) ($_POST['founder_bio'] ?? []);
            $rowCount = max(count($names), count($roles), count($imageUrls), count($bios));
            for ($i = 0; $i < $rowCount; $i++) {
                $name = Security::sanitizeText((string) ($names[$i] ?? ''));
                $role = Security::sanitizeText((string) ($roles[$i] ?? ''));
                $image = Security::sanitizeText((string) ($imageUrls[$i] ?? ''));
                $bio = Security::sanitizeText((string) ($bios[$i] ?? ''));
                $uploadField = 'founder_image_' . $i;
                if (!empty($_FILES[$uploadField]['name'])) {
                    $image = $this->handleImageUpload($uploadField, $pageKey, '/admin/pages/' . $pageKey . '/custom-new?type=founder');
                }
                if ($name === '' && $role === '' && $image === '' && $bio === '') {
                    continue;
                }
                $lines[] = implode('|', [$name, $role, $image, $bio]);
            }
        } else {
            $lines = preg_split('/\r\n|\r|\n/', (string) ($_POST['extras'] ?? ''));
            $lines = array_map('trim', $lines ?: []);
            $lines = array_values(array_filter($lines, static fn (string $l): bool => $l !== ''));
        }
        if ($lines === []) {
            $errors[] = 'At least one ' . strtolower($typeDef['extrasLabel'] === 'Boxes' ? 'box' : rtrim(strtolower($typeDef['extrasLabel']), 's')) . ' is required.';
        }

        if ($errors !== []) {
            $this->setFlash('danger', implode(' ', $errors));
            $this->redirect($existing !== null
                ? '/admin/pages/' . $pageKey . '/custom-' . (int) $existing['id'] . '/edit'
                : '/admin/pages/' . $pageKey . '/custom-new?type=' . rawurlencode($type));
            return;
        }

        $data = [
            'page_key' => $pageKey,
            'section_key' => $existing !== null
                ? (string) $existing['section_key']
                : 'custom-' . date('YmdHis'),
            'heading' => $heading,
            'kicker' => Security::sanitizeText((string) ($_POST['kicker'] ?? '')),
            'content' => Security::sanitizeText((string) ($_POST['content'] ?? '')),
            'sub_content' => $type,
            'extras' => json_encode($lines, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];
        if ($existing !== null) {
            $data['id'] = (int) $existing['id'];
        }

        if (PageSectionModel::save($data)) {
            $this->setFlash('success', $existing !== null ? 'Section updated.' : $typeDef['label'] . ' section added to the page.');
        } else {
            $errorDetail = PageSectionModel::lastError();
            $msg = 'Could not save.';
            if ($errorDetail !== '') { $msg .= ' ' . $errorDetail; }
            $this->setFlash('danger', $msg);
        }

        $this->redirect('/admin/pages/' . $pageKey);
    }

    /**
     * Delete a custom section row entirely.
     */
    private function deleteCustomSection(string $pageKey, array $existing): void
    {
        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect('/admin/pages/' . $pageKey);
            return;
        }

        if (PageSectionModel::delete((int) $existing['id'])) {
            $this->setFlash('success', 'Section "' . (string) $existing['heading'] . '" deleted.');
        } else {
            $this->setFlash('danger', 'Could not delete that section.');
        }

        $this->redirect('/admin/pages/' . $pageKey);
    }

    /* ---------- Admin user management (/admin/users) ---------- */

    /**
     * Dispatch /admin/users requests.
     * Routes: /admin/users           → list
     *         /admin/users/new       → create form
     *         /admin/users/edit?id=N → edit form
     *         /admin/users/save      → POST create/update
     *         /admin/users/delete    → POST delete
     */
    public function users(string $method, string $path): void
    {
        $this->requireAdmin();

        $rest = trim(substr($path, strlen('/admin/users')), '/');
        $segments = $rest === '' ? [] : explode('/', $rest);
        $sub = $segments[0] ?? '';

        switch ($sub) {
            case '':
                if ($method === 'GET') {
                    $this->userList();
                    return;
                }
                break;
            case 'new':
                if ($method === 'GET') {
                    $this->userForm(false);
                    return;
                }
                break;
            case 'edit':
                if ($method === 'GET') {
                    $this->userForm(true);
                    return;
                }
                break;
            case 'save':
                if ($method === 'POST') {
                    $this->saveUser();
                    return;
                }
                break;
            case 'delete':
                if ($method === 'POST') {
                    $this->deleteUser();
                    return;
                }
                break;
        }

        $this->setFlash('danger', 'Invalid request.');
        $this->redirect('/admin/users');
    }

    private function userList(): void
    {
        $this->render('admin/users', [
            'title' => 'Admin Users',
            'layout' => 'admin',
            'users' => AdminUserModel::all(),
            'csrf' => Security::csrfToken(),
        ]);
    }

    private function userForm(bool $isEdit): void
    {
        $user = null;
        if ($isEdit) {
            $id = (int) ($_GET['id'] ?? 0);
            $user = $id > 0 ? AdminUserModel::find($id) : null;
            if ($user === null) {
                $this->setFlash('danger', 'User not found.');
                $this->redirect('/admin/users');
                return;
            }
        }

        $this->render('admin/user_form', [
            'title' => ($isEdit ? 'Edit ' : 'Add ') . 'Admin User',
            'layout' => 'admin',
            'user' => $user,
            'isEdit' => $isEdit,
            'csrf' => Security::csrfToken(),
            'users' => AdminUserModel::all(),
        ]);
    }

    private function saveUser(): void
    {
        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token. Please try again.');
            $this->redirect('/admin/users');
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $email = strtolower(Security::sanitizeEmail($_POST['email'] ?? ''));
        $username = strtolower(Security::sanitizeText($_POST['username'] ?? ''));
        $displayName = Security::sanitizeText($_POST['display_name'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['confirm_password'] ?? '');
        $role = in_array($_POST['role'] ?? '', ['admin', 'staff'], true) ? $_POST['role'] : 'admin';

        $errors = [];

        // Email is required and must be valid.
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }

        // Auto-generate username from email if not provided.
        if ($username === '') {
            $username = str_replace(['.', '+'], ['_', ''], explode('@', $email)[0]);
        }
        if (!preg_match('/^[a-z0-9_\-.]+$/', $username) || strlen($username) < 3 || strlen($username) > 40) {
            $errors[] = 'Username must be 3–40 characters using letters, numbers, dashes or underscores.';
        }

        $changingPassword = $id === 0 || $password !== '';
        if ($changingPassword) {
            if (strlen($password) < 8) {
                $errors[] = 'The password must be at least 8 characters.';
            }
            if (!hash_equals($password, $confirm)) {
                $errors[] = 'The passwords do not match.';
            }
        }

        // Email must be unique.
        $existingEmail = AdminUserModel::findByEmail($email);
        if ($existingEmail !== null && (int) $existingEmail['id'] !== $id) {
            $errors[] = 'That email address is already registered.';
        }

        // Username must be unique.
        $existing = AdminUserModel::findByUsername($username);
        if ($existing !== null && (int) $existing['id'] !== $id) {
            $errors[] = 'That username is already taken.';
        }

        if ($errors !== []) {
            $this->setFlash('danger', implode(' ', $errors));
            $this->redirect($id > 0 ? '/admin/users/edit?id=' . $id : '/admin/users/new');
            return;
        }

        $passwordHash = $changingPassword ? password_hash($password, PASSWORD_DEFAULT) : null;

        error_log('[saveUser] Saving: email=' . $email . ' username=' . $username . ' role=' . $role . ' hash_len=' . strlen((string) $passwordHash));

        $ok = $id > 0
            ? AdminUserModel::update($id, $email, $username, $passwordHash, $displayName, $role)
            : AdminUserModel::create($email, $username, (string) $passwordHash, $displayName, $role);

        if ($ok) {
            error_log('[saveUser] OK: id=' . $ok);
            // Verify the user can be found immediately
            $check = AdminUserModel::findByEmail($email);
            error_log('[saveUser] Verification: findByEmail=' . ($check ? 'FOUND (hash_len=' . strlen((string)($check['password_hash'] ?? '')) . ')' : 'NOT FOUND!'));
            $this->setFlash('success', $id > 0 ? 'User updated.' : 'User created.');
        } else {
            error_log('[saveUser] FAILED');
            $this->setFlash('danger', 'Could not save the user. Is the database running?');
        }

        // Redirect back to the form page so the user sees the list below.
        $this->redirect($id > 0 ? '/admin/users/edit?id=' . $id : '/admin/users/new');
    }

    private function deleteUser(): void
    {
        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect('/admin/users');
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);

        // Never allow deleting the account you are currently signed in with.
        $current = strtolower((string) ($_SESSION['admin_username'] ?? ''));
        $target = $id > 0 ? AdminUserModel::find($id) : null;
        if ($target === null) {
            $this->setFlash('danger', 'User not found.');
            $this->redirect('/admin/users');
            return;
        }
        if ($current !== '' && strtolower((string) $target['username']) === $current) {
            $this->setFlash('danger', 'You cannot delete the account you are signed in with.');
            $this->redirect('/admin/users');
            return;
        }

        if (AdminUserModel::delete($id)) {
            $this->setFlash('success', 'User deleted.');
        } else {
            $this->setFlash('danger', 'Could not delete that user.');
        }

        $this->redirect('/admin/users');
    }

    /**
     * Save an uploaded image into public/uploads/ and return its public URL.
     * Redirects back with a danger flash if the file is invalid.
     */
    private function handleImageUpload(string $fieldName, string $sectionKey, string $redirect = ''): string
    {
        $maxBytes = 5 * 1024 * 1024; // 5 MB
        $tmp = (string) ($_FILES[$fieldName]['tmp_name'] ?? '');
        $size = (int) ($_FILES[$fieldName]['size'] ?? 0);

        $redirect = $redirect !== '' ? $redirect : '/admin/content/' . $sectionKey;

        if (($_FILES[$fieldName]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
            || $tmp === '' || $size <= 0 || $size > $maxBytes) {
            if (($_FILES[$fieldName]['error'] ?? 0) === UPLOAD_ERR_INI_SIZE) {
                $this->setFlash('danger', 'Upload rejected: the file exceeds the server upload limit (upload_max_filesize).');
            } else {
                $this->setFlash('danger', 'Upload rejected: file is missing or larger than 5 MB.');
            }
            $this->redirect($redirect);
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($tmp);
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];
        if (!isset($allowed[$mime]) || @getimagesize($tmp) === false) {
            $this->setFlash('danger', 'Upload rejected: only JPG, PNG, WEBP and GIF images are allowed.');
            $this->redirect($redirect);
        }

        // Find a writable directory for the upload
        $filename = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
        $dir = $this->findUploadDir();
        if ($dir === '') {
            error_log('[Admin] Upload: no writable directory found');
            $this->setFlash('danger', 'Upload failed: could not find a writable uploads folder.');
            $this->redirect($redirect);
        }
        $dest = $dir . '/' . $filename;

        // Multi-strategy upload (handles open_basedir restrictions on shared hosting)
        $ok = @move_uploaded_file($tmp, $dest);
        if (!$ok) {
            $ok = @copy($tmp, $dest);
            if ($ok) @unlink($tmp);
        }
        if (!$ok) {
            $content = @file_get_contents($tmp);
            if ($content !== false) {
                $ok = @file_put_contents($dest, $content) !== false;
                if ($ok) @unlink($tmp);
            }
        }
        if (!$ok) {
            error_log('[Admin] Upload failed for ' . $filename);
            $this->setFlash('danger', 'Upload failed: could not save the image. Try a smaller file.');
            $this->redirect($redirect);
        }

        @chmod($dest, 0644);

        // Auto-optimize: convert to WebP to save bandwidth and storage.
        $dest = ImageOptimizer::optimize($dest);
        $filename = basename($dest);

        // Build the public URL based on where the file actually ended up.
        $publicRoot = APP_ROOT . '/public';
        if (str_starts_with($dir, $publicRoot)) {
            // $dir = /var/www/public/uploads → /public/uploads
            return BASE_URL . '/public' . substr($dir, strlen($publicRoot)) . '/' . $filename;
        }
        return $dir . '/' . $filename;
    }

    /**
     * Find a writable uploads directory, creating if needed.
     *
     * @return string absolute path, or '' on failure
     */
    private function findUploadDir(): string
    {
        $candidates = [
            APP_ROOT . '/public/uploads',
            APP_ROOT . '/storage',
        ];

        foreach ($candidates as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
                if (!is_dir($dir)) {
                    @mkdir($dir, 0777);
                }
            }
            if (!is_dir($dir)) {
                continue;
            }
            if (!is_writable($dir)) {
                @chmod($dir, 0777);
            }
            if (!is_writable($dir)) {
                continue;
            }
            // Verify we can actually write
            $test = $dir . '/.write-test-' . bin2hex(random_bytes(4));
            if (@file_put_contents($test, 'ok') !== false) {
                @unlink($test);
                return $dir;
            }
        }

        return '';
    }

    /**
     * Delete a file that lives inside public/uploads/ (if any).
     */
    private function deleteUploadedFile(string $url): void
    {
        $prefix = BASE_URL . '/public/uploads/';
        if (!str_starts_with($url, $prefix)) {
            return;
        }

        // Preserve any subfolder in the URL (e.g. uploads/qr-payments/...),
        // but never allow path traversal outside public/uploads/.
        $relative = str_replace('\\', '/', substr($url, strlen($prefix)));
        if (str_contains($relative, '..')) {
            return;
        }

        $file = APP_ROOT . '/public/uploads/' . $relative;
        if (is_file($file)) {
            @unlink($file);
        }
    }

    /* ---------- Appointment actions ---------- */

    public function delete(): void
    {
        $this->requireAuth();

        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect('/admin');
        }

        $id = Security::sanitizeText($_POST['id'] ?? '');
        $redirect = $this->filterRedirect();

        if ($id !== '' && (new AppointmentModel())->delete($id)) {
            $this->setFlash('success', 'Appointment deleted.');
        } else {
            $this->setFlash('danger', 'Could not delete that appointment.');
        }

        $this->redirect($redirect);
    }

    public function updateStatus(): void
    {
        $this->requireAuth();

        if (!$this->requireValidCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect('/admin');
        }

        $id = Security::sanitizeText($_POST['id'] ?? '');
        $status = Security::sanitizeText($_POST['status'] ?? '');
        $redirect = $this->filterRedirect();

        if ($id !== '' && in_array($status, self::ALLOWED_STATUSES, true)) {
            $model = new AppointmentModel();
            if ($model->updateStatus($id, $status)) {
                $this->setFlash('success', 'Appointment status updated.');

                // Send email to customer when confirmed, rejected, or completed.
                if (in_array($status, ['confirmed', 'rejected', 'completed'], true)) {
                    $allAppts = $model->all();
                    foreach ($allAppts as $a) {
                        if (($a['id'] ?? '') === $id) {
                            Mailer::sendAppointmentStatusEmail($a, $status);
                            break;
                        }
                    }
                }
            } else {
                $this->setFlash('danger', 'Could not update that appointment.');
            }
        } else {
            $this->setFlash('danger', 'Could not update that appointment.');
        }

        $this->redirect($redirect);
    }

    /**
     * Keep the active status filter after an action (e.g. ?status=new).
     */
    private function filterRedirect(): string
    {
        $filter = Security::sanitizeText($_POST['filter'] ?? '');
        return in_array($filter, self::ALLOWED_STATUSES, true)
            ? '/admin/appointments?status=' . $filter
            : '/admin/appointments';
    }
}
