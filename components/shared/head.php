<?php
/**
 * Shared HTML <head> content
 * Includes fonts, Material Icons, and global stylesheet
 * 
 * Variables:
 * $pageTitle - Page title for <title> tag
 * $pageCss   - Array of page-specific CSS filenames (optional)
 */
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?? 'Hireable' ?></title>
<link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,400;0,500;0,700;1,400&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../public/assets/css/global.css">
<link rel="stylesheet" href="../../public/assets/css/layout.css">
<?php if (isset($pageCss) && is_array($pageCss)): ?>
    <?php foreach ($pageCss as $css): ?>
        <link rel="stylesheet" href="../../public/assets/css/<?= $css ?>">
    <?php endforeach; ?>
<?php endif; ?>
<script src="/public/assets/js/custom-select.js" defer></script>
