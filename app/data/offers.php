<?php
/**
 * "What We Offer" service cards shown on the home page.
 * Managed from /admin/content/offers (falls back to this file only when
 * MySQL is unavailable or the table is missing).
 *
 * Note: use plain apostrophes here — Security::e() HTML-escapes on render,
 * so HTML entities like &rsquo; would show up literally.
 *
 * @return array<int, array<string, mixed>>
 */
return [
    [
        'title' => 'Naturopathy',
        'description' => 'Natural treatments that detoxify the body, boost immunity, and restore balance.',
        'image' => BASE_URL . '/public/uploads/photos/photo-1466692476868-aef1dfb1e735.jpg',
        'icon' => 'icon-leaf',
        'link' => '/treatments',
    ],
    [
        'title' => 'Yoga & Meditation',
        'description' => 'Improve flexibility, mental peace, and emotional balance through mindful practice.',
        'image' => BASE_URL . '/public/uploads/photos/photo-1545205597-3d9d02c29597.jpg',
        'icon' => 'icon-moon',
        'link' => '/treatments',
    ],
    [
        'title' => 'Physiotherapy',
        'description' => 'Restore mobility, strength, and physical function after injury or illness.',
        'image' => BASE_URL . '/public/uploads/photos/photo-1576091160399-112ba8d25d1d.jpg',
        'icon' => 'icon-activity',
        'link' => '/physiotherapy',
    ],
    [
        'title' => 'Diet Therapy',
        'description' => 'Nutritional programs crafted for healing, detoxification, and healthy living.',
        'image' => BASE_URL . '/public/uploads/photos/photo-1512621776951-a57141f2eefd.jpg',
        'icon' => 'icon-droplet',
        'link' => '/diet-therapy',
    ],
    [
        'title' => 'Special Therapies',
        'description' => 'Unique therapies for relaxation, detoxification, and disease management.',
        'image' => BASE_URL . '/public/uploads/photos/photo-1571019613454-1cb2f99b2d8b.jpg',
        'icon' => 'icon-sun',
        'link' => '/special-therapies',
    ],
    [
        'title' => 'Acupuncture',
        'description' => 'Targeted stimulation to restore energy flow, relieve pain, and calm the mind.',
        'image' => BASE_URL . '/public/uploads/photos/photo-1512290923902-8a9f81dc236c.jpg',
        'icon' => 'icon-zap',
        'link' => '/treatments',
    ],
];
