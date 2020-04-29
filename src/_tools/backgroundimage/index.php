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
 * Tool instance
 */
class WoodenToolBackgroundImage extends WoodkitTool{
	
	public function __construct(){
		parent::__construct(array(
				'slug' => 'backgroundimage',
				'name' => __("Background Image", 'woodkit'),
				'description' => __("Add background image to your site and posts", 'woodkit'),
				'has_config' => true,
		));
	}
	
	public function get_config_fields(){
		return array(
				'auto-insert'
		);
	}
	
	public function get_config_default_values(){
		return array(
				'active' => 'off',
				'auto-insert' => 'on'
		);
	}
	
	public function display_config_fields(){
		?>
		<div class="section">
			<h2 class="section-title">
				<?php _e("Auto insert", 'woodkit'); ?>
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
						<input type="checkbox" name="auto-insert" <?php echo $checked; ?> />
						<p class="field-description"><?php _e('otherwise, you can insert this code directly in your theme templates :', 'woodkit'); ?><br /><code style="font-size: 0.7rem;">&lt;?php woodkit_backgroundimage(); ?&gt;</code></p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	
}
add_filter("woodkit-register-tool", function($tools){
	$tools[] = new WoodenToolBackgroundImage();
	return $tools;
});
