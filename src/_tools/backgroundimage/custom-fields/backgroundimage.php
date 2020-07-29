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

define('BACKGROUNDIMAGE_NONCE_BACKGROUNDIMAGE_ACTION', 'backgroundimage_action');

define('BACKGROUNDIMAGE_URL', 'backgroundimage-url');
define('BACKGROUNDIMAGE_ID', 'backgroundimage-id');
define('BACKGROUNDCOLOR_CODE', 'backgroundcolor-code');
define('BACKGROUNDCOLOR_OPACITY', 'backgroundcolor-opacity');

if (!function_exists("backgroundimage_admin_init")):
/**
 * Hooks the WP admin_init action to add metaboxe backgroundimage on post-type
*
* @return void
*/
function backgroundimage_admin_init() {
	$available_posttypes = get_displayed_post_types();
	$available_posttypes = apply_filters("tool_backgroundimage_available_posttypes", $available_posttypes);
	foreach ($available_posttypes as $post_type){
		add_meta_box('backgroundimage', __( 'Background Image', 'woodkit'), 'backgroundimage_add_inner_meta_boxes', $post_type, 'side', 'low');
	}
}
add_action('admin_init', 'backgroundimage_admin_init');
endif;

if (!function_exists("backgroundimage_add_inner_meta_boxes")):
/**
 * include backgroundimage template
* @param unknown $post
*/
function backgroundimage_add_inner_meta_boxes($post) {
	include(wooden_get_tools_directory().'backgroundimage'.'/custom-fields/templates/backgroundimage.php');
}
endif;

if (!function_exists("backgroundimage_save_post")):
/**
 * save BACKGROUNDIMAGE fields on post type
* @param unknown $post_id
*/
function backgroundimage_save_post($post_id){
	$available_posttypes = get_displayed_post_types();
	$available_posttypes = apply_filters("tool_backgroundimage_available_posttypes", $available_posttypes);
	
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
	if (!isset($_POST[BACKGROUNDIMAGE_NONCE_BACKGROUNDIMAGE_ACTION]) || !wp_verify_nonce($_POST[BACKGROUNDIMAGE_NONCE_BACKGROUNDIMAGE_ACTION], BACKGROUNDIMAGE_NONCE_BACKGROUNDIMAGE_ACTION))
		return;

	// BACKGROUNDIMAGE_URL
	if (!empty($_POST[BACKGROUNDIMAGE_URL])){
		update_post_meta($post_id, BACKGROUNDIMAGE_URL, sanitize_text_field($_POST[BACKGROUNDIMAGE_URL]));
	}else{
		delete_post_meta($post_id, BACKGROUNDIMAGE_URL);
	}

	// BACKGROUNDIMAGE_ID
	if (!empty($_POST[BACKGROUNDIMAGE_ID])){
		update_post_meta($post_id, BACKGROUNDIMAGE_ID, sanitize_text_field($_POST[BACKGROUNDIMAGE_ID]));
	}else{
		delete_post_meta($post_id, BACKGROUNDIMAGE_ID);
	}

	// BACKGROUNDCOLOR_CODE
	if (!empty($_POST[BACKGROUNDCOLOR_CODE])){
		update_post_meta($post_id, BACKGROUNDCOLOR_CODE, sanitize_text_field($_POST[BACKGROUNDCOLOR_CODE]));
	}else{
		delete_post_meta($post_id, BACKGROUNDCOLOR_CODE);
	}

	// BACKGROUNDCOLOR_OPACITY
	if (!empty($_POST[BACKGROUNDCOLOR_OPACITY])){
		update_post_meta($post_id, BACKGROUNDCOLOR_OPACITY, sanitize_text_field($_POST[BACKGROUNDCOLOR_OPACITY]));
	}else{
		delete_post_meta($post_id, BACKGROUNDCOLOR_OPACITY);
	}
}
add_action('save_post', 'backgroundimage_save_post');
endif;
