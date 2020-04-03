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
 * register JS
 */
function video_ajax_admin_enqueue_scripts(){
	$video_ajax_js_file = locate_web_ressource(WOODKIT_PLUGIN_TOOLS_FOLDER.VIDEO_TOOL_NAME.'/ajax/video-ajax.js');
	if (!empty($video_ajax_js_file)){
		wp_enqueue_script('video-ajax', $video_ajax_js_file, array('jquery'), "1.0");
		wp_localize_script('video-ajax', 'VideoAjax', array(
		'ajaxUrl' => admin_url('admin-ajax.php'),
		'ajaxNonce' => wp_create_nonce('video-ajax-nonce'),
		)
		);
	}
}
add_action('admin_enqueue_scripts', 'video_ajax_admin_enqueue_scripts');

/**
 * retrieve configuration's form for video présentation
*/
function video_ajax_get_video_preview() {
	if (!check_ajax_referer('video-ajax-nonce', 'ajaxNonce', false)){
		die ('Busted!');
	}

	$response = array('what'=>'video_ajax_get_video_preview',
			'action'=>'video_ajax_get_video_preview',
			'id'=>'1'
	);

	$meta_video_url = isset($_POST['video_url']) ? $_POST['video_url'] : "";
	$meta_video_width = isset($_POST['video_width']) ? $_POST['video_width'] : "";
	$meta_video_height = isset($_POST['video_height']) ? $_POST['video_height'] : "";
	
	$results = get_video_embed_code($meta_video_url, $meta_video_width, $meta_video_height);

	$response['data'] = $results;
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send();
	exit();
}
add_action('wp_ajax_get_video_preview', 'video_ajax_get_video_preview');
add_action('wp_ajax_nopriv_get_video_preview', 'video_ajax_get_video_preview');