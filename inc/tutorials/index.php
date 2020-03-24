<?php
/**
 Theme Name: BETTI
Author: S. Chandonay - C. Tissot
Author URI: https://www.seb-c.com
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
defined ( 'ABSPATH' ) or die ( "Go Away!" );

/**
 * Plugin tutorials page
 */
if (is_admin ()) {
	if (! function_exists ( "wooden_tutorials_add_menu_config" )) {
		function wooden_tutorials_add_menu_config() {
			$page_name = __ ( "Tutoriels", 'wooden' );
			$menu_name = __ ( "Tutoriels", 'wooden' );
			$callback = "wooden_page_tutorials_callback_function";
			if (function_exists ( $callback )) {
				add_menu_page ( $page_name, $menu_name, "read", "wooden-tutorials-page", $callback, 'dashicons-welcome-learn-more');
			}
		}
		add_action ( 'admin_menu', 'wooden_tutorials_add_menu_config' );
	}
	
	if (! function_exists ( "wooden_page_tutorials_callback_function" )) {
		function wooden_page_tutorials_callback_function() {
			require_once (get_template_directory () . "/inc/tutorials/render.php");
			new Wooden_Tutorials();
		}
	}
}