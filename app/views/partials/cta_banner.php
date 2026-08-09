<?php
/**
 * Reusable CTA banner driven by the page's `cta` section.
 * Expects: $sections (page sections array) and optional $ctaTitleOverrides
 * (array of token replacements applied to heading + button labels/urls,
 * e.g. ['{title}' => 'Naturopathy', '{catUrl}' => '/treatments', '{catLabel}' => 'Treatments']).
 */
require APP_ROOT . '/app/views/partials/section_helpers.php';

$ctaHeading = sec($sections, 'cta', 'heading', 'Ready to Begin Your Wellness Journey?');
$ctaKicker = sec($sections, 'cta', 'kicker', 'Start Your Journey');
$ctaContent = sec($sections, 'cta', 'content', '');
$ctaButtons = sec_buttons($sections, 'cta');

foreach (($ctaTitleOverrides ?? []) as $token => $replacement) {
    $ctaHeading = str_replace($token, (string) $replacement, $ctaHeading);
    $ctaKicker = str_replace($token, (string) $replacement, $ctaKicker);
    $ctaContent = str_replace($token, (string) $replacement, $ctaContent);
    foreach ($ctaButtons as $i => $b) {
        $ctaButtons[$i]['label'] = str_replace($token, (string) $replacement, $b['label']);
        $ctaButtons[$i]['url'] = str_replace($token, (string) $replacement, $b['url']);
    }
}
?>
<div class="cta-banner reveal">
    <div>
        <span class="kicker" style="color:var(--apricot)"><?= Security::e($ctaKicker) ?></span>
        <h2><?= Security::e($ctaHeading) ?></h2>
        <p><?= Security::e($ctaContent) ?></p>
    </div>
    <?php if ($ctaButtons !== []): ?>
        <div class="cta-actions">
            <?php foreach ($ctaButtons as $b): ?>
                <a class="btn btn-<?= Security::e($b['style']) ?>" href="<?= Security::e(sec_url($b['url'])) ?>">
                    <?= Security::e($b['label']) ?>
                    <?php if ($b['style'] === 'primary'): ?><svg class="icon"><use href="#icon-arrow"/></svg><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
