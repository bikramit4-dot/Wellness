<?php
/**
 * All individual therapy pages.
 * Each entry is keyed by URL slug and grouped by category path.
 */
return [

    /* ============================ TREATMENTS ============================ */
    'naturopathy' => [
        'title' => 'Naturopathy',
        'category' => '/treatments',
        'categoryLabel' => 'Treatments',
        'icon' => 'icon-leaf',
        'image' => BASE_URL . '/public/uploads/photos/photo-1519823551278-64ac92734fb1.jpg',
        'intro' => 'A natural healthcare system that helps your body heal itself through gentle, drug-free methods.',
        'about' => [
            'Naturopathy is a complete system of natural medicine built on the belief that the body has an innate ability to heal itself. Instead of suppressing symptoms, our naturopaths work to remove the causes of illness and restore balance through water, earth, heat, herbs, and wholesome food.',
            'Every program begins with a detailed health assessment, after which our team designs a personal treatment plan combining therapies that detoxify, strengthen immunity, and re-energize your body&rsquo;s own healing power.',
        ],
        'methods' => ['Hydrotherapy', 'Mud therapy', 'Herbal therapy', 'Massage therapy', 'Steam therapy', 'Detoxification programs'],
        'benefits' => ['Supports the body&rsquo;s natural self-healing', 'Helps detoxify and cleanse the system', 'Strengthens immunity and vitality', 'Reduces dependence on medication', 'Improves long-term energy balance'],
    ],

    'yoga-therapy' => [
        'title' => 'Yoga Therapy',
        'category' => '/treatments',
        'categoryLabel' => 'Treatments',
        'icon' => 'icon-moon',
        'image' => BASE_URL . '/public/uploads/photos/photo-1545205597-3d9d02c29597.jpg',
        'intro' => 'Physical postures, breathing, and meditation woven together to restore body and mind.',
        'about' => [
            'Yoga therapy applies the ancient science of yoga as a personalized therapeutic tool. Led by certified instructors, sessions combine gentle postures, controlled breathing, and guided meditation to address everything from stress and posture to chronic pain.',
            'Whether you are a beginner or an experienced practitioner, each session is adapted to your body and goals — helping you move with ease, breathe more freely, and find a calmer, more balanced state of mind.',
        ],
        'methods' => ['Hatha Yoga', 'Pranayama (breathwork)', 'Guided meditation', 'Stress management', 'Flexibility training', 'Weight management'],
        'benefits' => ['Increases flexibility and strength', 'Calms the mind and reduces anxiety', 'Improves breathing and circulation', 'Supports healthy weight management', 'Cultivates lasting inner peace'],
    ],

    'acupuncture' => [
        'title' => 'Acupuncture',
        'category' => '/treatments',
        'categoryLabel' => 'Treatments',
        'icon' => 'icon-zap',
        'image' => BASE_URL . '/public/uploads/photos/photo-1544161515-4ab6ce6db874.jpg',
        'intro' => 'Fine needles and precise points that restore the flow of energy and ease pain.',
        'about' => [
            'Acupuncture is a time-honored therapy that stimulates specific points on the body to restore the smooth flow of energy (Qi). Thin, sterile needles are gently inserted at these points, encouraging the body to release tension, reduce inflammation, and switch on its own healing response.',
            'Our practitioners use a gentle, modern approach to acupuncture, making each session comfortable and effective — whether you come for persistent pain, stress, or simply to rebalance your system.',
        ],
        'methods' => ['Fine needle therapy', 'Moxibustion (heat therapy)', 'Cupping therapy', 'Electro-acupuncture', 'Meridian assessment'],
        'benefits' => ['Effective relief from back and neck pain', 'Eases migraines and tension headaches', 'Calms anxiety and improves sleep', 'Supports joint health in arthritis', 'Restores smooth energy flow'],
    ],

    /* ============================ PHYSIOTHERAPY ============================ */
    'physical-therapy' => [
        'title' => 'Physical Therapy',
        'category' => '/physiotherapy',
        'categoryLabel' => 'Physiotherapy',
        'icon' => 'icon-activity',
        'image' => BASE_URL . '/public/uploads/photos/photo-1571019613454-1cb2f99b2d8b.jpg',
        'intro' => 'Guided exercise and manual therapy that rebuild strength, mobility, and function.',
        'about' => [
            'Physical therapy helps you move better, feel stronger, and live without pain. Our physiotherapists assess how your body moves, then guide you through targeted exercises and hands-on techniques that restore strength, flexibility, and confidence.',
            'From recovering after an injury to improving everyday movement, every plan is personal — so you progress safely and see lasting results.',
        ],
        'methods' => ['Manual therapy', 'Therapeutic exercises', 'Stretching and mobility work', 'Gait and balance training', 'Home exercise programs'],
        'benefits' => ['Restores strength and mobility', 'Reduces pain and stiffness', 'Improves posture and balance', 'Speeds recovery from injury', 'Prevents future problems'],
    ],

    'electrotherapy' => [
        'title' => 'Electrotherapy',
        'category' => '/physiotherapy',
        'categoryLabel' => 'Physiotherapy',
        'icon' => 'icon-zap',
        'image' => BASE_URL . '/public/uploads/photos/photo-1576091160399-112ba8d25d1d.jpg',
        'intro' => 'Modern electrical stimulation that reduces pain and accelerates the body\'s healing.',
        'about' => [
            'Electrotherapy uses safe, targeted electrical currents to relieve pain, reduce swelling, and stimulate muscles and tissues that need to heal. It is a highly effective complement to hands-on therapy for both acute injuries and chronic conditions.',
            'Our therapists choose the most suitable modality for your condition and adjust intensity to keep every session comfortable and effective.',
        ],
        'methods' => ['TENS for pain relief', 'Ultrasound therapy', 'Interferential therapy', 'Muscle stimulation', 'Heat and cold modalities'],
        'benefits' => ['Quick, effective pain relief', 'Reduces swelling and inflammation', 'Stimulates muscle recovery', 'Accelerates tissue healing', 'Drug-free treatment option'],
    ],

    'rehabilitation-therapy' => [
        'title' => 'Rehabilitation Therapy',
        'category' => '/physiotherapy',
        'categoryLabel' => 'Physiotherapy',
        'icon' => 'icon-heart',
        'image' => BASE_URL . '/public/uploads/photos/photo-1571019613454-1cb2f99b2d8b.jpg',
        'intro' => 'Structured programs that guide you back to full function after injury, surgery, or illness.',
        'about' => [
            'Rehabilitation therapy is your roadmap back to an active life. Whether you are recovering from surgery, a sports injury, a stroke, or a neurological condition, our therapists build a step-by-step program that rebuilds strength, balance, and independence.',
            'We track your progress closely and adjust the plan as you improve — so you recover at the right pace, without setbacks.',
        ],
        'methods' => ['Post-injury rehabilitation', 'Post-surgical recovery', 'Neurological rehabilitation', 'Sports recovery programs', 'Balance and coordination training'],
        'benefits' => ['Faster, safer recovery', 'Restores independence and confidence', 'Rebuilds strength and coordination', 'Prevents re-injury', 'Personalized, closely supervised care'],
    ],

    'postural-therapy' => [
        'title' => 'Postural Therapy',
        'category' => '/physiotherapy',
        'categoryLabel' => 'Physiotherapy',
        'icon' => 'icon-shield',
        'image' => BASE_URL . '/public/uploads/photos/photo-1519823551278-64ac92734fb1.jpg',
        'intro' => 'Correct alignment and ergonomic guidance that relieve the strain of modern life.',
        'about' => [
            'Hours at desks and screens take a toll on posture — and posture takes a toll on your whole body. Postural therapy identifies the imbalances behind your aches, then uses targeted correction, strengthening, and practical ergonomic advice to bring your body back into alignment.',
            'The result is less tension, fewer headaches, and a more comfortable, confident posture that lasts.',
        ],
        'methods' => ['Posture assessment', 'Muscle balancing', 'Core strengthening', 'Ergonomic guidance', 'Desk and screen habit coaching'],
        'benefits' => ['Relieves neck, shoulder, and back tension', 'Improves alignment and balance', 'Reduces screen-related strain', 'Prevents posture-related pain', 'Builds a stronger core'],
    ],

    'fitness-therapy' => [
        'title' => 'Fitness Therapy',
        'category' => '/physiotherapy',
        'categoryLabel' => 'Physiotherapy',
        'icon' => 'icon-sun',
        'image' => BASE_URL . '/public/uploads/photos/photo-1544367567-0f2fcb009e0b.jpg',
        'intro' => 'Customized exercise programs that build fitness around your body and goals.',
        'about' => [
            'Fitness therapy treats exercise like medicine — prescribed to your exact needs. Rather than a one-size-fits-all gym routine, our therapists design a program that builds strength, stamina, and flexibility safely, whatever your starting point.',
            'Ideal for anyone who wants to get fitter, manage a condition, or simply feel more capable in daily life.',
        ],
        'methods' => ['Custom exercise programs', 'Strength training', 'Endurance conditioning', 'Flexibility sessions', 'Progress tracking'],
        'benefits' => ['Improves overall fitness and stamina', 'Builds functional strength', 'Supports healthy weight', 'Boosts energy and mood', 'Safe for all fitness levels'],
    ],

    'cardiovascular-endurance-training' => [
        'title' => 'Cardiovascular & Endurance Training',
        'category' => '/physiotherapy',
        'categoryLabel' => 'Physiotherapy',
        'icon' => 'icon-wind',
        'image' => BASE_URL . '/public/uploads/photos/photo-1544367567-0f2fcb009e0b.jpg',
        'intro' => 'Heart-smart exercise programs that build stamina and protect your cardiovascular health.',
        'about' => [
            'A stronger heart means more energy for everything you love. Our endurance programs use heart-rate-guided aerobic training to safely build your cardiovascular fitness — from gentle conditioning to more advanced interval work.',
            'Every session is monitored and adjusted to your capacity, making this program ideal for improving stamina, managing heart-health risk factors, and feeling more energetic.',
        ],
        'methods' => ['Aerobic conditioning', 'Interval training', 'Heart-rate-guided sessions', 'Breathing and pacing techniques', 'Recovery and rest management'],
        'benefits' => ['Strengthens heart and lungs', 'Builds stamina and endurance', 'Improves circulation', 'Supports healthy blood pressure', 'Increases daily energy'],
    ],

    'xone-exercise' => [
        'title' => 'Xone Exercise',
        'category' => '/physiotherapy',
        'categoryLabel' => 'Physiotherapy',
        'icon' => 'icon-activity',
        'image' => BASE_URL . '/public/uploads/photos/photo-1519823551278-64ac92734fb1.jpg',
        'intro' => 'Advanced exercise techniques for flexibility, mobility, and deep muscle strengthening.',
        'about' => [
            'Xone Exercise is a modern training approach that combines advanced movement patterns with targeted strengthening. It focuses on mobility, flexibility, and activating the deep muscles that support everyday movement and athletic performance.',
            'Our therapists guide you through precise, progressive movements — perfect for athletes, active adults, and anyone looking to move with more freedom.',
        ],
        'methods' => ['Advanced movement patterns', 'Mobility and flexibility work', 'Deep muscle activation', 'Progressive strengthening', 'Athletic conditioning'],
        'benefits' => ['Enhances flexibility and range of motion', 'Builds deep, functional strength', 'Improves movement quality', 'Supports athletic performance', 'Reduces injury risk'],
    ],

    /* ============================ DIET THERAPY ============================ */
    'therapeutic-fasting' => [
        'title' => 'Therapeutic Fasting',
        'category' => '/diet-therapy',
        'categoryLabel' => 'Diet Therapy',
        'icon' => 'icon-droplet',
        'image' => BASE_URL . '/public/uploads/photos/photo-1498837167922-ddd27525d352.jpg',
        'intro' => 'A medically supervised fasting program that resets digestion and detoxifies the body.',
        'about' => [
            'Therapeutic fasting gives your digestive system a well-deserved rest while the body turns inward to cleanse and repair. Under close supervision, fasting is a powerful tool for detoxification, improving metabolism, and breaking unhealthy food habits.',
            'Our programs are always personalized and medically supervised — you are guided through every phase, from preparation to a gentle, nourishing reintroduction of food.',
        ],
        'methods' => ['Medically supervised fasting', 'Juice and water fasting plans', 'Gradual food reintroduction', 'Detox support therapies', 'Personalized guidance throughout'],
        'benefits' => ['Supports natural detoxification', 'Improves digestion and metabolism', 'Breaks unhealthy eating habits', 'Boosts energy and mental clarity', 'Safe, closely supervised program'],
    ],

    'raw-diet-therapy' => [
        'title' => 'Raw Diet Therapy',
        'category' => '/diet-therapy',
        'categoryLabel' => 'Diet Therapy',
        'icon' => 'icon-leaf',
        'image' => BASE_URL . '/public/uploads/photos/photo-1512621776951-a57141f2eefd.jpg',
        'intro' => 'Fresh, living foods rich in enzymes that energize and gently heal the body.',
        'about' => [
            'A raw food diet is rich in the enzymes, vitamins, and living energy that cooking can destroy. Our nutritionists design delicious raw meal plans using fresh fruits, vegetables, sprouts, and nuts that support healing and natural vitality.',
            'Raw diet therapy is introduced gradually and balanced to suit your body — making it sustainable and enjoyable, not extreme.',
        ],
        'methods' => ['Fresh fruit meals', 'Raw vegetable dishes', 'Sprouting and soaking', 'Nuts, seeds, and superfoods', 'Gradual transition guidance'],
        'benefits' => ['Preserves natural enzymes and nutrients', 'Boosts energy and vitality', 'Supports healthy weight loss', 'Improves skin and digestion', 'Gently detoxifies the body'],
    ],

    'bland-food-therapy' => [
        'title' => 'Bland Food Therapy',
        'category' => '/diet-therapy',
        'categoryLabel' => 'Diet Therapy',
        'icon' => 'icon-heart',
        'image' => BASE_URL . '/public/uploads/photos/photo-1498837167922-ddd27525d352.jpg',
        'intro' => 'Simple, gentle meals that soothe the digestive system and support recovery.',
        'about' => [
            'When the digestive system is sensitive, the answer is simplicity. Bland food therapy provides easy-to-digest, low-irritation meals that let your stomach and intestines rest, recover, and rebalance.',
            'It is especially helpful for digestive complaints, during recovery from illness, and as part of detox programs. Our nutritionists keep the meals nutritious and varied — gentle on the stomach, never boring.',
        ],
        'methods' => ['Easy-to-digest cooked meals', 'Soothing soups and porridges', 'Small, frequent meals', 'Low-irritation food choices', 'Personalized meal rotation'],
        'benefits' => ['Soothes sensitive digestion', 'Supports gut recovery', 'Reduces bloating and discomfort', 'Nutritious yet gentle', 'Ideal support during detox'],
    ],

    'natural-botanicals' => [
        'title' => 'Natural Botanicals',
        'category' => '/diet-therapy',
        'categoryLabel' => 'Diet Therapy',
        'icon' => 'icon-sun',
        'image' => BASE_URL . '/public/uploads/photos/photo-1466692476868-aef1dfb1e735.jpg',
        'intro' => 'Herbs and botanicals used with care to support balance and natural healing.',
        'about' => [
            'For centuries, plants have been our most reliable medicine. Our team blends the wisdom of herbal tradition with modern safety standards, using teas, tinctures, and botanical remedies to support immunity, digestion, and calm.',
            'Every botanical is chosen for your specific constitution and condition, and always reviewed alongside your overall wellness plan.',
        ],
        'methods' => ['Herbal teas and infusions', 'Tinctures and extracts', 'Traditional herbal remedies', 'Botanical supplements', 'Personalized herbal blends'],
        'benefits' => ['Supports immune health', 'Calms digestion and nerves', 'Offers natural, gentle remedies', 'Complements other therapies', 'Chosen for your specific needs'],
    ],

    'organic-farm-produce' => [
        'title' => 'Organic Farm Produce',
        'category' => '/diet-therapy',
        'categoryLabel' => 'Diet Therapy',
        'icon' => 'icon-shield',
        'image' => BASE_URL . '/public/uploads/photos/photo-1466692476868-aef1dfb1e735.jpg',
        'intro' => 'Chemical-free fruits and vegetables, fresh from the farm to your plate.',
        'about' => [
            'Healing begins with clean food. We source fresh, chemical-free fruits and vegetables — much of it from our own organic farm — so every meal is as pure and nourishing as possible.',
            'From farm to table, our kitchen turns these wholesome ingredients into healing meals that support every diet program we offer.',
        ],
        'methods' => ['Chemical-free farm produce', 'Freshly harvested daily', 'Seasonal menu planning', 'Farm-to-table meals', 'Support for garden therapy'],
        'benefits' => ['Free from harmful chemicals', 'Higher in natural nutrients', 'Better taste and freshness', 'Supports sustainable farming', 'The purest foundation for healing'],
    ],

    /* ============================ SPECIAL THERAPIES ============================ */
    'salt-glow-massage' => [
        'title' => 'Salt Glow Massage',
        'category' => '/special-therapies',
        'categoryLabel' => 'Special Therapies',
        'icon' => 'icon-sun',
        'image' => BASE_URL . '/public/uploads/photos/photo-1544161515-4ab6ce6db874.jpg',
        'intro' => 'A therapeutic exfoliation massage that revitalizes skin and relaxes muscles.',
        'about' => [
            'Salt glow massage combines gentle exfoliation with therapeutic massage to leave skin polished, refreshed, and glowing. Mineral-rich salts buff away dead skin cells while the massage eases muscle tension and stimulates circulation.',
            'It is the perfect way to unwind, detoxify, and leave your skin feeling renewed — a favorite in our wellness programs.',
        ],
        'methods' => ['Mineral salt exfoliation', 'Therapeutic massage', 'Warm oil application', 'Circulation-boosting strokes', 'Full-body or targeted sessions'],
        'benefits' => ['Leaves skin soft and renewed', 'Boosts circulation', 'Deeply relaxes muscles', 'Supports gentle detoxification', 'A luxurious, calming experience'],
    ],

    'chakra-mindfulness-practices' => [
        'title' => 'Chakra Mindfulness Practices',
        'category' => '/special-therapies',
        'categoryLabel' => 'Special Therapies',
        'icon' => 'icon-moon',
        'image' => BASE_URL . '/public/uploads/photos/photo-1506126613408-eca07ce68773.jpg',
        'intro' => 'Meditation and energy practices that bring emotional balance and inner peace.',
        'about' => [
            'Chakra mindfulness practices blend guided meditation, breathwork, and visualization to harmonize your body&rsquo;s energy centers. These gentle practices help release emotional tension, sharpen focus, and restore a deep sense of calm.',
            'No experience is needed — our guides meet you where you are and lead you through practices that feel nourishing and transformative.',
        ],
        'methods' => ['Guided meditation', 'Breathwork techniques', 'Energy balancing', 'Visualization practices', 'Group and one-on-one sessions'],
        'benefits' => ['Promotes emotional balance', 'Deepens inner peace', 'Reduces stress and anxiety', 'Improves focus and clarity', 'Supports holistic well-being'],
    ],

    'paida-lajin-therapy' => [
        'title' => 'Paida Lajin Therapy',
        'category' => '/special-therapies',
        'categoryLabel' => 'Special Therapies',
        'icon' => 'icon-activity',
        'image' => BASE_URL . '/public/uploads/photos/photo-1545205597-3d9d02c29597.jpg',
        'intro' => 'A traditional healing practice that boosts circulation and flexibility through guided exercise.',
        'about' => [
            'Paida (guided tapping) and Lajin (stretching) are traditional Chinese healing practices that activate the body&rsquo;s natural repair systems. Gentle, rhythmic tapping stimulates energy flow, while deep stretching keeps tendons and muscles supple.',
            'Performed under the guidance of our therapists, this practice is simple, powerful, and effective for pain, stiffness, and low energy.',
        ],
        'methods' => ['Guided tapping (Paida)', 'Deep stretching (Lajin)', 'Meridian activation', 'Breathing coordination', 'Progressive home practice'],
        'benefits' => ['Improves circulation', 'Relieves pain and stiffness', 'Increases flexibility', 'Boosts energy and vitality', 'Easy to practice at home'],
    ],

    'alkaline-water-therapy' => [
        'title' => 'Alkaline Water Therapy',
        'category' => '/special-therapies',
        'categoryLabel' => 'Special Therapies',
        'icon' => 'icon-droplet',
        'image' => BASE_URL . '/public/uploads/photos/photo-1512290923902-8a9f81dc236c.jpg',
        'intro' => 'Structured hydration that supports the body\'s natural balance and detoxification.',
        'about' => [
            'Proper hydration is the foundation of health, and alkaline water therapy takes it a step further. By raising the body&rsquo;s pH toward a healthier, less acidic state, this simple therapy supports detoxification, digestion, and sustained energy.',
            'We combine pH-balanced water with a guided hydration schedule that fits your daily routine — one of the easiest therapies to integrate into modern life.',
        ],
        'methods' => ['pH-balanced drinking water', 'Guided hydration schedule', 'Detoxification support', 'Lifestyle integration tips', 'Progress monitoring'],
        'benefits' => ['Supports natural detoxification', 'Helps balance body pH', 'Improves hydration and energy', 'Aids digestion', 'Simple to fit into daily life'],
    ],

    'agnihotra-therapy' => [
        'title' => 'Agnihotra Therapy',
        'category' => '/special-therapies',
        'categoryLabel' => 'Special Therapies',
        'icon' => 'icon-zap',
        'image' => BASE_URL . '/public/uploads/photos/photo-1519823551278-64ac92734fb1.jpg',
        'intro' => 'An ancient fire-based practice that purifies the environment and calms the mind.',
        'about' => [
            'Agnihotra is an ancient Vedic practice performed at sunrise and sunset, in which herbal offerings are placed into a small fire while specific mantras are chanted. The subtle smoke is believed to purify the air and environment, creating a deeply calming atmosphere.',
            'Many guests find Agnihotra practice remarkably grounding — a daily ritual that reduces stress, improves focus, and adds rhythm to the day.',
        ],
        'methods' => ['Sunrise and sunset fire ritual', 'Herbal offerings', 'Traditional mantras', 'Guided practice sessions', 'Environment purification'],
        'benefits' => ['Purifies the surrounding environment', 'Reduces stress and anxiety', 'Improves focus and clarity', 'Adds calm daily rhythm', 'Connects you to ancient tradition'],
    ],

    'weight-nutritional-management' => [
        'title' => 'Weight & Nutritional Management',
        'category' => '/special-therapies',
        'categoryLabel' => 'Special Therapies',
        'icon' => 'icon-heart',
        'image' => BASE_URL . '/public/uploads/photos/photo-1498837167922-ddd27525d352.jpg',
        'intro' => 'A complete program for healthy, sustainable weight loss and balanced nutrition.',
        'about' => [
            'Weight is about far more than calories — it is about habits, metabolism, nutrition, and mindset. Our program takes a whole-person approach, combining personalized nutrition plans, gentle physical activity, and ongoing support.',
            'The goal is not quick fixes but lasting change: a healthier weight, better energy, and habits you can keep for life.',
        ],
        'methods' => ['Body composition analysis', 'Personalized nutrition plans', 'Guided physical activity', 'Habit and mindset coaching', 'Regular progress reviews'],
        'benefits' => ['Healthy, sustainable weight loss', 'Balanced, nourishing nutrition', 'Improved energy and confidence', 'Lasting habit change', 'Personal, supportive guidance'],
    ],

    'asthma-care-program' => [
        'title' => 'Asthma Care Program',
        'category' => '/special-therapies',
        'categoryLabel' => 'Special Therapies',
        'icon' => 'icon-wind',
        'image' => BASE_URL . '/public/uploads/photos/photo-1544367567-0f2fcb009e0b.jpg',
        'intro' => 'Breathing, yoga, and natural therapies that support easier, calmer breathing.',
        'about' => [
            'Asthma care goes beyond medication. Our program combines specific breathing exercises, gentle yoga, and natural therapies to strengthen your respiratory system, reduce triggers, and help you breathe with more ease.',
            'Working alongside your medical care, our specialists teach you practical techniques that support lung health and greater confidence in daily life.',
        ],
        'methods' => ['Therapeutic breathing exercises', 'Yoga and pranayama', 'Natural respiratory remedies', 'Trigger awareness guidance', 'Lifestyle support'],
        'benefits' => ['Supports easier breathing', 'Reduces frequency of symptoms', 'Strengthens respiratory health', 'Teaches calming breath techniques', 'Complements medical treatment'],
    ],

    'diabetes-management-program' => [
        'title' => 'Diabetes Management Program',
        'category' => '/special-therapies',
        'categoryLabel' => 'Special Therapies',
        'icon' => 'icon-activity',
        'image' => BASE_URL . '/public/uploads/photos/photo-1512621776951-a57141f2eefd.jpg',
        'intro' => 'A personalized lifestyle program for healthy blood sugar and lasting energy.',
        'about' => [
            'Managing diabetes well is about the whole picture: food, movement, stress, and sleep. Our program builds a personalized lifestyle plan that supports healthy blood sugar levels, sustainable weight, and steady energy throughout the day.',
            'Guided by our wellness team and complementary to your medical care, this program empowers you to take confident control of your health.',
        ],
        'methods' => ['Personalized diet plans', 'Guided physical activity', 'Stress management practices', 'Regular blood sugar awareness', 'Progress monitoring and support'],
        'benefits' => ['Supports healthy blood sugar levels', 'Promotes steady, lasting energy', 'Helps with healthy weight management', 'Builds sustainable healthy habits', 'Empowering, personal guidance'],
    ],
];
