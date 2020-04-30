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

function tool_breadcrumb_get_the_title($post_id = null){
	if (empty($post_id))
		$post_id = get_the_ID();
		if (function_exists("woodkit_display_title"))
			return woodkit_display_title($post_id, false, false, '', '');
			else
				return get_the_title($post_id);
}

/**
 * Hook WP Menu to add classes
 */
$breadcrumb_menu_management_active = $GLOBALS['woodkit']->tools->get_tool_option('breadcrumb', 'breadcrumb-menu-management-active');
if ($breadcrumb_menu_management_active == 'on'){
	add_filter('nav_menu_css_class', function ( $classes, $item ) {
		if ($item->type === 'post_type'){
			$post_id = $item->object_id;
			if (breadcrumb_is_in_current_breadcrumb($post_id, 'post')){
				if (!in_array('current-menu-ancestor', $classes)){
					$classes[] = 'current-menu-ancestor';
				}
			}
		}else if($item->type === 'taxonomy'){
			$term_id = $item->object_id;
			if (breadcrumb_is_in_current_breadcrumb($term_id, 'term')){
				if (!in_array('current-menu-ancestor', $classes)){
					$classes[] = 'current-menu-ancestor';
				}
			}
		}
		return $classes;
	}, 10, 2 );
}

/**
 * retrieve or display breadcrumb
 * @param array $args {
 *     Optional.
 *     @type string		$seperator		displayed after each breadcrumb element, except the last
 *     @type string		$final			displayed after last breadcrumb item
 *     @type string		$home-item		displayed in place of home's breadcrumb item content
 * }
 * @param string $display : true to display, otherwise return result
 * @return string
 */
