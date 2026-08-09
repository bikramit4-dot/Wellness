<?php
/**
 * "Why Choose Us" feature cards shown on the home page.
 * Managed from /admin/content/features (falls back to this file only when
 * MySQL is unavailable or the table is missing).
 *
 * @return array<int, array<string, mixed>>
 */
return [
    [
        'title' => 'Expert Healthcare Team',
        'description' => 'Doctors, physiotherapists, yoga instructors, and nutritionists collaborate for complete care.',
        'image' => BASE_URL . '/public/uploads/photos/photo-1576091160399-112ba8d25d1d.jpg',
        'icon' => 'icon-users',
    ],
    [
        'title' => 'Personalized Treatment Plans',
        'description' => 'Every patient receives a custom program based on their health condition and lifestyle.',
        'image' => BASE_URL . '/public/uploads/photos/photo-1544161515-4ab6ce6db874.jpg',
        'icon' => 'icon-heart',
    ],
    [
        'title' => 'Natural Healing Methods',
        'description' => "We focus on drug-free treatments that activate the body's own self-healing abilities.",
        'image' => BASE_URL . '/public/uploads/photos/photo-1519823551278-64ac92734fb1.jpg',
        'icon' => 'icon-leaf',
    ],
    [
        'title' => 'Modern Facilities',
        'description' => 'Modern therapy equipment blends seamlessly with tried-and-tested traditional methods.',
        'image' => BASE_URL . '/public/uploads/photos/photo-1506126613408-eca07ce68773.jpg',
        'icon' => 'icon-shield',
    ],
    [
        'title' => 'Holistic Wellness',
        'description' => 'We nurture your physical, mental, emotional, and spiritual balance together.',
        'image' => BASE_URL . '/public/uploads/photos/photo-1545205597-3d9d02c29597.jpg',
        'icon' => 'icon-sun',
    ],
];
