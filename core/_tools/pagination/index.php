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
define('PAGINATION_TOOL_NAME', 'pagination');

/**
 * Tool instance
 */
class WoodkitToolPagination extends WoodkitTool{
	
	public function __construct(){
		parent::__construct(
				'pagination', 								// slug
				__("Pagination", 'woodkit'),				// name
				__("Intelligent pagination for all post-types", 'woodkit'),	// description
				true,										// has config page
				false,										// add config page in woodkit submenu
				WOODKIT_URL_DOCUMENTATION.'/pagination'		// documentation url
			);
	}
	
	public function get_config_fields(){
		return array(
				'taxnav-active',
				'loop-active',
		);
	}
	
	public function get_config_default_values(){
		return array(
				'active' => 'off',
				'taxnav-active' => 'on',
				'loop-active' => 'on',
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
						$value = $this->get_option('taxnav-active');
						$checked = '';
						if ($value == 'on'){
							$checked = ' checked="checked"';
						}
						?>
						<input type="checkbox" id="taxnav-active" name="taxnav-active" <?php echo $checked; ?> />
						<label for="taxnav-active"><?php _e("Include taxonomies", 'woodkit'); ?></label>
					</div>
					<p class="description"><?php _e('include taxonomies context if appropriate', 'woodkit'); ?></p>
				</div>
				<div class="field checkbox">
					<div class="field-content">
						<?php
						$value = $this->get_option('loop-active');
						$checked = '';
						if ($value == 'on'){
							$checked = ' checked="checked"';
						}
						?>
						<input type="checkbox" id="loop-active" name="loop-active" <?php echo $checked; ?> />
						<label for="loop-active"><?php _e("Loop", 'woodkit'); ?></label>
					</div>
					<p class="description"><?php _e('generate loop navigation', 'woodkit'); ?></p>
				</div>
			</div>
		</div>
		<div class="section">
			<h2 class="section-title">
				<?php _e("Integration", 'woodkit'); ?>
			</h2>
			<div class="section-content">
				<div class="section-info">
					<?php _e('insert this code in your theme templates :', 'woodkit'); ?><br /><code style="font-size: 0.7rem;">&lt;?php woodkit_pagination(); ?&gt;</code>
				</div>
			</div>
		</div>
		<?php
	}
}
add_filter("woodkit-register-tool", function($tools){
	$tools[] = new WoodkitToolPagination();
	return $tools;
});
