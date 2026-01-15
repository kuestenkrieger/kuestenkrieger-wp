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

/**
 * Registriert eigene Bildgrößen für die Galerie und das Portfolio.
 */
function kuestenkrieger_custom_image_sizes() {
    add_image_size('gallery-thumb', 600, 600, false);
    add_image_size('gallery-landscape', 1200, 600, false);
    add_image_size('gallery-fullsize', 1920, 1080, false);
    add_image_size('portfolio-hero', 2560, 1440, false);
}
add_action('after_setup_theme', 'kuestenkrieger_custom_image_sizes');

/**
 * Bereinigt das HTML des Gallery-Block Containers.
 */
function kuestenkrieger_clean_gallery_block_output($block_content, $block) {
    if ('core/gallery' === $block['blockName']) {
        $pattern = '/<figure[^>]*class="([^"]*)"[^>]*>/i';
        return preg_replace_callback($pattern, function($matches) {
            $new_classes = 'gallery-block grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-10 !list-none !p-0';
            return str_replace($matches[1], $new_classes, $matches[0]);
        }, $block_content, 1);
    }
    return $block_content;
}
add_filter('render_block', 'kuestenkrieger_clean_gallery_block_output', 11, 2);

/**
 * Optimiert die einzelnen Bilder innerhalb der Galerie:
 * - Ersetzt alle Klassen durch 'gallery-block-image'
 * - Erkennt Querformate (2 Spalten)
 * - Weist passende Thumbnails UND responsive srcsets zu
 * - Erzwingt loading="lazy"
 */
function kuestenkrieger_purify_gallery_image_html($block_content, $block) {
    if ('core/image' === $block['blockName']) {

        $base_class = 'gallery-block-image';
        $target_size = 'gallery-thumb'; // Standard für Hochformat/Quadrat

        if (!empty($block['attrs']['id'])) {
            $attachment_id = $block['attrs']['id'];
            $meta = wp_get_attachment_metadata($attachment_id);

            // 1. Querformat erkennen
            if ($meta && isset($meta['width'], $meta['height']) && $meta['width'] > $meta['height']) {
                $base_class .= ' md:col-span-2 is-landscape';
                $target_size = 'gallery-landscape';
            }

            // 2. Responsive Image Daten generieren (Korrektur: wp_get_attachment_image_srcset)
            $image_src    = wp_get_attachment_image_src($attachment_id, $target_size);
            $image_srcset = wp_get_attachment_image_srcset($attachment_id, $target_size);

            // Sizes Attribut passend zum Tailwind-Grid definieren
            $image_sizes = ($target_size === 'gallery-landscape')
                ? '(max-width: 768px) 100vw, 50vw'
                : '(max-width: 768px) 50vw, 25vw';

            if ($image_src) {
                // SRC ersetzen
                $block_content = preg_replace('/src="([^"]*)"/i', 'src="' . $image_src[0] . '"', $block_content);

                // srcset hinzufügen oder ersetzen
                if ($image_srcset) {
                    if (str_contains($block_content, 'srcset="')) {
                        $block_content = preg_replace('/srcset="([^"]*)"/i', 'srcset="' . $image_srcset . '"', $block_content);
                    } else {
                        $block_content = str_replace('<img ', '<img srcset="' . $image_srcset . '" ', $block_content);
                    }
                }

                // sizes hinzufügen oder ersetzen
                if (str_contains($block_content, 'sizes="')) {
                    $block_content = preg_replace('/sizes="([^"]*)"/i', 'sizes="' . $image_sizes . '"', $block_content);
                } else {
                    $block_content = str_replace('<img ', '<img sizes="' . $image_sizes . '" ', $block_content);
                }
            }
        }

        // 3. Figure-Klassen bereinigen
        $pattern_figure = '/<figure[^>]*class="([^"]*)"[^>]*>/i';
        $block_content = preg_replace_callback($pattern_figure, function($matches) use ($base_class) {
            return str_replace('class="' . $matches[1] . '"', 'class="' . $base_class . '"', $matches[0]);
        }, $block_content);

        // 4. Img-Tag Klassen & Lazy Loading
        $block_content = preg_replace('/(<img[^>]+)class="[^"]*"([^>]*>)/i', '$1class="w-full h-full object-cover"$2', $block_content);

        if (!str_contains($block_content, 'loading="')) {
            $block_content = str_replace('<img ', '<img loading="lazy" ', $block_content);
        }
    }
    return $block_content;
}
add_filter('render_block', 'kuestenkrieger_purify_gallery_image_html', 10, 2);

/**
 * Verhindert, dass WordPress versucht, SVGs zuzuschneiden.
 * Dies entfernt die "Zuschneiden"-Option im Editor für SVG-Dateien.
 */
add_filter('wp_prepare_attachment_for_js', function($response, $attachment, $meta) {
    if ($response['mime'] === 'image/svg+xml') {
        // Wir geben dem SVG fiktive Maße, falls keine da sind,
        // damit WordPress nicht "denkt" es sei kaputt
        if (empty($response['width'])) {
            $response['width'] = 100;
            $response['height'] = 100;
        }

        $response['non_resizable'] = true;
    }
    return $response;
}, 10, 3);

/**
 * Optimiert Bilder im kuestenkrieger/slide Block
 */
function kuestenkrieger_optimize_slider_images($block_content, $block) {
    if ('kuestenkrieger/slide' === $block['blockName'] && !empty($block['attrs']['mediaId'])) {
        $attachment_id = $block['attrs']['mediaId'];

        // Generiere das vollständige Responsive Image Tag für die 2k-Größe
        $image_html = wp_get_attachment_image($attachment_id, '2k-resolution', false, [
            'class' => 'slide-image',
            'loading' => 'lazy'
        ]);

        if ($image_html) {
            // Ersetzt das einfache <img> Tag aus der save() Funktion durch das optimierte WP-Tag
            return preg_replace('/<img[^>]+>/i', $image_html, $block_content);
        }
    }
    return $block_content;
}
add_filter('render_block', 'kuestenkrieger_optimize_slider_images', 10, 2);