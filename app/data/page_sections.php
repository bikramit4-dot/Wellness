<?php
/**
 * Default content for every editable section of every public page.
 *
 * Structure: [page_key][section_key] => fields
 *   kicker      — eyebrow / small label above the heading
 *   heading     — the h1 / h2 / h3
 *   content     — main paragraph or short text
 *   sub_content — lede, badge, or secondary text (some views split on "|")
 *   image       — photo path or URL
 *   media       — video file name inside public/uploads/ (hero)
 *   link        — button URL / map query
 *   link_label  — button label
 *   extras      — list of lines (checklists, stats, buttons, cards).
 *                 Buttons use the format "Label|URL|style" (style: primary,
 *                 light, dark, outline). Cards use "Title|Text|icon".
 *
 * Seeded into the `page_sections` table by app/scripts/seed.php and used as
 * the fallback whenever MySQL is unavailable.
 */

/* ---------- Site-wide settings ---------- */
$site = [
    'brand' => [
        'heading' => 'Chitrawan Nature Cure Hospital',
        'image' => '', // empty → leaf icon + name; upload a logo to replace it
    ],
    // Top bar information (editable from Admin → Pages → Site Settings → Top Bar).
    //   link=phone · sub_content=email · kicker=opening hours
    // Navigation menu (editable from Admin → Pages → Site Settings →
    // Navigation Menu). Each line: Label | URL. When the list is empty the
    // header falls back to the standard menu with therapy dropdowns
    // auto-generated from the `therapies` table (admin Content → Therapies).
    'nav' => [
        'extras' => [],
    ],
    'topbar' => [
        'link' => '+977 56-535213',
        'sub_content' => 'nchchitwan@gmail.com',
        'kicker' => 'Sun – Fri: 8:00 AM – 7:00 PM',
    ],
    // Footer information (editable from Admin → Pages → Site Settings →
    // Footer Information). Reuses the shared section columns:
    //   content=tagline · heading=address · sub_content=email · link=phone ·
    //   kicker=opening hours · link_label=copyright tagline ·
    //   extras=social links (one "Label | URL" per line)
    'footer' => [
        'content' => 'Where ancient healing traditions meet modern therapeutic practices. We help you achieve physical, mental, and emotional balance through personalized natural care.',
        'heading' => 'Chitrawan Nature Cure Hospital, Bharatpur-15, Nepal',
        'sub_content' => 'nchchitwan@gmail.com',
        'link' => '+977 56-535213',
        'kicker' => "Sun – Fri: 8 AM – 7 PM\nSat: 9 AM – 4 PM",
        'link_label' => 'Healing naturally, living fully.',
        'extras' => [
            'Facebook | #',
            'Instagram | #',
            'TikTok | #',
        ],
    ],
];

