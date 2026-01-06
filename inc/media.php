<?php
/**
 * Media and Image handling
 */

// SVG Support
add_filter('upload_mimes', function($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});

add_action('admin_head', function() {
    echo '<style>td.media-icon img[src$=".svg"], img[src$=".svg"].attachment-post-thumbnail { width: 100% !important; height: auto !important; }</style>';
});

// WebP Konvertierung
add_filter('wp_handle_upload', function($upload) {
    if ($upload['type'] == 'image/jpeg' || $upload['type'] == 'image/png') {
        $image = wp_get_image_editor($upload['file']);
        if (!is_wp_error($image)) {
            $info = pathinfo($upload['file']);
            $webp_path = $info['dirname'] . '/' . $info['filename'] . '.webp';
            $image->set_quality(80);
            $image->save($webp_path, 'image/webp');
        }
    }
    return $upload;
});

add_filter('image_editor_output_format', function($formats) {
    $formats['image/jpeg'] = 'image/webp';
    $formats['image/png']  = 'image/webp';
    return $formats;
});