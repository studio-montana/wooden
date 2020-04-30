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
define('BREADCRUMB_TOOL_NAME', 'breadcrumb');

/**
 * Tool instance
 */
class WK_Tool_BreadCrumb extends WK_Tool{

	public function __construct(){
		parent::__construct(array(
				'uri' => get_template_directory_uri().'/src/tools/breadcrumb/', // must be explicitly defined to support symbolic link context
				'slug' => 'breadcrumb',
				'name' => __("Breadcrumb", 'wooden'),
				'description' => __("Display breadcrumb on you website frontend", 'wooden'),
				'has_config' => true,
				'add_config_in_menu' => true,
				'context' => 'Wooden',
			));
	}

	public function launch() {
		require_once ($this->path.'utils.php');
		require_once ($this->path.'custom-fields/breadcrumb.php');
		require_once ($this->path.'gutenberg/plugins/breadcrumbmeta/index.php');
		add_action('admin_enqueue_scripts', function () {
			wp_enqueue_script('tool-breadcrumb-js', $this->uri.'js/tool-breadcrumb-admin.js', array('jquery'), WOODEN_WEBCACHE_VERSION, true);
			wp_enqueue_style('tool-breadcrumb-css', $this->uri.'css/tool-breadcrumb-admin.css', array(), WOODEN_WEBCACHE_VERSION);
		});
	}

	public function get_config_fields(){
		return array(
				'breadcrumb-post-types',
				'breadcrumb-menu-management-active',
		);
	}

	public function get_config_default_values(){
		return array(
				'active' => 'off',
				'breadcrumb-post-types' => array(),
				'breadcrumb-menu-management-active' => 'on',
		);
	}

	/**
	 * Override...
	 * {@inheritDoc}
	 * @see WK_Tool::display_config_fields()
	 */
	public function display_config_fields(){
		?>
		<div class="section">
			<h2 class="section-title">
				<?php _e("Breacrumb Options", 'woodkit'); ?>
			</h2>
			<div class="section-content">
				<div class="field checkbox">
					<div class="field-content">
						<?php
						$value = $this->get_option('breadcrumb-menu-management-active');
						$checked = '';
						if ($value == 'on'){
							$checked = ' checked="checked"';
						}
						?>
						<input type="checkbox" id="breadcrumb-menu-management-active" name="breadcrumb-menu-management-active" <?php echo $checked; ?> />
						<label for="breadcrumb-menu-management-active"><?php _e("Activate menu management", 'woodkit'); ?></label>
					</div>
					<p class="description"><?php _e('WP menus will be altered (classes) by this tool. Ex. : add "active" classe on breadcrumb elements.', 'woodkit'); ?></p>
				</div>
			</div>
		</div>
		<div class="section">
			<h2 class="section-title">
				<?php _e("Post types settings", 'woodkit'); ?>
			</h2>
			<div class="section-content">
				<?php
				$value = $this->get_option('breadcrumb-post-types');
				$post_types = get_post_types(array('public' => true), 'objects');
				if (!empty($post_types)){
					foreach ($post_types as $post_type){
						$type = isset($value[$post_type->name]) && isset($value[$post_type->name]['type']) ? $value[$post_type->name]['type'] : 'classic';
						$items = isset($value[$post_type->name]) && isset($value[$post_type->name]['items']) ? $value[$post_type->name]['items'] : array();
						$js_data = "";
						$js_data .= "[";
						if (!empty($items) && is_array($items)){
							$first_item = true;
							foreach ($items as $item){
								if (!$first_item){
									$js_data .= ",";
								}
								$js_data .= "'".$item."'";
								$first_item = false;
							}
						}
						$js_data .= "]";
						?>
						<div id="breadcrumb-post-types-<?php echo $post_type->name; ?>" class="breadcrumb-post-types field select">
							<div class="field-content">
								<label for="breadcrumb-post-types-<?php echo $post_type->name; ?>-type"><?php echo $post_type->label; ?></label>
								<select name="breadcrumb-post-types[<?php echo $post_type->name; ?>][type]" id="breadcrumb-post-types-<?php echo $post_type->name; ?>-type">
									<option value="classic" <?php if (empty($type) || $type == "classic"){ ?> selected="selected"<?php } ?>><?php _e('classic', 'woodkit'); ?></option>
									<option value="customized" <?php if (!empty($type) && $type == "customized"){ ?> selected="selected"<?php } ?>><?php _e('customized', 'woodkit'); ?></option>
								</select>
								<div id="breadcrumb-customize-items-<?php echo $post_type->name; ?>" class="breadcrumb-customize-items" style="display: none;">
									<div><i class="fa fa-long-arrow-down" style="margin: 0 9px 6px 0;"></i><?php _e('Home', 'woodkit'); ?></div>
									<div id="breadcrumb-customize-items-<?php echo $post_type->name; ?>-manager"></div>
								</div>
							</div>
						</div>
						<script type="text/javascript">
						jQuery(document).ready(function($){
							$("#breadcrumb-post-types-<?php echo $post_type->name; ?>-type").on('change', function(e){
								if ($(this).val() === "customized"){
									$("#breadcrumb-customize-items-<?php echo $post_type->name; ?>").fadeIn();
								}else{
									$("#breadcrumb-customize-items-<?php echo $post_type->name; ?>").fadeOut(0);
								}
							});
							if ($("#breadcrumb-post-types-<?php echo $post_type->name; ?>-type").val() === "customized"){
								$("#breadcrumb-customize-items-<?php echo $post_type->name; ?>").fadeIn();
							}else{
								$("#breadcrumb-customize-items-<?php echo $post_type->name; ?>").fadeOut(0);
							}
							$("#breadcrumb-customize-items-<?php echo $post_type->name; ?>-manager").breadcrumbmanager({
								base_name : "breadcrumb-post-types[<?php echo $post_type->name; ?>][items]",
								base_id : "breadcrumb-post-types-<?php echo $post_type->name; ?>-items",
								label_add_breadcrumbitem : "<?php echo str_replace('"', '\"', __('Add Breadcrumb Item', 'woodkit')); ?>",
								label_remove_breadcrumbitem : "<?php echo str_replace('"', '\"', __('Remove this breadcrumb item', 'woodkit')); ?>",
								label_select_option_item : "<?php echo str_replace('"', '\"', __('Choose item', 'woodkit')); ?>",
								options: '<?php echo str_replace("'", "\'", woodkit_get_posts_options()); ?>',
								data: <?php echo $js_data; ?>,
							});
						});
						</script>
						<?php
					}
				}?>
				<p class="info"><?php _e('You can define, for each post type, a customized breadcrumb.', 'woodkit'); ?></p>
			</div>
		</div>
		<div class="section">
			<h2 class="section-title">
				<?php _e("Integration", 'woodkit'); ?>
			</h2>
			<div class="section-content">
				<div class="section-info">
					<?php _e('PHP :', 'woodkit'); ?><br /><code style="font-size: 0.7rem;">&lt;?php tool_breadcrumb(array(), true); ?&gt;</code>
				</div>
			</div>
		</div>
		<?php
	}

}
add_filter("woodkit-register-tool", function($tools){
	$tools[] = new WK_Tool_BreadCrumb();
	return $tools;
});
