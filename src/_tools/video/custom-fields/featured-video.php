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

define('VIDEO_NONCE_VIDEO_ACTION', 'video_action');

define('META_VIDEO_FEATURED_URL', 'meta-video-featured-url');
define('META_VIDEO_FEATURED_EMBED', 'meta-video-featured-embed');

if (!function_exists("video_admin_init")):
/**
 * Hooks the WP admin_init action to add metaboxe video on post-type
*
* @return void
*/
function video_admin_init() {
	$available_posttypes = get_displayed_post_types();
	$available_posttypes = apply_filters("tool_video_available_posttypes", $available_posttypes);
	foreach ($available_posttypes as $post_type){
		add_meta_box('video', __( 'Featured Video', 'woodkit'), 'video_add_inner_meta_boxes', $post_type, 'side', 'low');
	}
}
add_action('admin_init', 'video_admin_init');
endif;

if (!function_exists("video_add_inner_meta_boxes")):
/**
 * include video template
* @param unknown $post
*/
function video_add_inner_meta_boxes($post) {
	include(locate_ressource('/'.WOODKIT_PLUGIN_TOOLS_FOLDER.VIDEO_TOOL_NAME.'/custom-fields/templates/featured-video.php'));
}
endif;

if (!function_exists("video_save_post")):
/**
 * save VIDEO fields on post type
* @param unknown $post_id
*/
function video_save_post($post_id){
	$available_posttypes = get_displayed_post_types();
	$available_posttypes = apply_filters("tool_video_available_posttypes", $available_posttypes);

	// verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;
	// verify if this post-type is available and editable.
	$is_post_available = false;
	$post_type = null;
	if (isset($_POST['post_type']) && !empty($_POST['post_type']) && in_array($_POST['post_type'], $available_posttypes)){
		$post_type = $_POST['post_type'];
	}
	if (empty($post_type))
		return;
	if ($post_type == 'page') {
		if (!current_user_can('edit_page', $post_id))
			return;
	} else {
		if (!current_user_can('edit_post', $post_id ))
			return;
	}
	if (!isset($_POST[VIDEO_NONCE_VIDEO_ACTION]) || !wp_verify_nonce($_POST[VIDEO_NONCE_VIDEO_ACTION], VIDEO_NONCE_VIDEO_ACTION))
		return;

	// META_VIDEO_FEATURED_URL
	if (!empty($_POST[META_VIDEO_FEATURED_URL])){
		update_post_meta($post_id, META_VIDEO_FEATURED_URL, sanitize_text_field($_POST[META_VIDEO_FEATURED_URL]));
	}else{
		delete_post_meta($post_id, META_VIDEO_FEATURED_URL);
	}

	// META_VIDEO_FEATURED_EMBED
	if (!empty($_POST[META_VIDEO_FEATURED_EMBED])){
		update_post_meta($post_id, META_VIDEO_FEATURED_EMBED, sanitize_text_field($_POST[META_VIDEO_FEATURED_EMBED]));
	}else{
		delete_post_meta($post_id, META_VIDEO_FEATURED_EMBED);
	}
}
add_action('save_post', 'video_save_post');
endif;
