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
define('LOGIN_TOOL_NAME', 'login');

/**
 * Tool instance
 */
class WK_Tool_Login extends WK_Tool{
	
	public function __construct(){
		parent::__construct(array(
				'uri' => get_template_directory_uri().'/src/tools/login/', // must be explicitly defined to support symbolic link context
				'slug' => 'login',
				'name' => __("Login", 'wooden'),
				'description' => __("Improve your experience on WP login page", 'wooden'),
				'customizer' => add_query_arg(array('autofocus[section]' => 'login_customizer'), admin_url('customize.php')),
				'context' => 'Wooden',
		));
	}
	
	public function launch() {
		require_once ($this->path.'inc/utils.php');
		require_once ($this->path.'inc/customizer.php');
	}
	
	public function get_config_default_values(){
		return array(
				'active' => 'on'
		);
	}
	
}
add_filter("woodkit-register-tool", function($tools){
	$tools[] = new WK_Tool_Login();
	return $tools;
});
