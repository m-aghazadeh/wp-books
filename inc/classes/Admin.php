<?php

namespace WPBooks;


if (!defined('ABSPATH')) exit;

final class Admin
{
    public static function hooks(Hooks $h): void
    {
        $h->add_action('admin_menu', [__CLASS__, 'menu']);
        $h->add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue']);
    }
    
    public static function menu(): void
    {
        add_menu_page('کتاب ها', 'کتاب ها', 'edit_posts', 'wp-books', [__CLASS__, 'page'], 'dashicons-book', 56);
    }
    
    public static function enqueue(): void
    {
        echo '<script>window.WPBooks=' . wp_json_encode([
                'restUrl' => esc_url_raw(rest_url('wp-books/v1/')),
                'nonce' => wp_create_nonce('wp_rest'),
            ]) . ';</script>';
        vite('Admin', true);
        
    }
    
    public static function page(): void
    {
        
        echo wpbooks_view('admin/books.php');
    }
}
