<?php require APP_ROOT . '/app/views/partials/section_helpers.php'; ?>

<?php
/* ---------- Structured content from the editable page sections ---------- */
/* Section order on this page (defined by the order in app/data/page_sections.php):
   01 About Us · 02 Our Founders · 03 Our Vision · 04 Our Mission ·
   05 Our Core Values · 06 Our Doctors · 07 Our Team. */

/* About Us intro (photo + text) */
$aboutIntroHasImage = trim((string) ($sections['aboutus']['image'] ?? '')) !== '';
$aboutIntroBadge = sec($sections, 'aboutus', 'sub_content', 'Since 2010|Healing with care & compassion');
[$aboutIntroBadgeTitle, $aboutIntroBadgeText] = array_pad(explode('|', $aboutIntroBadge, 2), 2, '');

/* Founders — first founder comes from sub_content/content/image, the rest from extras. */
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
foreach ((array) ($founderSection['extras'] ?? []) as $line) {
    $line = trim((string) $line);
    if ($line === '') { continue; }
    [$name, $role, $image, $bio] = array_pad(array_map('trim', explode('|', $line, 4)), 4, '');
    $founders[] = ['name' => $name, 'role' => $role, 'image' => $image, 'bio' => $bio];
}

/* Core values ("approach" section): Title | Text | icon | optional photo */
$approaches = [];
foreach ((array) ($sections['approach']['extras'] ?? []) as $line) {
    $line = trim((string) $line);
    if ($line === '') { continue; }
    [$title, $text, $icon, $image] = array_pad(array_map('trim', explode('|', $line, 4)), 4, '');
    $approaches[] = ['title' => $title, 'text' => $text, 'icon' => $icon, 'image' => $image];
}

/* Doctors: Name | Role | Photo */
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
$visionIntro = sec($sections, 'vision', 'content');
$visionQuote = sec($sections, 'vision', 'sub_content');

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

$initials = static function (string $name): string {
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $letters = '';
    foreach ($parts as $p) {
        if ($p !== '' && preg_match('/[A-Za-z\x{0900}-\x{097F}]/u', $p[0])) {
            $letters .= mb_strtoupper(mb_substr($p, 0, 1));
        }
        if (mb_strlen($letters) >= 2) { break; }
    }
    return $letters !== '' ? $letters : '·';
};

$listHtml = static function (array $items): string {
    if ($items === []) { return ''; }
    $html = '<ul class="info-modal-list">';
    foreach ($items as $item) {
        $html .= '<li><svg class="icon"><use href="#icon-check"/></svg> ' . Security::e((string) $item) . '</li>';
    }
    return $html . '</ul>';
};

/**
 * Modal popup shown when a box is clicked. Person modals ($variant = 'person')
 * show the full photo uncropped at the top; default modals show a wide banner.
 */
