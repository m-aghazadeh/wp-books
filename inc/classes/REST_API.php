<?php
namespace WPBooks;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

if (!defined('ABSPATH')) exit;

final class REST_API {
    public static function hooks(Hooks $h): void {
        $h->add_action('rest_api_init', [__CLASS__, 'routes']);
    }
    
    public static function routes(): void {
        register_rest_route('wp-books/v1', '/books', [
            [
                'methods'  => 'GET',
                'callback' => [__CLASS__, 'index'],
                'permission_callback' => '__return_true',
                'args' => [
                    'search'   => ['type'=>'string','required'=>false],
                    'page'     => ['type'=>'integer','required'=>false],
                    'per_page' => ['type'=>'integer','required'=>false],
                ],
            ],
            [
                'methods'  => 'POST',
                'callback' => [__CLASS__, 'store'],
                'permission_callback' => function(){ return current_user_can('edit_posts'); },
            ],
        ]);
        
        register_rest_route('wp-books/v1', '/books/(?P<id>\d+)', [
            [
                'methods'  => 'GET',
                'callback' => [__CLASS__, 'show'],
                'permission_callback' => function(){ return current_user_can('edit_posts'); },
            ],
            [
                'methods'  => 'PUT,PATCH',
                'callback' => [__CLASS__, 'update'],
                'permission_callback' => function(){ return current_user_can('edit_posts'); },
            ],
            [
                'methods'  => 'DELETE',
                'callback' => [__CLASS__, 'destroy'],
                'permission_callback' => function(){ return current_user_can('delete_posts'); },
            ],
        ]);
    }
    
    private static function payload(WP_REST_Request $r) {
        $title  = sanitize_text_field((string)$r->get_param('title'));
        $author = sanitize_text_field((string)$r->get_param('author'));
        $year   = (int)$r->get_param('published_year');
        if ($title === '' || $author === '') return new WP_Error('invalid','title/author required',['status'=>422]);
        $max = (int) date('Y') + 1;
        if ($year < 0 || $year > $max) return new WP_Error('invalid_year','invalid year',['status'=>422]);
        return ['title'=>$title,'author'=>$author,'published_year'=>$year];
    }
    
    public static function index(WP_REST_Request $r): WP_REST_Response {
        $data = DB::all([
            'search'   => sanitize_text_field((string)$r->get_param('search')),
            'page'     => (int)$r->get_param('page'),
            'per_page' => (int)$r->get_param('per_page'),
        ]);
        return new WP_REST_Response($data, 200);
    }
    
    public static function store(WP_REST_Request $r) {
        $p = static::payload($r);
        if (is_wp_error($p)) return $p;
        $id = DB::insert($p);
        return new WP_REST_Response(DB::get($id), 201);
    }
    
    public static function show(WP_REST_Request $r) {
        $row = DB::get((int)$r['id']);
        if (!$row) return new WP_Error('not_found','not found',['status'=>404]);
        return new WP_REST_Response($row, 200);
    }
    
    public static function update(WP_REST_Request $r) {
        $id = (int)$r['id'];
        $p = static::payload($r);
        if (is_wp_error($p)) return $p;
        DB::update($id, $p);
        return new WP_REST_Response(DB::get($id), 200);
    }
    
    public static function destroy(WP_REST_Request $r) {
        DB::delete((int)$r['id']);
        return new WP_REST_Response(null, 204);
    }
}
