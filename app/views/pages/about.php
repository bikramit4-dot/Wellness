<?php require APP_ROOT . '/app/views/partials/section_helpers.php'; ?>

<?php
/* ---------- Structured content from the editable page sections ---------- */
$founderSection = $sections['founder'] ?? [];
$founderDefaults = !isset($founderSection['id']);
[$fName, $fRole] = array_pad(explode('|', (string) ($founderDefaults ? 'Dr. Rajesh Sharma|Founder & Holistic Medicine Specialist' : ($founderSection['sub_content'] ?? '')), 2), 2, '');
$founders = [];
if ($fName !== '') {
    $founders[] = [
        'name' => $fName,
        'role' => $fRole,
        'image' => (string) ($founderDefaults ? sec($sections, 'founder', 'image') : ($founderSection['image'] ?? '')),
        'bio' => (string) ($founderDefaults ? sec($sections, 'founder', 'content') : ($founderSection['content'] ?? '')),
    ];
}
foreach ((array) ($sections['founder']['extras'] ?? []) as $line) {
    $line = trim((string) $line);
    if ($line === '') { continue; }
    [$name, $role, $image, $bio] = array_pad(array_map('trim', explode('|', $line, 4)), 4, '');
    $founders[] = ['name' => $name, 'role' => $role, 'image' => $image, 'bio' => $bio];
}

$approaches = [];
foreach ((array) ($sections['approach']['extras'] ?? []) as $line) {
    $line = trim((string) $line);
    if ($line === '') { continue; }
    [$title, $text, $icon, $image] = array_pad(array_map('trim', explode('|', $line, 4)), 4, '');
    $approaches[] = ['title' => $title, 'text' => $text, 'icon' => $icon, 'image' => $image];
}

$doctors = [];
foreach ((array) ($sections['doctors']['extras'] ?? []) as $line) {
    $line = trim((string) $line);
    if ($line === '') { continue; }
    [$name, $role, $image] = array_pad(array_map('trim', explode('|', $line, 3)), 3, '');
    $doctors[] = ['name' => $name, 'role' => $role, 'image' => $image];
}

$missionItems = array_values(array_filter(
    (array) ($sections['mission']['extras'] ?? []),
    static fn ($line): bool => trim((string) $line) !== ''
));
$team = $team ?? (require APP_ROOT . '/app/data/team.php');

$visions = [];
foreach ((array) ($sections['vision']['extras'] ?? []) as $line) {
    $line = trim((string) $line);
    if ($line === '') { continue; }
    [$title, $text, $icon] = array_pad(array_map('trim', explode('|', $line, 3)), 3, '');
    $visions[] = ['title' => $title, 'text' => $text, 'icon' => $icon];
}

$customSections = [];
foreach ($sections as $sectionKey => $sectionData) {
    if (!str_starts_with($sectionKey, 'custom-')) { continue; }
    $items = array_values(array_filter(
        (array) ($sectionData['extras'] ?? []),
        static fn ($line): bool => trim((string) $line) !== ''
    ));
    if ($items === []) { continue; }
    $customSections[] = [
        'key' => $sectionKey,
        'type' => trim((string) ($sectionData['sub_content'] ?? 'custom')),
        'kicker' => trim((string) ($sectionData['kicker'] ?? '')),
        'heading' => trim((string) ($sectionData['heading'] ?? 'Untitled section')),
        'content' => trim((string) ($sectionData['content'] ?? '')),
        'items' => $items,
    ];
}

$paragraphs = static function (string $text): string {
    $html = '';
    foreach (preg_split('/\n\s*\n/', $text) ?: [] as $paragraph) {
        if (trim($paragraph) !== '') {
            $html .= '<p>' . Security::e(trim($paragraph)) . '</p>';
        }
    }
    return $html;
};

$listHtml = static function (array $items): string {
    if ($items === []) { return ''; }
    $html = '<ul class="info-modal-list">';
    foreach ($items as $item) {
        $html .= '<li><svg class="icon"><use href="#icon-check"/></svg> ' . Security::e((string) $item) . '</li>';
    }
    return $html . '</ul>';
};