$renderModal = static function (string $id, string $title, string $subtitle, string $image, string $bodyHtml, string $variant = 'default') use ($initials): void {
    $isPerson = $variant === 'person';
    ?>
    <div class="info-modal" id="<?= Security::e($id) ?>" role="dialog" aria-modal="true" aria-labelledby="<?= Security::e($id) ?>-title" hidden>
        <div class="info-modal-backdrop" data-modal-close></div>
        <div class="info-modal-panel<?= $isPerson ? ' info-modal-person' : '' ?>">
            <button type="button" class="info-modal-close" data-modal-close aria-label="Close">&times;</button>
            <?php if ($image !== ''): ?>
                <div class="info-modal-media<?= $isPerson ? ' info-modal-media-person' : '' ?>"><img src="<?= Security::e($image) ?>" alt="<?= Security::e($title) ?>" loading="lazy"></div>
            <?php elseif ($isPerson): ?>
                <div class="info-modal-media info-modal-media-person info-modal-avatar"><span><?= Security::e($initials($title)) ?></span></div>
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

/**
 * Clickable box + its modal.
 * $variant: 'person' → photo fills the box top (portrait crop, never cut off
 * faces) with an initials avatar when no photo exists; 'icon' → leaf/icon tile.
 */
$renderBox = static function (string $id, string $title, string $summary, string $image, string $modalTitle, string $modalSubtitle, string $bodyHtml, int $index = 0, string $variant = 'default') use ($renderModal, $initials): void {
    $isPerson = $variant === 'person';
    $isIcon = $variant === 'icon';
    ?>
    <button type="button" class="info-box reveal<?= $isPerson ? ' info-box-person' : '' ?>" style="--d:<?= min($index * 0.08, 0.4) ?>s" data-modal-target="#<?= Security::e($id) ?>" aria-haspopup="dialog" aria-label="View full information about <?= Security::e($title) ?>">
        <span class="info-box-media<?= $image === '' ? ($isPerson ? ' info-box-avatar' : ' info-box-media-icon') : '' ?>">
            <?php if ($image !== ''): ?>
                <img src="<?= Security::e($image) ?>" alt="<?= Security::e($title) ?>" loading="lazy">
            <?php elseif ($isPerson): ?>
                <span class="info-avatar-initials"><?= Security::e($initials($title)) ?></span>
            <?php else: ?>
                <svg class="icon"><use href="#<?= $isIcon ? Security::e($summary !== '' && str_starts_with($summary, 'icon-') ? $summary : 'icon-leaf') : 'icon-leaf' ?>"/></svg>
            <?php endif; ?>
            <span class="info-box-plus" aria-hidden="true">+</span>
        </span>
        <span class="info-box-body">
            <strong><?= Security::e($title) ?></strong>
            <?php if ($summary !== '' && !str_starts_with($summary, 'icon-')): ?><span><?= Security::e($summary) ?></span><?php endif; ?>
            <em>View</em>
        </span>
    </button>
    <?php $renderModal($id, $modalTitle, $modalSubtitle, $image, $bodyHtml, $variant); ?>
    <?php
};
?>

<!-- ============ 01 · About Us ============ -->
<section class="section" id="aboutus">
    <div class="container split">
        <?php if ($aboutIntroHasImage): ?>
            <div class="media-frame reveal">
                <img src="<?= Security::e(sec($sections, 'aboutus', 'image')) ?>" alt="<?= Security::e(sec($sections, 'aboutus', 'heading', 'Chitrawan Nature Cure Hospital')) ?>" loading="lazy">
                <div class="media-badge">
                    <strong><?= Security::e($aboutIntroBadgeTitle) ?></strong>
                    <span><?= Security::e($aboutIntroBadgeText) ?></span>
                </div>
            </div>
        <?php endif; ?>
        <div class="reveal<?= $aboutIntroHasImage ? '' : ' about-intro-centered' ?>" style="--d:.12s">
            <span class="kicker"><?= Security::e(sec($sections, 'aboutus', 'kicker', '01 · About Us')) ?></span>
            <h2><?= Security::e(sec($sections, 'aboutus', 'heading', 'Healing Naturally, Living Fully')) ?></h2>
            <?= $paragraphs(sec($sections, 'aboutus', 'content')) ?>
            <ul class="check-list">
                <?php foreach ((array) ($sections['aboutus']['extras'] ?? []) as $line): ?>
                    <?php if (trim((string) $line) === '') { continue; } ?>
                    <li><svg class="icon"><use href="#icon-check"/></svg> <?= Security::e((string) $line) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<!-- ============ 02 · Our Mission ============ -->
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
                $renderBox($modalId, (string) $item, 'Our commitment to your wellbeing', sec($sections, 'mission', 'image'), (string) $item, '', $body, $idx, 'icon');
                ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ 03 · Our Vision (narrative) ============ -->
<section class="section" id="vision">
    <div class="container split">
        <?php if (trim((string) sec($sections, 'vision', 'image')) !== ''): ?>
            <div class="media-frame reveal vision-media">
                <img src="<?= Security::e(sec($sections, 'vision', 'image')) ?>" alt="<?= Security::e(sec($sections, 'vision', 'heading', 'Our Vision')) ?>" loading="lazy">
                <?php if ($visionQuote !== ''): ?>
                    <div class="vision-quote">
                        <span class="vision-quote-mark" aria-hidden="true">&ldquo;</span>
                        <p><?= Security::e($visionQuote) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="vision-narrative reveal" style="--d:.12s">
            <span class="kicker"><?= Security::e(sec($sections, 'vision', 'kicker', '03 · Our Vision')) ?></span>
            <h2><?= Security::e(sec($sections, 'vision', 'heading', 'Our Vision')) ?></h2>
            <?php if ($visionIntro !== ''): ?><p class="vision-lead"><?= Security::e($visionIntro) ?></p><?php endif; ?>
            <?php foreach ($visions as $vision): ?>
                <div class="vision-beat">
                    <h3><svg class="icon"><use href="<?= $vision['icon'] !== '' ? '#' . Security::e($vision['icon']) : '#icon-sun' ?>"/></svg><?= Security::e($vision['title']) ?></h3>
                    <p><?= Security::e($vision['text']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ 04 · Our Core Values ============ -->
<section class="section alt" id="approach">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'approach', 'kicker', '04 · Our Core Values')) ?></span>
            <h2><?= Security::e(sec($sections, 'approach', 'heading', 'Our Core Values')) ?></h2>
            <p class="lede"><?= Security::e(sec($sections, 'approach', 'content')) ?></p>
        </div>
        <div class="info-box-grid">
            <?php foreach ($approaches as $idx => $approach): ?>
                <?php
                $modalId = 'modal-approach-' . $idx;
                $body = '<p>' . Security::e($approach['text']) . '</p>';
                $renderBox($modalId, $approach['title'], $approach['icon'] !== '' ? $approach['icon'] : 'Learn about our approach', $approach['image'], $approach['title'], '', $body, $idx, 'icon');
                ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ 05 · Our Founders (spotlight + cards) ============ -->
