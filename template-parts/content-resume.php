<?php
/**
 * Template part for displaying post resume
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package wooden
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(array('content-resume', 'content-resume-'.get_post_type())); ?>>

	<header class="entry-header">
		<a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1" title="<?php echo esc_attr(get_the_title()); ?>"><?php the_post_thumbnail('large'); ?></a>
		<h1><a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1" title="<?php echo esc_attr(get_the_title()); ?>"><?php the_title(); ?></a></h1>
	</header>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div>
	<div class="entry-readmore">
		<a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1" title="<?php echo esc_attr(get_the_title()); ?>"><?php _e('Read more', 'wooden'); ?></a>
	</div>

</article>
