<?php
/**
 * Theme basic setup
 */

function kuestenkrieger_setup() {
    register_nav_menus(array(
        'menu-1' => esc_html__( 'Primary', 'kuestenkrieger' ),
    ));

    add_theme_support( 'custom-logo', array(
        'height'      => 250,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
        'header-text' => ['site-title', 'site-description'],
    ));

    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('title-tag');
}
add_action( 'after_setup_theme', 'kuestenkrieger_setup' );