function tool_breadcrumb($args = array(), $display = true){

	$id_blog_page = get_option('page_for_posts');
	$id_front_page = get_option('page_on_front');

	$defaults = array(
			'before' => '',
			'after' => '',
			'seperator' => '<li class="separator"><i class="fa fa-caret-right"></i></li>',
			'final' => '<li class="final"></li>',
			'home-item' => __("Home", 'woodkit')
	);
	$args = wp_parse_args($args, $defaults);

	$separator = $args['seperator'];
	$final = $args['final'];

	$res = $args['before'].'<ul class="tool-breadcrumb">';

	// -------------------- home
	$home_url = esc_url(home_url('/'));
	$home_class = " home";
	if (is_front_page()){ // site home page
		$res .= tool_breadcrumb_post_ancestors(get_the_ID(), $separator, array(get_the_ID()));
		$home_class .= " current";
	}
	$res .= '<li class="breadcrumb-item'.$home_class.'"><a href="'.$home_url.'" title="'.__("Home", 'woodkit').'">'.$args['home-item'].'</a></li>';

	if (is_front_page()){ // site home page
		$res .= $final;
	}else if (!is_home()){ // site inner page
		$res .= $separator;
	}else { // site inner page
		$res .= $separator;
	}

	// -------------------- inner
	if (!is_front_page()){
		if (is_home() && !empty($id_front_page)) {
			if (!empty($id_blog_page) && is_numeric($id_blog_page)){
				$res .= tool_breadcrumb_post_ancestors($id_blog_page, $separator, array($id_blog_page));
				$res .= '<li class="breadcrumb-item current"><a href="'.get_permalink($id_blog_page).'" title="'.esc_attr(tool_breadcrumb_get_the_title($id_blog_page)).'">'.tool_breadcrumb_get_the_title($id_blog_page).'</a></li>'.$final;
			}
		}else if(function_exists('is_product_category') && is_product_category()){
			$shop_id = wc_get_page_id('shop');
			if (!empty($shop_id) && is_numeric($shop_id)){
				$res .= tool_breadcrumb_post_ancestors($shop_id, $separator, array($shop_id));
				$res .= '<li class="breadcrumb-item current"><a href="'.get_permalink($shop_id).'" title="'.esc_attr(tool_breadcrumb_get_the_title($shop_id)).'">'.tool_breadcrumb_get_the_title($shop_id).'</a></li>'.$separator;
			}
			$current_term = get_queried_object();
			$res .= tool_breadcrumb_term_ancestors($current_term->term_id, $current_term->taxonomy, $separator);
			$res .= '<li class="breadcrumb-item current"><a href="'.get_the_permalink().'" title="'.esc_attr($current_term->name).'">'.$current_term->name.'</a></li>'.$final;
		}else if (is_single()) {
			if (get_post_type() == 'product' && function_exists('wc_get_page_id')){ // woocommerce product - display shop page
				$shop_id = wc_get_page_id('shop');
				if (!empty($shop_id) && is_numeric($shop_id)){
					$res .= tool_breadcrumb_post_ancestors($shop_id, $separator, array($shop_id));
					$res .= '<li class="breadcrumb-item current"><a href="'.get_permalink($shop_id).'" title="'.esc_attr(tool_breadcrumb_get_the_title($shop_id)).'">'.tool_breadcrumb_get_the_title($shop_id).'</a></li>'.$separator;
				}
			}
			if (get_post_type() == 'post'){ // blog article - display blog page if is different of front page and if front page is set
				if (!empty($id_blog_page) && is_numeric($id_blog_page) && !empty($id_front_page) && $id_blog_page != $id_front_page){
					$res .= tool_breadcrumb_post_ancestors($id_blog_page, $separator, array($id_blog_page));
					$res .= '<li class="breadcrumb-item current"><a href="'.get_permalink($id_blog_page).'" title="'.esc_attr(tool_breadcrumb_get_the_title($id_blog_page)).'">'.tool_breadcrumb_get_the_title($id_blog_page).'</a></li>'.$separator;
				}
			}
			$res .= tool_breadcrumb_post_ancestors(get_the_ID(), $separator, array(get_the_ID()));
			$res .= '<li class="breadcrumb-item current"><a href="'.get_the_permalink().'" title="'.esc_attr(tool_breadcrumb_get_the_title()).'">'.tool_breadcrumb_get_the_title().'</a></li>'.$final;
		}else if (is_archive()){
			if (is_tax() || is_category() || is_tag() ){
				$current_term = get_queried_object();
				if (is_tax()){
					$res .= tool_breadcrumb_term_ancestors($current_term->term_id, $current_term->taxonomy, $separator);
				}else if(is_category()){
					$res .= tool_breadcrumb_term_ancestors($current_term->term_id, 'category', $separator);
				}
				$res .= '<li class="breadcrumb-item current"><a href="'.get_the_permalink().'" title="'.esc_attr($current_term->name).'">'.$current_term->name.'</a></li>'.$final;
			}else{
				// post archive -> get customized post-type breadcrumb
				$post_type = get_post_type();
				if (!empty($post_type)){
					$items = breadcrumb_get_customized_post_type_settings($post_type);
					if ($items !== false && !empty($items) && is_array($items)){
						$count = 0;
						foreach ($items as $item){
							$last_item = $count == (count($items) - 1);
							list($item_gender, $item_type, $item_id) = explode('|', $item);
							if ($item_gender === 'post'){
								$res .= '<li class="breadcrumb-item"><a href="'.get_permalink($item_id).'" title="'.esc_attr(tool_breadcrumb_get_the_title($item_id)).'">'.tool_breadcrumb_get_the_title($item_id).'</a></li>'.($last_item ? $final : $separator);
							}else if ($item_gender === 'term' || $item_gender === 'tax'){
								$term = get_term($item_id, $item_type);
								$res .= '<li class="breadcrumb-item"><a href="'.get_term_link($term, $item_type).'" title="'.esc_attr($term->name).'">'.$term->name.'</a></li>'.($last_item ? $final : $separator);
							}
							$count ++;
						}
					}
				}
			}
		}else if(is_page()) {
			$res .= tool_breadcrumb_post_ancestors(get_the_ID(), $separator, array(get_the_ID()));
			$res .= '<li class="breadcrumb-item current"><a href="'.get_the_permalink().'" title="'.esc_attr(tool_breadcrumb_get_the_title()).'">'.tool_breadcrumb_get_the_title().'</a></li>'.$final;
		}else if(is_404()){
			$res .= '<li class="breadcrumb-item current"><a href="'.get_the_permalink().'" title="'.esc_attr(tool_breadcrumb_get_the_title()).'">404</a></li>'.$final;
		}else if(is_search()){
			$res .= '<li class="breadcrumb-item current"><span>'.__('search','woodkit').'</span></li>'.$final;
		}else if(is_tag()) {
			single_tag_title();
		}else if(is_day()) {
			$res .= '<li class="breadcrumb-item current">'.__('Archive ', 'woodkit')." "; the_time('F jS, Y'); $res .= '</li>'.$final;
		}else if(is_month()) {
			$res .= '<li class="breadcrumb-item current">'.__('Archive ', 'woodkit')." "; the_time('F, Y'); $res .= '</li>'.$final;
		}else if(is_year()) {
			$res .= '<li class="breadcrumb-item current">'.__('Archive ', 'woodkit')." "; the_time('Y'); $res .= '</li>'.$final;
		}else if(is_author()) {
			$res .= '<li class="breadcrumb-item current">'.__('Author', 'woodkit'); $res .= '</li>'.$final;
		}else if(isset($_GET['paged']) && !empty($_GET['paged'])) {
			$res .= '<li class="breadcrumb-item current">'.__("Blog archives", 'woodkit'); $res .= '</li>'.$final;
		}
	}
	$res .= '</ul>'.$args['after'].'<div style="clear: both"></div>';

	if ($display == true){
		echo $res;
	}else{
		return $res;
	}
}

