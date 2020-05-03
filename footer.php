<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package wooden
 */

?>

		</div><!-- .wrapper -->
	</div><!-- #content -->
	
	<?php do_action('wooden_before_footer'); ?>

	<footer id="colophon" class="site-footer">
		<div class="wrapper">
			<div class="inner">
				<div class="site-info">
					&copy; <?php _e('Tous droits réservés', 'wooden'); ?> - <?php bloginfo('name'); ?> <?php echo date('Y'); ?> - <a href="<?php echo get_privacy_policy_url(); ?>"><?php _e('Politique de confidentialité', 'wooden'); ?></a>
				</div><!-- .site-info -->
			</div>
		</div>
	</footer><!-- #colophon -->
	
	<?php do_action('wooden_after_footer'); ?>
	
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
