<nav id="site-navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'kuestenkrieger' ); ?>">
    <?php
    wp_nav_menu(
        array(
            'theme_location' => 'menu-1',
            'menu_id'        => 'primary-menu',
            'container'      => false,
            'fallback_cb'    => false,
        )
    );
    ?>
</nav>