$renderModal = static function (string $id, string $title, string $subtitle, string $image, string $bodyHtml): void {
    ?>
    <div class="info-modal" id="<?= Security::e($id) ?>" role="dialog" aria-modal="true" aria-labelledby="<?= Security::e($id) ?>-title" hidden>
        <div class="info-modal-backdrop" data-modal-close></div>
        <div class="info-modal-panel">
            <button type="button" class="info-modal-close" data-modal-close aria-label="Close">&times;</button>
            <?php if ($image !== ''): ?>
                <div class="info-modal-media"><img src="<?= Security::e($image) ?>" alt="<?= Security::e($title) ?>" loading="lazy"></div>
            <?php else: ?>
                <div class="info-modal-media info-modal-icon"><svg class="icon"><use href="#icon-leaf"/></svg></div>
            <?php endif; ?>
            <div class="info-modal-body">
                <h3 id="<?= Security::e($id) ?>-title"><?= Security::e($title) ?></h3>
                <?php if ($subtitle !== ''): ?><p class="info-modal-sub"><?= Security::e($subtitle) ?></p><?php endif; ?>
                <?= $bodyHtml ?>
            </div>
        </div>
    </div>
    <?php
};

$renderBox = static function (string $id, string $title, string $summary, string $image, string $modalTitle, string $modalSubtitle, string $bodyHtml, int $index = 0) use ($renderModal): void {
    ?>
    <button type="button" class="info-box reveal" style="--d:<?= min($index * 0.08, 0.4) ?>s" data-modal-target="#<?= Security::e($id) ?>" aria-haspopup="dialog" aria-label="View full information about <?= Security::e($title) ?>">
        <span class="info-box-media<?= $image === '' ? ' info-box-media-icon' : '' ?>">
            <?php if ($image !== ''): ?>
                <img src="<?= Security::e($image) ?>" alt="<?= Security::e($title) ?>" loading="lazy">
            <?php else: ?>
                <svg class="icon"><use href="#icon-leaf"/></svg>
            <?php endif; ?>
            <span class="info-box-plus" aria-hidden="true">+</span>
        </span>
        <span class="info-box-body">
            <strong><?= Security::e($title) ?></strong>
            <?php if ($summary !== ''): ?><span><?= Security::e($summary) ?></span><?php endif; ?>
            <em>View</em>
        </span>
    </button>
    <?php $renderModal($id, $modalTitle, $modalSubtitle, $image, $bodyHtml); ?>
    <?php
};
?>

<!-- ============ 01 · Our Founders ============ -->
<section class="section" id="founder">
    <div class="container">
        <div class="section-head center founder-head reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'founder', 'kicker', '01 · Our Founders')) ?></span>
            <h2><?= Security::e(sec($sections, 'founder', 'heading', 'Meet Our Founders')) ?></h2>
            <div class="lede founder-intro"><?= $paragraphs(sec($sections, 'founder', 'content')) ?></div>
        </div>
        <div class="info-box-grid">
            <?php foreach ($founders as $idx => $founder): ?>
                <?php
                $modalId = 'modal-founder-' . $idx;
                $body = $paragraphs($founder['bio']);
                $renderBox($modalId, $founder['name'], $founder['role'], $founder['image'], $founder['name'], $founder['role'], $body, $idx);
                ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ 02 · Our Approach ============ -->
<section class="section alt" id="approach">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'approach', 'kicker', '02 · Our Approach')) ?></span>
            <h2><?= Security::e(sec($sections, 'approach', 'heading', 'Treating the Root Cause')) ?></h2>
            <p class="lede"><?= Security::e(sec($sections, 'approach', 'content')) ?></p>
        </div>
        <div class="info-box-grid">
            <?php foreach ($approaches as $idx => $approach): ?>
                <?php
                $modalId = 'modal-approach-' . $idx;
                $body = '<p>' . Security::e($approach['text']) . '</p>';
                $renderBox($modalId, $approach['title'], 'Learn about our approach', $approach['image'], $approach['title'], '', $body, $idx);
                ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ 03 · Our Doctors ============ -->
<section class="section" id="doctors">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'doctors', 'kicker', '03 · Our Doctors')) ?></span>
            <h2><?= Security::e(sec($sections, 'doctors', 'heading', 'Our Doctors & Specialists')) ?></h2>
            <p class="lede"><?= Security::e(sec($sections, 'doctors', 'content', 'A multidisciplinary team working together for your complete care.')) ?></p>
        </div>
        <div class="info-box-grid">
            <?php foreach ($doctors as $idx => $doctor): ?>
                <?php
                $modalId = 'modal-doctor-' . $idx;
                $body = '<p>' . Security::e($doctor['role']) . '</p>';
                $renderBox($modalId, $doctor['name'], $doctor['role'], $doctor['image'], $doctor['name'], $doctor['role'], $body, $idx);
                ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ 04 · Our Mission ============ -->
