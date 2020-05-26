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

/**
 * Wooden navigation for old Woodkit (before v.2.0.0) support dependencies
 * This navigation tool was migrated from Woodkit 1.x.x to Wooden (since Woodkit 2.0.0)
 * 
 * @deprecated since Wooden exists
 * 
 * @param array $args
 * @param string $display
 * @param string $before_links
 * @param string $after_links
 * @param string $text_link_previous
 * @param string $text_link_next
 * @param string $before_previous_link
 * @param string $after_previous_link
 * @param string $before_next_link
 * @param string $after_next_link
 * @return string
 */
function woodkit_pagination($args = array(), $display = true, $before_links = '', $after_links = '', $text_link_previous = '', $text_link_next = '', $before_previous_link = '', $after_previous_link = '', $before_next_link = '', $after_next_link = '') {
	$navigation = wooden_posts_navigation($args, array(
			'before_links' => $before_links,
			'after_links' => $after_links,
			'text_link_previous' => $text_link_previous,
			'text_link_next' => $text_link_next,
			'before_previous_link' => $before_previous_link,
			'after_previous_link' => $after_previous_link,
			'before_next_link' => $before_next_link,
			'after_next_link' => $after_next_link
	));
	if (!$display) {
		return $navigation;
	}
	echo $navigation;
}

function the_wooden_posts_navigation($args = array(), $display_args = array()){
	echo wooden_posts_navigation($args, $display_args);
}

/**
 * construct HTML link of previous and next posts from current post
* @param array $args : wp_query arguments
* @param string $display : echo result, return result otherwise
* @param string $before_links : displayed before tags
* @param string $after_links : displayed after tags
* @param string $text_link_previous : replace post title in previous link
* @param string $text_link_next : replace post title in next link
* @param string $before_previous_link : displayed before previous a tag
* @param string $after_previous_link : displayed after previous a tag
* @param string $before_next_link : displayed before next a tag
* @param string $after_next_link : displayed after next a tag
* @return string : HTML link
*/
function wooden_posts_navigation($args = array(), $display_args = array()){
	$res = '';
	$current_post_id = get_the_ID();
	$current_post_type = get_post_type();
	$navigation_post_types_allowed = apply_filters("wooden_posts_navigation_post_types_allowed", get_displayed_post_types());
	if (in_array(get_post_type(), $navigation_post_types_allowed)){

		// display args
		$display_args = wp_parse_args($display_args, apply_filters("wooden_posts_navigation_default_display_args", array(
				'before_links' => '',
				'after_links' => '',
				'text_link_previous' => '',
				'text_link_next' => '',
				'before_previous_link' => '',
				'after_previous_link' => '',
				'before_next_link' => '',
				'after_next_link' => ''
		)));

		if (!empty($current_post_type)){

			$args['post_type'] = $current_post_type; // force current post-type

			$args = wp_parse_args($args, array(
					'orderby' => array('menu_order' => 'ASC', 'date' => 'ASC'),
					'order' => 'DESC',
					'numberposts' => -1,
					'suppress_filters' => FALSE, // keep current language posts (WPML compatibility)
					'post_parent' => wp_get_post_parent_id($current_post_id), // keep hierarchical context... navigate only in brothers
			));

			// tax_query
			if (!isset($args['include_tax'])){
				$taxnav_active = $GLOBALS['woodkit']->tools->get_tool_option(POSTSNAVIGATION_TOOL_NAME, 'taxnav-active');
				if ($taxnav_active == 'on'){
					$args['include_tax'] = true;
				}else{
					$args['include_tax'] = false;
				}
			}
			if ($args['include_tax'] == true){
				$tax_query_terms = array();
				$taxes = get_taxonomies(array(), false);
				foreach ($taxes as $tax){
					$tax_post_type = $tax->object_type;
					foreach ($tax_post_type as $tpt){
						if ($tpt == $current_post_type){
							$post_terms = get_the_terms($current_post_id, $tax->name);
							if (!empty($post_terms)){
								$tax_query_terms_values = array();
								foreach ($post_terms as $post_term){
									$tax_query_terms_values[] = $post_term->slug;
								}
								$tax_query_terms[] = array(
										'taxonomy' => $tax->name,
										'field'    => 'slug',
										'terms'    => $tax_query_terms_values
								);
							}
						}
					}
				}
				if (!empty($tax_query_terms)){
					$tax_query = array();
					$tax_query['relation'] = 'AND';
					foreach ($tax_query_terms as $tax_query_term){
						$tax_query[] = $tax_query_term;
					}
					$args['tax_query'] = $tax_query;
				}
			}

			$nav_posts = get_posts($args);

			if (!empty($nav_posts)){
				// current post is in result ?
				$current_in_result = false;
				foreach ($nav_posts as $nav_post){
					if ($current_post_id == $nav_post->ID){
						$current_in_result = true;
						break;
					}
				}

				$prev_post_nav = null;
				$next_post_nav = null;
				$first_post_nav = null;
				$last_post_nav = null;
				if (!$current_in_result){
					// current post isn't in result -> just navigate to next post and last if loop
					if (count($nav_posts) > 0){
						$next_post_nav = $nav_posts[0]; // first post
						$last_post_nav = $nav_posts[count($nav_posts)-1]; // last post
					}
				}else{
					// current post is in result -> standard navigation
					$stop = false;
					foreach ($nav_posts as $nav_post){
						if ($first_post_nav == null)
							$first_post_nav = $nav_post;
						if (!$stop){
							if ($current_post_id == $nav_post->ID)
								$stop = true;
							else
								$prev_post_nav = $nav_post;
						}else{
							if ($next_post_nav == null)
								$next_post_nav = $nav_post;
						}
						$last_post_nav = $nav_post;
					}
				}
				// loop
				$loop_active = $GLOBALS['woodkit']->tools->get_tool_option('postsnavigation', 'loop-active');
				if ($loop_active == 'on'){
					if ($prev_post_nav == null && $last_post_nav != null && $last_post_nav->ID != $current_post_id){
						$prev_post_nav = $last_post_nav;
					}
					if ($next_post_nav == null && $first_post_nav != null && $first_post_nav->ID != $current_post_id){
						$next_post_nav = $first_post_nav;
					}
				}

				if ($prev_post_nav != null){
					$res .= $display_args['before_previous_link'].'<a class="navigation navigation-previous" href="'.get_the_permalink($prev_post_nav->ID).'">';
					if (!empty($display_args['text_link_previous'])){
						$res .= $display_args['text_link_previous'];
					}else{
						$res .= $prev_post_nav->post_title;
					}
					$res .= '</a>'.$display_args['after_previous_link'];
				}

				if ($next_post_nav != null){
					$res .= $display_args['before_next_link'].'<a class="navigation navigation-next" href="'.get_the_permalink($next_post_nav->ID).'">';
					if (!empty($display_args['text_link_next'])){
						$res .= $display_args['text_link_next'];
					}else{
						$res .= $next_post_nav->post_title;
					}
					$res .= '</a>'.$display_args['after_next_link'];
				}
			}

			$res = $display_args['before_links'].$res.$display_args['after_links'];
		}
	}

	return $res;
}
