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
define('META_PAGINATION_DISPLAY_PAGINATION', 'meta_pagination_display_pagination');

if (!function_exists("pagination_add_inner_meta_boxes")):
/**
 * This action is called by Woodkit when metabox is displayed on post-type
* @param unknown $post
*/
function pagination_add_inner_meta_boxes($post){
	$pagination_post_types_allowed = apply_filters("woodkit_pagination_post_types_allowed", get_displayed_post_types());
	if (in_array(get_post_type($post), $pagination_post_types_allowed)){
		include(locate_ressource('/'.WOODKIT_PLUGIN_TOOLS_FOLDER.PAGINATION_TOOL_NAME.'/custom-fields/templates/display-pagination.php'));
	}
}
add_action("customfields_add_inner_meta_boxes", "pagination_add_inner_meta_boxes");
endif;

if (!function_exists("pagination_save_post")):
/**
 * This action is called by Woodkit when post-type is saved
* @param int $post_id
*/
function pagination_save_post($post_id){
	$pagination_post_types_allowed = apply_filters("woodkit_pagination_post_types_allowed", get_displayed_post_types());
	if (in_array(get_post_type($post_id), $pagination_post_types_allowed)){
		// META_PAGINATION_DISPLAY_PAGINATION
		if (!empty($_POST[META_PAGINATION_DISPLAY_PAGINATION])){
			update_post_meta($post_id, META_PAGINATION_DISPLAY_PAGINATION, sanitize_text_field($_POST[META_PAGINATION_DISPLAY_PAGINATION]));
		}else{
			update_post_meta($post_id, META_PAGINATION_DISPLAY_PAGINATION, "off");
		}
	}
}
add_action("customfields_save_post", "pagination_save_post");
endif;