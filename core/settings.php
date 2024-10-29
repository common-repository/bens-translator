<?php
/**
 * Defines Settings for Plugin
 * Action and hooks to be moved here TODO 
 * @since 0.9
 */
$bentr_version_number = "1.7.2";
define('HARD_CLEAN', true);
define('FLAG_BAR_BEGIN', '<!--FLAG_BAR_BEGIN-->');
define('FLAG_BAR_END', '<!--FLAG_BAR_END-->');
define('USER_AGENT','Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.2.1) Gecko/20021204');
define('LANGS_PATTERN', 'it|ko|zh-CN|zh-TW|pt|en|de|fr|es|ja|ar|ru|el|nl|bg|cs|hr|da|fi|pl|sv|no|iw|sr|sk|th|tr|hu|ro');
define('LANGS_PATTERN_WITH_SLASHES', '/it/|/ko/|/zh-CN/|/zh-TW/|/pt/|/en/|/de/|/fr/|/es/|/ja/|/ar/|/ru/|/el/|/nl/|/bg/|/cs/|/hr/|/da/|/fi/|/pl/|/sv/|/no/|/iw/|/sr/|/sk/|/th/|/tr/|/hu/|/ro/');
define('LANGS_PATTERN_WITHOUT_FINAL_SLASH', '/it|/ko|/zh-CN|/zh-TW|/pt|/en|/de|/fr|/es|/ja|/ar|/ru|/el|/nl|/bg|/cs|/hr|/da|/fi|/pl|/sv|/no|/iw|/sr|/sk|/th|/tr|/hu|/ro');
define('CONN_INTERVAL', get_option('bentr_conn_interval'));
define('DEBUG', get_option('bentr_enable_debug'));
define('SITEMAP_INTEGRATION', get_option('bentr_sitemap_integration'));
define('DEBUG_UA', false);
define('BASE_LANG', get_option('bentr_base_lang'));
define('BAR_COLUMNS', get_option('bentr_col_num'));
define('BAN_PREVENTION', get_option('bentr_ban_prevention'));
define('HTML_BAR_TAG', get_option('bentr_html_bar_tag'));
define('HTML_BAR_TAG_FLAG', get_option('bentr_html_bar_flag'));
define('TRANSLATION_ENGINE', get_option('bentr_my_translation_engine'));
define('SITEMAP_INTEGRATION', get_option('bentr_sitemap_integration'));
define('EXPIRE_TIME', get_option('bentr_cache_expire_time'));
define('COMPRESS_CACHE', get_option('bentr_compress_cache'));
define('BLOG_HOME', get_settings('home'));
define('BLOG_HOME_ESCAPED', str_replace('/', '\\/', BLOG_HOME));
define('NOT_FOUND','<html><head><title>404 Not found</title></head><body><center><h2>404 Error: content not found</h2></center></body></html>');
add_filter('query_vars', 'bentr_insert_my_rewrite_query_vars');
add_action('parse_query', 'bentr_insert_my_rewrite_parse_query',-1);//this action should have the maximum priority!
add_action('admin_menu', 'bentr_add_options_page');
add_action('init', 'bentr_translator_init');
add_action('delete_post', 'bentr_erase_common_cache_files');
add_action('save_post', 'bentr_erase_common_cache_files');
add_action('wp_head', 'bentr_add_header');

// register bens-translator-language
$plugin_dir = basename(dirname(__FILE__));
?>