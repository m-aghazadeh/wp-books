<?php
// متغیرهای ورودی: $rows, $message, $errors, $old
$rows = $rows ?? [];
$message = isset($message) ? (string)$message : '';
$errors = is_array($errors ?? null) ? $errors : [];
$old = is_array($old ?? null) ? $old : ['title' => '', 'author' => '', 'published_year' => ''];
?>
<div class="wpbooks-list" style="max-width:920px;margin:0 auto">
    <?php if ($message): ?>
        <div style="margin:12px 0;padding:10px;border:1px solid #c6f6d5;background:#f0fff4;color:#22543d;border-radius:6px">
            <?php echo esc_html($message); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div style="margin:12px 0;padding:10px;border:1px solid #fed7d7;background:#fff5f5;color:#742a2a;border-radius:6px">
            <ul style="margin:0;padding-inline-start:20px">
                <?php foreach ($errors as $e): ?>
                    <li><?php echo esc_html($e); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="overflow-x-auto">
        <table class="wpbooks-table" style="width:100%;border-collapse:collapse;background:#fff;border:1px solid #eee;border-radius:8px;overflow:hidden">
            <thead>
            <tr style="background:#f9fafb">
                <th style="text-align:right;border-bottom:1px solid #eee;padding:10px">عنوان</th>
                <th style="text-align:right;border-bottom:1px solid #eee;padding:10px">نویسنده</th>
                <th style="text-align:right;border-bottom:1px solid #eee;padding:10px">سال انتشار</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($rows)): ?>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td style="padding:10px;border-bottom:1px solid #f2f2f2"><?php echo esc_html($r['title']); ?></td>
                        <td style="padding:10px;border-bottom:1px solid #f2f2f2"><?php echo esc_html($r['author']); ?></td>
                        <td style="padding:10px;border-bottom:1px solid #f2f2f2"><?php echo (int)$r['published_year']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td style="padding:12px" colspan="3">کتابی ثبت نشده است.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <form method="post" style="margin-top:16px;background:#fff;border:1px solid #eee;padding:12px;border-radius:8px">
        <?php wp_nonce_field('wpbooks_form', 'wpbooks_nonce'); ?>
        <div style="
        display:flex;
        gap:8px;flex-wrap:wrap;
        align-items:flex-end"
        >
            <div style="display:flex;flex-direction:column;gap:6px">
                <label for="wpbooks_title">عنوان</label>
                <input id="wpbooks_title" name="title" type="text" required
                       value="<?php echo esc_attr($old['title'] ?? ''); ?>"
                       style="padding:8px;min-width:220px;border:1px solid #ddd;border-radius:6px">
            </div>
            <div style="display:flex;flex-direction:column;gap:6px">
                <label for="wpbooks_author">نویسنده</label>
                <input id="wpbooks_author" name="author" type="text" required
                       value="<?php echo esc_attr($old['author'] ?? ''); ?>"
                       style="padding:8px;min-width:220px;border:1px solid #ddd;border-radius:6px">
            </div>
            <div style="display:flex;flex-direction:column;gap:6px">
                <label for="wpbooks_year">سال انتشار</label>
                <input id="wpbooks_year" name="published_year" type="number" min="1" required
                       value="<?php echo esc_attr($old['published_year'] ?? ''); ?>"
                       style="padding:8px;width:140px;border:1px solid #ddd;border-radius:6px">
            </div>
            <button type="submit" style="padding:10px 16px;border-radius:8px;background:#2563eb;color:#fff;border:0;cursor:pointer">
                افزودن کتاب
            </button>
        </div>
    </form>
</div>
