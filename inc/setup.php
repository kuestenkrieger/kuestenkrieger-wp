<?php
/**
 * Theme basic setup
 */

function kuestenkrieger_setup() {
    register_nav_menus(array(
        'menu-1' => esc_html__( 'Primary', 'kuestenkrieger' ),
    ));

    add_theme_support( 'custom-logo', array(
        'height'      => 50,
        'width'       => 400,
        'flex-height' => false,
        'flex-width'  => true,
    ));

    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('title-tag');
}
add_action( 'after_setup_theme', 'kuestenkrieger_setup' );