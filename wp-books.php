<?php
/**
 * Plugin Name: WP Books
 * Description: Books CRUD with React admin, REST API, public shortcode, Vite assets.
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

define('WPBOOKS_FILE', __FILE__);
define('WPBOOKS_DIR', plugin_dir_path(__FILE__));
define('WPBOOKS_URL', plugin_dir_url(__FILE__));
define('WPBOOKS_VITE_PORT', 3000);

if (is_file(WPBOOKS_DIR.'vendor/autoload.php')) {
    require WPBOOKS_DIR.'vendor/autoload.php';
}

require WPBOOKS_DIR.'inc/bootstrap.php';
