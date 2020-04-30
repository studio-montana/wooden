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
 * Retieve upcomping events (from date_from to date_to)
 * @param int $date_from : null for now
 * @param int $date_to : null for 100 years after date_from
 * @param string $meta_args
 * @param unknown $args
 * @return array of upcomping events
 */
function get_event_post_types_upcoming($date_from = null, $date_to = null, $meta_args = null, $args=array(), $check_date_begin = true, $check_date_end = true, $operator = 'OR'){
	if (!isset($date_from) || empty($date_from)){
		$date_from = time();
	}
	if (!isset($date_to) || empty($date_to)){
		$date_to = $date_from + ((((365*100)*60)*60)*24);
	}
	$args['orderby'] = 'meta_value_num';
	$args['meta_key'] = 'meta_event_date_begin';
	$args['order'] = 'ASC';

	if ($check_date_begin && $check_date_end){
		$args['meta_query'] = array(
				'relation' => $operator,
				array(
						'key' => 'meta_event_date_begin',
						'value' => array($date_from, $date_to),
						'compare' => 'BETWEEN'
				),
				array(
						'key' => 'meta_event_date_end',
						'value' => array($date_from, $date_to),
						'compare' => 'BETWEEN'
				)
		);
	}else if($check_date_end){
		$args['meta_query'] = array(
				array(
						'key' => 'meta_event_date_end',
						'value' => array($date_from, $date_to),
						'compare' => 'BETWEEN'
				)
		);
	}else{
		$args['meta_query'] = array(
				array(
						'key' => 'meta_event_date_begin',
						'value' => array($date_from, $date_to),
						'compare' => 'BETWEEN'
				)
		);
	}
	return get_event_post_types($meta_args, $args);
}

/**
 * Retrieve pretty event date
 * @param string $event or get current post if null
 */