function tool_breadcrumb_post_ancestors($id_post, $separator, $existing_ids_in_breadcrumb = array()){
	$output = '';
	$breadcrumb_type = get_post_meta($id_post, META_BREADCRUMB_TYPE, true);
	if (!empty($breadcrumb_type) && $breadcrumb_type == 'customized'){
		$output .= tool_breadcrumb_customized_post_ancestors($id_post, $separator, $existing_ids_in_breadcrumb);
	}else{
		// classic breadcrumb
		$id_post_parent = wp_get_post_parent_id($id_post);
		if (!empty($id_post_parent)){
			if (is_numeric($id_post_parent) && !in_array($id_post_parent, $existing_ids_in_breadcrumb)){
				$existing_ids_in_breadcrumb[] = $id_post_parent;
				$output .= tool_breadcrumb_post_ancestors($id_post_parent, $separator, $existing_ids_in_breadcrumb);
				$output .= '<li class="breadcrumb-item"><a href="'.get_permalink($id_post_parent).'" title="'.esc_attr(tool_breadcrumb_get_the_title($id_post_parent)).'">'.tool_breadcrumb_get_the_title($id_post_parent).'</a></li>'.$separator;
			}
		}else{
			// post type breadcrumb - get customized post-type breadcrumb ONLY for the last post parent
			$post_type = get_post_type($id_post);
			if (!empty($post_type)){
				$items = breadcrumb_get_customized_post_type_settings($post_type);
				if ($items !== false && !empty($items) && is_array($items)){
					foreach ($items as $item){
						list($item_gender, $item_type, $item_id) = explode('|', $item);
						if ($item_gender === 'post'){
							$output .= '<li class="breadcrumb-item"><a href="'.get_permalink($item_id).'" title="'.esc_attr(tool_breadcrumb_get_the_title($item_id)).'">'.tool_breadcrumb_get_the_title($item_id).'</a></li>'.$separator;
						}else if ($item_gender === 'term' || $item_gender === 'tax'){
							$term = get_term($item_id, $item_type);
							$output .= '<li class="breadcrumb-item"><a href="'.get_term_link($term, $item_type).'" title="'.esc_attr($term->name).'">'.$term->name.'</a></li>'.$separator;
						}
					}
				}
			}
		}
	}
	return $output;
}

