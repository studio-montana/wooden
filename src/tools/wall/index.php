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
 * Tool instance
 */
class WK_Tool_Wall extends WK_Tool{

	public function __construct(){
		parent::__construct(array(
				'uri' => get_template_directory_uri().'/src/tools/wall/', // must be explicitly defined to support symbolic link context
				'slug' => 'wall',
				'context' => 'Wooden',
			));
	}

	public function get_name() {
		return __("Wall", 'wooden');
	}

	public function get_description() {
		return __("Create Wall Gutenberg Block and display any contents as a gallery", 'wooden');
	}

	public function launch() {
		require_once ($this->path.'gutenberg/inc/rest/index.php');
		require_once ($this->path.'gutenberg/inc/helpers/index.php');
		require_once ($this->path.'gutenberg/blocks/wall/index.php');
	}

	public function get_config_fields(){
		return array();
	}

	public function get_config_default_values(){
		return array(
				'active' => 'off',
		);
	}
}
add_filter("woodkit-register-tool", function($tools){
	$tools[] = new WK_Tool_Wall();
	return $tools;
});
