<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class('bg-offwhite text-black flex flex-col'); ?>>
<header class="header fixed left-0 h-14 z-50 bg-offwhite/50 backdrop-blur-md px-6 w-screen flex items-center justify-between text-black">
    <?php
    if ( has_custom_logo() ) {
        the_custom_logo();
    } else {
        echo '<a href="'.esc_url( home_url( '/' ) ).'" title="go to homepage"><span class="font-bold text-xl">' . get_bloginfo('name') . '</span></a>';
    }
    ?>
    <?php get_template_part('parts/navigation'); ?>
</header>