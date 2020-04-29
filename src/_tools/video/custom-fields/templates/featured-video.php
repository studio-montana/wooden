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

if (!defined ('ABSPATH')) die ('No direct access allowed');
?>
<input type="hidden" name="<?php echo VIDEO_NONCE_VIDEO_ACTION; ?>" value="<?php echo wp_create_nonce(VIDEO_NONCE_VIDEO_ACTION);?>" />

<div id="tool-video-featured-video" class="custom-fields-section">
	<div class="custom-fields-section-content">
		<table class="fields">
			<tr valign="top">
				<td valign="middle">
					<?php 
					$meta = get_post_meta(get_the_ID(), META_VIDEO_FEATURED_URL, true);
					?>
					<input type="text" size="50" id="<?php echo META_VIDEO_FEATURED_URL; ?>" name="<?php echo META_VIDEO_FEATURED_URL; ?>" value="<?php echo $meta; ?>" placeholder="https://vimeo.com/28449482" />
					<br />
					<em style="font-size: 10px;">youtube/vimeo/dailymotion/vine</em>
				</td>
			</tr>
		</table>
		<div class="featured-video-options">
			<div id="featured-video-preview">
				<!-- content set by ajax call -->
			</div>
		</div>
		<script type="text/javascript">
		(function($) {
			$(document).ready(function(){
				$("#<?php echo META_VIDEO_FEATURED_URL; ?>").on("change", function(e){
					update_video_preview();
				});
				update_video_preview();
			});
			function update_video_preview(){
				var video_url = $("*[name='<?php echo META_VIDEO_FEATURED_URL; ?>']").val();
				if (video_url != ''){
					wait($("#tool-video-featured-video"));
					get_video_preview(video_url, $("#featured-video-preview").width(), 150, function(response){
						unwait($("#tool-video-featured-video"));
						$("#featured-video-preview").html($(response).text());
					}, function(){
						unwait($("#tool-video-featured-video"));
						$("#featured-video-preview").html("");
					});
				}else{
					$("#featured-video-preview").html("");
				}
			}
		})(jQuery);
		</script>
	</div>
</div>