function tool_breadcrumb_customized_post_ancestors($id_post, $separator, $existing_ids_in_breadcrumb = array()){
	$output = '';
	$breadcrumb_items = get_post_meta($id_post, META_BREADCRUMB_CUSTOM_ITEMS, true);
	if (!empty($breadcrumb_items)){
		$breadcrumb_items = json_decode($breadcrumb_items, true);
		if (!empty($breadcrumb_items)){
			foreach ($breadcrumb_items as $breadcrumb_item){
				$parts = explode("|", $breadcrumb_item);
				if (count($parts) == 3){
					$type = $parts[0];
					$type_slug = $parts[1];
					$id = $parts[2];
					if (!in_array($id, $existing_ids_in_breadcrumb)){
						if (!empty($type) && !empty($type_slug) && !empty($id) && is_numeric($id)){
							if ($type == 'post'){
								$post = get_post($id);
								if ($post){
									$existing_ids_in_breadcrumb[] = $id;
									$output .= tool_breadcrumb_post_ancestors($id, $separator, $existing_ids_in_breadcrumb);
									$output .= '<li class="breadcrumb-item"><a href="'.get_the_permalink($post).'">'.get_the_title($post).'</a></li>'.$separator;
								}
							}else if ($type == 'tax'){
								$term = get_term($id);
								if ($term){
									$output .= tool_breadcrumb_term_ancestors($id, $type_slug, $separator);
									$output .= '<li class="breadcrumb-item"><a href="'.get_term_link($term).'">'.$term->name.'</a></li>'.$separator;
								}
							}
						}
					}
				}
			}
		}
	}
	return $output;
}

function tool_breadcrumb_term_ancestors($id, $taxonomy, $separator){
	$output = '';
	$ancestors = get_ancestors($id, $taxonomy);
	if ($ancestors){
		sort($ancestors, -1);
		foreach ($ancestors as $ancestor ) {
			$term = get_term($ancestor, $taxonomy);
			$output .= '<li class="breadcrumb-item"><a href="'.get_term_link($term, $taxonomy).'" title="'.esc_attr($term->name).'">'.$term->name.'</a></li>'.$separator;
		}
	}
	return $output;
}

/**
 * Retrieve customized breadcrumb for specified post-type (return false if post type isn't customized)
 * @param unknown $post_type
 * @return Array|boolean
 */
function breadcrumb_get_customized_post_type_settings($post_type){
	$value = $GLOBALS['woodkit']->tools->get_tool_option('breadcrumb', 'breadcrumb-post-types');
	if (!empty($value) && is_array($value) && isset($value[$post_type]) && !empty($value[$post_type]) && is_array($value[$post_type]) && isset($value[$post_type]['type']) && $value[$post_type]['type'] === 'customized'){
		return $value[$post_type]['items'];
	}
	return false;
}

function breadcrumb_is_in_current_breadcrumb($id, $type = 'post'){
	if ($type === 'tax'){
		$type = 'term'; // secure this slug - 'tax' is 'term' too
	}
	$res = false;
	$current_id = null;
	$current_type = null;
	if (is_single() || is_page()){
		$current_id = get_the_ID();
		$current_type = 'post';
	}else if (is_category()) {
		$categories = get_the_category();
		$current_id = $categories[0]->cat_ID;
		$current_type = 'term';
	}else if (is_archive()){
		if (is_tax() || is_category() || is_tag() ){
			$term = get_queried_object();
			$current_id = $term->term_id;
			$current_type = 'term';
		}
	}
	if (!empty($current_id) && !empty($current_type)){
		$b_items = tool_breadcrumb_get_items($current_id, $current_type);
		if (!empty($b_items) && is_array($b_items)){
			foreach ($b_items as $b_item){
				if ($b_item->id == $id && $b_item->type == $type){
					$res = true;
					break;
				}
			}
		}
	}
	return $res;
}

/**
 * Retrieve Breadcrumb Items Object relative to specified $id
 * @param int $id
 * @param string $type : 'post' | 'term' | 'tax'
 * @param array $breadcrumb_items
 * @param array $in_array_ids
 * @return array of BreadcrumbItem
 */
