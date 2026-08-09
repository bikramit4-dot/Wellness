<?php
/** @var array<string, mixed> $therapy */
$catUrl = $therapy['category'] ?? '/treatments';
$catLabel = $therapy['categoryLabel'] ?? 'Treatments';
$icon = $therapy['icon'] ?? 'icon-leaf';
$title = $therapy['title'] ?? '';
?>

<!-- Overview -->
<section class="section">
    <div class="container split">
        <div class="media-frame reveal">
            <img src="<?= Security::e($therapy['image'] ?? '') ?>" alt="<?= Security::e($title) ?> at Harmony Wellness Center" loading="lazy">
            <div class="media-badge">
                <strong><?= Security::e($title) ?></strong>
                <span><?= Security::e($catLabel) ?> &middot; Harmony Wellness Center</span>
            </div>
        </div>
        <div class="reveal" style="--d:.12s">
            <span class="kicker"><?= Security::e($catLabel) ?></span>
            <h2>About <?= Security::e($title) ?></h2>
            <?php foreach (($therapy['about'] ?? []) as $para): ?>
                <p><?= $para ?></p>
            <?php endforeach; ?>
            <ul class="check-list">
                <li><svg class="icon"><use href="#icon-check"/></svg> Guided by experienced, certified therapists</li>
                <li><svg class="icon"><use href="#icon-check"/></svg> Personalized to your health and goals</li>
                <li><svg class="icon"><use href="#icon-check"/></svg> Natural, drug-free approach</li>
            </ul>
            <a class="btn btn-dark" href="<?= BASE_URL ?>/contact">Book <?= Security::e($title) ?> <svg class="icon"><use href="#icon-arrow"/></svg></a>
        </div>
    </div>
</section>

<!-- Methods / techniques -->
<section class="section alt">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker">How It Works</span>
            <h2>Techniques &amp; Methods</h2>
            <p class="lede">What a <?= Security::e($title) ?> session at Harmony includes.</p>
        </div>
        <div class="card-grid">
            <?php foreach (($therapy['methods'] ?? []) as $i => $method): ?>
                <article class="card reveal" style="--d:<?= min($i * 0.08, 0.4) ?>s">
                    <div class="card-icon"><svg class="icon"><use href="#<?= Security::e($icon) ?>"/></svg></div>
                    <h3><?= $method ?></h3>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Benefits -->
<section class="section">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker">Why It Helps</span>
            <h2>Key Benefits</h2>
            <p class="lede">How <?= Security::e($title) ?> supports your wellness journey.</p>
        </div>
        <div class="benefits-grid">
            <?php foreach (($therapy['benefits'] ?? []) as $i => $benefit): ?>
                <div class="benefit reveal" style="--d:<?= min($i * 0.08, 0.4) ?>s">
                    <svg class="icon"><use href="#icon-check"/></svg>
                    <span><?= $benefit ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section alt">
    <div class="container">
        <?php
        $ctaTitleOverrides = [
            '{title}' => $title,
            '{catUrl}' => $catUrl,
            '{catLabel}' => $catLabel,
        ];
        require APP_ROOT . '/app/views/partials/cta_banner.php';
        ?>
    </div>
</section>
