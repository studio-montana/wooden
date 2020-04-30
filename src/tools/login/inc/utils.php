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
 * add background image div before #page element
*/
function tool_login_display_background(){
	$background_image_url = get_theme_mod("login_backgroundimage");
	
	if (!empty($background_image_url)){
		$background_image_id = woodkit_get_image_id_for_url($background_image_url);
		if (!empty($background_image_id)){
			$image = wp_get_attachment_image_src($background_image_id, 'woodkit-1200');
			if ($image){
				list($background_image_url, $width, $height) = $image;
			}
			$image_loading = wp_get_attachment_image_src($background_image_id, 'woodkit-100');
			if ($image_loading){
				list($background_image_loading_url, $width_loading, $height_loading) = $image_loading;
			}
		}
	}

	if (!empty($background_image_url)){
		?>
<div id="tool-login-background" class="tool-login-background-loading" style="background: url('<?php echo $background_image_loading_url; ?>') no-repeat center center fixed;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			-ms-background-size: cover;
			background-size: cover;
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			z-index: -100;">
	<div id="tool-login-background" class="tool-login-background" style="background: url('<?php echo $background_image_url; ?>') no-repeat center center fixed;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			-ms-background-size: cover;
			background-size: cover;
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			z-index: -90;">
	</div>
</div>
<?php
	}
}
add_action('login_footer', 'tool_login_display_background');
