<?php
/**
 * Kuestenkrieger Theme Functions
 */

$kk_includes = [
    'inc/setup.php',   // Grund-Konfiguration
    'inc/assets.php',  // Vite & Scripts
    'inc/cleanup.php', // Performance & Sicherheit
    'inc/media.php',   // Bilder (SVG/WebP)
];

foreach ($kk_includes as $file) {
    require get_template_directory() . '/' . $file;
}