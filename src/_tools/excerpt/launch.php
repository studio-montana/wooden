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
 * REQUIREMENTS
 */
require_once (wooden_get_tools_directory().EXCERPT_TOOL_NAME.'/custom-fields/excerpt.php');

/**
 * Enqueue styles for the back end.
*/
function tool_excerpt_woodkit_admin_enqueue_styles_tools($dependencies) {

	$css_excerpt = locate_web_ressource(WOODKIT_PLUGIN_TOOLS_FOLDER.EXCERPT_TOOL_NAME.'/css/tool-excerpt-admin.css');
	if (!empty($css_excerpt))
		wp_enqueue_style('tool-excerpt-admin-css', $css_excerpt, $dependencies, '1.0');
}
add_action('woodkit_admin_enqueue_styles_tools', 'tool_excerpt_woodkit_admin_enqueue_styles_tools');