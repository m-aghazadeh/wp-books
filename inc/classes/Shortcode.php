<?php
namespace WPBooks;

if (!defined('ABSPATH')) exit;

final class Shortcode {
    public static function hooks(Hooks $h): void {
        add_shortcode('book_list', [__CLASS__, 'render']);
    }
    
    private static function handle_post(): array {
        $out = ['message' => '', 'errors' => [], 'old' => ['title'=>'','author'=>'','published_year'=>'']];
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $out;
        if (empty($_POST['wpbooks_nonce']) || !wp_verify_nonce($_POST['wpbooks_nonce'], 'wpbooks_form')) {
            $out['errors'][] = 'خطای امنیتی: درخواست معتبر نیست.';
            return $out;
        }
        
        // برداشت امن
        $title  = isset($_POST['title']) ? sanitize_text_field( wp_unslash((string)$_POST['title']) ) : '';
        $author = isset($_POST['author']) ? sanitize_text_field( wp_unslash((string)$_POST['author']) ) : '';
        $year   = isset($_POST['published_year']) ? (int) $_POST['published_year'] : 0;
        
        $out['old'] = ['title'=>$title,'author'=>$author,'published_year'=>$year];
        
        // اعتبارسنجی
        $currentY = (int) current_time('Y'); // سال سرور وردپرس
        $max = $currentY + 1;
        
        if ($title === '')             $out['errors'][] = 'عنوان الزامی است.';
        if (mb_strlen($title) > 255)   $out['errors'][] = 'عنوان خیلی بلند است.';
        if ($author === '')            $out['errors'][] = 'نام نویسنده الزامی است.';
        if (mb_strlen($author) > 255)  $out['errors'][] = 'نام نویسنده خیلی بلند است.';
        if ($year <= 0 || $year > $max) $out['errors'][] = 'سال انتشار معتبر نیست.';
        
        if (!empty($out['errors'])) {
            return $out;
        }
        
        // درج در DB (DB::insert از قبل دارید)
        $ok = DB::insert([
            'title'          => $title,
            'author'         => $author,
            'published_year' => $year,
        ]);
        
        if ($ok) {
            // PRG: جلوگیری از ارسال مجدد
            $url = add_query_arg(['wpbooks'=>'added'], remove_query_arg(['wpbooks']));
            wp_safe_redirect($url);
            exit;
        }
        
        $out['errors'][] = 'ثبت کتاب ناموفق بود.';
        return $out;
    }
    
    public static function render($atts = []): string {
        $state = self::handle_post();
        
        // پیام موفقیت با GET
        if (isset($_GET['wpbooks']) && $_GET['wpbooks'] === 'added') {
            $state['message'] = 'کتاب با موفقیت افزوده شد.';
        }
        
        // واکشی ردیف‌ها
        $rows = DB::all(['per_page' => 1000]);
        
        return wpbooks_view(
            'public/book-list.php',
            [
                'rows'    => $rows,
                'message' => $state['message'],
                'errors'  => $state['errors'],
                'old'     => $state['old'],
            ]
        );
    }
}
