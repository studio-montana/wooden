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
 * Add BACKGROUNDIMAGE fields for the Customizer.
 *
 * @since Woodkit BACKGROUNDIMAGE 1.0
 *
 * @param WP_Customize_Manager $wp_customize_manager Customizer object.
 */
function backgroundimage_customize_register($wp_customize_manager) {
	// ------ background section
	$wp_customize_manager->add_section('backgroundimage_customizer', array(
			'title' => __('Background Image', 'woodkit'),
	) );
	
	// background image
	$wp_customize_manager->add_setting('backgroundimage_image', array('transport'=>'refresh'));
	$wp_customize_manager->add_control(
			new WP_Customize_Image_Control(
					$wp_customize_manager,
					'woodkit_background_image',
					array(
							'label'      => __('Background image', 'woodkit'),
							'section'    => 'backgroundimage_customizer',
							'settings'   => 'backgroundimage_image'
					)
			)
	);
	
	// background color
	$wp_customize_manager->add_setting('backgroundimage_color', array('default' => '#ffffff', 'transport'=>'refresh'));
	$wp_customize_manager->add_control(
			new WP_Customize_Color_Control(
					$wp_customize_manager,
					'woodkit_backgroundimage_color',
					array(
							'label'      => __('Background color', 'woodkit'),
							'section'    => 'backgroundimage_customizer',
							'settings'   => 'backgroundimage_color',
					) )
	);
	
	// background opacity
	$wp_customize_manager->add_setting('backgroundimage_opacity', array('default' => '0', 'transport'=>'refresh'));
	$wp_customize_manager->add_control('backgroundimage_opacity', array(
			'type' => 'number',
			'section' => 'backgroundimage_customizer',
			'label' => __('Opacity', 'woodkit'),
			'description' => __('expressed in %', 'woodkit'),
	) );
}
add_action('customize_register', 'backgroundimage_customize_register');