function tool_breadcrumb_get_items($id, $type = 'post', $breadcrumb_items = array(), $in_array_ids = array()) {
	if ($type === 'post' && !empty($id)) {
		$item_breadcrumb_type = get_post_meta ( $id, META_BREADCRUMB_TYPE, true );
		if (! empty ( $item_breadcrumb_type ) && $item_breadcrumb_type == 'customized') {
			$item_breadcrumb_items = get_post_meta($id, META_BREADCRUMB_CUSTOM_ITEMS, true);
			if (!empty($item_breadcrumb_items)){
				$item_breadcrumb_items = json_decode($item_breadcrumb_items, true);
				if (!empty($item_breadcrumb_items)){
					foreach ($item_breadcrumb_items as $item_breadcrumb_item){
						$parts = explode("|", $item_breadcrumb_item);
						if (count($parts) > 2){
							list($item_type, $item_type_slug, $item_id) = $parts;
							if (!in_array($item_breadcrumb_item, $in_array_ids)){
								$in_array_ids[] = $item_breadcrumb_item;
								if (!empty($item_type) && !empty($item_type_slug) && !empty($item_id) && is_numeric($item_id)){
									if ($item_type == 'post'){
										$post = get_post($item_id);
										if ($post){
											$breadcrumb_items = tool_breadcrumb_get_items($item_id, $item_type, $breadcrumb_items, $in_array_ids);
										}
									}else if ($item_type === 'tax' || $item_type === 'term'){
										$term = get_term($item_id);
										if ($term){
											$breadcrumb_items = tool_breadcrumb_get_items($item_id, $item_type, $breadcrumb_items, $in_array_ids);
										}
									}
								}
							}
						}
					}
				}
			}
		}else{
			$id_post_parent = wp_get_post_parent_id($id);
			if (!empty($id_post_parent)){
				$post_parent_breadcrumb_item = 'post|'.get_post_type($id_post_parent).'|'.$id_post_parent;
				if (!in_array($post_parent_breadcrumb_item, $in_array_ids)){
					$in_array_ids[] = $post_parent_breadcrumb_item;
					$post = get_post($id_post_parent);
					if ($post && !empty($id_post_parent)){
						$breadcrumb_items = tool_breadcrumb_get_items($id_post_parent, 'post', $breadcrumb_items, $in_array_ids);
					}
				}
			}else{
				// post type breadcrumb - get customized post-type breadcrumb ONLY for the last post parent
				$post_type = get_post_type($id);
				if (!empty($post_type)){
					$items = breadcrumb_get_customized_post_type_settings($post_type);
					if ($items !== false && !empty($items) && is_array($items)){
						foreach ($items as $item){
							list($item_gender, $item_type, $item_id) = explode('|', $item);
							if ($item_gender === 'post'){
								$breadcrumb_items[] = new BreadcrumbItem(get_post($item_id));
							}else if ($item_gender === 'term' || $item_gender === 'tax'){
								$breadcrumb_items[] = new BreadcrumbItem(get_term($item_id, $item_type));
							}
						}
					}
				}
			}
		}
		$breadcrumb_items[] = new BreadcrumbItem(get_post($id));

	} else if (($type === 'tax' || $type === 'term') && !empty($id)) {
		$term = WP_Term::get_instance($id);
		$ancestors = get_ancestors($id, $term->taxonomy);
		if ($ancestors){
			sort($ancestors, -1);
			foreach ($ancestors as $ancestor ) {
				$term = WP_Term::get_instance($ancestor, $term->taxonomy);
				if ($term){
					$breadcrumb_items[] = new BreadcrumbItem($term);
				}
			}
		}
		$breadcrumb_items[] = new BreadcrumbItem($term);
	}
	return $breadcrumb_items;
}

class BreadcrumbItem {

	public $id;
	public $slug;
	public $title;
	public $type;
	public $term_type;
	public $post_type;
	public $post;
	public $term;
	public $type_id;
	public $permalink;

	function __construct($wp_object){
		if (is_a($wp_object, 'WP_Term')){
			$this->id = $wp_object->term_id;
			$this->slug = $wp_object->slug;
			$this->title = $wp_object->name;
			$this->type = 'term';
			$this->term_type = $wp_object->taxonomy;
			$this->term = $wp_object;
			$this->type_id = 'term_'.$this->id;
			$this->permalink = get_term_link($this->term);
		}else if (is_a($wp_object, 'WP_Post')){
			$this->id = $wp_object->ID;
			$this->slug = $wp_object->post_name;
			$this->title = get_the_title($wp_object->ID);
			$this->type = 'post';
			$this->post_type = $wp_object->post_type;
			$this->post = $wp_object;
			$this->type_id = 'post_'.$this->id;
			$this->permalink = get_the_permalink($this->post);
		}
	}
}