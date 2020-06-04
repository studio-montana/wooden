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
class WK_Tool_PostsNavigation extends WK_Tool{

	public function __construct(){
		parent::__construct(array(
				'uri' => get_template_directory_uri().'/src/tools/postsnavigation/', // must be explicitly defined to support symbolic link context
				'slug' => 'postsnavigation',
				'has_config' => true,
				'context' => 'Wooden',
		));
	}

	public function get_name() {
		return __("Posts Navigation", 'wooden');
	}

	public function get_description() {
		return __("Display 'Previous-Next' single post navigation", 'wooden');
	}

	public function launch() {
		require_once ($this->path.'utils.php');
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
		<div class="wk-panel">
			<h2 class="wk-panel-title">
				<?php _e("General", 'wooden'); ?>
			</h2>
			<div class="wk-panel-content">
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
						<label for="taxnav-active"><?php _e("Include taxonomies", 'wooden'); ?></label>
					</div>
					<p class="description"><?php _e('include taxonomies context if appropriate', 'wooden'); ?></p>
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
						<label for="loop-active"><?php _e("Loop", 'wooden'); ?></label>
					</div>
					<p class="description"><?php _e('generate loop navigation', 'wooden'); ?></p>
				</div>
			</div>
		</div>
		<div class="wk-panel">
			<h2 class="wk-panel-title">
				<?php _e("Integration", 'wooden'); ?>
			</h2>
			<div class="wk-panel-content">
				<div class="wk-panel-info">
					<?php _e('Paste this code into your theme templates :', 'wooden'); ?><br /><code style="font-size: 0.7rem;">&lt;?php the_wooden_posts_navigation(); ?&gt;</code>
				</div>
			</div>
		</div>
		<?php
	}
}
add_filter("woodkit-register-tool", function($tools){
	$tools[] = new WK_Tool_PostsNavigation($args);
	return $tools;
});
