<?php get_header(); ?>

<main class="grow pt-14 px-6">
    <?php
    // ... existing code ...
    $args = [
        'post_type'      => 'photo_set',
        'posts_per_page' => -1, // -1 listet alle Sets auf
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    $photo_sets_query = new WP_Query($args);

    if ($photo_sets_query->have_posts()) :
        echo '<div class="photo-sets-grid">';
        while ($photo_sets_query->have_posts()) : $photo_sets_query->the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <?php if (has_post_thumbnail()) : ?>
                    <div class="photo-set-thumbnail">
                        <?php the_post_thumbnail('medium'); ?>
                    </div>
                <?php endif; ?>
                <div class="entry-excerpt">
                    <?php the_excerpt(); ?>
                </div>
            </article>
        <?php
        endwhile;
        echo '</div>';
        wp_reset_postdata();
    else :
        echo '<p>Keine Fotosets gefunden.</p>';
    endif;
    ?>
</main>

<?php get_footer(); ?>