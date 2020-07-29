/**
ASSETS
Author: S. Chandonay - C. Tissot
Author URI: https://www.seb-c.com
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Pure Javascript
 */
function escapeSpecialChars(string){
  return string.replace(/\\n/g, "\\n")
  .replace(/\\'/g, "\\'")
  .replace(/\\"/g, '\\"')
  .replace(/\\&/g, "\\&")
  .replace(/\\r/g, "\\r")
  .replace(/\\t/g, "\\t")
  .replace(/\\b/g, "\\b")
  .replace(/\\f/g, "\\f");
};
function isFloat(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}
function isInt(n) {
  return !isNaN(parseInt(n)) && isFinite(n);
}
function getTotalOuterWidth(items_selector) {
  var w_total = 0;
  jQuery(items_selector).each(function(index) {
    var w_item = jQuery(this).outerWidth(true);
    // on n'aime pas les chiffres impaires ...
    if (w_item % 2 == 1) {
      w_item += 1;
    }
    w_total += w_item;
  });
  return w_total;
}
function isset(variable) {
  if (typeof (variable) == undefined || variable == null) {
    return false;
  }
  return true;
}
function empty(variable) {
  if (!isset(variable) || variable == '') {
    return true;
  }
  return false;
}
function indexOf(value, array) {
  var res = -1;
  if (!empty(array)) {
    for ( var i = 0; i < array.length; i++) {
      if (array[i] === value)
      res = i;
    }
  }
  return res;
}
function get_url_param(param) {
	var query = window.location.search.substring(1);
	var vars = query.split("&");
	for ( var i = 0; i < vars.length; i++) {
		var pair = vars[i].split("=");
		if (pair[0] == param) {
			return decodeURIComponent(pair[1]);
		}
	}
}
function get_url_anchor() {
    return (document.URL.split('#').length > 1) ? document.URL.split('#')[1] : null;
}
function hexToRgb(hex) {
  var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return result ? {
    r : parseInt(result[1], 16),
    g : parseInt(result[2], 16),
    b : parseInt(result[3], 16)
  } : null;
}

/**
 * JQuery
 */
(function($) {
	
	/**
	 * Image load
	 */
	$.image_load = function($element, on_load_function) {
		var load_timer = null;
		if ($element.prop("tagName") == 'IMG') {
			load_timer = setInterval(function() {
				if ($element.get(0).complete) {
					clearInterval(load_timer);
					on_load_function.call($element);
				}
			}, 200);
		}
		return this;
	};
	$.fn.image_load = function(on_load_function) {
		return this.each(function() {
			if (!isset($(this).data('image_load'))) {
				var plugin = new $.image_load($(this), on_load_function);
				$(this).data('image_load', plugin);
			}
		});
	};

	/**
	 * Viewport
	 */
	$.fw_viewport = (function() {
		function some_private_function(){}
		return {
			is_mobile: function () {
				return $(window).width() <= 1024;
			}
		};
	})();

	/**
	 * Modal Box
	 */
	$.fw_modalbox = (function() {
		var settings = '';
		var $fw_modalbox = null;
		var $fw_modalboxcontent = null;
		var $fw_modalboxwrapper = null;
		var $fw_modalboxclose = null;
		var resize_timer = null;
	
		function init (options) {
			settings = $.extend({
				content : '',
				fullscreen: false,
				onopen : null, // function()
				onclear : null, // function()
				onclose : null, // function()
				style: 'dark', // available style : dark|light|none
				classes: [],
			}, options);
			if ($fw_modalbox == null) {
				var additional_modal_classes = '';
				if (isset(settings.fullscreen) && settings.fullscreen == true){
					additional_modal_classes += ' fullscreen';
				}
				if (isset(settings.style)){
					additional_modal_classes += ' style-'+settings.style;
				}
				if (isset(settings.classes) && settings.classes.length > 0){
					for (let i = 0; i < settings.classes.length; i++){
						additional_modal_classes += ' '+settings.classes[i];
					}
				}
				$("body").append('<div id="fwmodalbox" class="fw-modalbox'+additional_modal_classes+'" style="display: none;"></div>');
				$("#fwmodalbox").append('<div id="fwmodalbox-wrapper" class="fw-modalbox-wrapper"></div>');
				$("#fwmodalbox-wrapper").append('<div id="fwmodalbox-content-wrapper" class="fw-modalbox-content-wrapper"><div id="fwmodalbox-close" class="fw-modalbox-close"><i class="fa fa-times"></i></div><div id="fwmodalbox-content" class="fw-modalbox-content"></div></div>');
				$fw_modalbox = $("#fwmodalbox");
				$fw_modalboxclose = $("#fwmodalbox-close");
				$fw_modalboxclose.on('click', function(e) {
					close();
				});
				$fw_modalboxcontent = $("#fwmodalbox-content");
				$fw_modalboxwrapper = $("#fwmodalbox-wrapper");
				$fw_modalboxcontent.empty();
				$(document).keyup(function(e) {
					if (e.keyCode === 27)
						close();
				});
				$(document).on('click', function(e) { // close on click outside
					if (!$fw_modalboxcontent.is(e.target) && $fw_modalboxcontent.has(e.target).length === 0 && ($fw_modalbox.is(e.target) || $fw_modalboxwrapper.is(e.target))) {
						close();
					}
				});
			}
			clear();
			if (isset(settings.content)) {
				$fw_modalboxcontent.append(settings.content);
			}
			open();
		}
		
		function getContent(){
			return $fw_modalboxcontent;
		}
		
		function open(){
			$fw_modalbox.fadeIn(0);
			trigger_onopen();
		}
		
		function clear(){
			if ($fw_modalboxcontent.length > 0){
				$fw_modalboxcontent.html('');
				trigger_onclear();
			}
		}
		
		function close(){
			if ($fw_modalbox != null) {
				$fw_modalbox.fadeOut(0);
				$fw_modalboxcontent.empty();
				trigger_onclose();
			}
		}
		
		/**
		 * trigger onopen
		 */
		function trigger_onopen(){
			if (isset(settings.onopen) && $.isFunction(settings.onopen)) {
				settings.onopen.call(null);
			}
		};
		/**
		 * trigger onclear
		 */
		function trigger_onclear(){
			if (isset(settings.onclear) && $.isFunction(settings.onclear)) {
				settings.onclear.call(null);
			}
		};
		/**
		 * trigger onclose
		 */
		function trigger_onclose(){
			if (isset(settings.onclose) && $.isFunction(settings.onclose)) {
				settings.onclose.call(null);
			}
		};
	
		return { // public interface
			open: function (options) {
				init(options);
			},
			clear: function () {
				clear();
			},
			close: function () {
				close();
			},
			getContent: function () {
				return getContent();
			}
		};
	})();
	
	/**
	 * Wait
	 */
	$.fw_wait = (function() {
		var settings = '';
		function init (options) {
			settings = $.extend({
				message: '',
				el: '',
			}, options);
			show();
		}
		function getbox(add_class){
			let box  = '<div class="fw-wait fw-flex ' + add_class + '" style="display: none;">';
			box 	+= '<div class="fw-wait-content fw-flex-content">';
			box 	+= '<img src="' + Commons.wait_img + '" alt="loading..." />';
			box 	+= '</div>';
			box 	+= '</div>';
			return box;
		}
		function show(){
			if (settings.el !== ''){
				settings.el.append(getbox('local'));
				settings.el.find(' > .fw-wait').fadeIn(200);
			}else{
				$('body').append(getbox('fw-wait-fixed'));
				$('body > .fw-wait').fadeIn(200);
			}
		}
		function hide(){
			if (settings.el !== ''){
				settings.el.find(' > .fw-wait').fadeIn(200, function(){
					settings.el.find(' > .fw-wait').remove();
				});
			}else{
				$('body > .fw-wait').fadeIn(200, function(){
					$('body > .fw-wait').remove();
				});
			}
		}
		return { // public interface
			show: function (options) {
				init(options);
			},
			hide: function () {
				hide();
			}
		};
	})();

})(jQuery);