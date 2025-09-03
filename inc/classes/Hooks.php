<?php
namespace WPBooks;

if (!defined('ABSPATH')) exit;

final class Hooks {
    private static $inst;
    private $registrars = [];
    
    public static function instance(): self {
        return self::$inst ?? (self::$inst = new self());
    }
    
    public function register(callable $callable): self {
        $this->registrars[] = $callable;
        return $this;
    }
    
    public function bind(): void {
        foreach ($this->registrars as $r) call_user_func($r, $this);
    }
    
    public function add_action($hook, $callback, $priority=10, $args=1): void {
        add_action($hook, $callback, $priority, $args);
    }
    
    public function add_filter($hook, $callback, $priority=10, $args=1): void {
        add_filter($hook, $callback, $priority, $args);
    }
}