<section class="section" id="founder">
    <div class="container">
        <div class="section-head center founder-head reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'founder', 'kicker', '05 · Our Founders')) ?></span>
            <h2><?= Security::e(sec($sections, 'founder', 'heading', 'Meet Our Founders')) ?></h2>
        </div>
        <?php if ($founders !== []): ?>
            <?php foreach ($founders as $fIdx => $founder): ?>
            <div class="founder-card reveal" style="--d:<?= min($fIdx * 0.1, 0.3) ?>s">
                <div class="founder-card-photo">
                    <span class="founder-card-frame" aria-hidden="true"></span>
                    <div class="founder-card-media<?= trim((string) $founder['image']) === '' ? ' founder-card-avatar' : '' ?>">
                        <?php if (trim((string) $founder['image']) !== ''): ?>
                            <img src="<?= Security::e($founder['image']) ?>" alt="<?= Security::e($founder['name']) ?>" loading="lazy">
                        <?php else: ?>
                            <span class="info-avatar-initials"><?= Security::e($initials($founder['name'])) ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="founder-card-badge" aria-hidden="true"><svg class="icon"><use href="#icon-leaf"/></svg></span>
                </div>
                <div class="founder-card-body">
                    <?php if ($founder['role'] !== ''): ?><span class="founder-card-role"><?= Security::e($founder['role']) ?></span><?php endif; ?>
                    <h3><?= Security::e($founder['name']) ?></h3>
                    <span class="founder-card-underline" aria-hidden="true"></span>
                    <?php
                    /* Short teaser on the card; View reveals the remaining paragraphs below. */
                    $bioParts = array_values(array_filter(array_map('trim', preg_split('/\n\s*\n/', trim((string) $founder['bio'])) ?: []), static fn (string $p): bool => $p !== ''));
                    $bioTeaser = $bioParts[0] ?? '';
                    $bioRest = array_slice($bioParts, 1);
                    ?>
                    <?php if ($bioTeaser !== ''): ?><div class="founder-card-teaser"><?= $paragraphs($bioTeaser) ?></div><?php endif; ?>
                    <!-- Expanding text sits BEFORE the button, so opening pushes the View button down; click it again to close. -->
                    <div class="founder-card-more" id="founder-bio-<?= $fIdx ?>" hidden>
                        <div class="founder-card-more-inner">
                            <?php if ($bioRest !== []): ?>
                                <?= $paragraphs(implode("\n\n", $bioRest)) ?>
                            <?php else: ?>
                                <?= $paragraphs($founder['bio']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button type="button" class="founder-card-btn" data-bio-toggle="#founder-bio-<?= $fIdx ?>" aria-expanded="false" aria-controls="founder-bio-<?= $fIdx ?>" aria-label="Read more about <?= Security::e($founder['name']) ?>">View <svg class="icon"><use href="#icon-arrow"/></svg></button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- ============ 06 · Our Doctors ============ -->
<section class="section alt" id="doctors">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'doctors', 'kicker', '06 · Our Doctors')) ?></span>
            <h2><?= Security::e(sec($sections, 'doctors', 'heading', 'Our Doctors & Specialists')) ?></h2>
            <p class="lede"><?= Security::e(sec($sections, 'doctors', 'content', 'A multidisciplinary team working together for your complete care.')) ?></p>
        </div>
        <div class="info-box-grid">
            <?php foreach ($doctors as $idx => $doctor): ?>
                <?php
                $modalId = 'modal-doctor-' . $idx;
                $body = '<p>' . Security::e($doctor['role']) . '</p>';
                $renderBox($modalId, $doctor['name'], $doctor['role'], $doctor['image'], $doctor['name'], $doctor['role'], $body, $idx, 'person');
                ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ 07 · Our Team ============ -->
<section class="section" id="group">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'group', 'kicker', '07 · Our Team')) ?></span>
            <h2><?= Security::e(sec($sections, 'group', 'heading', 'Our Team')) ?></h2>
            <p class="lede"><?= Security::e(sec($sections, 'group', 'content', 'The people who make Chitrawan Nature Cure Hospital a place of healing.')) ?></p>
        </div>
        <div class="info-box-grid">
            <?php foreach ($team as $idx => $member): ?>
                <?php
                $modalId = 'modal-team-' . $idx;
                $name = (string) ($member['name'] ?? '');
                $role = (string) ($member['role'] ?? '');
                $memberImage = (string) ($member['image'] ?? '');
                $renderBox($modalId, $name, $role, $memberImage, $name, $role, '<p>' . Security::e($role) . '</p>', $idx, 'person');
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
                    $isPersonType = $custom['type'] === 'founder' || $custom['type'] === 'doctors' || $custom['type'] === 'group';
                    $renderBox($modalId, $title, $summary, $image, $title, '', $body, $itemIndex, $isPersonType ? 'person' : ($custom['type'] === 'approach' || $custom['type'] === 'vision' ? 'icon' : 'default'));
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
