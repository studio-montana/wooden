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
 * Enqueue scripts for the front end.
*/
function tool_fancybox_woodkit_front_enqueue_scripts_tools($dependencies) {
	$js_tool_fancybox_fancybox = locate_web_ressource(WOODKIT_PLUGIN_TOOLS_FOLDER.FANCYBOX_TOOL_NAME.'/js/tool-fancybox.js');
	if (!empty($js_tool_fancybox_fancybox)){
		wp_enqueue_script('tool-fancybox-script-fancybox', $js_tool_fancybox_fancybox, $dependencies, '1.0', true);
		$background_color = apply_filters('woodkit-tool-fancybox-backgroundcolor', "rgba(0, 0, 0, 0.85)");
		wp_localize_script ('tool-fancybox-script-fancybox', 'ToolFancybox', array (
				'backgroundcolor' => $background_color,
		) );
	}
}
add_action('woodkit_front_enqueue_scripts_tools', 'tool_fancybox_woodkit_front_enqueue_scripts_tools');

function tool_fancybox_image_send_to_editor($html, $id, $caption, $title, $align, $url, $size, $alt = '' ){
	$fancybox_wordpress_contents_active = $GLOBALS['woodkit']->tools->get_tool_option(FANCYBOX_TOOL_NAME, 'wordpress-contents');
	if (!empty($fancybox_wordpress_contents_active) && $fancybox_wordpress_contents_active == 'on'){
		$classes = 'fancybox'; // separated by spaces, e.g. 'img image-link'
		if ( preg_match('/<a.*? class=".*?">/', $html) ) {
			$html = preg_replace('/(<a.*? class=".*?)(".*?>)/', '$1 ' . $classes . '$2', $html);
		} else {
			$html = preg_replace('/(<a.*?)>/', '$1 class="' . $classes . '" >', $html);
		}
		$rel = 'group-single';
		if ( preg_match('/<a.*? rel=".*?">/', $html) ) {
			$html = preg_replace('/(<a.*? rel=".*?)(".*?>)/', '$1 ' . $rel . '$2', $html);
		} else {
			$html = preg_replace('/(<a.*?)>/', '$1 rel="' . $rel . '" >', $html);
		}
	}
	return $html;
}
add_filter('image_send_to_editor','tool_fancybox_image_send_to_editor',10,8);
