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
<div id="tool-pagination-display-pagination" class="custom-fields-section">
	<header class="custom-fields-section-header">
		<h3><?php _e('Pagination options', 'woodkit'); ?></h3>
	</header>
	<div class="custom-fields-section-content">
		<table class="fields">
			<tr valign="top">
				<th class="metabox_label_column" align="left" valign="middle"><label
					for="<?php echo META_PAGINATION_DISPLAY_PAGINATION; ?>"><?php _e('Display pagination', 'woodkit'); ?> : </label>
				</th>
				<td valign="middle">
					<?php $meta = get_post_meta(get_the_ID(), META_PAGINATION_DISPLAY_PAGINATION, true); ?>
					<input type="checkbox" id="<?php echo META_PAGINATION_DISPLAY_PAGINATION; ?>" name="<?php echo META_PAGINATION_DISPLAY_PAGINATION; ?>" <?php if (empty($meta) || $meta == 'on'){ echo 'checked="checked"'; }?> />
				</td>
				<td valign="middle"></td>
			</tr>
		</table>
	</div>
</div>
