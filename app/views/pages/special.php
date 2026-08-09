<section class="section">
    <div class="container">
        <?php
        $therapies = $therapies ?? (require APP_ROOT . '/app/data/therapies.php');
        $items = array_filter($therapies, static fn (array $t): bool => ($t['category'] ?? '') === '/special-therapies');
        require APP_ROOT . '/app/views/partials/therapy_cards.php';
        ?>
    </div>
</section>

<section class="section alt">
    <div class="container">
        <?php require APP_ROOT . '/app/views/partials/cta_banner.php'; ?>
    </div>
</section>
