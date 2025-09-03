<?php
namespace WPBooks;

if (!defined('ABSPATH')) exit;

final class DB {
    public static function table(): string {
        global $wpdb;
        return $wpdb->prefix.'books';
    }
    
    public static function install(): void {
        global $wpdb;
        $table = static::table();
        $charset = $wpdb->get_charset_collate();
        require_once ABSPATH.'wp-admin/includes/upgrade.php';
        $sql = "CREATE TABLE {$table}(
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            author VARCHAR(255) NOT NULL,
            published_year INT NOT NULL,
            PRIMARY KEY(id),
            KEY author_idx(author),
            KEY year_idx(published_year)
        ) {$charset};";
        dbDelta($sql);
    }
    
    public static function insert(array $data): int {
        global $wpdb;
        $wpdb->insert(static::table(), [
            'title' => $data['title'],
            'author'=> $data['author'],
            'published_year' => (int)$data['published_year'],
        ], ['%s','%s','%d']);
        return (int)$wpdb->insert_id;
    }
    
    public static function update(int $id, array $data): int {
        global $wpdb;
        return (int)$wpdb->update(static::table(), [
            'title' => $data['title'],
            'author'=> $data['author'],
            'published_year' => (int)$data['published_year'],
        ], ['id'=>$id], ['%s','%s','%d'], ['%d']);
    }
    
    public static function delete(int $id): int {
        global $wpdb;
        return (int)$wpdb->delete(static::table(), ['id'=>$id], ['%d']);
    }
    
    public static function get(int $id): ?array {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT id,title,author,published_year FROM ".static::table()." WHERE id=%d", $id);
        $row = $wpdb->get_row($sql, ARRAY_A);
        return $row ?: null;
    }
    
    public static function all(array $args=[]): array {
        global $wpdb;
        $table = static::table();
        $where = '1=1';
        $params = [];
        if (!empty($args['search'])) {
            $like = '%'.$wpdb->esc_like($args['search']).'%';
            $where .= " AND (title LIKE %s OR author LIKE %s)";
            $params[] = $like; $params[] = $like;
        }
        $limit  = isset($args['per_page']) ? max(1,(int)$args['per_page']) : 100;
        $offset = isset($args['page']) ? max(0, ((int)$args['page']-1)*$limit) : 0;
        
        $sql = $wpdb->prepare("SELECT id,title,author,published_year
            FROM {$table} WHERE {$where} ORDER BY id DESC LIMIT %d OFFSET %d",
            array_merge($params, [$limit, $offset])
        );
        return $wpdb->get_results($sql, ARRAY_A) ?: [];
    }
}
