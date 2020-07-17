<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package wooden
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<meta name="theme-color" content="#FFFFFF">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'wooden' ); ?></a>
	
	<?php do_action('wooden_before_header'); ?>
	
	<header id="masthead" class="site-header">
		<div class="wrapper">
			<div class="inner">
				<div class="site-branding">
					<div class="site-title"><a href="<?php echo home_url('/'); ?>"><?php bloginfo('name'); ?></a></div>
				</div>
				<nav id="site-navigation" class="main-navigation">
					<?php wp_nav_menu( array(
						'theme_location' => 'main-menu',
						'menu_id'        => 'main-menu',
					) ); ?>
				</nav>
			</div>
		</div>
	</header>
	
	<?php do_action('wooden_after_header'); ?>

	<div id="content" class="site-content">
		<div class="wrapper">
