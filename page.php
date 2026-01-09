<?php get_header(); ?>

	<main class="grow pt-14">
		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'parts/content', 'page' );

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php get_footer(); ?>
