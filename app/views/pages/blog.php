<section class="section">
    <div class="container">
        <div class="card-grid">
            <?php
            $posts = $posts ?? (require APP_ROOT . '/app/data/posts.php');
            $idx = 0;
            ?>
            <?php foreach ($posts as $slug => $p): ?>
                <?php $delay = min($idx++ * 0.08, 0.4); ?>
                <a class="media-card post-card reveal" style="--d:<?= $delay ?>s" href="<?= BASE_URL ?>/blog/<?= Security::e($slug) ?>">
                    <div class="media-thumb"><img src="<?= Security::e($p['image'] ?? '') ?>" alt="<?= Security::e($p['title'] ?? '') ?>" loading="lazy"></div>
                    <div class="media-body">
                        <div class="blog-meta"><span class="chip"><?= Security::e($p['category'] ?? 'Wellness') ?></span><span><?= Security::e($p['date'] ?? '') ?></span></div>
                        <h3><?= Security::e($p['title'] ?? '') ?></h3>
                        <p><?= Security::e($p['excerpt'] ?? '') ?></p>
                        <span class="card-link">Read article <svg class="icon"><use href="#icon-arrow"/></svg></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section alt">
    <div class="container">
        <?php require APP_ROOT . '/app/views/partials/cta_banner.php'; ?>
    </div>
</section>
