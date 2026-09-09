<?php require APP_ROOT . '/app/views/partials/section_helpers.php'; ?>

<section class="section">
    <div class="container container-narrow">

        <!-- Back link -->
        <a class="article-back" href="<?= BASE_URL ?>/tariff">
            <svg class="icon"><use href="#icon-arrow" /></svg> Back to Tariff
        </a>

        <!-- Plan header card -->
        <div class="plan-detail reveal">
            <?php if (!empty($plan['featured'])): ?>
                <span class="plan-flag"><?= Security::e($plan['badge'] ?? 'Popular') ?></span>
            <?php endif; ?>

            <span class="kicker"><?= Security::e($plan['kicker'] ?? 'Wellness Package') ?></span>
            <h1><?= Security::e($plan['name'] ?? 'Plan') ?></h1>

            <div class="plan-detail-price">
                <?= Security::e($plan['price'] ?? '') ?>
                <small>/ <?= Security::e($plan['period'] ?? '') ?></small>
            </div>

            <p class="plan-detail-desc"><?= Security::e($plan['description'] ?? '') ?></p>

            <!-- What's included -->
            <div class="plan-detail-includes">
                <h2>What's Included</h2>
                <ul class="check-list">
                    <?php foreach (($plan['features'] ?? []) as $feature): ?>
                        <li>
                            <svg class="icon"><use href="#icon-check" /></svg>
                            <?= Security::e($feature) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Additional details if available -->
            <?php if (!empty($plan['details'])): ?>
                <div class="plan-detail-extra">
                    <h2>Plan Details</h2>
                    <?php foreach (preg_split('/\R\s*\R/', $plan['details']) as $para): ?>
                        <?php if (trim($para) !== ''): ?>
                            <p><?= Security::e(trim($para)) ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- CTA buttons -->
            <div class="plan-detail-actions">
                <a class="btn btn-primary" href="<?= BASE_URL ?>/tariff?package=<?= urlencode($plan['name'] ?? '') ?>#qr-payment">
                    Pay Advance for This Plan <svg class="icon"><use href="#icon-arrow" /></svg>
                </a>
                <a class="btn btn-outline" href="<?= BASE_URL ?>/contact">
                    Book This Plan
                </a>
                <a class="btn btn-outline" href="<?= BASE_URL ?>/contact">
                    Ask a Question
                </a>
            </div>

            <!-- Trust note -->
            <div class="plan-detail-trust">
                <svg class="icon"><use href="#icon-shield" /></svg>
                <p>Your booking is confirmed by our team within 24 hours. No payment required until confirmation.</p>
            </div>
        </div>

        <!-- Related: Rate Card -->
        <div class="plan-detail-related reveal" style="--d:.12s">
            <h2>Individual Service Rates</h2>
            <p>Prefer a single session instead? View our per-service pricing.</p>
            <a class="btn btn-dark" href="<?= BASE_URL ?>/tariff#rate-card">
                View Rate Card <svg class="icon"><use href="#icon-arrow" /></svg>
            </a>
        </div>
    </div>
</section>
