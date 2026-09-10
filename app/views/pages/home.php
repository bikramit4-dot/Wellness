<?php require APP_ROOT . '/app/views/partials/section_helpers.php'; ?>

<!-- ============ Hero ============ -->
<section class="hero">
    <?php
    $heroVideo = sec($sections, 'hero', 'media', '');
    if ($heroVideo !== ''): ?>
        <video class="hero-video" autoplay muted loop playsinline preload="metadata" aria-hidden="true" tabindex="-1">
            <source src="<?= BASE_URL ?>/public/uploads/<?= Security::e(rawurlencode($heroVideo)) ?>" type="video/mp4">
        </video>
    <?php endif; ?>
    <div class="container hero-content">
        <span class="eyebrow"><svg class="icon"><use href="#icon-leaf"/></svg> <?= Security::e(sec($sections, 'hero', 'kicker', 'Natural Healing · Holistic Care')) ?></span>
        <h1><?= Security::e(sec($sections, 'hero', 'heading', 'Rejuvenate Your Body, Mind & Soul')) ?></h1>
        <p class="hero-sub"><?= Security::e(sec($sections, 'hero', 'content', 'Welcome to Chitrawan Nature Cure Hospital, where ancient healing traditions meet modern therapeutic practices. Our team helps you achieve physical, mental, and emotional balance with personalized natural care.')) ?></p>
        <div class="button-row">
            <?php foreach (sec_buttons($sections, 'hero') as $b): ?>
                <a class="btn btn-<?= Security::e($b['style']) ?>" href="<?= Security::e(sec_url($b['url'])) ?>"><?= Security::e($b['label']) ?><?php if ($b['style'] === 'primary'): ?><svg class="icon"><use href="#icon-arrow"/></svg><?php endif; ?></a>
            <?php endforeach; ?>
        </div>
        <div class="hero-trust">
            <div class="avatar-stack" aria-hidden="true">
                <span>SP</span><span>AR</span><span>MK</span>
            </div>
            <p><span class="stars" aria-hidden="true"><svg class="icon"><use href="#icon-star"/></svg><svg class="icon"><use href="#icon-star"/></svg><svg class="icon"><use href="#icon-star"/></svg><svg class="icon"><use href="#icon-star"/></svg><svg class="icon"><use href="#icon-star"/></svg></span><strong><?= Security::e(sec($sections, 'hero', 'sub_content', '4.9/5 rated by 1,200+ happy patients')) ?></strong></p>
        </div>
    </div>
</section>

<!-- ============ Stats ============ -->
<section class="stats-band">
    <div class="container">
        <div class="stats reveal">
            <?php foreach (sec_pairs($sections, 'stats') as $stat): ?>
                <div class="stat"><strong><?= Security::e($stat['title']) ?></strong><span><?= Security::e($stat['text']) ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ About ============ -->
<section class="section">
    <div class="container split">
        <div class="media-frame reveal">
            <img src="<?= Security::e(sec($sections, 'about_intro', 'image')) ?>" alt="A calming yoga session at Chitrawan Nature Cure Hospital" loading="lazy">
            <div class="media-badge">
                <?php $badge = sec($sections, 'about_intro', 'sub_content', 'Since 2010|Healing with care & compassion'); ?>
                <?php [$badgeTitle, $badgeText] = array_pad(explode('|', $badge, 2), 2, ''); ?>
                <strong><?= Security::e($badgeTitle) ?></strong>
                <span><?= Security::e($badgeText) ?></span>
            </div>
        </div>
        <div class="reveal" style="--d:.12s">
            <span class="kicker"><?= Security::e(sec($sections, 'about_intro', 'kicker', 'About Chitrawan')) ?></span>
            <h2><?= Security::e(sec($sections, 'about_intro', 'heading', 'Healing Naturally, Living Fully')) ?></h2>
            <p><?= Security::e(sec($sections, 'about_intro', 'content', '')) ?></p>
            <ul class="check-list">
                <?php foreach ((array) ($sections['about_intro']['extras'] ?? []) as $line): ?>
                    <li><svg class="icon"><use href="#icon-check"/></svg> <?= Security::e((string) $line) ?></li>
                <?php endforeach; ?>
            </ul>
            <a class="btn btn-dark" href="<?= BASE_URL ?><?= Security::e(sec($sections, 'about_intro', 'link', '/about')) ?>"><?= Security::e(sec($sections, 'about_intro', 'link_label', 'More About Us')) ?> <svg class="icon"><use href="#icon-arrow"/></svg></a>
        </div>
    </div>
</section>

