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
		<h3><?php _e("Display options", 'woodkit'); ?></h3>
	</header>
	<div class="custom-fields-section-content">
		<table class="fields">
			<tr valign="top">
				<th class="metabox_label_column" align="left" valign="middle"><label
					for="<?php echo META_DISPLAY_CUSTOMTITLE; ?>"><?php _e('Customized title', 'woodkit'); ?> : </label>
				</th>
				<td valign="middle">
					<?php 
					$meta = get_post_meta(get_the_ID(), META_DISPLAY_CUSTOMTITLE, true);
					?>
					<input type="text" size="50" id="<?php echo META_DISPLAY_CUSTOMTITLE; ?>" name="<?php echo META_DISPLAY_CUSTOMTITLE; ?>" value="<?php echo $meta; ?>" />
				</td>
				<td valign="middle"><em><?php _e("Replace native title", 'woodkit'); ?></em></td>
			</tr>
			<tr valign="top">
				<th class="metabox_label_column" align="left" valign="middle"><label
					for="<?php echo META_DISPLAY_SUBTITLE; ?>"><?php _e('Subtitle', 'woodkit'); ?> : </label>
				</th>
				<td valign="middle">
					<?php 
					$meta = get_post_meta(get_the_ID(), META_DISPLAY_SUBTITLE, true);
					?>
					<input type="text" size="50" id="<?php echo META_DISPLAY_SUBTITLE; ?>" name="<?php echo META_DISPLAY_SUBTITLE; ?>" value="<?php echo $meta; ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th class="metabox_label_column" align="left" valign="middle"><label
					for="<?php echo META_DISPLAY_HIDE_TITLE; ?>"><?php _e('Hide title', 'woodkit'); ?> : </label>
				</th>
				<td valign="middle">
					<?php 
					$meta = get_post_meta(get_the_ID(), META_DISPLAY_HIDE_TITLE, true);
					?>
					<input type="checkbox" id="<?php echo META_DISPLAY_HIDE_TITLE; ?>" name="<?php echo META_DISPLAY_HIDE_TITLE; ?>" <?php if (!empty($meta) && $meta == 'on'){ echo 'checked="checked"'; }?> />
				</td>
				<td valign="middle"><em><?php _e("Hide title or woodkit title except in lists", 'woodkit'); ?></em></td>
			</tr>
			<tr valign="top">
				<th class="metabox_label_column" align="left" valign="middle"><label
					for="<?php echo META_DISPLAY_HIDE_THUMBNAIL; ?>"><?php _e('Hide thumbnail', 'woodkit'); ?> : </label>
				</th>
				<td valign="middle">
					<?php 
					$meta = get_post_meta(get_the_ID(), META_DISPLAY_HIDE_THUMBNAIL, true);
					?>
					<input type="checkbox" id="<?php echo META_DISPLAY_HIDE_THUMBNAIL; ?>" name="<?php echo META_DISPLAY_HIDE_THUMBNAIL; ?>" <?php if (!empty($meta) && $meta == 'on'){ echo 'checked="checked"'; }?> />
				</td>
				<td valign="middle"><em><?php _e("Hide thumbnail except in lists", 'woodkit'); ?></em></td>
			</tr>
			<?php 
			if (woodkit_is_activated_tool('video')){
			?>
			<tr valign="top">
				<th class="metabox_label_column" align="left" valign="middle"><label
					for="<?php echo META_DISPLAY_HIDE_FEATURED_VIDEO; ?>"><?php _e('Hide featured video', 'woodkit'); ?> : </label>
				</th>
				<td valign="middle">
					<?php 
					$meta = get_post_meta(get_the_ID(), META_DISPLAY_HIDE_FEATURED_VIDEO, true);
					?>
					<input type="checkbox" id="<?php echo META_DISPLAY_HIDE_FEATURED_VIDEO; ?>" name="<?php echo META_DISPLAY_HIDE_FEATURED_VIDEO; ?>" <?php if (!empty($meta) && $meta == 'on'){ echo 'checked="checked"'; }?> />
				</td>
				<td valign="middle"><em><?php _e("Hide featured video except in lists", 'woodkit'); ?></em></td>
			</tr>
			<?php 
			}
			?>
			<tr valign="top">
				<th class="metabox_label_column" align="left" valign="middle"><label
					for="<?php echo META_DISPLAY_BADGED; ?>"><?php _e("Badge", 'woodkit'); ?> : </label>
				</th>
				<td valign="middle">
					<?php 
					$meta = get_post_meta(get_the_ID(), META_DISPLAY_BADGED, true);
					?>
					<input type="checkbox" id="<?php echo META_DISPLAY_BADGED; ?>" name="<?php echo META_DISPLAY_BADGED; ?>" <?php if (!empty($meta) && $meta == 'on'){ echo 'checked="checked"'; }?> />
				</td>
				<td valign="middle"><em><?php _e("Add awesome badge for new stuff !", 'woodkit'); ?></em></td>
			</tr>
			<tr valign="top" class="badge-text" style="display: none;">
				<th class="metabox_label_column" align="left" valign="middle"><label
					for="<?php echo META_DISPLAY_BADGE_TEXT; ?>"><?php _e("Badge's text", 'woodkit'); ?> : </label>
				</th>
				<td valign="middle">
					<?php 
					$meta = get_post_meta(get_the_ID(), META_DISPLAY_BADGE_TEXT, true);
					if (empty($meta)){
						$meta = __("new", 'woodkit');
					}
					?>
					<input type="text" size="50" id="<?php echo META_DISPLAY_BADGE_TEXT; ?>" name="<?php echo META_DISPLAY_BADGE_TEXT; ?>" value="<?php echo $meta; ?>" />
				</td>
				<td valign="middle"></td>
			</tr>
		</table>
		<script type="text/javascript">
			(function($) {
				$("input[name='<?php echo META_DISPLAY_BADGED ?>']").on('click', function(e) {
					if ($(this).prop('checked')){
						$(".badge-text").fadeIn();
					}else{
						$(".badge-text").fadeOut();
					}
				});
				if ($("input[name='<?php echo META_DISPLAY_BADGED ?>']").prop('checked')){
					$(".badge-text").fadeIn();
				}else{
					$(".badge-text").fadeOut();
				}
			})(jQuery);
		</script>
	</div>
</div>