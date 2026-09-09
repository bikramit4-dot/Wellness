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
        'methods' => [
            ['title' => 'Hydrotherapy', 'definition' => 'Water, in all its forms — warm baths, cool compresses, wraps and jets — is used at precise temperatures to boost circulation, calm pain and support the body\'s natural detoxification, one of the oldest and most effective natural medicines.'],
            ['title' => 'Mud therapy', 'definition' => 'Mineral-rich mud is applied over the skin to absorb impurities, draw out heat and soothe inflammation. As the mud dries it gently pulls congestion away from the body, calming digestion and easing joint and muscle discomfort.'],
            ['title' => 'Herbal therapy', 'definition' => 'Selected herbs, teas and natural extracts are matched to your constitution to support immunity, digestion and rest. Herbal remedies work gently with your body to restore balance without the side effects of synthetic medicines.'],
            ['title' => 'Massage therapy', 'definition' => 'Hands-on therapeutic massage relaxes tight muscles, improves blood and lymphatic flow, and releases deeply held tension. It is a powerful way to relieve stress, ease pain and wake up the body\'s own healing response.'],
            ['title' => 'Steam therapy', 'definition' => 'Warm herbal steam opens the pores, loosens congestion and encourages gentle sweating — the body\'s natural way of releasing impurities. Regular steam sessions refresh the skin, relax the nerves and support the respiratory system.'],
            ['title' => 'Detoxification programs', 'definition' => 'A guided, multi-day cleanse that combines diet, hydration, rest and supportive therapies to help the body eliminate accumulated toxins. Each program is personalized and carefully supervised so the process is safe, gentle and effective.'],
        ],
        'benefits' => ['Supports the body&rsquo;s natural self-healing', 'Helps detoxify and cleanse the system', 'Strengthens immunity and vitality', 'Reduces dependence on medication', 'Improves long-term energy balance'],
    ],

    'yoga-therapy' => [
        'title' => 'Yoga Therapy',
        'category' => '/treatments',
        'categoryLabel' => 'Treatments',
        'icon' => 'icon-moon',
        'image' => BASE_URL . '/public/uploads/photos/photo-1558618666-fcd25c85cd64.jpg',
        'intro' => 'Physical postures, breathing, and meditation woven together to restore body and mind.',
        'about' => [
            'Yoga therapy applies the ancient science of yoga as a personalized therapeutic tool. Led by certified instructors, sessions combine gentle postures, controlled breathing, and guided meditation to address everything from stress and posture to chronic pain.',
            'Whether you are a beginner or an experienced practitioner, each session is adapted to your body and goals — helping you move with ease, breathe more freely, and find a calmer, more balanced state of mind.',
        ],
        'methods' => [
            ['title' => 'Hatha Yoga', 'definition' => 'A classical, gentle style of yoga that pairs physical postures with steady breathing. It stretches and strengthens the body slowly and safely, making it ideal for beginners and for restoring calm.'],
            ['title' => 'Pranayama (breathwork)', 'definition' => 'Controlled breathing exercises that calm the nervous system and increase oxygen flow. Practiced daily, they reduce stress, steady the heart and clear the mind.'],
            ['title' => 'Guided meditation', 'definition' => 'A therapist-led practice that carries you into deep relaxation, easing the busy mind. Regular sessions reduce anxiety, improve sleep and build lasting inner calm.'],
            ['title' => 'Stress management', 'definition' => 'Practical yoga-based tools — breathing, movement and mindfulness — that lower stress hormones and build resilience. You learn to recognize tension early and release it before it builds.'],
            ['title' => 'Flexibility training', 'definition' => 'Progressive stretching routines that lengthen tight muscles and improve joint range of motion. Over time, everyday movement becomes easier, freer and more comfortable.'],
            ['title' => 'Weight management', 'definition' => 'A balanced approach that combines mindful movement, better breathing and healthy habits. It supports natural weight loss, boosts metabolism and helps you keep results long-term.'],
        ],
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
        'methods' => [
            ['title' => 'Fine needle therapy', 'definition' => 'Ultra-thin, sterile needles are gently placed at precise points on the body to restore the smooth flow of energy (Qi). The insertion is barely felt, yet it can reduce pain, release tension and switch on the body\'s healing response.'],
            ['title' => 'Moxibustion (heat therapy)', 'definition' => 'A warming herbal preparation (moxa) is burned close to the skin over specific points, gently heating them to stimulate circulation and energy flow. It is especially helpful for cold, sluggish conditions and deep-seated aches.'],
            ['title' => 'Cupping therapy', 'definition' => 'Silicone or glass cups are placed on the skin to create gentle suction, lifting the tissues to boost blood flow and loosen tight muscles. The result is deep relief, especially for the back and shoulders.'],
            ['title' => 'Electro-acupuncture', 'definition' => 'A mild, comfortable electrical pulse is passed through the needles to amplify the stimulation at each point. It is particularly effective for stubborn pain and for re-energizing tired muscles.'],
            ['title' => 'Meridian assessment', 'definition' => 'Before treatment begins, the practitioner maps the body\'s energy pathways to find where flow is blocked. This diagnosis shapes every treatment, so each session targets the true root of your complaint.'],
        ],
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
        // Each method can be a plain string (title only) or an array with a
        // 'title' + 'definition' — the definition is revealed on the page when
        // the visitor clicks the technique.
        'methods' => [
            ['title' => 'Manual therapy', 'definition' => 'Hands-on techniques applied by your physiotherapist — including joint mobilization, soft-tissue release and therapeutic massage — that loosen tight muscles, improve joint movement and reduce pain without medication.'],
            ['title' => 'Therapeutic exercises', 'definition' => 'A personalized set of strengthening and stabilization exercises prescribed to rebuild muscle power, endurance and control in the areas that need it most, progressing safely at your own pace.'],
            ['title' => 'Stretching and mobility work', 'definition' => 'Guided stretching routines that restore flexibility and range of motion to stiff muscles and joints, making everyday movements easier and more comfortable.'],
            ['title' => 'Gait and balance training', 'definition' => 'Exercises that improve the way you walk and stand — strengthening stability, correcting your walking pattern and reducing the risk of falls.'],
            ['title' => 'Home exercise programs', 'definition' => 'A simple routine you continue between sessions, with clear instructions and checkpoints, so your recovery keeps progressing even when you are not at the center.'],
        ],
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
        'methods' => [
            ['title' => 'TENS for pain relief', 'definition' => 'Transcutaneous Electrical Nerve Stimulation uses gentle, low-intensity electrical pulses through small pads placed on the skin to interrupt pain signals and encourage the body\'s own natural pain relief — a drug-free way to manage both acute and chronic pain.'],
            ['title' => 'Ultrasound therapy', 'definition' => 'Therapeutic ultrasound sends high-frequency sound waves deep into soft tissues, generating gentle warmth that speeds up healing, reduces inflammation and eases pain in muscles, tendons and ligaments.'],
            ['title' => 'Interferential therapy', 'definition' => 'Two mid-frequency currents are crossed at the treatment area to deliver a deep, comfortable stimulation that reaches painful joints and muscles, helping to reduce swelling and relax spasms.'],
            ['title' => 'Muscle stimulation', 'definition' => 'Controlled electrical impulses are applied directly to weakened or recovering muscles, causing safe, gentle contractions that rebuild strength and prevent muscle wasting after injury or surgery.'],
            ['title' => 'Heat and cold modalities', 'definition' => 'Simple but powerful — targeted heat relaxes stiff muscles and boosts circulation, while cold reduces acute swelling and numbs pain; your therapist applies the right one for each stage of healing.'],
        ],
        'benefits' => ['Quick, effective pain relief', 'Reduces swelling and inflammation', 'Stimulates muscle recovery', 'Accelerates tissue healing', 'Drug-free treatment option'],
    ],

    'rehabilitation-therapy' => [
        'title' => 'Rehabilitation Therapy',
        'category' => '/physiotherapy',
        'categoryLabel' => 'Physiotherapy',
        'icon' => 'icon-heart',
        'image' => BASE_URL . '/public/uploads/photos/photo-1571019614242-c5c5dee9f50b.jpg',
        'intro' => 'Structured programs that guide you back to full function after injury, surgery, or illness.',
        'about' => [
            'Rehabilitation therapy is your roadmap back to an active life. Whether you are recovering from surgery, a sports injury, a stroke, or a neurological condition, our therapists build a step-by-step program that rebuilds strength, balance, and independence.',
            'We track your progress closely and adjust the plan as you improve — so you recover at the right pace, without setbacks.',
        ],
        'methods' => [
            ['title' => 'Post-injury rehabilitation', 'definition' => 'A structured, phased program that guides you from the day of injury back to full activity — protecting the area first, then progressively rebuilding strength, mobility and confidence.'],
            ['title' => 'Post-surgical recovery', 'definition' => 'A closely supervised recovery plan designed around your procedure, restoring range of motion, strength and function safely so you heal well and avoid complications.'],
            ['title' => 'Neurological rehabilitation', 'definition' => 'Specialist therapy for conditions affecting the brain and nervous system — such as stroke or Parkinson\'s — that retrains movement, balance, coordination and daily skills to maximize independence.'],
            ['title' => 'Sports recovery programs', 'definition' => 'Sport-specific conditioning that gets athletes back on the field faster and stronger, with drills and strengthening matched to the demands of their sport and a clear plan to prevent re-injury.'],
            ['title' => 'Balance and coordination training', 'definition' => 'Targeted exercises that retrain the body\'s balance systems, improving stability, reaction and control — essential after injury, for older adults, and for anyone wanting to move with confidence.'],
        ],
        'benefits' => ['Faster, safer recovery', 'Restores independence and confidence', 'Rebuilds strength and coordination', 'Prevents re-injury', 'Personalized, closely supervised care'],
    ],

    'postural-therapy' => [
        'title' => 'Postural Therapy',
        'category' => '/physiotherapy',
        'categoryLabel' => 'Physiotherapy',
        'icon' => 'icon-shield',
        'image' => BASE_URL . '/public/uploads/photos/photo-1552693673-1bf958298935.jpg',
        'intro' => 'Correct alignment and ergonomic guidance that relieve the strain of modern life.',
        'about' => [
            'Hours at desks and screens take a toll on posture — and posture takes a toll on your whole body. Postural therapy identifies the imbalances behind your aches, then uses targeted correction, strengthening, and practical ergonomic advice to bring your body back into alignment.',
            'The result is less tension, fewer headaches, and a more comfortable, confident posture that lasts.',
        ],
        'methods' => [
            ['title' => 'Posture assessment', 'definition' => 'A detailed analysis of how you stand, sit and move, identifying the muscle imbalances and habits behind your aches before a single correction is made.'],
            ['title' => 'Muscle balancing', 'definition' => 'Gentle techniques that release overworked, tight muscles and re-awaken weak ones, restoring the natural balance that holds your body in good alignment.'],
            ['title' => 'Core strengthening', 'definition' => 'Focused exercises that build the deep core muscles supporting your spine, so good posture becomes effortless rather than something you have to force.'],
            ['title' => 'Ergonomic guidance', 'definition' => 'Practical advice for your work and home setup — chair height, screen position, desk layout — that removes the everyday strain feeding your posture problems.'],
            ['title' => 'Desk and screen habit coaching', 'definition' => 'Simple habit changes for long hours at desks and phones — micro-breaks, repositioning cues and daily routines that keep your new posture from slipping away.'],
        ],
        'benefits' => ['Relieves neck, shoulder, and back tension', 'Improves alignment and balance', 'Reduces screen-related strain', 'Prevents posture-related pain', 'Builds a stronger core'],
    ],

    'fitness-therapy' => [
        'title' => 'Fitness Therapy',
        'category' => '/physiotherapy',
        'categoryLabel' => 'Physiotherapy',
        'icon' => 'icon-sun',
        'image' => BASE_URL . '/public/uploads/photos/photo-1571902943202-507ec2618e8f.jpg',
        'intro' => 'Customized exercise programs that build fitness around your body and goals.',
        'about' => [
            'Fitness therapy treats exercise like medicine — prescribed to your exact needs. Rather than a one-size-fits-all gym routine, our therapists design a program that builds strength, stamina, and flexibility safely, whatever your starting point.',
            'Ideal for anyone who wants to get fitter, manage a condition, or simply feel more capable in daily life.',
        ],
        'methods' => [
            ['title' => 'Custom exercise programs', 'definition' => 'An exercise plan built around your body, goals and current fitness level — nothing generic, everything prescribed to the way you move and what you want to achieve.'],
            ['title' => 'Strength training', 'definition' => 'Progressive resistance work that builds functional muscle power safely, making daily activities easier and protecting your joints and bones.'],
            ['title' => 'Endurance conditioning', 'definition' => 'Cardiovascular training designed to build stamina step by step, improving your heart and lung fitness at a pace your body can comfortably sustain.'],
            ['title' => 'Flexibility sessions', 'definition' => 'Structured stretching that keeps muscles long and joints free, improving range of motion and reducing the stiffness that comes with age and inactivity.'],
            ['title' => 'Progress tracking', 'definition' => 'Regular measurements and check-ins so you can see real improvements — and your therapist can adjust the program as you get stronger.'],
        ],
        'benefits' => ['Improves overall fitness and stamina', 'Builds functional strength', 'Supports healthy weight', 'Boosts energy and mood', 'Safe for all fitness levels'],
    ],

    'cardiovascular-endurance-training' => [
        'title' => 'Cardiovascular & Endurance Training',
        'category' => '/physiotherapy',
        'categoryLabel' => 'Physiotherapy',
        'icon' => 'icon-wind',
        'image' => BASE_URL . '/public/uploads/photos/photo-1534438327276-14e5300c3a48.jpg',
        'intro' => 'Heart-smart exercise programs that build stamina and protect your cardiovascular health.',
        'about' => [
            'A stronger heart means more energy for everything you love. Our endurance programs use heart-rate-guided aerobic training to safely build your cardiovascular fitness — from gentle conditioning to more advanced interval work.',
            'Every session is monitored and adjusted to your capacity, making this program ideal for improving stamina, managing heart-health risk factors, and feeling more energetic.',
        ],
        'methods' => [
            ['title' => 'Aerobic conditioning', 'definition' => 'Continuous, moderate-intensity exercise that steadily strengthens your heart and lungs, building a foundation of stamina you can feel in daily life.'],
            ['title' => 'Interval training', 'definition' => 'Alternating bursts of higher effort with easier recovery periods — an efficient way to improve fitness faster, carefully dosed to your capacity.'],
            ['title' => 'Heart-rate-guided sessions', 'definition' => 'Training zones set from your individual heart-rate response, so every session works the right intensity for your body — safe, effective and never guessing.'],
            ['title' => 'Breathing and pacing techniques', 'definition' => 'Methods to keep your breathing steady and your effort controlled during exercise, improving efficiency and helping you go further with less fatigue.'],
            ['title' => 'Recovery and rest management', 'definition' => 'Structured rest and recovery guidance — because the body adapts and grows stronger during recovery, not only during the workout itself.'],
        ],
        'benefits' => ['Strengthens heart and lungs', 'Builds stamina and endurance', 'Improves circulation', 'Supports healthy blood pressure', 'Increases daily energy'],
    ],

    'xone-exercise' => [
        'title' => 'Xone Exercise',
        'category' => '/physiotherapy',
        'categoryLabel' => 'Physiotherapy',
        'icon' => 'icon-activity',
        'image' => BASE_URL . '/public/uploads/photos/photo-1543269865-cbf427effbad.jpg',
        'intro' => 'Advanced exercise techniques for flexibility, mobility, and deep muscle strengthening.',
        'about' => [
            'Xone Exercise is a modern training approach that combines advanced movement patterns with targeted strengthening. It focuses on mobility, flexibility, and activating the deep muscles that support everyday movement and athletic performance.',
            'Our therapists guide you through precise, progressive movements — perfect for athletes, active adults, and anyone looking to move with more freedom.',
        ],
        'methods' => [
            ['title' => 'Advanced movement patterns', 'definition' => 'Precise, multi-joint movements that teach your body to move as one coordinated unit — improving the quality of everyday and athletic movement.'],
            ['title' => 'Mobility and flexibility work', 'definition' => 'Targeted drills that expand your usable range of motion, releasing tightness so you can move freely and powerfully without restriction.'],
            ['title' => 'Deep muscle activation', 'definition' => 'Exercises that switch on the deep stabilizer muscles around your joints — the foundations that support every stronger movement you make.'],
            ['title' => 'Progressive strengthening', 'definition' => 'A step-by-step strengthening system that loads the muscles progressively, building real functional strength without overloading joints.'],
            ['title' => 'Athletic conditioning', 'definition' => 'Sport-specific power, speed and agility work that translates strength into performance — for athletes and active adults alike.'],
        ],
        'benefits' => ['Enhances flexibility and range of motion', 'Builds deep, functional strength', 'Improves movement quality', 'Supports athletic performance', 'Reduces injury risk'],
    ],

    /* ============================ DIET THERAPY ============================ */
    'therapeutic-fasting' => [
        'title' => 'Therapeutic Fasting',
        'category' => '/diet-therapy',
        'categoryLabel' => 'Diet Therapy',
        'icon' => 'icon-droplet',
        'image' => BASE_URL . '/public/uploads/photos/photo-1599447421416-3414500d18a5.jpg',
        'intro' => 'A medically supervised fasting program that resets digestion and detoxifies the body.',
        'about' => [
            'Therapeutic fasting gives your digestive system a well-deserved rest while the body turns inward to cleanse and repair. Under close supervision, fasting is a powerful tool for detoxification, improving metabolism, and breaking unhealthy food habits.',
            'Our programs are always personalized and medically supervised — you are guided through every phase, from preparation to a gentle, nourishing reintroduction of food.',
        ],
        'methods' => [
            ['title' => 'Medically supervised fasting', 'definition' => 'Your fast is guided and monitored by trained professionals, who track your energy, hydration and wellbeing throughout. Supervision makes the cleanse safe, comfortable and effective.'],
            ['title' => 'Juice and water fasting plans', 'definition' => 'Fresh vegetable and fruit juices, herbal teas and pure water nourish the body while giving digestion a complete rest. Plans are tailored to your constitution so you stay energized while you cleanse.'],
            ['title' => 'Gradual food reintroduction', 'definition' => 'Breaking a fast is as important as the fast itself. Simple, easy-to-digest foods are added back slowly, so your system adjusts gently and the benefits of the cleanse are locked in.'],
            ['title' => 'Detox support therapies', 'definition' => 'Massage, steam and mud therapies are combined with fasting to help the body release impurities through the skin and lymphatic system. They make the cleanse deeper, gentler and more comfortable.'],
            ['title' => 'Personalized guidance throughout', 'definition' => 'Every phase — preparation, fasting and reintroduction — is planned around your needs and reviewed with you. You are never alone: your team adjusts the plan as your body responds.'],
        ],
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
        'methods' => [
            ['title' => 'Fresh fruit meals', 'definition' => 'Seasonal fruits, served whole or freshly prepared, deliver living enzymes, vitamins and natural sugars in their most vital form. They energize the body and support gentle cleansing.'],
            ['title' => 'Raw vegetable dishes', 'definition' => 'Colorful salads, cold soups and grated vegetables preserve the nutrients and enzymes that cooking destroys. They are rich in fiber, keeping digestion regular and the body well-nourished.'],
            ['title' => 'Sprouting and soaking', 'definition' => 'Grains, seeds and legumes are soaked and sprouted to unlock their nutrients and make them easier to digest. Sprouting boosts vitamins and enzymes while reducing anti-nutrients.'],
            ['title' => 'Nuts, seeds, and superfoods', 'definition' => 'Raw nuts, seeds and nutrient-dense superfoods add healthy fats, protein and minerals to a raw diet. They keep meals satisfying and support skin, brain and heart health.'],
            ['title' => 'Gradual transition guidance', 'definition' => 'Moving to a raw diet is done step by step so your digestion adapts comfortably. Your nutritionist plans the transition around your routine and preferences, making it sustainable, not extreme.'],
        ],
        'benefits' => ['Preserves natural enzymes and nutrients', 'Boosts energy and vitality', 'Supports healthy weight loss', 'Improves skin and digestion', 'Gently detoxifies the body'],
    ],

    'bland-food-therapy' => [
        'title' => 'Bland Food Therapy',
        'category' => '/diet-therapy',
        'categoryLabel' => 'Diet Therapy',
        'icon' => 'icon-heart',
        'image' => BASE_URL . '/public/uploads/photos/photo-1505751172876-fa1923c5c528.jpg',
        'intro' => 'Simple, gentle meals that soothe the digestive system and support recovery.',
        'about' => [
            'When the digestive system is sensitive, the answer is simplicity. Bland food therapy provides easy-to-digest, low-irritation meals that let your stomach and intestines rest, recover, and rebalance.',
            'It is especially helpful for digestive complaints, during recovery from illness, and as part of detox programs. Our nutritionists keep the meals nutritious and varied — gentle on the stomach, never boring.',
        ],
        'methods' => [
            ['title' => 'Easy-to-digest cooked meals', 'definition' => 'Softly cooked grains, vegetables and light proteins are prepared simply to reduce strain on the digestive tract. Gentle cooking releases nutrients while keeping the stomach calm.'],
            ['title' => 'Soothing soups and porridges', 'definition' => 'Warm, smooth soups and porridges coat and comfort the digestive lining, easing irritation and supporting recovery. They are easy to absorb and deeply nourishing.'],
            ['title' => 'Small, frequent meals', 'definition' => 'Eating smaller portions more often keeps the digestive system working steadily without overload. This reduces bloating, indigestion and post-meal fatigue.'],
            ['title' => 'Low-irritation food choices', 'definition' => 'Spicy, fried, acidic and heavily processed foods are set aside in favor of mild, wholesome options. Removing irritants lets an inflamed gut rest and repair.'],
            ['title' => 'Personalized meal rotation', 'definition' => 'Meals are rotated and varied so the diet stays nutritious and never boring. Your nutritionist adjusts the plan as your digestion improves.'],
        ],
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
        'methods' => [
            ['title' => 'Herbal teas and infusions', 'definition' => 'Gently brewed teas from leaves, flowers and roots deliver plant medicine in a warm, soothing form. They are a simple daily way to support digestion, immunity and restful sleep.'],
            ['title' => 'Tinctures and extracts', 'definition' => 'Concentrated herbal extracts capture the active compounds of plants in a few drops. They are potent, easy to dose and quickly absorbed, ideal for more targeted support.'],
            ['title' => 'Traditional herbal remedies', 'definition' => 'Time-tested formulas passed down through generations bring proven relief for common ailments. They are blended with care and reviewed for safety alongside your wellness plan.'],
            ['title' => 'Botanical supplements', 'definition' => 'Carefully sourced plant-based supplements provide concentrated nutrition where the diet falls short. Each one is chosen for purity and potency, and checked against your overall health picture.'],
            ['title' => 'Personalized herbal blends', 'definition' => 'Herbs are selected specifically for your constitution, condition and goals. A personalized blend works far better than a one-size-fits-all remedy.'],
        ],
        'benefits' => ['Supports immune health', 'Calms digestion and nerves', 'Offers natural, gentle remedies', 'Complements other therapies', 'Chosen for your specific needs'],
    ],

    'organic-farm-produce' => [
        'title' => 'Organic Farm Produce',
        'category' => '/diet-therapy',
        'categoryLabel' => 'Diet Therapy',
        'icon' => 'icon-shield',
        'image' => BASE_URL . '/public/uploads/photos/photo-1498837167922-ddd27525d352.jpg',
        'intro' => 'Chemical-free fruits and vegetables, fresh from the farm to your plate.',
        'about' => [
            'Healing begins with clean food. We source fresh, chemical-free fruits and vegetables — much of it from our own organic farm — so every meal is as pure and nourishing as possible.',
            'From farm to table, our kitchen turns these wholesome ingredients into healing meals that support every diet program we offer.',
        ],
        'methods' => [
            ['title' => 'Chemical-free farm produce', 'definition' => 'Fruits and vegetables grown without synthetic pesticides, herbicides or fertilizers. Clean, pure produce gives the body the nourishment it needs to heal without added chemical burden.'],
            ['title' => 'Freshly harvested daily', 'definition' => 'Produce is picked at peak ripeness and served the same day, when flavor and nutrients are at their highest. Fresh food simply tastes better and nourishes more deeply.'],
            ['title' => 'Seasonal menu planning', 'definition' => 'Meals follow nature\'s calendar, using what grows best in each season. Seasonal eating is more nutritious, more flavorful and gentler on the environment.'],
            ['title' => 'Farm-to-table meals', 'definition' => 'From the farm straight to the table, meals are prepared from whole ingredients with minimal processing. This short journey preserves nutrients and keeps food alive and nourishing.'],
            ['title' => 'Support for garden therapy', 'definition' => 'Working with plants and soil is itself a healing practice. Gentle time in the garden reduces stress, improves mood and reconnects you with the source of your food.'],
        ],
        'benefits' => ['Free from harmful chemicals', 'Higher in natural nutrients', 'Better taste and freshness', 'Supports sustainable farming', 'The purest foundation for healing'],
    ],

    /* ============================ SPECIAL THERAPIES ============================ */
    'salt-glow-massage' => [
        'title' => 'Salt Glow Massage',
        'category' => '/special-therapies',
        'categoryLabel' => 'Special Therapies',
        'icon' => 'icon-sun',
        'image' => BASE_URL . '/public/uploads/photos/photo-1574680096145-d05b474e2155.jpg',
        'intro' => 'A therapeutic exfoliation massage that revitalizes skin and relaxes muscles.',
        'about' => [
            'Salt glow massage combines gentle exfoliation with therapeutic massage to leave skin polished, refreshed, and glowing. Mineral-rich salts buff away dead skin cells while the massage eases muscle tension and stimulates circulation.',
            'It is the perfect way to unwind, detoxify, and leave your skin feeling renewed — a favorite in our wellness programs.',
        ],
        'methods' => [
            ['title' => 'Mineral salt exfoliation', 'definition' => 'Fine mineral salts gently buff away dead skin cells, leaving skin smooth and renewed. The salt also draws impurities to the surface and stimulates circulation.'],
            ['title' => 'Therapeutic massage', 'definition' => 'Soothing massage strokes relax tense muscles and calm the nervous system. It eases stiffness, encourages deep rest and leaves the whole body feeling lighter.'],
            ['title' => 'Warm oil application', 'definition' => 'Warm, nourishing oil is massaged into the skin to soften, hydrate and protect it. The warmth also helps muscles release more fully and deeply.'],
            ['title' => 'Circulation-boosting strokes', 'definition' => 'Rhythmic, upward strokes encourage blood and lymph to move, carrying oxygen to tissues and waste away from them. Better circulation means glowing skin and renewed energy.'],
            ['title' => 'Full-body or targeted sessions', 'definition' => 'Choose a complete full-body glow or focus on specific areas like the back, legs or shoulders. Every session is shaped around what you need that day.'],
        ],
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
        'methods' => [
            ['title' => 'Guided meditation', 'definition' => 'A guide leads you through gentle meditation that settles the mind and opens awareness. It is a safe, supportive way to begin, whatever your experience.'],
            ['title' => 'Breathwork techniques', 'definition' => 'Conscious breathing patterns calm the nervous system and move energy through the body. These simple techniques can be used anywhere, anytime stress builds.'],
            ['title' => 'Energy balancing', 'definition' => 'Practices that harmonize the body\'s energy centers (chakras), releasing blockages that show up as tension, fatigue or low mood. The result is a renewed sense of flow and ease.'],
            ['title' => 'Visualization practices', 'definition' => 'Guided imagery helps you picture healing energy moving through the body. Visualization deepens relaxation and makes inner states of peace tangible and repeatable.'],
            ['title' => 'Group and one-on-one sessions', 'definition' => 'Practice in the supportive energy of a group or receive personal guidance in a private session. Both formats are adapted to your comfort and goals.'],
        ],
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
        'methods' => [
            ['title' => 'Guided tapping (Paida)', 'definition' => 'Gentle, rhythmic tapping with the palms stimulates energy flow and wakes up the body\'s natural repair systems. It is simple to learn, surprisingly powerful, and can be done anywhere.'],
            ['title' => 'Deep stretching (Lajin)', 'definition' => 'Held, deep stretches lengthen the tendons and muscles, restoring flexibility and relieving stiffness. Stretching regularly keeps the body supple and free of tension.'],
            ['title' => 'Meridian activation', 'definition' => 'Tapping and stretching are directed along the body\'s energy pathways (meridians) to clear blockages and restore smooth flow. This is what gives the practice its deep, systemic effect.'],
            ['title' => 'Breathing coordination', 'definition' => 'Breath is synchronized with each tap and stretch, calming the nervous system and making the practice more effective. Coordinated breathing keeps effort gentle and steady.'],
            ['title' => 'Progressive home practice', 'definition' => 'You are taught a simple daily routine you can continue at home. Regular practice compounds the benefits, keeping circulation, flexibility and energy high between visits.'],
        ],
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
        'methods' => [
            ['title' => 'pH-balanced drinking water', 'definition' => 'Structured alkaline water supports a healthier, less acidic internal environment. Drinking it regularly helps the body maintain its natural pH balance and function at its best.'],
            ['title' => 'Guided hydration schedule', 'definition' => 'You receive a simple hydration plan spread across the day, so your body stays consistently and properly hydrated. Timing and quantity are tailored to your routine and needs.'],
            ['title' => 'Detoxification support', 'definition' => 'Steady, adequate hydration helps the kidneys and liver flush waste products more efficiently. It is one of the easiest ways to support the body\'s daily detoxification work.'],
            ['title' => 'Lifestyle integration tips', 'definition' => 'Practical advice on how to weave alkaline hydration into work, travel and daily life. Small habits make the therapy effortless to maintain long-term.'],
            ['title' => 'Progress monitoring', 'definition' => 'Your energy, digestion and hydration are reviewed over time, and the plan is adjusted as needed. Tracking keeps the therapy effective and your results visible.'],
        ],
        'benefits' => ['Supports natural detoxification', 'Helps balance body pH', 'Improves hydration and energy', 'Aids digestion', 'Simple to fit into daily life'],
    ],

    'agnihotra-therapy' => [
        'title' => 'Agnihotra Therapy',
        'category' => '/special-therapies',
        'categoryLabel' => 'Special Therapies',
        'icon' => 'icon-zap',
        'image' => BASE_URL . '/public/uploads/photos/photo-1599901860904-17e6ed7083a0.jpg',
        'intro' => 'An ancient fire-based practice that purifies the environment and calms the mind.',
        'about' => [
            'Agnihotra is an ancient Vedic practice performed at sunrise and sunset, in which herbal offerings are placed into a small fire while specific mantras are chanted. The subtle smoke is believed to purify the air and environment, creating a deeply calming atmosphere.',
            'Many guests find Agnihotra practice remarkably grounding — a daily ritual that reduces stress, improves focus, and adds rhythm to the day.',
        ],
        'methods' => [
            ['title' => 'Sunrise and sunset fire ritual', 'definition' => 'A small fire is lit at the exact times of sunrise and sunset, following an ancient Vedic ritual. The practice anchors the day with rhythm, intention and a profound sense of calm.'],
            ['title' => 'Herbal offerings', 'definition' => 'Specially prepared herbs, ghee and rice are offered into the fire, releasing a subtle, fragrant smoke. The offerings purify the air and create a serene atmosphere around you.'],
            ['title' => 'Traditional mantras', 'definition' => 'Ancient mantras are chanted as the offerings are made, adding the power of sound to the ritual. Together, fire, herb and sound bring the mind into deep stillness.'],
            ['title' => 'Guided practice sessions', 'definition' => 'Our guides lead the practice step by step, so you learn the correct form and meaning. Guidance makes the ritual accessible and meaningful, whatever your background.'],
            ['title' => 'Environment purification', 'definition' => 'The subtle smoke from the ritual is believed to cleanse the surrounding environment and neutralize pollution. Many guests notice fresher air, fewer allergens and a calmer space.'],
        ],
        'benefits' => ['Purifies the surrounding environment', 'Reduces stress and anxiety', 'Improves focus and clarity', 'Adds calm daily rhythm', 'Connects you to ancient tradition'],
    ],

    'weight-nutritional-management' => [
        'title' => 'Weight & Nutritional Management',
        'category' => '/special-therapies',
        'categoryLabel' => 'Special Therapies',
        'icon' => 'icon-heart',
        'image' => BASE_URL . '/public/uploads/photos/photo-1600334129128-685c5582fd35.jpg',
        'intro' => 'A complete program for healthy, sustainable weight loss and balanced nutrition.',
        'about' => [
            'Weight is about far more than calories — it is about habits, metabolism, nutrition, and mindset. Our program takes a whole-person approach, combining personalized nutrition plans, gentle physical activity, and ongoing support.',
            'The goal is not quick fixes but lasting change: a healthier weight, better energy, and habits you can keep for life.',
        ],
        'methods' => [
            ['title' => 'Body composition analysis', 'definition' => 'A detailed measurement of body fat, muscle and water gives an honest starting picture. Tracking composition — not just weight — shows real progress and guides the plan.'],
            ['title' => 'Personalized nutrition plans', 'definition' => 'Meal plans are built around your tastes, culture, lifestyle and health needs. Realistic, enjoyable eating is what makes weight loss sustainable.'],
            ['title' => 'Guided physical activity', 'definition' => 'Gentle, structured activity is prescribed at a level your body can safely handle. Movement supports fat loss, boosts metabolism and protects muscle while you slim down.'],
            ['title' => 'Habit and mindset coaching', 'definition' => 'Sustainable weight loss is as much about behavior as biology. Coaching helps you replace old patterns, manage cravings and stay motivated through the journey.'],
            ['title' => 'Regular progress reviews', 'definition' => 'Your results are reviewed at every milestone and the plan is adjusted accordingly. Regular check-ins keep you accountable and celebrating real change.'],
        ],
        'benefits' => ['Healthy, sustainable weight loss', 'Balanced, nourishing nutrition', 'Improved energy and confidence', 'Lasting habit change', 'Personal, supportive guidance'],
    ],

    'asthma-care-program' => [
        'title' => 'Asthma Care Program',
        'category' => '/special-therapies',
        'categoryLabel' => 'Special Therapies',
        'icon' => 'icon-wind',
        'image' => BASE_URL . '/public/uploads/photos/photo-1600880292203-757bb62b4baf.jpg',
        'intro' => 'Breathing, yoga, and natural therapies that support easier, calmer breathing.',
        'about' => [
            'Asthma care goes beyond medication. Our program combines specific breathing exercises, gentle yoga, and natural therapies to strengthen your respiratory system, reduce triggers, and help you breathe with more ease.',
            'Working alongside your medical care, our specialists teach you practical techniques that support lung health and greater confidence in daily life.',
        ],
        'methods' => [
            ['title' => 'Therapeutic breathing exercises', 'definition' => 'Specific techniques like pursed-lip and diaphragmatic breathing strengthen the respiratory muscles and improve oxygen exchange. Practiced daily, they make breathing feel easier and calmer.'],
            ['title' => 'Yoga and pranayama', 'definition' => 'Gentle yoga postures and controlled breathing open the chest, relax the airways and reduce breathlessness. Regular practice builds lung capacity and respiratory resilience.'],
            ['title' => 'Natural respiratory remedies', 'definition' => 'Herbal steam, warm fluids and natural preparations soothe the airways and loosen mucus. These gentle remedies support easier breathing alongside your medical treatment.'],
            ['title' => 'Trigger awareness guidance', 'definition' => 'You learn to recognize the allergens, pollutants and habits that aggravate your symptoms. Avoiding triggers is one of the most effective ways to prevent flare-ups.'],
            ['title' => 'Lifestyle support', 'definition' => 'Sleep, stress, diet and indoor air all influence asthma. Practical lifestyle adjustments create an environment where your lungs can stay calm and clear.'],
        ],
        'benefits' => ['Supports easier breathing', 'Reduces frequency of symptoms', 'Strengthens respiratory health', 'Teaches calming breath techniques', 'Complements medical treatment'],
    ],

    'diabetes-management-program' => [
        'title' => 'Diabetes Management Program',
        'category' => '/special-therapies',
        'categoryLabel' => 'Special Therapies',
        'icon' => 'icon-activity',
        'image' => BASE_URL . '/public/uploads/photos/photo-1518611012118-696072aa579a.jpg',
        'intro' => 'A personalized lifestyle program for healthy blood sugar and lasting energy.',
        'about' => [
            'Managing diabetes well is about the whole picture: food, movement, stress, and sleep. Our program builds a personalized lifestyle plan that supports healthy blood sugar levels, sustainable weight, and steady energy throughout the day.',
            'Guided by our wellness team and complementary to your medical care, this program empowers you to take confident control of your health.',
        ],
        'methods' => [
            ['title' => 'Personalized diet plans', 'definition' => 'Meal plans are designed to keep blood sugar steady, balancing carbohydrates, protein and healthy fats. Plans fit your culture and preferences so healthy eating is easy to sustain.'],
            ['title' => 'Guided physical activity', 'definition' => 'Appropriate, enjoyable activity helps muscles use glucose more efficiently and supports healthy weight. Exercise is prescribed safely around your current fitness and health.'],
            ['title' => 'Stress management practices', 'definition' => 'Chronic stress raises blood sugar, so calming the mind is part of the plan. Breathing, meditation and gentle movement keep stress — and glucose — in check.'],
            ['title' => 'Regular blood sugar awareness', 'definition' => 'You learn simple, practical ways to understand how food, activity and stress affect your levels. Self-awareness turns daily numbers into confident health decisions.'],
            ['title' => 'Progress monitoring and support', 'definition' => 'Ongoing reviews track your results and adjust the plan as you improve. Consistent, personal support keeps you motivated and in control of your health.'],
        ],
        'benefits' => ['Supports healthy blood sugar levels', 'Promotes steady, lasting energy', 'Helps with healthy weight management', 'Builds sustainable healthy habits', 'Empowering, personal guidance'],
    ],
];
