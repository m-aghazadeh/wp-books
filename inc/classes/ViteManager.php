<?php

namespace WPBooks;

if (!defined('ABSPATH')) exit;

final class ViteManager
{
    private static array $queue = [];
    private static ?bool $isDev = null;
    private static bool $initialized = false;
    private static array $manifest = [];
    
    private static string $host = 'localhost';
    private static int $port = 3000;
    private static float $timeout = 0.1;
    
    private static string $entryBasePath = '/src/ts/pages/';
    
    private static string $pluginPath;
    private static string $distUri;
    private static string $manifestPath;
    private static string $manifestAltPath;
    private static string $viteDevUri;
    
    private static bool $preamblePrinted = false;
    
    public static function init(): void
    {
        if (self::$initialized) return;
        self::$initialized = true;
        
        self::$pluginPath = rtrim(WPBOOKS_DIR, '/\\');
        self::$distUri = trailingslashit(WPBOOKS_URL . 'dist');
        self::$manifestPath = self::$pluginPath . '/dist/.vite/manifest.json';
        self::$manifestAltPath = self::$pluginPath . '/dist/manifest.json';
        self::$viteDevUri = 'http://' . self::$host . ':' . self::$port;
        
        self::$isDev = self::detectDevServer();
        
        if (!self::$isDev) {
            $mf = is_file(self::$manifestPath) ? self::$manifestPath : self::$manifestAltPath;
            if (is_file($mf)) {
                $json = file_get_contents($mf);
                self::$manifest = $json ? (json_decode($json, true) ?: []) : [];
            }
        }
        
        add_action('wp_head', [self::class, 'outputHead'], 1);
        add_action('wp_footer', [self::class, 'outputFooter'], 1);
        add_action('admin_head', [self::class, 'outputHead'], 1);
        add_action('admin_footer', [self::class, 'outputFooter'], 1);
    }
    
    private static function detectDevServer(): bool
    {
        $c = @fsockopen(self::$host, self::$port, $e, $s, self::$timeout);
        if ($c) {
            fclose($c);
            return true;
        }
        return false;
    }
    
    public static function enqueue(string $handle, bool $adminOnly = false): void
    {
        if (self::$isDev === null) self::init();
        self::$queue[] = ['handle' => $handle, 'adminOnly' => $adminOnly];
    }
    
    public static function outputHead(): void
    {
        self::output('head');
    }
    
    public static function outputFooter(): void
    {
        self::output('footer');
    }
    
    private static function resolveEntryExt(string $handle): string
    {
        $base = rtrim(self::$entryBasePath, '/') . "/$handle/" . basename($handle);
        $absTs = self::$pluginPath . $base . '.ts';
        $absTsx = self::$pluginPath . $base . '.tsx';
        if (is_file($absTsx)) return '.tsx';
        if (is_file($absTs)) return '.ts';
        return '.tsx';
    }
    
    private static function output(string $position): void
    {
        // ===== DEV =====
        if (self::$isDev) {
            if ($position === 'head' || $position === 'footer' && !self::$preamblePrinted) {
                self::$preamblePrinted = true;
                echo '<script type="module">
import RefreshRuntime from "' . esc_js(self::$viteDevUri . '/@react-refresh') . '";
RefreshRuntime.injectIntoGlobalHook(window);
window.$RefreshReg$ = () => {};
window.$RefreshSig$ = () => (type) => type;
window.__vite_plugin_react_preamble_installed__ = true;
</script>';
                
                echo '<script type="module" src="' . esc_url(self::$viteDevUri . '/@vite/client') . '" crossorigin></script>';
            }
            
            if ($position === 'footer') {
                foreach (self::$queue as $entry) {
                    if ($entry['adminOnly'] && !is_admin()) continue;
                    
                    $ext = self::resolveEntryExt($entry['handle']);
                    $src = self::$viteDevUri . self::$entryBasePath . $entry['handle'] . '/' . basename($entry['handle']) . $ext;
                    echo '<script type="module" crossorigin src="' . esc_url($src) . '"></script>';
                }
            }
            return;
        }
        
        foreach (self::$queue as $entry) {
            if ($entry['adminOnly'] && !is_admin()) continue;
            
            $keyTsx = ltrim(self::$entryBasePath, '/') . $entry['handle'] . '/' . basename($entry['handle']) . '.tsx';
            $keyTs = ltrim(self::$entryBasePath, '/') . $entry['handle'] . '/' . basename($entry['handle']) . '.ts';
            $node = self::$manifest[$keyTsx] ?? self::$manifest[$keyTs] ?? null;
            if (!$node) continue;
            
            $js = [];
            $css = [];
            
            if (!empty($node['file'])) $js[] = $node['file'];
            if (!empty($node['css'])) $css = array_merge($css, $node['css']);
            if (!empty($node['imports'])) {
                foreach ($node['imports'] as $impKey) {
                    if (!empty(self::$manifest[$impKey])) {
                        $imp = self::$manifest[$impKey];
                        if (!empty($imp['file'])) $js[] = $imp['file'];
                        if (!empty($imp['css'])) $css = array_merge($css, $imp['css']);
                    }
                }
            }
            
            $js = array_values(array_unique($js));
            $css = array_values(array_unique($css));
            if ($position === 'head') {
                foreach ($css as $c) {
                    $href = self::$distUri . ltrim($c, '/');
                    echo '<link rel="preload" href="' . esc_url($href) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
                    echo '<noscript><link rel="stylesheet" href="' . esc_url($href) . '"></noscript>';
                }
            }
            
            if ($position === 'footer') {
                foreach ($js as $j) {
                    $src = self::$distUri . ltrim($j, '/');
                    echo '<script type="module" defer crossorigin src="' . esc_url($src) . '"></script>';
                }
            }
        }
    }
}
