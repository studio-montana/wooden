<?php
/**
 * @package Woodkit
 * @author Sébastien Chandonay www.seb-c.com / Cyril Tissot www.cyriltissot.com
 * License: GPL2
 * Text Domain: woodkit
 *
 * Copyright 2016 Sébastien Chandonay (email : please contact me from my website)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
defined('ABSPATH') or die("Go Away!");

/**
 * REQUIREMENTS
*/
require_once (wooden_get_tools_directory().'/backgroundimage/custom-fields/backgroundimage.php');
require_once (wooden_get_tools_directory().'/backgroundimage/inc/customizer.php');

if (!function_exists("backgroundimage_get_url")):
/**
 * retrieve backgroundimage url
* @param string $id_post - current post if null
*/
function backgroundimage_get_url($id_post = null){
	$url_backgroundimage = "";
	if (empty($id_post) || !is_numeric($id_post)){
		$_queried_post = get_queried_object();
		if (is_home()){ // blog page
			$id_post = get_option('page_for_posts');
		}else if (!is_404() && $_queried_post){
			$id_post = $_queried_post->ID;
		}
	}
	if ($id_post && (is_single() || is_page() || is_home() || (function_exists("is_shop") && is_shop()))){
		$url_backgroundimage = get_post_meta($id_post, BACKGROUNDIMAGE_URL, true);
	}
	if (empty($url_backgroundimage)){
		$url_backgroundimage = get_theme_mod('backgroundimage_image');
	}

	return $url_backgroundimage;
}
endif;

if (!function_exists("backgroundimage_is_default")):
/**
 * retrieve backgroundimage url
* @param string $id_post - current post if null
*/
function backgroundimage_is_default($id_post = null){
	$default = true;
	if (empty($id_post) || !is_numeric($id_post)){
		$_queried_post = get_queried_object();
		if (is_home()){ // blog page
			$id_post = get_option('page_for_posts');
		}else if (!is_404() && $_queried_post){
			$id_post = $_queried_post->ID;
		}
	}
	// background image
	if ($id_post && (is_single() || is_page() || is_home() || (function_exists("is_shop") && is_shop()))){
		$post_meta = get_post_meta($id_post, BACKGROUNDIMAGE_URL, true);
		if (!empty($post_meta)){
			$default = false;
		}
	}
	return $default;
}
endif;

if (!function_exists("backgroundimage_get_color")):
/**
 * retrieve backgroundimage color
* @param string $id_post - current post if null
*/
function backgroundimage_get_color($id_post = null){
	$background_color_code = "";
	if (empty($id_post) || !is_numeric($id_post)){
		$_queried_post = get_queried_object();
		if (is_home()){ // blog page
			$id_post = get_option('page_for_posts');
		}else if (!is_404() && $_queried_post){
			$id_post = $_queried_post->ID;
		}
	}
	if ($id_post && (is_single() || is_page() || is_home() || (function_exists("is_shop") && is_shop()))){
		$background_color_code = get_post_meta($id_post, BACKGROUNDCOLOR_CODE, true);
		$background_color_opacity = get_post_meta($id_post, BACKGROUNDCOLOR_OPACITY, true);
		if (empty($background_color_opacity) || $background_color_opacity == 0){
			$background_color_code = '';
		}
	}
	if (empty($background_color_code)){
		$background_color_code = get_theme_mod('backgroundimage_color');
	}
	if (!empty($background_color_code)){
		$background_color_code = hex_to_rgb($background_color_code, true);
	}
	return $background_color_code;
}
endif;

if (!function_exists("backgroundimage_get_color_opacity")):
/**
 * retrieve backgroundimage color's opacity
* @param string $id_post - current post if null
*/
function backgroundimage_get_color_opacity($id_post = null){
	$background_color_opacity = "";
	if (empty($id_post) || !is_numeric($id_post)){
		$_queried_post = get_queried_object();
		if (is_home()){ // blog page
			$id_post = get_option('page_for_posts');
		}else if (!is_404() && $_queried_post){
			$id_post = $_queried_post->ID;
		}
	}
	if ($id_post && (is_single() || is_page() || is_home() || (function_exists("is_shop") && is_shop()))){
		$background_color_opacity = get_post_meta($id_post, BACKGROUNDCOLOR_OPACITY, true);
	}
	if (empty($background_color_opacity) || $background_color_opacity == 0){
		$background_color_opacity = get_theme_mod('backgroundimage_opacity');
	}
	if (empty($background_color_opacity))
		$background_color_opacity = 0;
	if ($background_color_opacity == 100){
		$background_color_opacity = "1";
	}else{
		$background_color_opacity = "0.".$background_color_opacity;
	}
	return $background_color_opacity;
}
endif;

if (!function_exists("woodkit_backgroundimage")):
/**
 * Display background image/color/opacity
* @param boolean $auto_insert : boolean - do not specify true if you use this method manualy in your template theme
*/
function woodkit_backgroundimage($position = 'absolute', $background_attachement = 'scroll', $z_index = '-999'){
	$id_post = null;
	if (is_home()){ // blog page
		$id_post = get_option('page_for_posts');
	}else if(function_exists("is_shop") && is_shop()){
		$id_post = get_option('woocommerce_shop_page_id');
	}else if (!is_404()){
		$_queried_post = get_queried_object();
		if ($_queried_post)
			$id_post = $_queried_post->ID;
	}

	// background image
	$url_backgroundimage = backgroundimage_get_url($id_post);

	// background color
	$background_color_code = backgroundimage_get_color($id_post);

	// background color opacity
	$background_color_opacity = backgroundimage_get_color_opacity($id_post);

	// default ?
	$class = "";
	if (backgroundimage_is_default($id_post))
		$class = "default";

	if (!empty($url_backgroundimage)){
		$variable_styles = "";
		$variable_styles .= " position: $position;";
		$variable_styles .= " z-index: $z_index;";
		?>
<div class="tool-backgroundimage" class="<?php echo $class; ?>" style="background: url('<?php echo $url_backgroundimage; ?>') no-repeat center center <?php echo $background_attachement; ?>;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			-ms-background-size: cover;
			background-size: cover;
			top: 0;
			right: 0;
			bottom: 0;
			left: 0;
			width: 100%;
			height: 100%;
			<?php echo $variable_styles; ?>">
	<?php
	if (!empty($background_color_code)){
?>
	<div class="tool-backgroundimage-color"
		style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(<?php echo $background_color_code; ?>, <?php echo $background_color_opacity; ?>);"></div>
	<?php } ?>
</div>
<?php
	}else if (!empty($background_color_code)){
		$variable_styles = "";
		$variable_styles .= " position: $position;";
		$variable_styles .= " z-index: $z_index;";
		?>
<div class="tool-backgroundimage-color"
		style="top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(<?php echo $background_color_code; ?>, <?php echo $background_color_opacity; ?>);<?php echo $variable_styles; ?>"></div>
<?php
	}
}
endif;

if (!function_exists("woodkit_backgroundimage_autoinsert")):
/**
 * Display background image/color/opacity
* @param boolean $auto_insert : boolean - do not specify true if you use this method manualy in your template theme
*/
function woodkit_backgroundimage_autoinsert($auto_insert = false){
	$backgroundimage_autoinsert = $GLOBALS['woodkit']->tools->get_tool_option('backgroundimage', 'auto-insert');
	if (!empty($backgroundimage_autoinsert) && $backgroundimage_autoinsert == 'on'){
		woodkit_backgroundimage('fixed', 'fixed');
	}
}
add_action('wp_footer', 'woodkit_backgroundimage_autoinsert');
endif;


