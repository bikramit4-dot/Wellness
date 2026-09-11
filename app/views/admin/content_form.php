<?php
// Provide defaults so the template works even if the controller forgets to
// pass these (e.g. the error re-render path). Defined before first use so no
// "undefined variable" warnings are raised.
$formAction = $formAction ?? (BASE_URL . '/admin/content/' . $sectionKey . '/save');
$backUrl = $backUrl ?? (BASE_URL . '/admin/content/' . $sectionKey);
?>

<div class="admin-head">
    <div>
        <span class="kicker">Page editor</span>
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
            <?php if ($type === 'founders'): ?><p class="form-hint">Add each founder below. You can upload a photo or paste a photo URL, then add another founder when needed.</p><?php endif; ?>
            <label for="field_<?= Security::e($name) ?>"><?= Security::e($label) ?><?= $required ? ' <span class="req">*</span>' : '' ?></label>

            <?php if ($type === 'textarea'): ?>
                <textarea id="field_<?= Security::e($name) ?>" name="<?= Security::e($name) ?>" rows="4" <?= $required ? 'required' : '' ?>><?= Security::e((string) $value) ?></textarea>

            <?php elseif ($type === 'list'): ?>
                <?php $lines = is_array($value) ? $value : (json_decode((string) $value, true) ?: []); ?>
                <textarea id="field_<?= Security::e($name) ?>" name="<?= Security::e($name) ?>" rows="5" placeholder="One item per line"><?= Security::e(implode("\n", (array) $lines)) ?></textarea>

            <?php elseif ($type === 'founders'): ?>
                <?php $founderRows = (array) ($item['founders'] ?? [['name' => '', 'role' => '', 'image' => '', 'bio' => '']]); ?>
                <div class="founder-editor" data-founder-editor>
                    <?php foreach ($founderRows as $founderIndex => $founderRow): ?>
                        <div class="founder-editor-row" data-founder-row>
                            <div class="form-row">
                                <div>
                                    <label for="founder_name_<?= (int) $founderIndex ?>">Name</label>
                                    <input type="text" id="founder_name_<?= (int) $founderIndex ?>" name="founder_name[]" value="<?= Security::e((string) ($founderRow['name'] ?? '')) ?>">
                                </div>
                                <div>
                                    <label for="founder_role_<?= (int) $founderIndex ?>">Role</label>
                                    <input type="text" id="founder_role_<?= (int) $founderIndex ?>" name="founder_role[]" value="<?= Security::e((string) ($founderRow['role'] ?? '')) ?>">
                                </div>
                            </div>
                            <label for="founder_image_url_<?= (int) $founderIndex ?>">Photo URL</label>
                            <input type="text" id="founder_image_url_<?= (int) $founderIndex ?>" name="founder_image_url[]" value="<?= Security::e((string) ($founderRow['image'] ?? '')) ?>" placeholder="https://...">
                            <label for="founder_image_<?= (int) $founderIndex ?>">Upload photo</label>
                            <input type="file" id="founder_image_<?= (int) $founderIndex ?>" name="founder_image_<?= (int) $founderIndex ?>" accept="image/jpeg,image/png,image/webp,image/gif">
                            <label for="founder_bio_<?= (int) $founderIndex ?>">Full bio</label>
                            <textarea id="founder_bio_<?= (int) $founderIndex ?>" name="founder_bio[]" rows="4"><?= Security::e((string) ($founderRow['bio'] ?? '')) ?></textarea>
                            <button type="button" class="btn btn-outline btn-xs" data-founder-remove>Remove founder</button>
                        </div>
                    <?php endforeach; ?>
                    <button type="button" class="btn btn-outline btn-sm" data-founder-add>+ Add another founder</button>
                </div>
                <script>
                    (function () {
                        var editor = document.querySelector('[data-founder-editor]');
                        if (!editor) return;
                        var add = editor.querySelector('[data-founder-add]');
                        var form = editor.closest('form');
                        var index = editor.querySelectorAll('[data-founder-row]').length;
                        add.addEventListener('click', function () {
                            var row = document.createElement('div');
                            row.className = 'founder-editor-row';
                            row.setAttribute('data-founder-row', '');
                            row.innerHTML = '<div class="form-row"><div><label>Name</label><input type="text" name="founder_name[]"></div><div><label>Role</label><input type="text" name="founder_role[]"></div></div><label>Photo URL</label><input type="text" name="founder_image_url[]" placeholder="https://..."><label>Upload photo</label><input type="file" name="founder_image_' + index + '" accept="image/jpeg,image/png,image/webp,image/gif"><label>Full bio</label><textarea name="founder_bio[]" rows="4"></textarea><button type="button" class="btn btn-outline btn-xs" data-founder-remove>Remove founder</button>';
                            editor.insertBefore(row, add);
                            index++;
                        });
                        editor.addEventListener('click', function (event) {
                            var remove = event.target.closest('[data-founder-remove]');
                            if (!remove) return;
                            var rows = editor.querySelectorAll('[data-founder-row]');
                            if (rows.length > 1) remove.closest('[data-founder-row]').remove();
                        });
                        if (form) form.addEventListener('submit', function () {
                            editor.querySelectorAll('[data-founder-row]').forEach(function (row, rowIndex) {
                                var file = row.querySelector('input[type="file"]');
                                if (file) file.name = 'founder_image_' + rowIndex;
                            });
                        });
                    })();
                </script>

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

    <button type="submit" class="btn btn-dark"><?= Security::e($isEdit ? 'Save Changes' : ($type === 'founders' ? 'Add Founder(s)' : 'Add Item')) ?> <svg class="icon"><use href="#icon-check"/></svg></button>
</form>
