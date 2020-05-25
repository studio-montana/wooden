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

class WKG_Module_Plugin_breadcrumbmeta extends WKG_Module_Plugin {

	function __construct() {
		parent::__construct('breadcrumbmeta', array(
				'uri' => wooden_get_tools_directory_uri().'breadcrumb/gutenberg/plugins/breadcrumbmeta/', // must be explicitly defined to support symbolic link context
				'i18n' => array(
						'domain' => 'wooden',
						'path' => get_template_directory().'lang/',
				)
		));
		add_action('init', array($this, 'init'), 10);
	}

	public function init () {
		register_post_meta('', '_breadcrumb_meta_type', array(
			'show_in_rest' => true,
			'single' => true,
			'type' => 'string',
			'auth_callback' => function() {
				return current_user_can('edit_posts');
			}
		));
		register_post_meta('', '_breadcrumb_meta_items', array(
			'single' => true,
			'type' => 'array',
			'show_in_rest' => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type' => 'object',
						'properties' => array(
							'key' => array(
								'type' => 'number', // order
							),
							'id' => array(
								'type' => 'number', // entity ID
							),
							'type' => array(
								'type' => 'string', // entity type (post|term)
							),
						),
					),
				),
			),
			'auth_callback' => function() {
				return current_user_can('edit_posts');
			}
		));
	}
}
new WKG_Module_Plugin_breadcrumbmeta();
