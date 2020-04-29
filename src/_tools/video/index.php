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
define('VIDEO_TOOL_NAME', 'video');

/**
 * Tool instance
 */
class WoodenToolVideo extends WoodkitTool{
	
	public function __construct(){
		parent::__construct(
				'video', 								// slug
				__("Video", 'woodkit'),				// name
				__("Add featured video to your posts", 'woodkit'),	// description
				true,										// has config page
				false,										// add config page in woodkit submenu
				WOODKIT_URL_DOCUMENTATION.'/video'		// documentation url
			);
	}
	
	public function get_config_fields(){
		return array(
				'auto-insert',
				'default-width',
				'default-height',
		);
	}
	
	public function get_config_default_values(){
		return array(
				'active' => 'off',
				'auto-insert' => 'on',
				'default-width' => '100%',
				'default-height' => '320px',
		);
	}
	
	public function display_config_fields(){
		?>
		<div class="section">
			<h2 class="section-title">
				<?php _e("General", 'woodkit'); ?>
			</h2>
			<div class="section-content">
				<div class="field checkbox">
					<div class="field-content">
						<?php
						$value = $this->get_option('auto-insert');
						$checked = '';
						if ($value == 'on'){
							$checked = ' checked="checked"';
						}
						?>
						<input type="checkbox" id="auto-insert" name="auto-insert" <?php echo $checked; ?> />
						<label for="auto-insert"><?php _e("Auto insert", 'woodkit'); ?></label>
					</div>
					<p class="description"><?php _e("replace featured image if exists, otherwise, you can insert this code directly in your theme templates, please see 'integration' section bellow.", 'woodkit'); ?></p>
				</div>
				<div class="field checkbox">
					<div class="field-content">
						<?php
						$value = $this->get_option('default-width');
						?>
						<label for="default-width"><?php _e("Default width", 'woodkit'); ?></label>
						<input type="text" id="default-width" name="default-width" value="<?php echo esc_attr($value); ?>" placeholder="auto, 100%, 320px, ..." />
					</div>
				</div>
				<div class="field checkbox">
					<div class="field-content">
						<?php
						$value = $this->get_option('default-height');
						?>
						<label for="default-height"><?php _e("Default height", 'woodkit'); ?></label>
						<input type="text" id="default-height" name="default-height" value="<?php echo esc_attr($value); ?>" placeholder="auto, 100%, 320px, ..." />
					</div>
				</div>
			</div>
		</div>
		<div class="section">
			<h2 class="section-title">
				<?php _e("Integration", 'woodkit'); ?>
			</h2>
			<div class="section-content">
				<div class="section-info">
					<?php _e('insert this code in your theme templates :', 'woodkit'); ?><br /><code style="font-size: 0.7rem;">&lt;?php video_get_featured_video(); ?&gt;</code>
				</div>
			</div>
		</div>
		<?php
	}
}
add_filter("woodkit-register-tool", function($tools){
	$tools[] = new WoodenToolVideo();
	return $tools;
});
