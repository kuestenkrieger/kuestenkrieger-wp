<?php

function kk_add_page_settings_meta_box() {
    add_meta_box(
        'kk_page_settings',
        'Seiten-Optionen',
        'kk_render_page_settings_meta_box',
        'page',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'kk_add_page_settings_meta_box');

function kk_render_page_settings_meta_box($post) {
    $hide_title = get_post_meta($post->ID, '_kk_hide_title', true);
    $remove_padding = get_post_meta($post->ID, '_kk_remove_padding', true);
    wp_nonce_field('kk_page_settings_nonce', 'kk_page_settings_nonce_field');
    ?>
    <p>
        <label>
            <input type="checkbox" name="kk_hide_title" value="1" <?php checked($hide_title, '1'); ?> />
            Titel ausblenden
        </label>
    </p>
    <p>
        <label>
            <input type="checkbox" name="kk_remove_padding" value="1" <?php checked($remove_padding, '1'); ?> />
            Oberen Abstand in Main-Container entfernen
        </label>
    </p>
    <?php
}

function kk_save_page_settings($post_id) {
    if (!isset($_POST['kk_page_settings_nonce_field']) || !wp_verify_nonce($_POST['kk_page_settings_nonce_field'], 'kk_page_settings_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_page', $post_id)) return;

    $hide_title = isset($_POST['kk_hide_title']) ? '1' : '0';
    $remove_padding = isset($_POST['kk_remove_padding']) ? '1' : '0';

    update_post_meta($post_id, '_kk_hide_title', $hide_title);
    update_post_meta($post_id, '_kk_remove_padding', $remove_padding);
}
add_action('save_post', 'kk_save_page_settings');