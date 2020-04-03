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
require_once (wooden_get_tools_directory().DISPLAY_TOOL_NAME.'/custom-fields/display.php');

if (!function_exists("woodkit_display_title")):
/**
 * display post title
* @param string $before
* @param string $after
*/
function woodkit_display_title($post_id = null, $display = true, $test_hide_property = true, $before = '<h1 class="entry-title">', $after = '</h1>'){
	$res = '';
	if (empty($post_id))
		$post_id = get_the_ID();
	$hide_title = 'off';
	if ($test_hide_property)
		$hide_title = get_post_meta($post_id, META_DISPLAY_HIDE_TITLE, true);
	if (empty($hide_title) || $hide_title != 'on'){
		$title = get_post_meta($post_id, META_DISPLAY_CUSTOMTITLE, true);
		if (empty($title))
			$title = get_the_title($post_id);
		$res = $before.$title.$after;
	}
	if ($display)
		echo $res;
	else
		return $res;
}
endif;

if (!function_exists("woodkit_display_subtitle")):
/**
 * display post subtitle
* @param string $before
* @param string $after
*/
function woodkit_display_subtitle($post_id = null, $display = true, $before = '<h2 class="entry-subtitle">', $after = '</h2>'){
	$res = '';
	if (empty($post_id))
		$post_id = get_the_ID();
	$subtitle = get_post_meta($post_id, META_DISPLAY_SUBTITLE, true);
	if (!empty($subtitle)){
		$res = $before.$subtitle.$after;
	}
	if ($display)
		echo $res;
	else
		return $res;
}
endif;

if (!function_exists("woodkit_display_thumbnail")):
/**
 * display post thumbnail
*/
function woodkit_display_thumbnail($post_id = null, $size = 'post-thumbnail', $attr = '' , $display = true, $test_hide_property = true, $before = '<div class="entry-thumbnail">', $after = '</div>'){
	$res = '';
	if (empty($post_id))
		$post_id = get_the_ID();
	if (has_post_thumbnail($post_id)){
		$hide_thumb = 'off';
		if ($test_hide_property)
			$hide_thumb = get_post_meta($post_id, META_DISPLAY_HIDE_THUMBNAIL, true);
		if (empty($hide_thumb) || $hide_thumb != 'on'){
			$res = $before.get_the_post_thumbnail($post_id, $size, $attr).$after;
		}
	}
	$res = apply_filters('post_thumbnail_html', $res, $post_id, get_post_thumbnail_id($post_id), $size, $attr);
	if ($display)
		echo $res;
	else
		return $res;
}
endif;

if (!function_exists("woodkit_display_badge")):
/**
 * display post subtitle
* @param string $before
* @param string $after
*/
function woodkit_display_badge($post_id = null, $display = true, $before = '<div class="has-badge"><span>', $after = '</span></div>'){
	$res = '';
	if (empty($post_id))
		$post_id = get_the_ID();
	$badged = get_post_meta($post_id, META_DISPLAY_BADGED, true);
	if (!empty($badged) && $badged == 'on'){
		$res = $before.get_post_meta($post_id, META_DISPLAY_BADGE_TEXT, true).$after;
	}
	if ($display)
		echo $res;
	else
		return $res;
}
endif;