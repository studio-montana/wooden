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
?>
<div id="tool-display-general" class="custom-fields-section">
	<header class="custom-fields-section-header">
		<h3><?php _e("Breadcrumb options", 'woodkit'); ?></h3>
	</header>
	<div class="custom-fields-section-content">
		<table class="fields">
			<tr valign="top">
				<th class="metabox_label_column" align="left" valign="middle"><label
					for="<?php echo META_BREADCRUMB_TYPE; ?>"><?php _e('Breadcrumb type', 'woodkit'); ?> : </label>
				</th>
				<td valign="middle">
					<?php 
					$meta = get_post_meta(get_the_ID(), META_BREADCRUMB_TYPE, true);
					?>
					<select name="<?php echo META_BREADCRUMB_TYPE; ?>">
						<option value="classic" <?php if (empty($meta) || $meta == "classic"){ ?> selected="selected"<?php } ?>><?php _e('classic', 'woodkit'); ?></option>
						<option value="customized" <?php if (!empty($meta) && $meta == "customized"){ ?> selected="selected"<?php } ?>><?php _e('customized', 'woodkit'); ?></option>
					</select>
				</td>
				<td valign="middle"></td>
			</tr>
		</table>
		<table class="fields breadcrumb-options breadcrumb-options-customized" style="display: none;">
			<tr valign="top">
				<th class="metabox_label_column" align="left" valign="middle">
					<label><?php _e('Breadcrumb composition', 'woodkit'); ?> : </label>
				</th>
				<td class="metabox_label_column" align="left" valign="middle">
					<label><i class="fa fa-long-arrow-down" style="margin-right: 12px;"></i><?php _e('Home', 'woodkit'); ?></label>
				</td>
			</tr>
			<tr valign="top">
				<th valign="middle"></th>
				<td valign="middle">
					<div id="breadcrumb-breadcrumbs-manager">
						<?php
						$breadcrumbs = array();
						$meta = get_post_meta(get_the_ID(), META_BREADCRUMB_CUSTOM_ITEMS, true);
						if (!empty($meta)){
							$breadcrumbs = json_decode($meta, true);
						}
						$nb_breadcrumbs = 0;
						if (!empty($breadcrumbs)){
							foreach ($breadcrumbs as $breadcrumb){
								$nb_breadcrumbs++;
								?>
								<div id="breadcrumb-item-<?php echo $nb_breadcrumbs; ?>" class="breadcrumb-item">
									<input type="hidden" name="breadcrumb-item-id-<?php echo $nb_breadcrumbs; ?>" value="<?php echo $nb_breadcrumbs; ?>" />
									<span class="following"><i class="fa fa-long-arrow-down"></i></span>
									<select id="breadcrumbs-select-<?php echo $nb_breadcrumbs; ?>" name="breadcrumbs-select-<?php echo $nb_breadcrumbs; ?>" class="breadcrumbs-select">
										<option value="0"><?php _e("Choose element", 'woodkit'); ?></option>
										<?php echo woodkit_get_posts_options($breadcrumb); ?>
									</select>
									<span class="button delete-breadcrumb-item" data-id="<?php echo $nb_breadcrumbs; ?>"><i class="fa fa-times"></i></span>
								</div>
								<?php
							}
						}
						// new empty item
						$nb_breadcrumbs++;
						?>
						<div id="breadcrumb-item-<?php echo $nb_breadcrumbs; ?>" class="breadcrumb-item">
							<input type="hidden" name="breadcrumb-item-id-<?php echo $nb_breadcrumbs; ?>" value="<?php echo $nb_breadcrumbs; ?>" />
							<span class="following"><i class="fa fa-long-arrow-down"></i></span>
							<select id="breadcrumbs-select-<?php echo $nb_breadcrumbs; ?>" name="breadcrumbs-select-<?php echo $nb_breadcrumbs; ?>" class="breadcrumbs-select">
								<option value="0"><?php _e("Choose element", 'woodkit'); ?></option>
								<?php echo woodkit_get_posts_options(); ?>
							</select>
							<span class="button delete-breadcrumb-item" data-id="<?php echo $nb_breadcrumbs; ?>"><i class="fa fa-times"></i></span>
						</div>
					</div>
					<div id="add-breadcrumb-item" class="button"><i class="fa fa-plus" style="margin-right: 6px;"></i><?php _e("Add breadcrumb element", 'woodkit'); ?></div>
					<script type="text/javascript">
						jQuery(document).ready(function($){
							$(document).on("change", "select[name='<?php echo META_BREADCRUMB_TYPE; ?>']", function(e){
								update_breadcrumb_options($);
							});
							var nb_breadcrumbs = <?php echo $nb_breadcrumbs; ?>;
							$(document).on("click", "#add-breadcrumb-item", function(e){
								nb_breadcrumbs ++;
								var new_breadcrumb_item = '<div id="breadcrumb-item-'+nb_breadcrumbs+'" class="breadcrumb-item">';
								new_breadcrumb_item += '<input type="hidden" name="breadcrumb-item-id-'+nb_breadcrumbs+'" value="'+nb_breadcrumbs+'" />';
								new_breadcrumb_item += '<span class="following"><i class="fa fa-long-arrow-down"></i></span>';
								new_breadcrumb_item += '<select id="breadcrumbs-select-'+nb_breadcrumbs+'" name="breadcrumbs-select-'+nb_breadcrumbs+'" class="breadcrumbs-select">';
								new_breadcrumb_item += '<option value="0"><?php _e("Choose element", 'woodkit'); ?></option>';
								new_breadcrumb_item += '<?php echo str_replace("'", "\'", woodkit_get_posts_options()); ?>';
								new_breadcrumb_item += '</select>';
								new_breadcrumb_item += '<span class="button delete-breadcrumb-item" data-id="'+nb_breadcrumbs+'"><i class="fa fa-times"></i></span>';
								new_breadcrumb_item += '</div>';
								$("#breadcrumb-breadcrumbs-manager").append(new_breadcrumb_item);
							});
							$(document).on("click", ".delete-breadcrumb-item", function() {
								var id = $(this).data("id");
								$("#breadcrumb-item-"+id).remove();
							});
							update_breadcrumb_options($);
						});
						function update_breadcrumb_options($){
							var type = $("select[name='<?php echo META_BREADCRUMB_TYPE; ?>']").val();
							$(".breadcrumb-options").fadeOut(0);
							$(".breadcrumb-options-"+type).fadeIn();
						}
					</script>
				</td>
			</tr>
		</table>
	</div>
</div>