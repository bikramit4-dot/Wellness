<?php
/** @var array<string, mixed> $post */
$postTitle = $post['title'] ?? '';
$category = $post['category'] ?? 'Wellness';
$date = $post['date'] ?? '';
$image = $post['image'] ?? '';
$excerpt = $post['excerpt'] ?? '';

// Estimate reading time (~200 words per minute).
$wordCount = 0;
foreach (($post['content'] ?? []) as $section) {
    $wordCount += str_word_count(strip_tags($section['p'] ?? ''));
}
$readTime = max(2, (int) ceil($wordCount / 200));
?>

<!-- Article header -->
<section class="article-head">
    <div class="container-narrow">
        <a class="article-back" href="<?= BASE_URL ?>/blog"><svg class="icon icon-sm"><use href="#icon-arrow"/></svg> All Articles</a>
        <div class="blog-meta">
            <span class="chip"><?= Security::e($category) ?></span>
            <span><?= Security::e($date) ?></span>
        </div>
        <h1><?= Security::e($postTitle) ?></h1>
        <div class="article-meta">
            <span class="avatar a2">HW</span>
            <span class="article-author"><strong>Harmony Wellness Team</strong></span>
            <span class="article-dot" aria-hidden="true">&middot;</span>
            <span><svg class="icon icon-sm"><use href="#icon-clock"/></svg> <?= $readTime ?> min read</span>
        </div>
    </div>
</section>

<!-- Article body -->
<section class="section article-body">
    <div class="container-narrow">
        <img class="article-hero-img reveal" src="<?= Security::e($image) ?>" alt="<?= Security::e($postTitle) ?>" loading="lazy">
        <p class="article-lead reveal"><?= Security::e($excerpt) ?></p>

        <?php foreach (($post['content'] ?? []) as $i => $section): ?>
            <div class="article-section reveal" style="--d:.05s">
                <h2><?= $section['h'] ?? '' ?></h2>
                <p><?= $section['p'] ?? '' ?></p>
            </div>
        <?php endforeach; ?>

        <?php if (!empty($post['keyPoints'])): ?>
            <aside class="article-takeaways reveal">
                <h3>Key Takeaways</h3>
                <ul class="check-list">
                    <?php foreach ($post['keyPoints'] as $point): ?>
                        <li><svg class="icon"><use href="#icon-check"/></svg> <?= $point ?></li>
                    <?php endforeach; ?>
                </ul>
            </aside>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section class="section alt">
    <div class="container">
        <?php require APP_ROOT . '/app/views/partials/cta_banner.php'; ?>
    </div>
</section>
