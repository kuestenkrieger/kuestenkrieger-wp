<?php
/**
 * Kuestenkrieger Theme Functions
 */

$kk_includes = [
    'inc/setup.php',   // Grund-Konfiguration
    'inc/assets.php',  // Vite & Scripts
    'inc/cleanup.php', // Performance & Sicherheit
    'inc/media.php',   // Bilder (SVG/WebP)
    'inc/portfolio.php', // Portfolio Custom Post Type & Taxonomy
    'inc/metabox.php', // Metabox für Seiten-Optionen
];

foreach ($kk_includes as $file) {
    require get_template_directory() . '/' . $file;
}