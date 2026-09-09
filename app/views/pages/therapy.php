<?php
/** @var array<string, mixed> $therapy */
$catUrl = $therapy['category'] ?? '/treatments';
$catLabel = $therapy['categoryLabel'] ?? 'Treatments';
$title = $therapy['title'] ?? '';
?>

<!-- Overview -->
<section class="section">
    <div class="container split">
        <div class="media-frame reveal">
            <img src="<?= Security::e($therapy['image'] ?? '') ?>" alt="<?= Security::e($title) ?> at Chitrawan Nature Cure Hospital" loading="lazy">
            <div class="media-badge">
                <strong><?= Security::e($title) ?></strong>
                <span><?= Security::e($catLabel) ?> &middot; Chitrawan Nature Cure Hospital</span>
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
            <p class="lede">What a <?= Security::e($title) ?> session at Chitrawan includes.</p>
        </div>
        <div class="methods-timeline">
            <?php foreach (($therapy['methods'] ?? []) as $i => $method): ?>
                <?php
                // A method is either a plain string (title only) or an array
                // with 'title' + 'definition' (definition expands on click).
                $mTitle = is_array($method) ? trim((string) ($method['title'] ?? '')) : trim((string) $method);
                $mDef = is_array($method) ? trim((string) ($method['definition'] ?? '')) : '';
                if ($mTitle === '') continue;
                $mId = 'tl-def-' . $i;
                ?>
                <article class="timeline-item reveal" style="--d:<?= min($i * 0.08, 0.4) ?>s">
                    <span class="timeline-node" aria-hidden="true"></span>
                    <div class="timeline-card">
                        <span class="timeline-step"><?= sprintf('%02d', $i + 1) ?></span>
                        <h3><?= Security::e($mTitle) ?></h3>
                        <?php if ($mDef !== ''): ?>
                            <button type="button" class="timeline-toggle" aria-expanded="false" aria-controls="<?= Security::e($mId) ?>">
                                Click to read the definition
                                <svg class="icon"><use href="#icon-chevron"/></svg>
                            </button>
                            <div class="timeline-def" id="<?= Security::e($mId) ?>">
                                <p><?= Security::e($mDef) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
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
