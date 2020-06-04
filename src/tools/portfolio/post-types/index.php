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

function tool_portfolio_add_post_types(){
	// woodkit post type
	$labels = array(
			'name'               => __('Portfolios', 'woodkit'),
			'singular_name'      => __('Portfolio', 'woodkit'),
			'add_new_item'       => __('Add Portfolio', 'woodkit'),
			'edit_item'          => __('Edit Portfolio', 'woodkit'),
			'new_item'           => __('New Portfolio', 'woodkit'),
			'all_items'          => __('Portfolios', 'woodkit'),
			'view_item'          => __('View Portfolio', 'woodkit'),
			'search_items'       => __('Search Portfolios', 'woodkit'),
			'not_found'          => __('No Portfolio found', 'woodkit'),
			'not_found_in_trash' => __('No Portfolio found in trash', 'woodkit')
	);
	$args = array(
			'labels'             => $labels,
			'exclude_from_search' => false,
			'public' => true,
			'show_ui' => true,
			'menu_icon' => 'dashicons-format-gallery',
			'show_in_menu' => true,
			'capability_type' => 'post',
			'hierarchical' => true,
			'supports' => array('title', 'editor', 'thumbnail', 'page-attributes', 'custom-fields', 'excerpt'),
			'rewrite'           => array('slug' => _x('portfolio', 'URL slug', 'woodkit')),
			'show_in_rest' => true,
			'rest_base' => '',
	);
	register_post_type('portfolio', $args);

	// woodkit taxonomy
	$labels = array(
			'name'              => __('Portfolio Types', 'woodkit'),
			'singular_name'     => __('Portfolio Type', 'woodkit'),
			'search_items'      => __('Search Portfolio Type', 'woodkit'),
			'all_items'         => __('All Portfolio Types', 'woodkit'),
			'parent_item'       => __("Portfolio Type's parent", 'woodkit'),
			'parent_item_colon' => __("Portfolio Type's parent", 'woodkit'),
			'edit_item'         => __('Edit Portfolio Type', 'woodkit'),
			'update_item'       => __('Update Portfolio Type', 'woodkit'),
			'add_new_item'      => __('Add Portfolio Type', 'woodkit'),
			'new_item_name'     => __('Name', 'woodkit'),
			'menu_name'         => __('Portfolio Type', 'woodkit')
	);
	$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug' => _x('portfolio-type', 'URL slug', 'woodkit')),
			"meta_box_cb" 		=> true,
			"show_in_rest" 		=> true,
	);
	register_taxonomy('portfoliotype', array( 'portfolio' ), $args);
}
add_action('init', 'tool_portfolio_add_post_types');

/**
 * woodkit listing columns
*/
function tool_portfolio_define_portfolio_columns($columns){
	$columns["portfolio-thumb"] = __("Thumb", 'woodkit');
	return $columns;
}
add_filter('manage_edit-portfolio_columns', 'tool_portfolio_define_portfolio_columns' );

/**
 * woodkit listing columns content
*/
function tool_portfolio_build_portfolio_columns($column, $post_id){
	switch($column){
		case "portfolio-thumb":
			if(has_post_thumbnail($post_id)){
				echo get_the_post_thumbnail($post_id, array("80", "120"));
			}
			break;
	}
}
add_action( 'manage_portfolio_posts_woodkit_column' , 'tool_portfolio_build_portfolio_columns', 10, 2 );
