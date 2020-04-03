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
 * Add LOGIN fields for the Customizer.
 *
 * @since Woodkit LOGIN 1.0
 *
 * @param WP_Customize_Manager $wp_customize_manager Customizer object.
 */
function login_customize_register($wp_customize_manager) {

	// ------ login section
	$wp_customize_manager->add_section('login_customizer', array(
			'title' => __( 'Login page', 'woodkit' ),
	) );

	// login background
	$wp_customize_manager->add_setting('login_backgroundimage', array());
	$wp_customize_manager->add_control(
			new WP_Customize_Image_Control(
					$wp_customize_manager,
					'login_backgroundimage',
					array(
							'label'      => __('Background image', 'woodkit'),
							'section'    => 'login_customizer',
							'settings'   => 'login_backgroundimage'
					)
			)
	);

}
add_action('customize_register', 'login_customize_register');



