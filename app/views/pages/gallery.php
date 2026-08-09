<section class="section">
    <div class="container">
        <?php
        $galleryItems = $galleryItems ?? (require APP_ROOT . '/app/data/gallery.php');
        ?>
        <div class="card-grid">
            <?php $i = 0; foreach ($galleryItems as $item): ?>
                <?php $delay = min($i * 0.08, 0.56); ?>
                <article class="media-card reveal" style="--d:<?= $delay ?>s">
                    <button type="button" class="media-open" data-lightbox aria-haspopup="dialog" aria-label="View larger photo: <?= Security::e($item['title'] ?? '') ?>">
                        <span class="media-thumb"><img src="<?= Security::e($item['image'] ?? '') ?>" alt="<?= Security::e($item['alt'] ?? $item['title'] ?? '') ?>" loading="lazy"></span>
                        <span class="media-zoom" aria-hidden="true"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg></span>
                    </button>
                    <div class="media-body"><h3><?= Security::e($item['title'] ?? '') ?></h3><p><?= Security::e($item['description'] ?? '') ?></p></div>
                </article>
                <?php $i++; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