<section class="section alt" id="mission">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'mission', 'kicker', '04 · Our Mission')) ?></span>
            <h2><?= Security::e(sec($sections, 'mission', 'heading', 'Our Mission')) ?></h2>
            <p class="lede"><?= Security::e(sec($sections, 'mission', 'content', 'The promises we make to every patient who walks through our doors.')) ?></p>
        </div>
        <div class="info-box-grid">
            <?php foreach ($missionItems as $idx => $item): ?>
                <?php
                $modalId = 'modal-mission-' . $idx;
                $body = '<p>' . Security::e((string) $item) . '</p>';
                if (sec($sections, 'mission', 'sub_content') !== '') {
                    $body .= '<p>' . Security::e(sec($sections, 'mission', 'sub_content')) . '</p>';
                }
                $renderBox($modalId, (string) $item, 'Our commitment to your wellbeing', sec($sections, 'mission', 'image'), (string) $item, '', $body, $idx);
                ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ 05 · Our Group ============ -->
<section class="section" id="group">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'group', 'kicker', '05 · Our Group')) ?></span>
            <h2><?= Security::e(sec($sections, 'group', 'heading', 'Our Group')) ?></h2>
            <p class="lede"><?= Security::e(sec($sections, 'group', 'content', 'The people who make Chitrawan Nature Cure Hospital a place of healing.')) ?></p>
        </div>
        <div class="info-box-grid">
            <?php foreach ($team as $idx => $member): ?>
                <?php
                $modalId = 'modal-team-' . $idx;
                $name = (string) ($member['name'] ?? '');
                $role = (string) ($member['role'] ?? '');
                $memberImage = (string) ($member['image'] ?? '');
                $renderBox($modalId, $name, $role, $memberImage, $name, $role, '<p>' . Security::e($role) . '</p>', $idx);
                ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ 06 · Our Vision ============ -->
<section class="section alt" id="vision">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'vision', 'kicker', '06 · Our Vision')) ?></span>
            <h2><?= Security::e(sec($sections, 'vision', 'heading', 'Our Vision')) ?></h2>
        </div>
        <div class="info-box-grid">
            <?php foreach ($visions as $idx => $vision): ?>
                <?php
                $modalId = 'modal-vision-' . $idx;
                $body = '<p>' . Security::e($vision['text']) . '</p>';
                $renderBox($modalId, $vision['title'], 'Read our vision', sec($sections, 'vision', 'image'), $vision['title'], '', $body, $idx);
                ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php foreach ($customSections as $sectionIndex => $custom): ?>
    <section class="section<?= $sectionIndex % 2 === 0 ? ' alt' : '' ?>" id="<?= Security::e($custom['key']) ?>">
        <div class="container">
            <div class="section-head center reveal">
                <?php if ($custom['kicker'] !== ''): ?><span class="kicker"><?= Security::e($custom['kicker']) ?></span><?php endif; ?>
                <h2><?= Security::e($custom['heading']) ?></h2>
                <?php if ($custom['content'] !== ''): ?><p class="lede"><?= Security::e($custom['content']) ?></p><?php endif; ?>
            </div>
            <div class="info-box-grid">
                <?php foreach ($custom['items'] as $itemIndex => $line): ?>
                    <?php
                    $parts = array_map('trim', explode('|', (string) $line, 4));
                    $title = $parts[0] ?? '';
                    $summary = $parts[1] ?? '';
                    $image = $parts[2] ?? '';
                    $modalId = 'modal-custom-' . $sectionIndex . '-' . $itemIndex;
                    $body = '<p>' . Security::e($summary) . '</p>';
                    $renderBox($modalId, $title, $summary, $image, $title, '', $body, $itemIndex);
                    ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endforeach; ?>

<!-- ============ CTA ============ -->
<section class="section-flush">
    <div class="container">
        <?php require APP_ROOT . '/app/views/partials/cta_banner.php'; ?>
    </div>
</section>