/* ---------- Home ---------- */
$home = [
    'hero' => [
        'kicker' => 'Natural Healing · Holistic Care',
        'heading' => 'Rejuvenate Your Body, Mind & Soul',
        'content' => 'Welcome to Chitrawan Nature Cure Hospital, where ancient healing traditions meet modern therapeutic practices. Our team helps you achieve physical, mental, and emotional balance with personalized natural care.',
        'sub_content' => '4.9/5 rated by 1,200+ happy patients',
        'media' => 'Olive and White Modern Spa and Wellness Banner Landscape.mp4',
        'extras' => [
            'Book an Appointment|/contact|primary',
            'Explore Our Programs|/treatments|light',
        ],
    ],
    'stats' => [
        'extras' => [
            '15+|Years of Experience',
            '10K+|Happy Patients',
            '25+|Expert Therapists',
            '40+|Natural Therapies',
        ],
    ],
    'about_intro' => [
        'kicker' => 'About Chitrawan',
        'heading' => 'Healing Naturally, Living Fully',
        'content' => 'Chitrawan Nature Cure Hospital is a holistic healthcare destination committed to promoting natural healing and preventive healthcare. Our peaceful environment, expert practitioners, and evidence-based therapies help patients restore balance and improve their quality of life.',
        'sub_content' => 'Since 2010|Healing with care & compassion',
        'image' => BASE_URL . '/public/uploads/photos/photo-1545205597-3d9d02c29597.jpg',
        'link' => '/about',
        'link_label' => 'More About Us',
        'extras' => [
            'Treating the root cause, not just symptoms',
            'Personalized plans for every patient',
            'Traditional wisdom meets modern science',
            'A calm, natural environment for healing',
        ],
    ],
    'why' => [
        'kicker' => 'Why Choose Us',
        'heading' => 'Care That Puts You First',
        'content' => 'Reasons patients trust us with their wellness journey.',
    ],
    'offers' => [
        'kicker' => 'What We Offer',
        'heading' => 'Our Core Services',
        'content' => 'A complete range of natural therapies, designed to support your health at every stage.',
    ],
    'testimonials' => [
        'kicker' => 'Patient Stories',
        'heading' => 'What Our Patients Say',
        'content' => 'Real experiences from people who found their balance with us.',
    ],
    'story' => [
        'kicker' => 'Our Story',
        'heading' => 'A Journey of Healing, Growing with Every Patient',
        'content' => "Chitrawan Nature Cure Hospital was born from a simple belief: true health comes from within. What began as a small clinic with two therapists has grown into a trusted wellness destination, guided every step of the way by the thousands of patients who trusted us with their care.\n\nEvery therapy we offer, every space we design, and every team member we welcome reflects the same promise — to treat you as family and walk beside you on your path to balance.",
        'sub_content' => '12+ Years|Of natural healing',
        'image' => BASE_URL . '/public/uploads/photos/photo-1512621776951-a57141f2eefd.jpg',
        'link' => '/about',
        'link_label' => 'Read Our Full Story',
        'extras' => [
            'Founded on natural, drug-free healing',
            'Thousands of patients helped since 2010',
            'A caring team that treats you like family',
        ],
    ],
    'cta' => [
        'kicker' => 'Start Your Journey',
        'heading' => 'Ready to Begin Your Wellness Journey?',
        'content' => 'Book an appointment today and take the first step toward a healthier, more balanced you.',
        'extras' => [
            'Book an Appointment|/contact|primary',
            'View Tariff|/tariff|light',
        ],
    ],
];

/* ---------- About ---------- */
$about = [
    'hero' => [
        'heading' => 'About Us',
        'content' => 'Meet the people and philosophy behind Chitrawan Nature Cure Hospital — where natural healing is a way of life.',
    ],
    'founder' => [
        'kicker' => '01 · Our Founders',
        'heading' => 'Meet Our Founders',
        'sub_content' => 'Dr. Rajesh Sharma|Founder & Holistic Medicine Specialist',
        'content' => "Dr. Rajesh Sharma established Chitrawan Nature Cure Hospital with the vision of creating a healthcare facility that promotes natural healing and healthy living. With more than twenty years of experience in holistic medicine, he believes prevention and lifestyle changes are the keys to long-term wellness.\n\nHis mission is to help people heal naturally and live healthier lives through personalized care and compassionate treatment.",
        'image' => BASE_URL . '/public/uploads/photos/photo-1576091160399-112ba8d25d1d.jpg',
        'extras' => [
            'Dr. Sunita Sharma|Co-Founder & Yoga Therapy Director|' . BASE_URL . '/public/uploads/photos/photo-1545205597-3d9d02c29597.jpg|Dr. Sunita Sharma co-founded Chitrawan Nature Cure Hospital with a vision of combining classical yoga therapy with modern natural medicine. She leads the yoga and mindfulness programs and has guided thousands of patients toward calmer, healthier lives.',
        ],
    ],
    'approach' => [
        'kicker' => '02 · Our Approach',
        'heading' => 'Treating the Root Cause',
        'content' => 'Our approach focuses on treating the root cause of diseases rather than simply managing symptoms. We believe true healing occurs when the body, mind, and spirit are in harmony.',
        'image' => BASE_URL . '/public/uploads/photos/photo-1544161515-4ab6ce6db874.jpg',
        'extras' => [
            'Natural healing without harmful side effects',
            'Prevention of disease through lifestyle change',
            'Personalized care for every individual',
            'Healthy nutrition as the foundation of health',
            'Physical, mental, and emotional wellness',
        ],
    ],
    'doctors' => [
        'kicker' => '03 · Our Doctors',
        'heading' => 'Our Doctors & Specialists',
        'content' => 'A multidisciplinary team working together for your complete care.',
        'extras' => [
            'Dr. Meena Gurung|Naturopathy Specialist|' . BASE_URL . '/public/uploads/photos/photo-1543269865-cbf427effbad.jpg',
            'Anil Thapa|Senior Physiotherapist|' . BASE_URL . '/public/uploads/photos/photo-1571019613454-1cb2f99b2d8b.jpg',
            'Sita Adhikari|Yoga Therapist|' . BASE_URL . '/public/uploads/photos/photo-1512290923902-8a9f81dc236c.jpg',
            'Nirmala K.C.|Nutritionist|' . BASE_URL . '/public/uploads/photos/photo-1498837167922-ddd27525d352.jpg',
            'Prakash Shrestha|Wellness Consultant|' . BASE_URL . '/public/uploads/photos/photo-1466692476868-aef1dfb1e735.jpg',
        ],
    ],
    'vision' => [
        'kicker' => '06 · Our Vision',
        'heading' => 'Our Vision',
        'image' => BASE_URL . '/public/uploads/photos/photo-1519823551278-64ac92734fb1.jpg',
        'extras' => [
            'Where We Are Headed|To become a leading center for holistic healthcare by promoting natural healing, preventive medicine, and healthy living practices worldwide.|icon-sun',
        ],
    ],
    'mission' => [
        'kicker' => '04 · Our Mission',
        'heading' => 'Our Mission',
        'content' => 'The promises we make to every patient who walks through our doors.',
        'sub_content' => 'What We Are Committed To',
        'image' => BASE_URL . '/public/uploads/photos/photo-1506126613408-eca07ce68773.jpg',
        'extras' => [
            'Provide high-quality natural healthcare services',
            'Promote preventive healthcare and wellness education',
            'Empower individuals to take control of their health',
            'Create awareness about healthy living',
            'Combine traditional wisdom with modern science',
        ],
    ],
    'group' => [
        'kicker' => '05 · Our Group',
        'heading' => 'Our Group',
        'content' => 'The people who make Chitrawan Nature Cure Hospital a place of healing.',
        'image' => BASE_URL . '/public/uploads/photos/photo-1544367567-0f2fcb009e0b.jpg',
    ],
    'cta' => [
        'kicker' => 'Work With Us',
        'heading' => 'Experience Care That Listens',
        'content' => 'Meet our doctors and begin a wellness plan designed around you.',
        'extras' => [
            'Book an Appointment|/contact|primary',
            'Our Treatments|/treatments|light',
        ],
    ],
];

