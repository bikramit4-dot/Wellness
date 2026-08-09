<?php
/**
 * Renders a grid of therapy cards linking to individual therapy pages.
 * Expects: $items = array of therapy entries keyed by slug.
 */
?>
<div class="card-grid">
    <?php $idx = 0; foreach ($items as $slug => $t): ?>
        <?php $delay = min($idx++ * 0.08, 0.4); ?>
        <article class="card reveal" style="--d:<?= $delay ?>s">
            <div class="card-icon"><svg class="icon"><use href="#<?= Security::e($t['icon'] ?? 'icon-leaf') ?>"/></svg></div>
            <h3><?= Security::e($t['title'] ?? '') ?></h3>
            <p><?= Security::e($t['intro'] ?? '') ?></p>
            <a class="card-link" href="<?= BASE_URL ?><?= Security::e($t['category'] ?? '') ?>/<?= Security::e($slug) ?>">View details <svg class="icon"><use href="#icon-arrow"/></svg></a>
        </article>
    <?php endforeach; ?>
</div>
