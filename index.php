<?php get_header(); ?>

<main class="container mx-auto pt-14 prose prose-slate grow max-w-none">
    <?php
    if (have_posts()) :
        while (have_posts()) : the_post();
            the_content();
        endwhile;
    endif;
    ?>
</main>

<?php get_footer(); ?>