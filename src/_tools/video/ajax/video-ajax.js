/**
 * VIDEO Tool
 * @package WordPress
 * @subpackage Woodkit
 * @since Woodkit 1.0
 * @author Sébastien Chandonay www.seb-c.com / Cyril Tissot www.cyriltissot.com
 */

function get_video_preview(video_url, video_width, video_height, success, failure) {
	jQuery.post(VideoAjax.ajaxUrl, {
		'action' : 'get_video_preview',
		'ajaxNonce' : VideoAjax.ajaxNonce,
		'video_url' : video_url,
		'video_width' : video_width,
		'video_height' : video_height
	}, function(response) {
		success.call(null, response);
	}).fail(function() {
		failure.call(null);
	}).always(function() {
	});
}