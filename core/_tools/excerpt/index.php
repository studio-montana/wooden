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
define('EXCERPT_TOOL_NAME', 'excerpt');

/**
 * Tool instance
 */
class WoodkitToolExcerpt extends WoodkitTool{
	
	public function __construct(){
		parent::__construct(
				'excerpt', 								// slug
				__("Excerpt", 'woodkit'),						// name
				__("Add a custom excerpt to your posts", 'woodkit'),	// description
				true,											// has config page
				false,											// add config page in woodkit submenu
				WOODKIT_URL_DOCUMENTATION.'/resume-personnalise'// documentation url
			);
	}
	
	public function get_config_fields(){
		return array(
				'editor-autop'
		);
	}
	
	public function get_config_default_values(){
		return array(
				'active' => 'off',
				'editor-autop' => 'on'
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
						$value = $this->get_option('editor-autop');
						$checked = '';
						if ($value == 'on'){
							$checked = ' checked="checked"';
						}
						?>
						<input type="checkbox" id="editor-autop" name="editor-autop" <?php echo $checked; ?> />
						<label for="editor-autop"><?php _e("Auto paragraph", 'woodkit'); ?></label>
					</div>
					<p class="description"><?php _e('activate auto paragraph for excerpt text editor', 'woodkit'); ?></p>
				</div>
			</div>
		</div>
		<?php
	}
	
}
add_filter("woodkit-register-tool", function($tools){
	$tools[] = new WoodkitToolExcerpt();
	return $tools;
});
