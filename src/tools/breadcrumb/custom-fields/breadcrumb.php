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
define('META_BREADCRUMB_TYPE', 'meta_breadcrumb_type');
define('META_BREADCRUMB_CUSTOM_ITEMS', 'meta_breadcrumb_custom_items');

if (!function_exists("breadcrumb_add_inner_meta_boxes")):
/**
 * This action is called by Woodkit when metabox is display on post-type
* @param unknown $post
*/
function breadcrumb_add_inner_meta_boxes($post){
	$available_posttypes = get_displayed_post_types();
	$available_posttypes = apply_filters("tool_breadcrumb_available_posttypes", $available_posttypes);
	if (in_array(get_post_type($post), $available_posttypes)){
		include(wooden_get_tools_directory().BREADCRUMB_TOOL_NAME.'/custom-fields/templates/breadcrumb.php');
	}
}
add_action("customfields_add_inner_meta_boxes", "breadcrumb_add_inner_meta_boxes", 10);
endif;

if (!function_exists("breadcrumb_save_post")):
/**
 * This action is called by Woodkit when post-type is saved
* @param int $post_id
*/
function breadcrumb_save_post($post_id){
	$available_posttypes = get_displayed_post_types();
	$available_posttypes = apply_filters("tool_breadcrumb_available_posttypes", $available_posttypes);
	if (in_array($_POST['post_type'], $available_posttypes)){
		// META_BREADCRUMB_TYPE
		if (!empty($_POST[META_BREADCRUMB_TYPE])){
			update_post_meta($post_id, META_BREADCRUMB_TYPE, sanitize_text_field($_POST[META_BREADCRUMB_TYPE]));
		}else{
			delete_post_meta($post_id, META_BREADCRUMB_TYPE);
		}
		// META_BREADCRUMB_CUSTOM_ITEMS
		if (!empty($_POST[META_BREADCRUMB_TYPE]) && $_POST[META_BREADCRUMB_TYPE] == "customized"){
			$meta_breadcrumb_ids = array(); // transform to json representation
			$meta_breadcrumb_labels = array(); // transform to json representation
			foreach ($_POST as $k => $v){
				if (startsWith($k, "breadcrumb-item-id-")){
					$breadcrumb_item_id = $v;
					if (isset($_POST['breadcrumbs-select-'.$breadcrumb_item_id]) && !empty($_POST['breadcrumbs-select-'.$breadcrumb_item_id])){
						$breadcrumb_id = $_POST['breadcrumbs-select-'.$breadcrumb_item_id];
						if (!empty($breadcrumb_id) && $breadcrumb_id != "0"){
							if (!in_array($breadcrumb_id, $meta_breadcrumb_ids)){
								$meta_breadcrumb_ids[] = $breadcrumb_id;
								$meta_breadcrumb_labels[] = get_the_title($breadcrumb_id);
							}
						}
					}
				}
			}
			if (!empty($meta_breadcrumb_ids)){
				update_post_meta($post_id, META_BREADCRUMB_CUSTOM_ITEMS, json_encode($meta_breadcrumb_ids));
			}else{
				delete_post_meta($post_id, META_BREADCRUMB_CUSTOM_ITEMS);
			}
		}else{
			delete_post_meta($post_id, META_BREADCRUMB_CUSTOM_ITEMS);
		}
	}
}
add_action("customfields_save_post", "breadcrumb_save_post");
endif;