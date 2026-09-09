<?php
/**
 * Renders a grid of therapy cards linking to individual therapy pages.
 * Expects: $items = array of therapy entries keyed by slug.
 *
 * Each card is a large clickable photo card: image with a gradient overlay,
 * a glassy icon badge, a faint step number, the title on the image, and the
 * intro + "View details" link in the body below.
 */
?>
<div class="therapy-grid">
    <?php $idx = 0; foreach ($items as $slug => $t): ?>
        <?php $delay = min($idx * 0.08, 0.4); ?>
        <?php $tCat = Security::e($t['category'] ?? ''); ?>
        <?php $tTitle = Security::e($t['title'] ?? ''); ?>
        <?php $tIntro = Security::e($t['intro'] ?? ''); ?>
        <a class="therapy-card reveal" style="--d:<?= $delay ?>s" href="<?= BASE_URL ?><?= $tCat ?>/<?= Security::e($slug) ?>" aria-label="View <?= $tTitle ?>">
            <span class="therapy-card-media">
                <?php $tImg = trim((string) ($t['image'] ?? '')); ?>
                <?php if ($tImg !== ''): ?>
                    <img src="<?= Security::e($tImg) ?>" alt="<?= $tTitle ?>" loading="lazy">
                <?php endif; ?>
                <span class="therapy-card-shade" aria-hidden="true"></span>
                <span class="therapy-card-icon" aria-hidden="true"><svg class="icon"><use href="#<?= Security::e($t['icon'] ?? 'icon-leaf') ?>"/></svg></span>
                <span class="therapy-card-num" aria-hidden="true"><?= sprintf('%02d', $idx + 1) ?></span>
                <span class="therapy-card-title"><?= $tTitle ?></span>
            </span>
            <span class="therapy-card-body">
                <span class="therapy-card-text"><?= $tIntro ?></span>
                <span class="therapy-card-link">View details <svg class="icon"><use href="#icon-arrow"/></svg></span>
            </span>
        </a>
        <?php $idx++; ?>
    <?php endforeach; ?>
</div>
