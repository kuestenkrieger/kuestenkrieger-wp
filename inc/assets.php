<?php
/**
 * Asset management (Vite, Scripts, Styles)
 */

function kuestenkrieger_enqueue_assets() {
    $is_dev = defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE === 'development';

    if ($is_dev) {
        wp_enqueue_script('vite-client', 'http://localhost:5173/@vite/client', [], null, true);
        wp_enqueue_script('kuestenkrieger-main', 'http://localhost:5173/src/main.js', ['vite-client'], null, true);
        wp_enqueue_style('kuestenkrieger-vite-style', 'http://localhost:5173/src/css/main.css', [], null);
    } else {
        $manifest_path = get_template_directory() . '/dist/.vite/manifest.json';

        if (file_exists($manifest_path)) {
            $manifest = json_decode(file_get_contents($manifest_path), true);
            if (isset($manifest['src/main.js'])) {
                wp_enqueue_script('kuestenkrieger-main', get_template_directory_uri() . '/dist/' . $manifest['src/main.js']['file'], [], null, true);
                if (isset($manifest['src/main.js']['css'])) {
                    foreach ($manifest['src/main.js']['css'] as $css_file) {
                        wp_enqueue_style('kuestenkrieger-style-' . $css_file, get_template_directory_uri() . '/dist/' . $css_file, [], null);
                    }
                }
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'kuestenkrieger_enqueue_assets');

add_filter('script_loader_tag', function($tag, $handle, $src) {
    if (in_array($handle, ['vite-client', 'kuestenkrieger-main'])) {
        return '<script type="module" src="' . esc_url($src) . '"></script>';
    }
    return $tag;
}, 10, 3);