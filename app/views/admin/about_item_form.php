<div class="admin-head">
    <div>
        <span class="kicker">About page</span>
        <h1><?= Security::e($title) ?></h1>
        <p class="admin-head-note">Enter the information for this item. It will appear in a box on the About page.</p>
    </div>
    <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/admin/pages/about/<?= Security::e($sectionKey) ?>/items">&larr; Back</a>
</div>

<form action="<?= BASE_URL ?>/admin/pages/about/<?= Security::e($sectionKey) ?>/item-save" method="post" enctype="multipart/form-data" class="contact-form admin-form">
    <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
    <input type="hidden" name="index" value="<?= $index === null ? -1 : (int) $index ?>">

    <?php if ($sectionKey === 'founder' || $sectionKey === 'doctors'): ?>
        <div>
            <label for="about-name">Name <span class="req">*</span></label>
            <input type="text" id="about-name" name="name" value="<?= Security::e((string) ($item['name'] ?? '')) ?>" required>
        </div>
        <div>
            <label for="about-role">Role</label>
            <input type="text" id="about-role" name="role" value="<?= Security::e((string) ($item['role'] ?? '')) ?>">
        </div>
    <?php else: ?>
        <div>
            <label for="about-title"><?= $sectionKey === 'vision' ? 'Title' : 'Title' ?> <span class="req">*</span></label>
            <input type="text" id="about-title" name="title" value="<?= Security::e((string) ($item['title'] ?? '')) ?>" required>
        </div>
    <?php endif; ?>

    <?php if ($sectionKey === 'founder'): ?>
        <div>
            <label for="about-bio">Full biography</label>
            <textarea id="about-bio" name="bio" rows="7"><?= Security::e((string) ($item['bio'] ?? '')) ?></textarea>
        </div>
    <?php elseif ($sectionKey === 'approach'): ?>
        <div>
            <label for="about-description">Description</label>
            <textarea id="about-description" name="description" rows="5"><?= Security::e((string) ($item['description'] ?? '')) ?></textarea>
        </div>
    <?php elseif ($sectionKey === 'vision'): ?>
        <div>
            <label for="about-text">Full text</label>
            <textarea id="about-text" name="text" rows="6"><?= Security::e((string) ($item['text'] ?? '')) ?></textarea>
        </div>
    <?php endif; ?>

    <?php if (in_array($sectionKey, ['founder', 'approach', 'doctors'], true)): ?>
        <div>
            <label for="about-image">Photo URL</label>
            <input type="text" id="about-image" name="image" value="<?= Security::e((string) ($item['image'] ?? '')) ?>" placeholder="https://...">
            <?php if (($item['image'] ?? '') !== ''): ?><img class="thumb form-image-preview" src="<?= Security::e($item['image']) ?>" alt="" loading="lazy"><?php endif; ?>
            <label for="about-image-file">Upload photo</label>
            <input type="file" id="about-image-file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
        </div>
    <?php endif; ?>

    <?php if (in_array($sectionKey, ['approach', 'vision'], true)): ?>
        <div>
            <label for="about-icon">Icon name</label>
            <input type="text" id="about-icon" name="icon" value="<?= Security::e((string) ($item['icon'] ?? '')) ?>" placeholder="icon-leaf">
        </div>
    <?php endif; ?>

    <button type="submit" class="btn btn-dark">Save <?= Security::e($definition['label']) ?> <svg class="icon"><use href="#icon-check"/></svg></button>
</form>
