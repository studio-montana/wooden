<?php
/**
 * Template part for displaying full post content
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package wooden
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(array('content', 'content-'.get_post_type())); ?>>

	<header class="entry-header">
		<?php the_post_thumbnail('large'); ?>
		<h1><?php the_title(); ?></h1>
	</header>

	<div class="entry-content">
		<?php
		the_content( sprintf(
			wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
				__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'wooden' ),
				array(
					'span' => array(
						'class' => array(),
					),
				)
			),
			get_the_title()
		) );
		?>
	</div>

</article>
