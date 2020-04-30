/**
 * @package Woodkit
 * @author Sébastien Chandonay www.seb-c.com License: GPL2 Text Domain: woodbreadcrumbitems
 * 
 * Copyright 2016 Sébastien Chandonay (email : please contact me from my website)
 * 
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License, version 2, as published by the Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301
 * USA
 */

(function($) {
	$.breadcrumbmanager = function(element, options) {
		var plugin = this;
		var settings = null;
		var $breadcrumbmanager = element;
		var $breadcrumbmanager_container = null;
		var $breadcrumbmanager_container_add = null;

		/**
		 * init/update settings
		 */
		plugin.settings = function(options_var) {
			settings = $.extend({
				base_name : "breadcrumb-items",
				base_id : "breadcrumb-items",
				label_add_breadcrumbitem : "Add Breadcrumb Item",
				label_remove_breadcrumbitem : "Remove this breadcrumb item",
				label_select_option_item : "Choose item",
				options : '', // '<option value="1">Option 1</option><option ...
				data : [],
			}, options_var);
		}

		/**
		 * initialization
		 */
		plugin.init = function() {
			
			$breadcrumbmanager.fadeIn();
			
			plugin.settings(options);

			/**
			 * Html structure
			 */
			$breadcrumbmanager_container = $('<div class="multiple-items-manager breadcrumbmanager-container"></div>').appendTo($('<div class="breadcrumbmanager-wrapper"></div>').appendTo($breadcrumbmanager));
			$breadcrumbmanager_container_add = $('<div class="woodkit-btn add-breadcrumbitem add"><span>' + settings['label_add_breadcrumbitem'] + '</span></div>').appendTo(
					$breadcrumbmanager);
			
			/**
			 * Set Data
			 */
			plugin.set_data(settings.data);

			/**
			 * Listener
			 */
			$breadcrumbmanager_container_add.on('click', function(e) {
				plugin.add_breadcrumbitem();
			});
			$breadcrumbmanager_container.on('click', '.delete-breadcrumb-item', function(e) {
				plugin.remove_breadcrumbitem($(this).attr('data-target'));
			});

		};

		/**
		 * initialization
		 */
		plugin.reinit = function(options_var) {
			plugin.settings(options_var);
		};

		/**
		 *  Set data (breadcrumbitems) - specified parameter must be an Array
		 */
		plugin.set_data = function(data) {
			for (var i = 0; i < data.length; i++) {
				plugin.add_breadcrumbitem(data[i]);
			}
		}

		/**
		 * Add breadcrumbitem html container - specified item must be an String value
		 */
		plugin.add_breadcrumbitem = function(item) {
			
			if (!item || empty(item)){
				item = "0";
			}
			
			var breadcrumbitem_id = plugin.get_unique_id(settings.base_id+'-', 0);
			
			var html = '<div id="'+settings.base_id+'-'+breadcrumbitem_id+'" class="breadcrumb-item">';
			html += '<span class="following"><i class="fa fa-long-arrow-down"></i></span>';
			html += '<select id="'+settings.base_id+'-select-'+breadcrumbitem_id+'" name="'+settings.base_name+'['+breadcrumbitem_id+']" class="breadcrumbs-select">';
			html += '<option value="0">'+settings.label_select_option_item+'</option>';
			html += settings.options;
			html += '</select>';
			html += '<span class="button delete-breadcrumb-item" data-target="#'+settings.base_id+'-'+breadcrumbitem_id+'"><i class="fa fa-times"></i></span>';
			html += '</div>';

			var $breadcrumbitem_container = $(html).appendTo($breadcrumbmanager_container);
			
			// select option
			$breadcrumbitem_container.find("select#"+settings.base_id+"-select-"+breadcrumbitem_id).val(item);
			
			return $breadcrumbitem_container;
		}

		/**
		 * Remove breadcrumbitem
		 */
		plugin.remove_breadcrumbitem = function(target) {
			if ($breadcrumbmanager_container.find(target).length > 0 && confirm(settings.label_remove_breadcrumbitem)) {
				$breadcrumbmanager_container.find(target).remove();
			}
		}

		/**
		 * get unique breadcrumbitem ID
		 */
		plugin.get_unique_id = function(prefix, id) {
			if ($breadcrumbmanager.find("#" + prefix + id).length > 0) {
				id++;
				return plugin.get_unique_id(prefix, id);
			} else {
				return id;
			}
		}

		plugin.init();

		return plugin;
	};

	$.fn.breadcrumbmanager = function(options) {
		var breadcrumbmanager = $(this).data('breadcrumbmanager');
		if (empty(breadcrumbmanager)) {
			breadcrumbmanager = new $.breadcrumbmanager($(this), options);
			$(this).data('breadcrumbmanager', breadcrumbmanager);
		} else {
			breadcrumbmanager.reinit(options);
		}
		return breadcrumbmanager;
	};

})(jQuery);