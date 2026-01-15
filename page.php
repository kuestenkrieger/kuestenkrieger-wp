<?php
$remove_padding = get_post_meta(get_the_ID(), '_kk_remove_padding', true);

// padding-Klasse bestimmen
$main_classes = 'grow';
$main_classes .= ($remove_padding === '1') ? ' pt-0' : ' px-6 pt-14';

get_header();
?>
	<main class="<?php echo esc_attr($main_classes); ?>">
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
