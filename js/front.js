/**
 * @package WordPress
 * @version 1.0
 * @author Sébastien Chandonay www.seb-c.com This file, like this theme, like WordPress, is licensed under the GPL.
 */

(function($) {

	/***********************************************************************
	 * Listing Filter manager
	 **********************************************************************/
	$.filtermanager = function($el, options) {
		var plugin = this;
		var settings = null;
		var listing_debounce_timeout;
		var $isotope = null;

		/**
		 * init/update options
		 */
		plugin.options = function(options) {
			settings = $.extend({
				filter_selector: '.filter',
				filter_data: 'filter',
				filter_class_active: 'active',
				item_selector: '.item',
				item_class_hidden: 'hidden',
				item_selector_no_result: '.no-result',
				url_parameter: 'param',
				debounce: true,
				debounce_time: 500,
			}, options);
		}

		/**
		 * initialization
		 */
		plugin.init = function() {
			plugin.options(options);

			// on history changes
			window.onpopstate = function(event) {
				plugin.set_filters_state();
				plugin.filter();
			};

			$isotope = $('#listing-filtering').isotope({
				itemSelector: 'article',
				layoutMode: 'fitRows',
			});

			// on filter clicked
			$(settings.filter_selector).on('click', function (e){
				e.preventDefault();
				// set filter state
				if ($(this).hasClass(settings.filter_class_active)) {
					$(this).removeClass(settings.filter_class_active);
				} else {
					$(this).addClass(settings.filter_class_active);
				}
				// set url
				plugin.set_url();
				// filter
				if (settings.debounce === true) {
					// debounce...
					plugin.debounce(function () {
						plugin.filter();
					});
				} else {
					plugin.filter();
				}
				return false;
			});
			// initialize
			plugin.set_filters_state();
			plugin.filter();
		};

		/**
		 * reinit
		 */
		plugin.reinit = function(options) {
			plugin.options(options);
		};

		/**
		 * set filter state
		 */
		plugin.set_filters_state = function () {
			let url_param = plugin.get_url_parameter(settings.url_parameter);
			if (!empty(url_param)) {
				let values = url_param.split("|");
				for (let i = 0; i < values.length; i++) {
					$(settings.filter_selector).each(function(index) {
						if ($(this).data(settings.filter_data) == values[i]) {
							if ($(this).hasClass(settings.filter_class_active)) {
								$(this).removeClass(settings.filter_class_active);
							} else {
								$(this).addClass(settings.filter_class_active);
							}
						}
					});
				}
			}
		}

		/**
		 * set url with parameters
		 */
		plugin.set_url = function() {
			let has_type = false;
			let url = "?";
			url += settings.url_parameter + "=";
			if ($(settings.filter_selector).length > 0) {
				$(settings.filter_selector + "." + settings.filter_class_active).each(function(i) {
					if (has_type) {
						url += encodeURIComponent("|");
					} else {
						has_type = true;
					}
					url += encodeURIComponent($(this).data(settings.filter_data));
				});
			}
			// change browser history
			if (has_type) {
				history.pushState(null, null, url);
			} else {
				history.pushState(null, null, '?');
			}
		}

		/**
		 * get URL parameter
		 */
		plugin.get_url_parameter = function(param) {
			var query = window.location.search.substring(1);
			var vars = query.split("&");
			for ( var i = 0; i < vars.length; i++) {
				var pair = vars[i].split("=");
				if (pair[0] == param) {
					return decodeURIComponent(pair[1]);
				}
			}
		}

		/**
		 * filter items (based on url parameter)
		 */
		plugin.filter = function() {
			var url_param = plugin.get_url_parameter(settings.url_parameter);
			if (!empty(url_param)) {
				var values = url_param.split("|");
				$('#listing-filtering > ' + settings.item_selector).each(function(i) {
					let has_all = true;
					for ( var i = 0; i < values.length; i++) {
						if (!$(this).hasClass(values[i]) && has_all !== false){
							has_all = false;
						}
					}
					if (has_all){
						$(this).removeClass(settings.item_class_hidden);
					} else {
						$(this).addClass(settings.item_class_hidden);
					}
				});
			} else {
				$('#listing-filtering > ' + settings.item_selector).removeClass(settings.item_class_hidden);
			}
			if ($('#listing-filtering > ' + settings.item_selector).length <= $('#listing-filtering > ' + settings.item_selector + '.' + settings.item_class_hidden).length) {
				$('#listing-filtering > '+settings.item_selector_no_result).fadeIn();
			} else {
				$('#listing-filtering > '+settings.item_selector_no_result).fadeOut(0);
			}
			$isotope.isotope();
			
			// survey for image load
			$('#listing-filtering > ' + settings.item_selector + ' img').each(function (i) {
				$(this).image_load(function () {
					$isotope.isotope();
				})
			});
			
		}

		/**
		 * debounce timeout
		 */
		plugin.debounce = function(fn) {
			if (listing_debounce_timeout) {
				clearTimeout(listing_debounce_timeout);
			}
			function delayed() {
				fn();
				listing_debounce_timeout = null;
			}
			listing_debounce_timeout = setTimeout(delayed, settings.debounce_time);
		}

		/**
		 * init plugin
		 */
		plugin.init();
		return plugin;
	};
	$.fn.filtermanager = function(options) {
		var filtermanager = $(this).data('filtermanager');
		if (empty(filtermanager)) {
			filtermanager = new $.filtermanager($(this), options);
			$(this).data('filtermanager', filtermanager);
		} else {
			filtermanager.reinit(options);
		}
		return filtermanager;
	};

	/***********************************************************************
	 * Slider
	 **********************************************************************/
	$.slider = function(element, options) {
		var plugin = this;
		var settings = $.extend({
			slider_selector : null,
			item_selector : '#slider .slide',
			control_prev_selector : "",
			control_next_selector : "",
			control_bullets_selector : "",
			class_added_onshow : 'onshow',
			class_added_onhide : 'onhide',
			min_height : "300px",
			slide_duration: 1000,
			auto_slide: true,
			auto_slide_duration: 5000,
		}, options);

		var $slider = null;
		if (isset(settings['slider_selector'])) {
			$slider = $(settings['slider_selector']);
		} else {
			$slider = element;
		}
		var slides = new Array();
		var current_id_slide = 0;
		var resize_timer = null;

		var $slides = $(settings['item_selector']);
		var _width = $slider.width();
		var _height = $slider.height();
		var cp_slides = 0;

		var auto_slide_interval = null;

		/**
		 * initialization
		 */
		plugin.init = function() {

			$slider.css("position", "relative");
			$slider.css("width", "100%");
			$slider.css("height", "auto");
			$slider.css("min-height", settings['min_height']);
			$slider.css("overflow", "hidden");
			$slider.fadeIn();

			$slides.css('opacity', '1');
			$slides.css('z-index', '0');
			$slides.css('position', 'absolute');
			$slides.css('top', '0');
			$slides.css('right', '0');
			$slides.css('bottom', '0');
			$slides.css('left', '0');
			$slides.addClass(settings['class_added_onhide']);

			$slides.each(function(i) {
				slides[cp_slides] = $(this);
				cp_slides++;
			});

			// swipe control
			$slider.on("swipe", function(e) {
				plugin.stop_auto_slide();
				if (e.swipestart && e.swipestop){
					if (e.swipestart.coords[0] > e.swipestop.coords[0]){
						slides[current_id_slide].animate({left : (e.swipestop.coords[0] - e.swipestart.coords[0]), right : -(e.swipestop.coords[0] - e.swipestart.coords[0])}, 500, function(){
							plugin.show_next_slide();
							$slides.css('left', '0');
							$slides.css('right', '0');
						});
					}else{
						slides[current_id_slide].animate({left : -(e.swipestart.coords[0] - e.swipestop.coords[0]), right : (e.swipestart.coords[0] - e.swipestop.coords[0])}, 500, function(){
							plugin.show_prev_slide();
							$slides.css('left', '0');
							$slides.css('right', '0');
						});
					}
				}
			});

			// prev control
			if (settings['control_prev_selector'] != '' && $(settings['control_prev_selector']).length > 0){
				$(settings['control_prev_selector']).on("click", function(e) {
					var prev_id_slide = current_id_slide - 1;
					if (current_id_slide <= 0) {
						prev_id_slide = slides.length - 1;
					}
					plugin.show_slide(prev_id_slide);
				});
			}
			// next control
			if (settings['control_next_selector'] != '' && $(settings['control_next_selector']).length > 0){
				$(settings['control_next_selector']).on("click", function(e) {
					var next_id_slide = current_id_slide + 1;
					if (current_id_slide >= slides.length - 1) {
						next_id_slide = 0;
					}
					plugin.show_slide(next_id_slide);
				});
			}
			// bullets control
			if (settings['control_bullets_selector'] != '' && $(settings['control_bullets_selector']).length > 0){
				for (var i = 0; i < slides.length ; i++){
					$(settings['control_bullets_selector']).append('<span class="bullet" data-slide="'+i+'"></span>');
				}
				$(settings['control_bullets_selector']).find(".bullet").on('click', function(e){
					plugin.stop_auto_slide();
					plugin.show_slide($(this).data("slide"));
				});
			}
			// show first slide
			plugin.show_slide(0);
			// auto slide
			if (!empty(settings['auto_slide'] && settings['auto_slide'] == true)){
				plugin.auto_slide();
			}
		};

		plugin.auto_slide = function(){
			auto_slide_interval = setInterval(function(){
				plugin.show_next_slide();
			}, settings['auto_slide_duration']);

			$slider.on("click", function () {
				plugin.stop_auto_slide();
			});

			/*
			$slider.on("mouseover", function () {
			   plugin.stop_auto_slide();
			});

			$slider.on("mouseleave", function () {
				plugin.start_auto_slide();
			});
			*/
		}

		plugin.stop_auto_slide = function(){
			if (auto_slide_interval != null){
				clearInterval(auto_slide_interval);
				auto_slide_interval = null;
			}
		}

		plugin.start_auto_slide = function(){
			auto_slide_interval = setInterval(function(){
				plugin.show_next_slide();
			}, settings['auto_slide_duration']);
		}

		plugin.show_slide = function(id_slide) {
			var $old_slide = null;
			if (current_id_slide != id_slide) {
				$old_slide = slides[current_id_slide];
				if ($old_slide.length > 0) {
					$old_slide.removeClass(settings['class_added_onshow']);
					$old_slide.addClass(settings['class_added_onhide']);
					$old_slide.animate({opacity : 0}, settings['slide_duration'], function(){
						$old_slide.css('z-index', 0);
					});
				}
			}
			current_id_slide = id_slide;
			var $slide = slides[id_slide];
			plugin.resize(function() {
				$slide.animate({opacity : 1}, settings['slide_duration'], function(){
					$slide.css('z-index', 1);
				});
				$slide.removeClass(settings['class_added_onhide']);
				$slide.addClass(settings['class_added_onshow']);
			});
			$(settings['control_bullets_selector']).find(".bullet").removeClass("active");
			$(settings['control_bullets_selector']).find(".bullet[data-slide='"+id_slide+"']").addClass("active");
		}

		plugin.show_next_slide = function() {
			var next_id_slide = current_id_slide + 1;
			if (current_id_slide >= slides.length - 1) {
				next_id_slide = 0;
			}
			plugin.show_slide(next_id_slide);
		}

		plugin.show_prev_slide = function() {
			var prev_id_slide = current_id_slide - 1;
			if (current_id_slide <= 0) {
				prev_id_slide = slides.length - 1;
			}
			plugin.show_slide(prev_id_slide);
		}

		/**
		 * window resize
		 */
		$(window).resize(function() {
			if (resize_timer != null)
				clearTimeout(resize_timer);
			resize_timer = setTimeout(plugin.resize, 500);
		});

		/**
		 * resize
		 */
		plugin.resize = function(on_end) {
			$slider.animate({
				'min-height' : slides[current_id_slide].outerHeight(true),
			}, 0, function() {
				if (isset(on_end) && $.isFunction(on_end)) {
					on_end.call(null);
				}
			});
		};

		plugin.init();

		return plugin;

	};
	$.fn.slider = function(options) {
		var plugin = new $.slider($(this), options);
		$(this).data('slider', plugin);
		return $(this).data('slider');
	};

	/***********************************************************************
	 * Image Loader
	 **********************************************************************/
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
			} else {
				// already instanciated - nothing to do
			}
		});
	};

})(jQuery);
