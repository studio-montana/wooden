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

if (!function_exists("pagination")):
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
function woodkit_pagination($args = array(), $display = true, $before_links = '', $after_links = '', $text_link_previous = '', $text_link_next = '', $before_previous_link = '', $after_previous_link = '', $before_next_link = '', $after_next_link = ''){
	$current_post_id = get_the_ID();
	$pagination_post_types_allowed = apply_filters("woodkit_pagination_post_types_allowed", get_displayed_post_types());
	if (in_array(get_post_type(), $pagination_post_types_allowed)){
		$meta_display_pagination = get_post_meta($current_post_id, META_PAGINATION_DISPLAY_PAGINATION, true);
		if (empty($meta_display_pagination) || $meta_display_pagination == 'on'){
	
			// content args
			$content_args = array();
			$content_args['before_links'] = $before_links;
			$content_args['after_links'] = $after_links;
			$content_args['text_link_previous'] = $text_link_previous;
			$content_args['text_link_next'] = $text_link_next;
			$content_args['before_previous_link'] = $before_previous_link;
			$content_args['after_previous_link'] = $after_previous_link;
			$content_args['before_next_link'] = $before_next_link;
			$content_args['after_next_link'] = $after_next_link;
			$content_args = apply_filters("woodkit_pagination_content_args", $content_args);
			$before_links = $content_args['before_links'];
			$after_links = $content_args['after_links'];
			$text_link_previous = $content_args['text_link_previous'];
			$text_link_next = $content_args['text_link_next'];
			$before_previous_link = $content_args['before_previous_link'];
			$after_previous_link = $content_args['after_previous_link'];
			$before_next_link = $content_args['before_next_link'];
			$after_next_link = $content_args['after_next_link'];
	
			$res = '';
			$type = get_post_type();
			if (!empty($type)){
	
				$args['post_type'] = $type;
				if (empty($args['orderby']))
					$args['orderby'] = array('menu_order' => 'ASC', 'date' => 'ASC');
				if (empty($args['order']))
					$args['order'] = 'DESC';
				if (empty($args['numberposts']))
					$args['numberposts'] = -1;
				if (empty($args['suppress_filters']))
					$args['suppress_filters'] = FALSE; // keep current language posts (WPML compatibility)
				if (empty($args['post_parent']))
					$args['post_parent'] = wp_get_post_parent_id($current_post_id); // keep hierarchical context... navigate only in brothers
	
				// tax_query
				if (!isset($args['include_tax'])){
					$taxnav_active = $GLOBALS['woodkit']->tools->get_tool_option(PAGINATION_TOOL_NAME, 'taxnav-active');
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
							if ($tpt == $type){
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
					$loop_active = $GLOBALS['woodkit']->tools->get_tool_option(PAGINATION_TOOL_NAME, 'loop-active');
					if ($loop_active == 'on'){
						if ($prev_post_nav == null && $last_post_nav != null && $last_post_nav->ID != $current_post_id){
							$prev_post_nav = $last_post_nav;
						}
						if ($next_post_nav == null && $first_post_nav != null && $first_post_nav->ID != $current_post_id){
							$next_post_nav = $first_post_nav;
						}
					}
	
					if ($prev_post_nav != null){
						$res .= $before_previous_link.'<a class="pagination pagination-previous" href="'.get_the_permalink($prev_post_nav->ID).'">';
						if (!empty($text_link_previous)){
							$res .= $text_link_previous;
						}else{
							$res .= $prev_post_nav->post_title;
						}
						$res .= '</a>'.$after_previous_link;
					}
	
					if ($next_post_nav != null){
						$res .= $before_next_link.'<a class="pagination pagination-next" href="'.get_the_permalink($next_post_nav->ID).'">';
						if (!empty($text_link_next)){
							$res .= $text_link_next;
						}else{
							$res .= $next_post_nav->post_title;
						}
						$res .= '</a>'.$after_next_link;
					}
				}
			}
	
			$res = $before_links.$res.$after_links;
	
			if ($display)
				echo $res;
			else
				return $res;
		}
	}
}
endif;