<!-- ============ Why Choose Us (photo cards, admin-managed) ============ -->
<section class="section alt why-section">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'why', 'kicker', 'Why Choose Us')) ?></span>
            <h2><?= Security::e(sec($sections, 'why', 'heading', 'Care That Puts You First')) ?></h2>
            <p class="lede"><?= Security::e(sec($sections, 'why', 'content', 'Reasons patients trust us with their wellness journey.')) ?></p>
        </div>
        <?php $features = $features ?? (require APP_ROOT . '/app/data/features.php'); ?>
        <?php if (empty($features)): ?>
            <p class="dash-empty">No feature cards yet. Add them from the admin panel.</p>
        <?php else: ?>
            <div class="carousel reveal" style="--d:.12s" data-carousel>
                <div class="carousel-viewport">
                    <div class="carousel-track">
                        <?php foreach ($features as $f): ?>
                            <article class="carousel-card">
                                <div class="carousel-card-media">
                                    <?php if (!empty($f['image'])): ?>
                                        <img src="<?= Security::e($f['image']) ?>" alt="<?= Security::e($f['title'] ?? 'Why choose Chitrawan') ?>" loading="lazy">
                                    <?php endif; ?>
                                </div>
                                <span class="carousel-card-icon" aria-hidden="true"><svg class="icon"><use href="#<?= Security::e($f['icon'] ?? 'icon-leaf') ?>"/></svg></span>
                                <div class="carousel-card-body">
                                    <h3><?= Security::e($f['title'] ?? '') ?></h3>
                                    <p><?= Security::e($f['description'] ?? '') ?></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button class="carousel-btn carousel-prev" type="button" aria-label="Previous cards"><svg class="icon"><use href="#icon-arrow"/></svg></button>
                <button class="carousel-btn carousel-next" type="button" aria-label="Next cards"><svg class="icon"><use href="#icon-arrow"/></svg></button>
                <div class="carousel-dots" role="tablist" aria-label="Choose a card"></div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============ What We Offer (photo cards, admin-managed) ============ -->
<section class="section offers-section">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'offers', 'kicker', 'What We Offer')) ?></span>
            <h2><?= Security::e(sec($sections, 'offers', 'heading', 'Our Core Services')) ?></h2>
            <p class="lede"><?= Security::e(sec($sections, 'offers', 'content', 'A complete range of natural therapies, designed to support your health at every stage.')) ?></p>
        </div>
        <?php $offers = $offers ?? (require APP_ROOT . '/app/data/offers.php'); ?>
        <?php if (empty($offers)): ?>
            <p class="dash-empty">No offer cards yet. Add them from the admin panel.</p>
        <?php else: ?>
            <div class="carousel reveal" style="--d:.12s" data-carousel>
                <div class="carousel-viewport">
                    <div class="carousel-track">
                        <?php foreach ($offers as $o): ?>
                            <article class="carousel-card">
                                <div class="carousel-card-media">
                                    <?php if (!empty($o['image'])): ?>
                                        <img src="<?= Security::e($o['image']) ?>" alt="<?= Security::e($o['title'] ?? 'What we offer') ?>" loading="lazy">
                                    <?php endif; ?>
                                </div>
                                <span class="carousel-card-icon" aria-hidden="true"><svg class="icon"><use href="#<?= Security::e($o['icon'] ?? 'icon-leaf') ?>"/></svg></span>
                                <div class="carousel-card-body">
                                    <h3><?= Security::e($o['title'] ?? '') ?></h3>
                                    <p><?= Security::e($o['description'] ?? '') ?></p>
                                    <a class="card-link" href="<?= BASE_URL ?><?= Security::e(trim((string) ($o['link'] ?? '')) ?: '/treatments') ?>">Learn more <svg class="icon"><use href="#icon-arrow"/></svg></a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button class="carousel-btn carousel-prev" type="button" aria-label="Previous cards"><svg class="icon"><use href="#icon-arrow"/></svg></button>
                <button class="carousel-btn carousel-next" type="button" aria-label="Next cards"><svg class="icon"><use href="#icon-arrow"/></svg></button>
                <div class="carousel-dots" role="tablist" aria-label="Choose a card"></div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============ Testimonials ============ -->
