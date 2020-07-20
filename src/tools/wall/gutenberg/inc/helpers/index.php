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
 * Retrieve wall items
 * @param  array  $args [description]
 * @return array       [description]
 */
function wkg_wall_get_items($args = array(), $thumb_size = 'thumbnail') {
	// parse/securise 'terms' arg
	if (isset($args['terms'])) {
		$terms = is_array($args['terms']) ? $args['terms'] : explode(',', $args['terms']);
		unset($args['terms']);
		if (!empty($terms)) {
			$tax_query_items = array();
			foreach ($terms as $term_id) {
				if (is_numeric($term_id)){
					$term = get_term($term_id);
					if ($term && !is_wp_error($term)) {
						$taxonomy = get_taxonomy($term->taxonomy);
						if ($taxonomy && isset($args['post_type']) && in_array($args['post_type'], $taxonomy->object_type)) {
							if (isset($tax_query_items[$term->taxonomy])) {
								$tax_query_items[$term->taxonomy][] = $term->term_id;
							} else {
								$tax_query_items[$term->taxonomy] = array($term->term_id);
							}
						}
					}
				}
			}
			if (!empty($tax_query_items)) {
				$args['tax_query'] = array();
				foreach ($tax_query_items as $taxonomy => $ids) {
					$args['tax_query'][] = array(
						'taxonomy' => $taxonomy,
						'field'    => 'id',
						'terms'    => $ids,
					);
				}
			}
		}
	}
	// parse/securise 'post_parent' arg
	if (isset($args['post_parent'])) {
		$post_parent = $args['post_parent'];
		unset($args['post_parent']);
		if (is_numeric($post_parent)) {
			$post_parent = get_post($post_parent);
			if ($post_parent && isset($args['post_type']) && get_post_type($post_parent) == $args['post_type']) {
				$args['post_parent'] = $post_parent->ID;
			}
		}
	}
	$posts = get_posts($args);
	$items = array();
	if (!empty($posts)) {
		global $post;
		foreach ($posts as $post) {
			setup_postdata($post);
			$post_data = array(
				'post_type' => get_post_type($post),
				'id' => $post->ID,
				'title' => get_the_title($post),
				'excerpt' => get_the_excerpt($post),
			);
			if (has_post_thumbnail($post)) {
				$thumb_id = get_post_thumbnail_id($post);
				$thumb = wp_get_attachment_image_src($thumb_id, $thumb_size);
				if ($thumb) {
					list($thumb_src, $thumb_width, $thumb_height) = $thumb;
					$post_data['thumb'] = array(
						'id' => $thumb_id,
						'source_url' => $thumb_src,
						'width' => $thumb_width,
						'height' => $thumb_height
					);
				}
			}
			$items[] = $post_data;
		}
		wp_reset_postdata();
	}
	return $items;
}

/**
 * Retrieve wall items parents options
 * @param  array  $args [description]
 * @return array       [description]
 */
function wkg_wall_get_items_parents_options ($args = array()) {
	$options = array();
	if (isset($args['post_type']) && is_post_type_hierarchical($args['post_type'])) {
		$post_options = wkg_get_posts_options($args['post_type']);
		if (!empty($post_options)) {
			$options = array(
				array('value' => -1, 'label' => 'Tous'),
				array('value' => 0, 'label' => 'Premier niveau')
			);
			$options = array_merge($options, $post_options);
		}
}
	return $options;
}

/**
 * Retrieve wall items terms options
 * @param  array  $args [description]
 * @return array       [description]
 */
function wkg_wall_get_items_terms_options ($args = array()) {
	$options = array();
	$term_options = wkg_get_terms_options(array('post_type' => $args['post_type']));
	if (!empty($term_options)) {
		$options = array(
			array('value' => -1, 'label' => 'Tous'),
		);
		$options = array_merge($options, $term_options);
	}
	return $options;
}
