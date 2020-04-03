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
<label class="hidden" for="page_template"><?php _e('Background Image', 'woodkit'); ?></label>

<input type="hidden" name="<?php echo BACKGROUNDIMAGE_NONCE_BACKGROUNDIMAGE_ACTION; ?>" value="<?php echo wp_create_nonce(BACKGROUNDIMAGE_NONCE_BACKGROUNDIMAGE_ACTION);?>" />

<!-- background image URL / background image ID -->
<?php 
$meta_backgroundimage_url = @get_post_meta(get_the_ID(), BACKGROUNDIMAGE_URL, true);
$meta_backgroundimage_id = @get_post_meta(get_the_ID(), BACKGROUNDIMAGE_ID, true);
$meta_backgroundcolor_code = @get_post_meta(get_the_ID(), BACKGROUNDCOLOR_CODE, true);
$meta_backgroundcolor_opacity = @get_post_meta(get_the_ID(), BACKGROUNDCOLOR_OPACITY, true);
if (empty($meta_backgroundcolor_code)){
	$meta_backgroundcolor_code = "#ffffff";
}
if (empty($meta_backgroundcolor_opacity)){
	$meta_backgroundcolor_opacity = 0;
}
?>
<div class="backgroundimage-box background-image-preview" style="
			position: relative;
			<?php if (!empty($meta_backgroundimage_url)){ ?>
			background:	url('<?php echo $meta_backgroundimage_url; ?>') no-repeat center center;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			-ms-background-size: cover;
			background-size: cover;
			<?php }else{ ?>
			background: none;
			<?php } ?>
			width: 100%;
			height: 120px;
			overflow: hidden;">
	<div class="background-color-preview" style="
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%; ">
		<table class="backgroundimage-input">
			<tr>
				<td style="height: 120px;">
					<input type="hidden" name="<?php echo BACKGROUNDIMAGE_URL; ?>" id="<?php echo BACKGROUNDIMAGE_URL; ?>" value="<?php echo $meta_backgroundimage_url; ?>" />
					<input type="hidden" name="<?php echo BACKGROUNDIMAGE_ID; ?>" id="<?php echo BACKGROUNDIMAGE_ID; ?>" value="<?php echo $meta_backgroundimage_id; ?>" />
					<button class="choose-backgroundimage button button-large"><?php _e("Choose image", 'woodkit'); ?></button>
					<button class="delete-backgroundimage button button-large" style="<?php if (empty($meta_backgroundimage_url)){ echo 'display: none;'; } ?>"><?php _e("Delete", 'woodkit'); ?></button>
				</td>
			</tr>
		</table>
	</div>
</div>
<table class="backgroundcolor-input">
	<tr>
		<td valign="middle" colspan="2">
			<label for="<?php echo BACKGROUNDCOLOR_CODE; ?>">Color</label>
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="2">
			<input type="color" name="<?php echo BACKGROUNDCOLOR_CODE; ?>" id="<?php echo BACKGROUNDCOLOR_CODE; ?>" value="<?php echo $meta_backgroundcolor_code; ?>" />
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="2">
			<label for="<?php echo BACKGROUNDCOLOR_OPACITY; ?>">Opacity</label>
		</td>
	</tr>
	<tr>
		<td valign="middle">
			<input type="range" min="0" max="100" name="<?php echo BACKGROUNDCOLOR_OPACITY; ?>" id="<?php echo BACKGROUNDCOLOR_OPACITY; ?>" value="<?php echo $meta_backgroundcolor_opacity; ?>" />
		</td>
		<td valign="middle">
			<input type="text" disabled="disabled" size="3" name="<?php echo BACKGROUNDCOLOR_OPACITY; ?>-preview" value="<?php echo $meta_backgroundcolor_opacity; ?>%" />
		</td>
	</tr>
</table>


<script type="text/javascript">
	jQuery(document).ready(function($){
		$("input[name='<?php echo BACKGROUNDCOLOR_CODE; ?>']").wpColorPicker({change: function(event, ui){
				background_change_color($);
			}});
		$('.choose-backgroundimage').click(function(e) {
			var woodkit_uploader;
	        e.preventDefault();
	        //If the uploader object has already been created, reopen the dialog 
	        if (woodkit_uploader) {
	            woodkit_uploader.open();
	            return;
	        }
	        //Extend the wp.media object 
	        woodkit_uploader = wp.media.frames.file_frame = wp.media({
	            title: '<?php _e("Choose document", 'woodkit'); ?>',
	            button: {
	                text: '<?php _e("Ok", 'woodkit'); ?>'
	            },
	            multiple: false
	        });
	        //When a file is selected, grab the URL and set it as the text field's value 
	        woodkit_uploader.on('select', function() {
	            attachment = woodkit_uploader.state().get('selection').first().toJSON();
	            $("input[name='<?php echo BACKGROUNDIMAGE_URL; ?>']").val(attachment.url);
	            $("input[name='<?php echo BACKGROUNDIMAGE_ID; ?>']").val(attachment.id);
	            $(".background-image-preview").css('background', 'url("'+attachment.url+'") no-repeat center center');
	            $(".delete-backgroundimage").fadeIn(0);
	        });
	        //Open the uploader dialog 
	        woodkit_uploader.open();
	        return false;
		});
		$('.delete-backgroundimage').click(function(e) {
			e.preventDefault();
			$("input[name='<?php echo BACKGROUNDIMAGE_URL; ?>']").val('');
			$("input[name='<?php echo BACKGROUNDIMAGE_ID; ?>']").val('');
			$(".background-image-preview").css('background', 'none');
			$(this).fadeOut(0);
		});
		$("input[name='<?php echo BACKGROUNDCOLOR_CODE; ?>']").on('change', function(e){
			background_change_color($);
		});
		$("input[name='<?php echo BACKGROUNDCOLOR_OPACITY; ?>']").on('change', function(e){
			background_change_color($);
		});
		background_change_color($);
	});
	function background_change_color($){
		var opacity = $("input[name='<?php echo BACKGROUNDCOLOR_OPACITY; ?>']").val();
		$("input[name='<?php echo BACKGROUNDCOLOR_OPACITY; ?>-preview']").val(opacity+"%");
		if (opacity == 100){
			opacity = "1";
		}else{
			opacity = "0."+opacity;
		}
		var color_rgb = hexToRgb($("input[name='<?php echo BACKGROUNDCOLOR_CODE; ?>']").val());
		if (color_rgb != null){
			var color_css_rgb = color_rgb.r+", "+color_rgb.g+", "+color_rgb.b;
			var css_bg_color = "rgba("+color_css_rgb+", "+opacity+")";
			$(".background-color-preview").css("background-color", css_bg_color);
		}
	}
</script>