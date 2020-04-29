<?php
/**
 * @package Wooden
* @author Sébastien Chandonay www.seb-c.com / Cyril Tissot www.cyriltissot.com
* License: GPL2
* Text Domain: wooden
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

function tool_event_add_post_types(){
	// post type
	$labels = array(
			'name'               => __('Events', 'wooden'),
			'singular_name'      => __('Event', 'wooden'),
			'add_new_item'       => __('Add Event', 'wooden'),
			'edit_item'          => __('Edit Event', 'wooden'),
			'new_item'           => __('New Event', 'wooden'),
			'all_items'          => __('Events', 'wooden'),
			'view_item'          => __('Look Event', 'wooden'),
			'search_items'       => __('Search Events', 'wooden'),
			'not_found'          => __('No Event found', 'wooden'),
			'not_found_in_trash' => __('No Event found in trash', 'wooden')
	);
	$args = array(
			'labels'             	=> $labels,
			'exclude_from_search' 	=> false,
			'public' 				=> true,
			'show_ui' 				=> true,
			'show_in_menu' 			=> true,
			"show_in_rest" 			=> true,
			'menu_icon' 			=> 'dashicons-calendar-alt',
			'capability_type' 		=> 'post',
			'hierarchical' 			=> true,
			'supports' 				=> array('title', 'editor', 'thumbnail', 'custom-fields'),
			'rewrite'           	=> array('slug' => _x('events', 'URL slug', 'wooden'))
	);
	register_post_type('event', $args);

	// taxonomy
	$labels = array(
			"name"              => __("Event Types", 'wooden'),
			"singular_name"     => __("Event Type", 'wooden'),
			"search_items"      => __("Search Event Type", 'wooden'),
			"all_items"         => __("All Event Types", 'wooden'),
			"parent_item"       => __("Event Type's parent", 'wooden'),
			"parent_item_colon" => __("Event Type's parent", 'wooden'),
			"edit_item"         => __("Edit Event Type", 'wooden'),
			"update_item"       => __("Update Event Type", 'wooden'),
			"add_new_item"      => __("Add Event Type", 'wooden'),
			"new_item_name"     => __("Name", 'wooden'),
			"menu_name"         => __("Event Type", 'wooden')
	);
	$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			"show_in_rest" 		=> true,
			'rewrite'           => array('slug' => _x('evenement-type', 'URL slug', 'wooden'))
	);
	register_taxonomy('eventtype', array( 'event' ), $args);
	
	do_action("tool_event_add_post_type_after", "event");
}
add_action('init', 'tool_event_add_post_types');

/**
 * wooden listing columns
*/
function tool_event_define_event_columns($columns){
	$columns["event-date-begin"] = __("Begin", 'wooden');
	$columns["event-date-end"] = __("End", 'wooden');
	return $columns;
}
add_filter('manage_edit-event_columns', 'tool_event_define_event_columns');

/**
 * wooden listing columns content
*/
function tool_event_build_event_columns($column, $post_id){
	switch($column){
		case "event-date-begin":
			$meta_date_begin = get_post_meta($post_id, "_event_meta_date_begin", true);
			$meta_date_begin_s = "";
			if (!empty($meta_date_begin) && is_numeric($meta_date_begin)){
				$meta_date_begin = new DateTime("@".$meta_date_begin);
				if ($meta_date_begin)
					echo $meta_date_begin->format("d")."/".$meta_date_begin->format("m")."/".$meta_date_begin->format("Y")." ".$meta_date_begin->format("H").":".$meta_date_begin->format("i");
				else
					echo '-';
			}else{
				echo '-';
			}
			break;
		case "event-date-end":
			$meta_date_end = get_post_meta($post_id, "_event_meta_date_end", true);
			$meta_date_end_s = "";
			if (!empty($meta_date_end) && is_numeric($meta_date_end)){
				$meta_date_end = new DateTime("@".$meta_date_end);
				if ($meta_date_end)
					echo $meta_date_end->format("d")."/".$meta_date_end->format("m")."/".$meta_date_end->format("Y")." ".$meta_date_end->format("H").":".$meta_date_end->format("i");
				else
					echo '-';
			}else{
				echo '-';
			}
			break;
	}
}
add_action('manage_event_posts_custom_column' , 'tool_event_build_event_columns', 10, 2);
