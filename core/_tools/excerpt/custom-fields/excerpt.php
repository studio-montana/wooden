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
 * CONSTANTS
 */
define('META_EXCERPT_CONTENT', 'meta_excerpt_content');

if (!function_exists("woodkit_get_the_excerpt")):
/**
 * get woodkit excerpt when wp excerpt wanted
 * @param string $excerpt
 * @return string woodkit excerpt if not empty, wp excerpt otherwise
 */
function woodkit_get_the_excerpt($excerpt){
	$post = get_post();
	if (empty($post)){
		return '';
	}
	$woodkit_excerpt = get_post_meta($post->ID, META_EXCERPT_CONTENT, true);
	if (!empty($woodkit_excerpt))
		return $woodkit_excerpt;
	return $excerpt;
}
add_filter('get_the_excerpt', 'woodkit_get_the_excerpt', 10);
endif;

if (!function_exists("excerpt_add_inner_meta_boxes")):
/**
 * This action is called by Woodkit when metabox is excerpt on post-type
* @param unknown $post
*/
function excerpt_add_inner_meta_boxes($post){
	$id_blog_page = get_option('page_for_posts');
	if ($id_blog_page != get_the_ID()){
		$available_posttypes = get_displayed_post_types();
		$available_posttypes = apply_filters("tool_excerpt_available_posttypes", $available_posttypes);
		if (in_array(get_post_type($post), $available_posttypes)){
			include(wooden_get_tools_directory().EXCERPT_TOOL_NAME.'/custom-fields/templates/excerpt.php');
		}
	}
}
add_action("customfields_add_inner_meta_boxes", "excerpt_add_inner_meta_boxes", 2);
endif;

if (!function_exists("excerpt_save_post")):
/**
 * This action is called by Woodkit when post-type is saved
* @param int $post_id
*/
function excerpt_save_post($post_id){
	$id_blog_page = get_option('page_for_posts');
	if ($id_blog_page != get_the_ID()){
		$available_posttypes = get_displayed_post_types();
		$available_posttypes = apply_filters("tool_excerpt_available_posttypes", $available_posttypes);
		if (in_array($_POST['post_type'], $available_posttypes)){
			// META_DISPLAY_WOODKITEXCERPT
			if (!empty($_POST[META_EXCERPT_CONTENT])){
				update_post_meta($post_id, META_EXCERPT_CONTENT, $_POST[META_EXCERPT_CONTENT]);
			}else{
				delete_post_meta($post_id, META_EXCERPT_CONTENT);
			}
		}
	}
}
add_action("customfields_save_post", "excerpt_save_post");
endif;