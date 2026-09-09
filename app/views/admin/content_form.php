<?php
// Provide defaults so the template works even if the controller forgets to
// pass these (e.g. the error re-render path). Defined before first use so no
// "undefined variable" warnings are raised.
$formAction = $formAction ?? (BASE_URL . '/admin/content/' . $sectionKey . '/save');
$backUrl = $backUrl ?? (BASE_URL . '/admin/content/' . $sectionKey);
?>

<div class="admin-head">
    <div>
        <span class="kicker">Content</span>
        <h1><?= Security::e($isEdit ? 'Edit ' : 'Add ') . Security::e($section['label']) ?></h1>
    </div>
    <a class="btn btn-outline btn-sm" href="<?= Security::e($backUrl) ?>">&larr; Back</a>
</div>
<form action="<?= Security::e($formAction) ?>" method="post" enctype="multipart/form-data" class="contact-form admin-form">
    <input type="hidden" name="csrf_token" value="<?= Security::e($csrf) ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int) ($item['id'] ?? 0) ?>">
    <?php endif; ?>

    <?php foreach ($section['fields'] as $field): ?>
        <?php
        $name = $field['name'];
        $type = $field['type'] ?? 'text';
        $label = $field['label'] ?? ucfirst($name);
        $value = $item[$name] ?? '';
        $required = !empty($field['required']);
        ?>
        <div>
            <label for="field_<?= Security::e($name) ?>"><?= Security::e($label) ?><?= $required ? ' <span class="req">*</span>' : '' ?></label>

            <?php if ($type === 'textarea'): ?>
                <textarea id="field_<?= Security::e($name) ?>" name="<?= Security::e($name) ?>" rows="4" <?= $required ? 'required' : '' ?>><?= Security::e((string) $value) ?></textarea>

            <?php elseif ($type === 'list'): ?>
                <?php $lines = is_array($value) ? $value : (json_decode((string) $value, true) ?: []); ?>
                <textarea id="field_<?= Security::e($name) ?>" name="<?= Security::e($name) ?>" rows="5" placeholder="One item per line"><?= Security::e(implode("\n", (array) $lines)) ?></textarea>

            <?php elseif ($type === 'methods'): ?>
                <?php $mLines = TherapyModel::methodsToLines($value); ?>
                <textarea id="field_<?= Security::e($name) ?>" name="<?= Security::e($name) ?>" rows="6" placeholder="One technique per line — format: Title | Definition"><?= Security::e(implode("\n", $mLines)) ?></textarea>

            <?php elseif ($type === 'number'): ?>
                <input type="number" id="field_<?= Security::e($name) ?>" name="<?= Security::e($name) ?>" value="<?= Security::e((string) $value) ?>" min="0" step="1">

            <?php elseif ($type === 'checkbox'): ?>
                <label class="checkbox-label">
                    <input type="checkbox" id="field_<?= Security::e($name) ?>" name="<?= Security::e($name) ?>" value="1" <?= $value ? 'checked' : '' ?>>
                    <span>Yes — <?= Security::e(strtolower($label)) ?></span>
                </label>

            <?php elseif ($type === 'select'): ?>
                <select id="field_<?= Security::e($name) ?>" name="<?= Security::e($name) ?>">
                    <?php foreach (($field['options'] ?? []) as $opt): ?>
                        <?php
                        // Options may use "Label | value" so the dropdown can
                        // show friendly names while storing the raw value.
                        $optParts = array_map('trim', explode('|', (string) $opt, 2));
                        $optVal = $optParts[1] ?? $optParts[0];
                        $optLabel = $optParts[0];
                        ?>
                        <option value="<?= Security::e($optVal) ?>" <?= (string) $value === (string) $optVal ? 'selected' : '' ?>><?= Security::e($optLabel) ?></option>
                    <?php endforeach; ?>
                </select>

            <?php elseif ($type === 'image'): ?>
                <?php if ($value !== ''): ?>
                    <img class="thumb form-image-preview" src="<?= Security::e((string) $value) ?>" alt="" loading="lazy">
                <?php endif; ?>
                <input type="text" id="field_<?= Security::e($name) ?>" name="<?= Security::e($name) ?>" value="<?= Security::e((string) $value) ?>" placeholder="https://...">
                <input type="file" id="file_<?= Security::e($name) ?>" name="<?= Security::e($name) ?>" accept="image/jpeg,image/png,image/webp,image/gif">

            <?php else: ?>
                <input type="text" id="field_<?= Security::e($name) ?>" name="<?= Security::e($name) ?>" value="<?= Security::e((string) $value) ?>" <?= $required ? 'required' : '' ?>>
            <?php endif; ?>

            <?php if (!empty($field['hint'])): ?>
                <span class="form-hint"><?= Security::e($field['hint']) ?></span>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-dark"><?= Security::e($isEdit ? 'Save Changes' : 'Add Item') ?> <svg class="icon"><use href="#icon-check"/></svg></button>
</form>
