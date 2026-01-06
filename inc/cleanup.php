<?php
/**
 * Security and Performance Cleanup
 */

// Emojis entfernen
add_action('init', function() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
});

// REST-API einschränken
add_filter('rest_authentication_errors', function($result) {
    if (!empty($result)) return $result;
    if (!is_user_logged_in()) {
        return new WP_Error('rest_not_logged_in', 'REST API limited.', ['status' => 401]);
    }
    return $result;
});

// XML-RPC & Header Cleanup
add_filter('xmlrpc_enabled', '__return_false');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');

// Version Strings entfernen
function kuestenkrieger_remove_wp_version_strings($src) {
    if (strpos($src, 'ver=' . get_bloginfo('version'))) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'kuestenkrieger_remove_wp_version_strings', 9999);
add_filter('script_loader_src', 'kuestenkrieger_remove_wp_version_strings', 9999);

// Generische Login-Fehler
add_filter('login_errors', function() {
    return 'Da lief etwas schief. Bitte versuche es erneut.';
});