<?php require APP_ROOT . '/app/views/partials/section_helpers.php'; ?>

<section class="section">
    <div class="container">
        <?php
        $tariff = $tariff ?? (require APP_ROOT . '/app/data/tariff.php');
        $plans = $tariff['plans'] ?? [];
        ?>
        <div class="section-head center reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'packages', 'kicker', 'Pricing')) ?></span>
            <h2><?= Security::e(sec($sections, 'packages', 'heading', 'Wellness Packages')) ?></h2>
            <p class="lede"><?= Security::e(sec($sections, 'packages', 'content', 'Simple, transparent pricing for every stage of your journey.')) ?></p>
        </div>
        <div class="plans">
            <?php $i = 0; foreach ($plans as $plan): ?>
                <?php $delay = min($i * 0.12, 0.24); ?>
                <article class="plan <?= !empty($plan['featured']) ? 'plan-featured' : '' ?> reveal" style="--d:<?= $delay ?>s">
                    <?php if (!empty($plan['badge'])): ?><span class="plan-flag"><?= Security::e($plan['badge']) ?></span><?php endif; ?>
                    <h3><?= Security::e($plan['name'] ?? '') ?></h3>
                    <p class="plan-desc"><?= Security::e($plan['description'] ?? '') ?></p>
                    <div class="plan-price"><?= Security::e($plan['price'] ?? '') ?> <small>/ <?= Security::e($plan['period'] ?? '') ?></small></div>
                    <ul>
                        <?php foreach (($plan['features'] ?? []) as $feature): ?>
                            <li><svg class="icon"><use href="#icon-check"/></svg> <?= Security::e($feature) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a class="btn <?= !empty($plan['featured']) ? 'btn-primary' : 'btn-outline' ?>" href="<?= BASE_URL ?>/tariff/plan/<?= $i ?>"><?= Security::e($plan['ctaLabel'] ?? 'View Details') ?> <svg class="icon"><use href="#icon-arrow"/></svg></a>
                </article>
                <?php $i++; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section alt">
    <div class="container">
        <div class="section-head reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'services', 'kicker', 'Rate Card')) ?></span>
            <h2><?= Security::e(sec($sections, 'services', 'heading', 'Individual Service Prices')) ?></h2>
            <p class="lede"><?= Security::e(sec($sections, 'services', 'content', 'All sessions include a personal assessment with your therapist.')) ?></p>
        </div>
        <div class="table-wrap reveal">
            <table class="table">
                <thead>
                    <tr><th>Service</th><th>Duration</th><th>Price</th></tr>
                </thead>
                <tbody>
                    <?php foreach (($tariff['services'] ?? []) as $svc): ?>
                        <tr><td><?= Security::e($svc['service'] ?? '') ?></td><td class="duration"><?= Security::e($svc['duration'] ?? '') ?></td><td class="price"><?= Security::e($svc['price'] ?? '') ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p style="color:var(--muted);font-size:.88rem;margin-top:1rem;"><?= Security::e(sec($sections, 'services', 'sub_content', 'Prices are indicative. Please contact us for the latest offers and package discounts.')) ?></p>
    </div>
</section>

