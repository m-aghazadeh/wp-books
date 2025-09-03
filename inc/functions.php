<?php
if (!defined('ABSPATH')) exit;

function wpbooks_view(string $path, array $vars = []): string {
    $file = WPBOOKS_DIR . 'views/' . ltrim($path, '/');
    if (!is_file($file)) return '';
    extract($vars, EXTR_SKIP);
    ob_start(); include $file; return ob_get_clean();
}

function vite(string $handle, bool $adminOnly = false): void {
    \WPBooks\ViteManager::enqueue($handle, $adminOnly);
}
