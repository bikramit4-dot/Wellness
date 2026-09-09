<?php require APP_ROOT . '/app/views/partials/section_helpers.php'; ?>

<!-- ============ 01 · Founder ============ -->
<section class="section" id="founder">
    <div class="container">
        <div class="section-head reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'founder', 'kicker', '01 · Founder')) ?></span>
            <h2><?= Security::e(sec($sections, 'founder', 'heading', 'Meet Our Founder')) ?></h2>
        </div>
        <div class="split">
            <div class="media-frame reveal">
                <img src="<?= Security::e(sec($sections, 'founder', 'image')) ?>" alt="Doctor consulting with a patient at Chitrawan Nature Cure Hospital" loading="lazy">
                <?php [$badgeName, $badgeRole] = array_pad(explode('|', sec($sections, 'founder', 'sub_content', 'Dr. Rajesh Sharma|Founder & Holistic Medicine Specialist'), 2), 2, ''); ?>
                <div class="media-badge">
                    <strong><?= Security::e($badgeName) ?></strong>
                    <span><?= Security::e($badgeRole) ?></span>
                </div>
            </div>
            <div class="reveal" style="--d:.12s">
                <h3><?= Security::e($badgeName) ?></h3>
                <?php foreach (preg_split('/\n\s*\n/', sec($sections, 'founder', 'content', '')) as $para): ?>
                    <?php if (trim($para) !== ''): ?><p><?= Security::e(trim($para)) ?></p><?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ============ 02 · Our Approach ============ -->
<section class="section alt" id="approach">
    <div class="container">
        <div class="split reverse">
            <div class="media-frame reveal">
                <img src="<?= Security::e(sec($sections, 'approach', 'image')) ?>" alt="A serene therapy space at Chitrawan Nature Cure Hospital" loading="lazy">
            </div>
            <div class="reveal" style="--d:.12s">
                <span class="kicker"><?= Security::e(sec($sections, 'approach', 'kicker', '02 · Our Approach')) ?></span>
                <h2><?= Security::e(sec($sections, 'approach', 'heading', 'Treating the Root Cause')) ?></h2>
                <p><?= Security::e(sec($sections, 'approach', 'content', '')) ?></p>
                <ul class="check-list">
                    <?php foreach ((array) ($sections['approach']['extras'] ?? []) as $line): ?>
                        <li><svg class="icon"><use href="#icon-check"/></svg> <?= Security::e((string) $line) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
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
        <div class="card-grid">
            <?php
            $specialists = $sections['doctors']['extras'] ?? [];
            $i = 0;
            foreach ($specialists as $line):
                [$specTitle, $specText, $specIcon, $specImg] = array_pad(explode('|', (string) $line, 4), 4, '');
                $delay = min($i * 0.08, 0.4);
                ?>
                <article class="card doctor-card reveal" style="--d:<?= $delay ?>s">
                    <?php if ($specImg !== ''): ?>
                        <div class="doctor-photo">
                            <img src="<?= Security::e($specImg) ?>" alt="<?= Security::e($specTitle) ?>" loading="lazy">
                        </div>
                    <?php endif; ?>
                    <div class="doctor-card-body">
                        <div class="card-icon"><svg class="icon"><use href="#<?= Security::e($specIcon !== '' ? $specIcon : 'icon-leaf') ?>"/></svg></div>
                        <h3><?= Security::e($specTitle) ?></h3>
                        <p><?= Security::e($specText) ?></p>
                    </div>
                </article>
            <?php $i++; endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ 04 · Our Vision ============ -->
<section class="section alt" id="vision">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'vision', 'kicker', '04 · Our Vision')) ?></span>
            <h2><?= Security::e(sec($sections, 'vision', 'heading', 'Our Vision')) ?></h2>
        </div>
        <div class="split">
            <div class="media-frame reveal">
                <img src="<?= Security::e(sec($sections, 'vision', 'image', BASE_URL . '/public/uploads/photos/photo-1519823551278-64ac92734fb1.jpg')) ?>" alt="Our vision for holistic healthcare" loading="lazy">
            </div>
            <div class="reveal" style="--d:.12s">
                <?php $vision = $sections['vision']['extras'] ?? []; ?>
                <?php foreach ($vision as $line): ?>
                    <?php [$vTitle, $vText, $vIcon] = array_pad(explode('|', (string) $line, 3), 3, ''); ?>
                    <article class="card vision-card">
                        <div class="card-icon"><svg class="icon"><use href="#<?= Security::e($vIcon !== '' ? $vIcon : 'icon-sun') ?>"/></svg></div>
                        <h3><?= Security::e($vTitle) ?></h3>
                        <p><?= Security::e($vText) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ============ 05 · Our Mission ============ -->
<section class="section" id="mission">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'mission', 'kicker', '05 · Our Mission')) ?></span>
            <h2><?= Security::e(sec($sections, 'mission', 'heading', 'Our Mission')) ?></h2>
            <p class="lede"><?= Security::e(sec($sections, 'mission', 'content', 'The promises we make to every patient who walks through our doors.')) ?></p>
        </div>
        <div class="split reverse">
            <div class="media-frame reveal">
                <img src="<?= Security::e(sec($sections, 'mission', 'image', BASE_URL . '/public/uploads/photos/photo-1506126613408-eca07ce68773.jpg')) ?>" alt="Our mission at Chitrawan Nature Cure Hospital" loading="lazy">
            </div>
            <div class="reveal" style="--d:.12s">
                <article class="card vision-card">
                    <div class="card-icon"><svg class="icon"><use href="#icon-heart"/></svg></div>
                    <h3><?= Security::e(sec($sections, 'mission', 'sub_content', 'What We Are Committed To')) ?></h3>
                    <ul class="check-list mission-list">
                        <?php foreach ((array) ($sections['mission']['extras'] ?? []) as $line): ?>
                            <li><svg class="icon"><use href="#icon-check"/></svg> <?= Security::e((string) $line) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            </div>
        </div>
    </div>
</section>

<!-- ============ 06 · Our Group ============ -->
<section class="section alt" id="group">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker"><?= Security::e(sec($sections, 'group', 'kicker', '06 · Our Group')) ?></span>
            <h2><?= Security::e(sec($sections, 'group', 'heading', 'Our Group')) ?></h2>
            <p class="lede"><?= Security::e(sec($sections, 'group', 'content', 'The people who make Chitrawan Nature Cure Hospital a place of healing.')) ?></p>
        </div>
        <div class="media-frame group-photo reveal">
            <img src="<?= Security::e(sec($sections, 'group', 'image', BASE_URL . '/public/uploads/photos/photo-1544367567-0f2fcb009e0b.jpg')) ?>" alt="The Chitrawan Nature Cure Hospital team" loading="lazy">
        </div>
        <div class="card-grid team-grid">
            <?php
            $team = $team ?? (require APP_ROOT . '/app/data/team.php');
            $i = 0;
            foreach ($team as $member):
                $delay = min($i * 0.08, 0.4);
                ?>
                <article class="team-card reveal" style="--d:<?= $delay ?>s">
                    <span class="avatar <?= Security::e($member['avatar'] ?? 'a1') ?>"><?= Security::e($member['initials'] ?? '') ?></span>
                    <h3><?= Security::e($member['name'] ?? '') ?></h3>
                    <p><?= Security::e($member['role'] ?? '') ?></p>
                </article>
            <?php $i++; endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ CTA ============ -->
<section class="section-flush">
    <div class="container">
        <?php require APP_ROOT . '/app/views/partials/cta_banner.php'; ?>
    </div>
</section>
