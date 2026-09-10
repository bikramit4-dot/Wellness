<?php
/**
 * Editable site brand used in the header and footer.
 *
 * Reads the `site`/`brand` page section (managed from
 * Admin → Pages → Site Settings → Logo & Brand). If a logo image is set it
 * replaces the leaf-icon mark; the brand name comes from the same section.
 *
 * Optional: $brandClass — extra class(es) for the <a> (e.g. footer-brand-name).
 */
if (!isset($GLOBALS['__siteSectionsLoaded'])) {
    $GLOBALS['__siteSectionsLoaded'] = true;
    $GLOBALS['__siteSections'] = PageSectionModel::forPage('site');
}
$brand = $GLOBALS['__siteSections']['brand'] ?? [];
$logoUrl = trim((string) ($brand['image'] ?? ''));
$brandName = trim((string) ($brand['heading'] ?? ''));
if ($brandName === '') {
    $brandName = 'Chitrawan Nature Cure Hospital';
}

// Split the name so the last word keeps the
// italic accent style (e.g. "Chitrawan Nature Cure <em>Hospital</em>").
$nameParts = preg_split('/\s+/', $brandName, -1, PREG_SPLIT_NO_EMPTY);
$accent = count($nameParts) > 1 ? array_pop($nameParts) : '';
$main = implode(' ', $nameParts);
$brandClass = $brandClass ?? '';
?>
<a class="brand <?= Security::e($brandClass) ?>" href="<?= BASE_URL ?>/">
    <?php if ($logoUrl !== ''): ?>
        <img class="brand-logo" src="<?= Security::e($logoUrl) ?>" alt="<?= Security::e($brandName) ?>" loading="lazy">
    <?php else: ?>
        <span class="brand-mark" aria-hidden="true"><svg class="icon"><use href="#icon-leaf"/></svg></span>
    <?php endif; ?>
    <span class="brand-name"><?= Security::e($main) ?><?php if ($accent !== ''): ?> <em><?= Security::e($accent) ?></em><?php endif; ?></span>
</a>