function get_event_date_pretty($post = null, $display_hours = true, $display_day = true, $display_year = true, $force_minutes = false){
	$date_s = "";
	$meta_date_begin_slug = 'meta_event_date_begin';
	$meta_date_end_slug = 'meta_event_date_end';
	$lang = woodkit_get_current_lang();
	if (is_object($post)){
		$post = $post->ID;
	}
	if (empty($post) || ! is_numeric($post)){
		$post = get_the_ID();
	}
	/**
	 * Date begin
	 */
	$meta_date_begin = get_post_meta($post, $meta_date_begin_slug, true);
	if (!empty($meta_date_begin) && is_numeric($meta_date_begin)){
		$begin_datetime = new DateTime();
		$begin_datetime->setTimestamp($meta_date_begin);
		$meta_day_begin_s = $begin_datetime->format("d");
		$meta_month_begin_s = function_exists('get_textual_month') ? get_textual_month($meta_date_begin) : $begin_datetime->format("m");
		$meta_year_begin_s = $begin_datetime->format("Y");
		if ($lang == 'en'){
			if (intval($begin_datetime->format("i")) > 0 || $force_minutes){
				$meta_date_hour_begin_s = $begin_datetime->format("g:ia");
			}else{
				$meta_date_hour_begin_s = $begin_datetime->format("ga");
			}
		}else{
			if (intval($begin_datetime->format("i")) > 0 || $force_minutes){
				$meta_date_hour_begin_s = $begin_datetime->format("H:i");
			}else{
				$meta_date_hour_begin_s = $begin_datetime->format("H")."h";
			}
		}
		/**
		 * Date end
		 */
		$meta_date_end = get_post_meta($post, $meta_date_end_slug, true);
		if (!empty($meta_date_end) && is_numeric($meta_date_end)){
			$end_datetime = new DateTime();
			$end_datetime->setTimestamp($meta_date_end);
			$meta_day_end_s = $end_datetime->format("d");
			$meta_month_end_s = function_exists('get_textual_month') ? get_textual_month($meta_date_end) : $end_datetime->format("m");
			$meta_year_end_s = $end_datetime->format("Y");
			if ($lang == 'en'){
				if (intval($end_datetime->format("i")) > 0 || $force_minutes){
					$meta_date_hour_end_s = $end_datetime->format("g:ia");
				}else{
					$meta_date_hour_end_s = $end_datetime->format("ga");
				}
			}else{
				if (intval($end_datetime->format("i")) > 0 || $force_minutes){
					$meta_date_hour_end_s = $end_datetime->format("H:i");
				}else{
					$meta_date_hour_end_s = $end_datetime->format("H")."h";
				}
			}
			/**
			 * Same day
			 */
			if ($meta_day_begin_s == $meta_day_end_s && $meta_month_begin_s == $meta_month_end_s && $meta_year_begin_s == $meta_year_end_s){
				if ($display_day){
					$date_s .= $meta_day_begin_s." ";
				}
				$date_s .= $meta_month_begin_s;
				if ($display_year){
					$date_s .= " ".$meta_year_begin_s;
				}
				if ($display_hours){
					if ($meta_date_hour_begin_s != $meta_date_hour_end_s){
						$date_s .= " ".__("from (hour)", 'wooden')." ".$meta_date_hour_begin_s." ".__("to (hour)", 'wooden')." ".$meta_date_hour_end_s;
					}else{
						$date_s .= " ".__("at (hour)", 'wooden')." ".$meta_date_hour_begin_s;
					}
				}
			}else{
				/**
				 * Same month
				 */
				if ($meta_month_begin_s == $meta_month_end_s && $meta_year_begin_s == $meta_year_end_s){
					if ($display_day){
						$date_s .= __("from (date)", 'wooden')." ".$meta_day_begin_s." ".__("to (date)", 'wooden')." ".$meta_day_end_s." ";
					}
					$date_s .= $meta_month_begin_s;
					if ($display_year){
						$date_s .= " ".$meta_year_begin_s;
					}
					if ($display_hours){
						if ($meta_date_hour_begin_s != $meta_date_hour_end_s){
							$date_s .= " ".__("from (hour)", 'wooden')." ".$meta_date_hour_begin_s." ".__("to (hour)", 'wooden')." ".$meta_date_hour_end_s;
						}else{
							$date_s .= " ".__("at (hour)", 'wooden')." ".$meta_date_hour_begin_s;
						}
					}
				}else{
					/**
					 * Same year
					 */
					if ($meta_year_begin_s == $meta_year_end_s){
						if( $display_day){
							$date_s .= __("from (date)", 'wooden');
							$date_s .= " ".$meta_day_begin_s;
						}else{
							$date_s .= __("from (month)", 'wooden');
						}
						$date_s .= " ".$meta_month_begin_s;
						if ($display_hours){
							$date_s .=" ".__("at (hour)", 'wooden')." ".$meta_date_hour_begin_s;
						}
						if( $display_day){
							$date_s .= " ".__("to (date)", 'wooden');
							$date_s .= " ".$meta_day_end_s;
						}else{
							$date_s .= " ".__("to (month)", 'wooden');
						}
						$date_s .= " ".$meta_month_end_s;
						if ($display_year){
							$date_s .= " ".$meta_year_begin_s;
						}
						if ($display_hours){
							$date_s .= " ".__("at (hour)", 'wooden')." ".$meta_date_hour_end_s;
						}
					}else{
						/**
						 * Different day / month / year
						 */
						if( $display_day){
							$date_s .= __("from (date)", 'wooden');
							$date_s .= " ".$meta_day_begin_s;
						}else{
							$date_s .= __("from (month)", 'wooden');
						}
						$date_s .= " ".$meta_month_begin_s;
						if ($display_year){
							$date_s .= " ".$meta_year_begin_s;
						}
						if ($display_hours){
							$date_s .=" ".__("at (hour)", 'wooden')." ".$meta_date_hour_begin_s;
						}
						if( $display_day){
							$date_s .= " ".__("to (date)", 'wooden');
							$date_s .= " ".$meta_day_end_s;
						}else{
							$date_s .= " ".__("to (month)", 'wooden');
						}
						$date_s .= " ".$meta_month_end_s;
						if ($display_year){
							$date_s .= " ".$meta_year_end_s;
						}
						if ($display_hours){
							$date_s .= " ".__("at (hour)", 'wooden')." ".$meta_date_hour_end_s;
						}
					}
				}
			}
		}else{
			/**
			 * One day
			 */
			if ($display_day){
				$date_s .= $meta_day_begin_s." ";
			}
			$date_s .= $meta_month_begin_s." ".$meta_year_begin_s;
			if ($display_year){
				$date_s .= " ".$meta_year_begin_s;
			}
			if ($display_hours){
				$date_s .= " ".__("at (hour)", 'wooden')." ".$meta_date_hour_begin_s;
			}
		}
	}
	return $date_s;
}