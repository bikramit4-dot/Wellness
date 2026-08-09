<?php
/**
 * Tariff page content: membership-style plans and the individual
 * service rate card. Seeded into the `tariff_plans` and
 * `tariff_services` tables by app/scripts/seed.php.
 */
return [
    'plans' => [
        [
            'name' => 'Daily Wellness',
            'description' => 'Perfect for busy days — drop in for a single therapy session.',
            'price' => '$25',
            'period' => 'session',
            'features' => [
                'Any single therapy of your choice',
                'Complimentary consultation',
                'Flexible scheduling',
            ],
            'featured' => false,
            'badge' => '',
            'ctaLabel' => 'Book a Session',
        ],
        [
            'name' => '7-Day Wellness Retreat',
            'description' => 'A full immersive week of healing, detox, and daily therapies.',
            'price' => '$140',
            'period' => 'week',
            'features' => [
                'Daily therapy sessions',
                'Yoga & meditation classes',
                'Personalized diet plan',
                'Priority scheduling',
            ],
            'featured' => true,
            'badge' => 'Most Popular',
            'ctaLabel' => 'Book the Retreat',
        ],
        [
            'name' => '30-Day Transformation',
            'description' => 'A month-long guided program for lasting lifestyle change.',
            'price' => '$480',
            'period' => 'month',
            'features' => [
                'Weekly consultations',
                'Full therapy schedule',
                'Nutrition & fitness coaching',
                'Progress tracking & reports',
            ],
            'featured' => false,
            'badge' => '',
            'ctaLabel' => 'Start Your Journey',
        ],
    ],
    'services' => [
        ['service' => 'Initial Consultation', 'duration' => '30 Minutes', 'price' => '$20'],
        ['service' => 'Yoga Session', 'duration' => '1 Hour', 'price' => '$15'],
        ['service' => 'Physiotherapy', 'duration' => '1 Hour', 'price' => '$25'],
        ['service' => 'Acupuncture', 'duration' => '45 Minutes', 'price' => '$30'],
        ['service' => 'Naturopathy Treatment', 'duration' => '1 Hour', 'price' => '$35'],
        ['service' => 'Diet Consultation', 'duration' => '45 Minutes', 'price' => '$20'],
        ['service' => 'Special Therapy', 'duration' => '1 Hour', 'price' => '$40'],
    ],
];
