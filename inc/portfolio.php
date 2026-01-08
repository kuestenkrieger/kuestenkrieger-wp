<?php
function portfolio_register_post_types() {
    // 1. Die Kategorien (Taxonomie)
    $taxonomy_labels = [
        'name'              => 'Foto-Kategorien',
        'singular_name'     => 'Foto-Kategorie',
        'search_items'      => 'Kategorien suchen',
        'all_items'         => 'Alle Kategorien',
        'edit_item'         => 'Kategorie bearbeiten',
        'update_item'       => 'Kategorie aktualisieren',
        'add_new_item'      => 'Neue Kategorie hinzufügen',
        'new_item_name'     => 'Neuer Kategoriename',
        'menu_name'         => 'Kategorien',
    ];

    register_taxonomy('photo_category', ['photo_set'], [
        'hierarchical'      => true, // Verhält sich wie normale Kategorien
        'labels'            => $taxonomy_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'portfolio'], // URL: /portfolio/hochzeit
        'show_in_rest'      => true, // Wichtig für den Gutenberg-Editor
    ]);

    // 2. Die Foto-Sets (Custom Post Type)
    $cpt_labels = [
        'name'               => 'Foto-Sets',
        'singular_name'      => 'Foto-Set',
        'add_new'            => 'Neues Set',
        'add_new_item'       => 'Neues Foto-Set anlegen',
        'edit_item'          => 'Set bearbeiten',
        'new_item'           => 'Neues Set',
        'view_item'          => 'Set ansehen',
        'search_items'       => 'Sets suchen',
        'not_found'          => 'Keine Foto-Sets gefunden',
        'menu_name'          => 'Foto-Sets',
    ];

    register_post_type('photo_set', [
        'labels'             => $cpt_labels,
        'public'             => true,
        'has_archive'        => true,
        'menu_icon'          => 'dashicons-format-gallery', // Icon im Admin
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
        'taxonomies'         => ['photo_category'],
        'rewrite'            => ['slug' => 'foto-set'], // URL: /foto-set/unser-grosser-tag
        'show_in_rest'       => true,
    ]);
}
add_action('init', 'portfolio_register_post_types');