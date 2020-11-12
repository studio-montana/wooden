<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package wooden
 */



get_header();

?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

			<header class="page-header">
				<h1><?php echo get_the_archive_title(); ?></h1>
				<div class="description"><?php echo get_the_archive_description(); ?></div>
			</header>

			<div class="page-content">
				<?php if ( have_posts() ) {
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content-resume', get_post_type());
					endwhile;
					the_posts_navigation();
				} ?>
			</div>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
