<?php require APP_ROOT . '/app/views/partials/section_helpers.php'; ?>

<section class="section">
    <div class="container contact-grid">
        <div>
            <div class="reveal">
                <span class="kicker"><?= Security::e(sec($sections, 'info', 'kicker', 'Reach Us')) ?></span>
                <h2><?= Security::e(sec($sections, 'info', 'heading', 'Contact Information')) ?></h2>
            </div>
            <?php
            $infoItems = $sections['info']['extras'] ?? [];
            $infoIcons = ['icon-map-pin', 'icon-phone', 'icon-mail', 'icon-clock'];
            $i = 0;
            foreach ($infoItems as $line):
                [$infoTitle, $infoText] = array_pad(explode('|', (string) $line, 2), 2, '');
                $delay = .05 + $i * .05;
                $icon = $infoIcons[$i] ?? 'icon-map-pin';
                ?>
                <div class="info-card reveal" style="--d:<?= $delay ?>s">
                    <div class="card-icon"><svg class="icon"><use href="#<?= Security::e($icon) ?>"/></svg></div>
                    <div>
                        <h3><?= Security::e($infoTitle) ?></h3>
                        <?php $escaped = Security::e($infoText); ?>
                        <?php if (str_starts_with($infoText, '+')): ?>
                            <p><a href="tel:<?= Security::e(preg_replace('/\s+/', '', $infoText)) ?>"><?= $escaped ?></a></p>
                        <?php elseif (str_contains($infoText, '@')): ?>
                            <p><a href="mailto:<?= Security::e($infoText) ?>"><?= $escaped ?></a></p>
                        <?php else: ?>
                            <p><?= nl2br($escaped) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php $i++; endforeach; ?>
        </div>

        <div class="form-card reveal" style="--d:.12s">
            <h2><?= Security::e(sec($sections, 'form', 'heading', 'Book an Appointment')) ?></h2>
            <p><?= Security::e(sec($sections, 'form', 'content', 'Fill in your details and our team will confirm your booking shortly.')) ?></p>
            <form action="<?= BASE_URL ?>/appointment" method="post" class="contact-form">
                <input type="hidden" name="csrf_token" value="<?= Security::e(Security::csrfToken()) ?>">
                <!-- Honeypot: invisible to humans; bots that fill it in are discarded -->
                <div class="hp-field" aria-hidden="true">
                    <label for="website">Website</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>
                <input type="hidden" name="form_loaded_at" value="<?= (int) ($_SESSION['contact_form_ts'] ?? time()) ?>">
                <div class="form-row">
                    <div>
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" required placeholder="Your full name">
                    </div>
                    <div>
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" placeholder="+977-...">
                    </div>
                </div>
                <div>
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="you@example.com">
                </div>
                <div>
                    <label for="treatment">Select Treatment</label>
                    <select id="treatment" name="treatment">
                        <option value="Naturopathy">Naturopathy</option>
                        <option value="Yoga Therapy">Yoga Therapy</option>
                        <option value="Acupuncture">Acupuncture</option>
                        <option value="Physiotherapy">Physiotherapy</option>
                        <option value="Diet Therapy">Diet Therapy</option>
                        <option value="Special Therapy">Special Therapy</option>
                    </select>
                </div>
                <div class="form-row">
                    <div>
                        <label for="preferred_date">Preferred Date</label>
                        <input type="date" id="preferred_date" name="preferred_date">
                    </div>
                    <div>
                        <label for="preferred_time">Preferred Time</label>
                        <input type="time" id="preferred_time" name="preferred_time">
                    </div>
                </div>
                <div>
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="4" placeholder="Tell us about your health goals or questions..."></textarea>
                </div>
                <button type="submit" class="btn btn-dark">Book Appointment <svg class="icon"><use href="#icon-arrow"/></svg></button>
            </form>
        </div>
    </div>
</section>

<!-- ============ Map ============ -->
<section class="section" style="padding-top:0">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'map', 'kicker', 'Find Us')) ?></span>
            <h2><?= Security::e(sec($sections, 'map', 'heading', 'Our Location')) ?></h2>
            <p class="lede"><?= Security::e(sec($sections, 'map', 'content', 'Harmony Wellness Center · Kathmandu, Nepal')) ?></p>
        </div>
        <div class="map-card reveal" style="--d:.1s">
            <iframe
                title="Map showing the location of Harmony Wellness Center in Kathmandu, Nepal"
                src="https://maps.google.com/maps?q=<?= Security::e(urlencode(sec($sections, 'map', 'link', 'Harmony Wellness Center, Kathmandu, Nepal'))) ?>&z=14&output=embed"
                width="600" height="420" loading="lazy" allowfullscreen
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</section>
