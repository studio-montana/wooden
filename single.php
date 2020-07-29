<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package wooden
 */

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">
		
			<?php while ( have_posts() ) :
				the_post();
				get_template_part('template-parts/content', get_post_type());
				the_posts_navigation();
			endwhile; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
