/**
 * MEDIA Tool
 * 
 * @package WordPress
 * @subpackage Woodkit
 * @since Woodkit 1.0
 * @author Sébastien Chandonay www.seb-c.com / Cyril Tissot www.cyriltissot.com
 */
(function($) {
	$(".fancybox").fancybox({
		beforeShow : function() {
	        this.title = $(this.element).attr('data-fancybox-title');
	    },
		helpers : {
			title : {
				type : 'inside'
			},
			overlay : {
				css : {
					'background' : ToolFancybox.backgroundcolor
				}
			}
		},
		padding : 0
	});
})(jQuery);
