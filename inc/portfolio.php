<?php
/**
 * Portfolio Post Type & Meta Boxen
 */

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
        'hierarchical'      => true,
        'labels'            => $taxonomy_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'portfolio'],
        'show_in_rest'      => true,
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
        'menu_icon'          => 'dashicons-format-gallery',
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
        'taxonomies'         => ['photo_category'],
        'rewrite'            => ['slug' => 'foto-set'],
        'show_in_rest'       => true,
    ]);
}
add_action('init', 'portfolio_register_post_types');

/**
 * Meta Boxen hinzufügen
 */
function portfolio_add_meta_boxes() {
    add_meta_box(
        'photo_set_details',
        'Foto-Set Details',
        'portfolio_render_meta_box',
        'photo_set',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'portfolio_add_meta_boxes');

/**
 * Meta Box HTML Rendern
 */
function portfolio_render_meta_box($post) {
    $models = get_post_meta($post->ID, '_portfolio_models', true);
    if (!is_array($models)) $models = [];

    $client_link = get_post_meta($post->ID, '_client_link', true);
    $shooting_date = get_post_meta($post->ID, '_shooting_date', true);

    wp_nonce_field('portfolio_save_meta_box_data', 'portfolio_meta_box_nonce');
    ?>
    <div id="portfolio-models-container" style="margin-bottom: 20px;">
        <label><strong>Models (Name & Link):</strong></label>
        <div class="models-list" style="margin-top: 10px;">
            <?php if (!empty($models)) : foreach ($models as $index => $model) : ?>
                <div class="model-row" style="margin-bottom: 10px; display: flex; gap: 10px;">
                    <input type="text" name="portfolio_models[<?php echo $index; ?>][name]" value="<?php echo esc_attr($model['name']); ?>" placeholder="Name" class="widefat">
                    <input type="url" name="portfolio_models[<?php echo $index; ?>][url]" value="<?php echo esc_url($model['url']); ?>" placeholder="Link (z.B. Instagram)" class="widefat">
                    <button type="button" class="button remove-model" title="Entfernen">×</button>
                </div>
            <?php endforeach; endif; ?>
        </div>
        <button type="button" class="button add-model">Model hinzufügen</button>
    </div>

    <script>
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-model')) {
                const container = document.querySelector('.models-list');
                const index = container.children.length;
                const row = document.createElement('div');
                row.className = 'model-row';
                row.style = 'margin-bottom: 10px; display: flex; gap: 10px;';
                row.innerHTML = `
                    <input type="text" name="portfolio_models[${index}][name]" placeholder="Name" class="widefat">
                    <input type="url" name="portfolio_models[${index}][url]" placeholder="Link (z.B. Instagram)" class="widefat">
                    <button type="button" class="button remove-model" title="Entfernen">×</button>
                `;
                container.appendChild(row);
            }
            if (e.target.classList.contains('remove-model')) {
                e.target.closest('.model-row').remove();
            }
        });
    </script>

    <hr>

    <p>
        <label for="client_link"><strong>Kunden Link (URL):</strong></label><br>
        <input type="url" id="client_link" name="client_link" value="<?php echo esc_url($client_link); ?>" class="widefat" placeholder="https://...">
    </p>

    <p>
        <label for="shooting_date"><strong>Shooting Datum:</strong></label><br>
        <input type="date" id="shooting_date" name="shooting_date" value="<?php echo esc_attr($shooting_date); ?>" class="widefat">
    </p>
    <?php
}

/**
 * Meta Box Daten speichern
 */
function portfolio_save_meta_box_data($post_id) {
    if (!isset($_POST['portfolio_meta_box_nonce']) || !wp_verify_nonce($_POST['portfolio_meta_box_nonce'], 'portfolio_save_meta_box_data')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // Models verarbeiten
    if (isset($_POST['portfolio_models']) && is_array($_POST['portfolio_models'])) {
        $sanitized_models = [];
        foreach ($_POST['portfolio_models'] as $model) {
            if (!empty($model['name'])) {
                $sanitized_models[] = [
                    'name' => sanitize_text_field($model['name']),
                    'url'  => esc_url_raw($model['url'])
                ];
            }
        }
        update_post_meta($post_id, '_portfolio_models', $sanitized_models);
    } else {
        delete_post_meta($post_id, '_portfolio_models');
    }

    // Andere Felder speichern
    if (isset($_POST['client_link'])) {
        update_post_meta($post_id, '_client_link', esc_url_raw($_POST['client_link']));
    }
    if (isset($_POST['shooting_date'])) {
        update_post_meta($post_id, '_shooting_date', sanitize_text_field($_POST['shooting_date']));
    }
}
add_action('save_post', 'portfolio_save_meta_box_data');