<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package wooden
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('content-page'); ?>>
	
	<header class="entry-header">
		<?php the_post_thumbnail('large'); ?>
		<h1><?php the_title(); ?></h1>
	</header>

	<div class="entry-content">
		<?php the_content(); ?>
	</div>
</article>
