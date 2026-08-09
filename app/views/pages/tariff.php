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
                    <a class="btn <?= !empty($plan['featured']) ? 'btn-primary' : 'btn-outline' ?>" href="<?= BASE_URL ?>/contact"><?= Security::e($plan['ctaLabel'] ?? 'Book a Session') ?></a>
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
