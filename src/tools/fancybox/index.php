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
 * Constants
 */
define('FANCYBOX_TOOL_NAME', 'fancybox');

/**
 * Tool instance
 */
class WK_Tool_Fancybox extends WK_Tool{
	
	public function __construct(){
		parent::__construct(array(
				'uri' => get_template_directory_uri().'/src/tools/fancybox/', // must be explicitly defined to support symbolic link context
				'slug' => 'fancybox',
				'context' => 'Wooden',
		));
	}
	
	public function get_name() { 
		return __("Fancybox", 'wooden');
	}
	
	public function get_description() { 
		return __("Enable Fancybox support on your website frontend", 'wooden');
	}
	
	public function launch() {
		add_action('wp_enqueue_scripts', function () {
			wp_enqueue_script('wooden-fancybox', $this->uri . 'js/fancybox-3.5.2/dist/jquery.fancybox.min.js', array('jquery'), '3.5.2', true);
			wp_enqueue_style('wooden-fancybox', $this->uri . 'js/fancybox-3.5.2/dist/jquery.fancybox.min.css', array(), '3.5.2');
			wp_enqueue_script('wooden-fancybox-front', $this->uri . 'js/front.js', array('wooden-fancybox'), WOODEN_WEBCACHE_VERSION, true);
		});
	}
	
	public function get_config_fields(){
		return array();
	}
	
	public function get_config_default_values(){
		return array(
				'active' => 'on'
		);
	}
	
}
add_filter("woodkit-register-tool", function($tools){
	$tools[] = new WK_Tool_Fancybox();
	return $tools;
});
