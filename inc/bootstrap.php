<?php

use Composer\Autoload\ClassLoader;
use WPBooks\Admin;
use WPBooks\Hooks;
use WPBooks\REST_API;
use WPBooks\Shortcode;

if (!defined('ABSPATH')) exit;

// fallback for auto loading if it isn't work.
if (!class_exists(ClassLoader::class)) {
    spl_autoload_register(function($class){
        $prefix = 'WPBooks\\';
        if (strncmp($class, $prefix, strlen($prefix)) !== 0) return;
        $rel = str_replace('\\', '/', substr($class, strlen($prefix)));
        $file = WPBOOKS_DIR . 'inc/classes/' . $rel . '.php';
        if (is_file($file)) require $file;
    });
    require_once WPBOOKS_DIR . 'inc/functions.php';
}

Hooks::instance()
    ->register([Admin::class,'hooks'])
    ->register([REST_API::class,'hooks'])
    ->register([Shortcode::class,'hooks'])
    ->bind();

register_activation_hook(WPBOOKS_FILE, ['WPBooks\DB','install']);