<!-- ============ Advance Payment by QR Code ============ -->
<section class="section" id="qr-payment">
    <div class="container">
        <div class="section-head reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'qr', 'kicker', 'Advance Payment')) ?></span>
            <h2><?= Security::e(sec($sections, 'qr', 'heading', 'Pay Your Advance by QR Code')) ?></h2>
            <p class="lede"><?= Security::e(sec($sections, 'qr', 'content', 'Secure your package or appointment with a small advance payment. Scan the QR code with any UPI payment app, then submit your details with the payment screenshot below.')) ?></p>
        </div>

        <div class="qr-grid">
            <!-- QR code + how to pay -->
            <div class="qr-card reveal">
                <?php $qrImage = trim((string) sec($sections, 'qr', 'image')); ?>
                <?php if ($qrImage !== ''): ?>
                    <img class="qr-image" src="<?= Security::e($qrImage) ?>" alt="Advance payment QR code" loading="lazy">
                <?php else: ?>
                    <div class="qr-placeholder" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="square" class="icon">
                            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                            <rect x="3" y="14" width="7" height="7"/>
                            <path d="M14 14h3v3h-3zM20 14h1M14 20h1M17 20h4v-3M20 17v-2"/>
                        </svg>
                        <span>QR code coming soon</span>
                    </div>
                <?php endif; ?>

                <?php $qrDetails = trim((string) sec($sections, 'qr', 'sub_content', '')); ?>
                <?php if ($qrDetails !== ''): ?>
                    <div class="qr-details"><svg class="icon"><use href="#icon-zap"/></svg> <?= Security::e($qrDetails) ?></div>
                <?php endif; ?>

                <?php $qrSteps = is_array($sections['qr']['extras'] ?? null) ? $sections['qr']['extras'] : []; ?>
                <?php if ($qrSteps !== []): ?>
                    <ol class="qr-steps">
                        <?php $qrN = 0; foreach ($qrSteps as $qrStep): $qrN++; ?>
                            <li><span><?= $qrN ?></span><?= Security::e($qrStep) ?></li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>

                <p class="qr-note"><?= Security::e(sec($sections, 'qr', 'link_label', 'Keep your payment reference number — it helps us verify your payment faster.')) ?></p>
            </div>

            <!-- Payment details form -->
            <div class="form-card qr-form reveal" style="--d:.12s">
                <h2>Submit Your Payment Details</h2>
                <p>After completing the payment, fill in this form so our team can verify your screenshot and confirm your booking.</p>
                <form action="<?= BASE_URL ?>/qr-payment" method="post" enctype="multipart/form-data" class="contact-form">
                    <input type="hidden" name="csrf_token" value="<?= Security::e(Security::csrfToken()) ?>">
                    <!-- Honeypot: invisible to humans; bots that fill it in are discarded -->
                    <div class="hp-field" aria-hidden="true">
                        <label for="website">Website</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>
                    <input type="hidden" name="form_loaded_at" value="<?= (int) ($_SESSION['tariff_form_ts'] ?? time()) ?>">
                    <div>
                        <label for="package">Select Package <span class="required">*</span></label>
                        <select id="package" name="package" required>
                            <option value="">-- Select a package --</option>
                            <?php
                            $selectedPkg = rawurldecode($_GET['package'] ?? '');
                            $tariffPlans = $tariff['plans'] ?? [];
                            foreach ($tariffPlans as $plan):
                                $planName = Security::e($plan['name'] ?? '');
                                $planPrice = Security::e($plan['price'] ?? '');
                                $planPeriod = Security::e($plan['period'] ?? '');
                                $isSelected = ($selectedPkg !== '' && $selectedPkg === ($plan['name'] ?? ''));
                            ?>
                                <option value="<?= $planName ?>"<?= $isSelected ? ' selected' : '' ?>><?= $planName ?> — <?= $planPrice ?>/<?= $planPeriod ?></option>
                            <?php endforeach; ?>
                            <option value="Individual Service"<?= ($selectedPkg === 'Individual Service') ? ' selected' : '' ?>>Individual Service (see rate card below)</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div>
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" required placeholder="Your full name">
                        </div>
                        <div>
                            <label for="phone">Phone Number <span class="required">*</span></label>
                            <input type="tel" id="phone" name="phone" required pattern="[+\d\s\-]{7,20}" placeholder="+977 56-535213">
                        </div>
                    </div>
                    <div>
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required placeholder="you@example.com">
                    </div>
                    <div>
                        <label for="address">Full Address</label>
                        <input type="text" id="address" name="address" required placeholder="House / street / city">
                    </div>
                    <div class="form-row">
                        <div>
                            <label for="amount">Advance Amount</label>
                            <input type="text" id="amount" name="amount" required placeholder="e.g. Rs. 2000">
                        </div>
                        <div>
                            <label for="transaction_id">Transaction / UTR No.</label>
                            <input type="text" id="transaction_id" name="transaction_id" required placeholder="Reference ID from your payment app">
                        </div>
                    </div>
                    <div>
                        <label for="message">Message <span style="font-weight:400;color:var(--muted)">(optional)</span></label>
                        <textarea id="message" name="message" rows="3" placeholder="e.g. Booking for the 7-day Naturopathy package"></textarea>
                    </div>
                    <div>
                        <label for="screenshot">Payment Screenshot</label>
                        <input type="file" id="screenshot" name="screenshot" accept="image/jpeg,image/png,image/webp,image/gif" required class="file-input">
                        <p class="field-hint">Attach a clear screenshot of your payment (JPG, PNG, WEBP or GIF, up to 5 MB). Auto-optimized to WebP for faster loading.</p>
                    </div>
                    <button type="submit" class="btn btn-dark">Submit Payment Details <svg class="icon"><use href="#icon-arrow"/></svg></button>
                </form>
            </div>
        </div>
    </div>
</section>