/* ---------- Contact ---------- */
$contact = [
    'hero' => [
        'heading' => 'Contact Us',
        'content' => 'We would love to hear from you. Reach out or book your appointment today.',
    ],
    'info' => [
        'kicker' => 'Reach Us',
        'heading' => 'Contact Information',
        'extras' => [
            'Our Address|Chitrawan Nature Cure Hospital, Bharatpur-15, Nepal',
            'Phone|+977 56-535213',
            'Email|nchchitwan@gmail.com',
            'Opening Hours|Sunday – Friday: 8:00 AM – 7:00 PM
Saturday: 9:00 AM – 4:00 PM',
        ],
    ],
    'form' => [
        'heading' => 'Book an Appointment',
        'content' => 'Fill in your details and our team will confirm your booking shortly.',
    ],
    'map' => [
        'kicker' => 'Find Us',
        'heading' => 'Our Location',
        'content' => 'Chitrawan Nature Cure Hospital · Bharatpur-15, Nepal',
        'link' => 'Chitrawan Nature Cure Hospital, Bharatpur-15, Nepal',
    ],
];

/* ---------- Category / list pages ---------- */
$treatments = [
    'hero' => [
        'heading' => 'Treatments',
        'content' => 'Explore our signature natural therapies, each designed to help your body heal itself.',
    ],
    'cta' => [
        'kicker' => 'Not Sure Where to Start?',
        'heading' => 'Talk to Our Wellness Team',
        'content' => 'Get a free consultation and let us recommend the right therapy for you.',
        'extras' => [
            'Book a Consultation|/contact|primary',
            'View Tariff|/tariff|light',
        ],
    ],
];
$physiotherapy = [
    'hero' => [
        'heading' => 'Physiotherapy',
        'content' => 'Rebuild strength, restore mobility, and live pain-free with expert-guided therapy.',
    ],
    'cta' => [
        'kicker' => 'Move Better, Live Better',
        'heading' => 'Restore Your Body’s Full Potential',
        'content' => 'Book a physiotherapy assessment and get a personalized recovery plan.',
        'extras' => [
            'Book a Session|/contact|primary',
            'View Tariff|/tariff|light',
        ],
    ],
];
$diet = [
    'hero' => [
        'heading' => 'Diet Therapy',
        'content' => 'Food is medicine. Discover nutritional programs tailored to your body and goals.',
    ],
    'cta' => [
        'kicker' => 'Food Is Medicine',
        'heading' => 'Let Nutrition Transform Your Health',
        'content' => 'Get a personalized diet plan built around your body, lifestyle, and goals.',
        'extras' => [
            'Book a Consultation|/contact|primary',
            'View Tariff|/tariff|light',
        ],
    ],
];
$special = [
    'hero' => [
        'heading' => 'Special Therapies',
        'content' => 'Unique, time-honored therapies for relaxation, detoxification, and disease management.',
    ],
    'cta' => [
        'kicker' => 'Something Unique',
        'heading' => 'Find the Therapy That Fits You',
        'content' => 'Ask our team about special programs and personalized treatment plans.',
        'extras' => [
            'Ask Our Team|/contact|primary',
            'View Tariff|/tariff|light',
        ],
    ],
];
$tariff = [
    'hero' => [
        'heading' => 'Tariff',
        'content' => 'Transparent pricing and flexible packages for every wellness journey.',
    ],
    'packages' => [
        'kicker' => 'Pricing',
        'heading' => 'Wellness Packages',
        'content' => 'Simple, transparent pricing for every stage of your journey.',
    ],
    'services' => [
        'kicker' => 'Rate Card',
        'heading' => 'Individual Service Prices',
        'content' => 'All sessions include a personal assessment with your therapist.',
        'sub_content' => 'Prices are indicative. Please contact us for the latest offers and package discounts.',
    ],
    // Advance payment by QR code (editable from Admin → Pages → Tariff →
    // QR Code & Advance Payment). image = the QR code patients scan; the form
    // next to it is fixed, but the heading/text/steps are editable.
    'qr' => [
        'kicker' => 'Advance Payment',
        'heading' => 'Pay Your Advance by QR Code',
        'content' => 'Secure your package or appointment with a small advance payment. Scan the QR code with any UPI payment app, then submit your details with the payment screenshot below.',
        'sub_content' => 'UPI ID: chitrawan naturecure@upi',
        'image' => '', // upload the QR code image from the admin panel
        'link_label' => 'Keep your payment reference number — it helps us verify your payment faster.',
        'extras' => [
            'Scan the QR code with your UPI / payment app',
            'Enter the advance amount shown in the package above',
            'Fill in the form with your details and attach the payment screenshot',
        ],
    ],
];
$gallery = [
    'hero' => [
        'heading' => 'Gallery',
        'content' => 'A glimpse into our healing spaces, therapies, and community.',
    ],
];
$blog = [
    'hero' => [
        'heading' => 'Blog',
        'content' => 'Insights, tips, and stories to support your path to better health.',
    ],
    'cta' => [
        'kicker' => 'Have a Question?',
        'heading' => 'Get Advice from Our Team',
        'content' => 'Our specialists are happy to answer your wellness questions in person or by phone.',
        'extras' => [
            'Contact Us|/contact|primary',
        ],
    ],
];

/* ---------- Dynamic detail pages (shared sections) ---------- */
$therapy = [
    'cta' => [
        'kicker' => 'Ready When You Are',
        'heading' => 'Begin Your {title} Journey',
        'content' => 'Book a session today — our team will guide you every step of the way.',
        'extras' => [
            'Book an Appointment|/contact|primary',
            'All {catLabel}|{catUrl}|light',
        ],
    ],
];
$post = [
    'cta' => [
        'kicker' => 'Put It Into Practice',
        'heading' => 'Ready to Feel the Difference?',
        'content' => 'Talk to our wellness team about a program tailored to you — from yoga and nutrition to physiotherapy and natural detox.',
        'extras' => [
            'Book a Consultation|/contact|primary',
            'More Articles|/blog|light',
        ],
    ],
];

return [
    'site' => $site,
    'home' => $home,
    'about' => $about,
    'contact' => $contact,
    'treatments' => $treatments,
    'physiotherapy' => $physiotherapy,
    'diet' => $diet,
    'special' => $special,
    'tariff' => $tariff,
    'gallery' => $gallery,
    'blog' => $blog,
    'therapy' => $therapy,
    'post' => $post,
];