<section class="section sand" id="reviews">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'testimonials', 'kicker', 'Patient Stories')) ?></span>
            <h2><?= Security::e(sec($sections, 'testimonials', 'heading', 'What Our Patients Say')) ?></h2>
            <p class="lede"><?= Security::e(sec($sections, 'testimonials', 'content', 'Real experiences from people who found their balance with us.')) ?></p>
        </div>
        <div class="card-grid">
            <?php
            $testimonials = $testimonials ?? (require APP_ROOT . '/app/data/testimonials.php');
            $i = 0;
            foreach ($testimonials as $t):
                $rating = (int) ($t['rating'] ?? 5);
                $delay = min($i * 0.1, 0.3);
                ?>
                <article class="quote-card reveal" style="--d:<?= $delay ?>s">
                    <div class="stars" aria-label="<?= $rating ?> out of 5 stars"><?php for ($s = 0; $s < $rating; $s++): ?><svg class="icon"><use href="#icon-star"/></svg><?php endfor; ?></div>
                    <blockquote>&ldquo;<?= Security::e($t['quote'] ?? '') ?>&rdquo;</blockquote>
                    <div class="quote-meta">
                        <span class="avatar <?= Security::e($t['avatar'] ?? 'a1') ?>"><?= Security::e($t['initials'] ?? '') ?></span>
                        <div><strong><?= Security::e($t['name'] ?? '') ?></strong><span><?= Security::e($t['role'] ?? '') ?></span></div>
                    </div>
                </article>
            <?php $i++; endforeach; ?>
        </div>

        <!-- Write a Review Form -->
        <div class="review-form-card reveal" style="--d:.15s">
            <h3>Share Your Experience</h3>
            <p>Had a session with us? We'd love to hear about your journey. Your review will appear after a quick approval.</p>
            <form action="<?= BASE_URL ?>/review" method="post" class="contact-form review-form">
                <input type="hidden" name="csrf_token" value="<?= Security::e(Security::csrfToken()) ?>">
                <!-- Honeypot -->
                <div class="hp-field" aria-hidden="true">
                    <label for="review-website">Website</label>
                    <input type="text" id="review-website" name="website" tabindex="-1" autocomplete="off">
                </div>
                <input type="hidden" name="form_loaded_at" value="<?= (int) ($_SESSION['review_form_ts'] ?? time()) ?>">
                <div class="form-row">
                    <div>
                        <label for="review_name">Your Name <span class="required">*</span></label>
                        <input type="text" id="review_name" name="review_name" required minlength="2" maxlength="120" placeholder="e.g. Suman P.">
                    </div>
                    <div>
                        <label for="review_role">Treatment / Program</label>
                        <input type="text" id="review_role" name="review_role" maxlength="120" placeholder="e.g. 7-Day Yoga Retreat">
                    </div>
                </div>
                <div>
                    <label for="review_rating_group" class="required-star-label">Your Rating <span class="required">*</span></label>
                    <div class="rating-input" role="radiogroup" id="review_rating_group" aria-label="Rating">
                        <input type="radio" name="review_rating" id="star5" value="5" required>
                        <label for="star5" class="rating-star" title="5 stars">
                            <svg class="icon"><use href="#icon-star"/></svg>
                        </label>
                        <input type="radio" name="review_rating" id="star4" value="4">
                        <label for="star4" class="rating-star" title="4 stars">
                            <svg class="icon"><use href="#icon-star"/></svg>
                        </label>
                        <input type="radio" name="review_rating" id="star3" value="3">
                        <label for="star3" class="rating-star" title="3 stars">
                            <svg class="icon"><use href="#icon-star"/></svg>
                        </label>
                        <input type="radio" name="review_rating" id="star2" value="2">
                        <label for="star2" class="rating-star" title="2 stars">
                            <svg class="icon"><use href="#icon-star"/></svg>
                        </label>
                        <input type="radio" name="review_rating" id="star1" value="1">
                        <label for="star1" class="rating-star" title="1 star">
                            <svg class="icon"><use href="#icon-star"/></svg>
                        </label>
                    </div>
                </div>
                <div>
                    <label for="review_quote">Your Review <span class="required">*</span></label>
                    <textarea id="review_quote" name="review_quote" rows="4" required minlength="10" maxlength="2000" placeholder="Tell us about your experience at Chitrawan Nature Cure Hospital..."></textarea>
                </div>
                <button type="submit" class="btn btn-dark">Submit Review <svg class="icon"><use href="#icon-arrow"/></svg></button>
            </form>
        </div>
    </div>
</section>

<!-- ============ Our Story ============ -->
<section class="section alt story-section">
    <div class="container split reverse">
        <div class="media-frame reveal">
            <img src="<?= Security::e(sec($sections, 'story', 'image')) ?>" alt="The story of Chitrawan Nature Cure Hospital" loading="lazy">
            <div class="media-badge">
                <?php $since = sec($sections, 'story', 'sub_content', '12+ Years|Of natural healing'); ?>
                <?php [$badgeTitle, $badgeText] = array_pad(explode('|', $since, 2), 2, ''); ?>
                <strong><?= Security::e($badgeTitle) ?></strong>
                <span><?= Security::e($badgeText) ?></span>
            </div>
        </div>
        <div class="reveal" style="--d:.12s">
            <span class="kicker"><?= Security::e(sec($sections, 'story', 'kicker', 'Our Story')) ?></span>
            <h2><?= Security::e(sec($sections, 'story', 'heading', 'A Journey of Healing, Growing with Every Patient')) ?></h2>
            <?php foreach (preg_split('/\R\s*\R/', sec($sections, 'story', 'content', '')) as $para): ?>
                <?php if (trim($para) !== ''): ?>
                    <p><?= Security::e(trim($para)) ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
            <ul class="check-list">
                <?php foreach ((array) ($sections['story']['extras'] ?? []) as $line): ?>
                    <li><svg class="icon"><use href="#icon-check"/></svg> <?= Security::e((string) $line) ?></li>
                <?php endforeach; ?>
            </ul>
            <a class="btn btn-dark" href="<?= Security::e(sec_url(sec($sections, 'story', 'link', '/about'))) ?>"><?= Security::e(sec($sections, 'story', 'link_label', 'Read Our Full Story')) ?> <svg class="icon"><use href="#icon-arrow"/></svg></a>
        </div>
    </div>
</section>

<!-- ============ CTA ============ -->
<section class="section">
    <div class="container">
        <?php require APP_ROOT . '/app/views/partials/cta_banner.php'; ?>
    </div>
</section>
