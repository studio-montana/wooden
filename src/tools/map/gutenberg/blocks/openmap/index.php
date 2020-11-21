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

class WKG_Module_Block_openmap extends WKG_Module_Block {

	function __construct() {
		parent::__construct('openmap', array(
				'uri' => wooden_get_tools_directory_uri().'map/gutenberg/blocks/openmap/', // must be explicitly defined to support symbolic link context
				'i18n' => array(
						'domain' => 'wooden',
						'path' => get_template_directory().'lang/',
				),
				'script_dependencies' => array_merge(self::$native_script_dependencies, array('wkg-openmap', 'wkg-openmap-leaflet', 'wkg-openmap-geocoder', 'wkg-openmap-gesture_handler')), // add leaflet dependencies
				'css_dependencies' => array('wkg-openmap-leaflet', 'wkg-openmap-gesture_handler'),
		));
		add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));
	}

	protected function before_init() {
		// register assets
		$path = 'assets/index.js';
		wp_register_script('wkg-openmap', $this->uri.$path, array(), filemtime($this->path.$path));
		$path = 'assets/lib/leaflet/leaflet.css';
		wp_register_style('wkg-openmap-leaflet', $this->uri.$path, array(), filemtime($this->path.$path));
		$path = 'assets/lib/leaflet/leaflet.js';
		wp_register_script('wkg-openmap-leaflet', $this->uri.$path, array(), filemtime($this->path.$path));
		/** geo search (static assets) */
		$path = 'assets/lib/leaflet-geosearch/bundle.min.js';
		wp_register_script('wkg-openmap-geocoder', $this->uri.$path, array(), filemtime($this->path.$path));
		/** geo search (CDN) */
		/**$path = 'https://unpkg.com/leaflet-geosearch@3.0.0/dist/geosearch.umd.js';
		wp_register_script('wkg-openmap-geocoder', $path, array(), filemtime($this->path.$path));*/
		$path = 'assets/lib/leaflet-gesture-handling/leaflet-gesture-handling.min.css';
		wp_register_style('wkg-openmap-gesture_handler', $this->uri.$path, array(), filemtime($this->path.$path));
		$path = 'assets/lib/leaflet-gesture-handling/leaflet-gesture-handling.min.js';
		wp_register_script('wkg-openmap-gesture_handler', $this->uri.$path, array(), filemtime($this->path.$path));
	}

	/**
	 *  Register scripts/styles for frontend
	 */
	public function wp_enqueue_scripts () {
		wp_enqueue_style('wkg-openmap-leaflet');
		wp_enqueue_style('wkg-openmap-gesture_handler');
		wp_enqueue_script('wkg-openmap');
		wp_enqueue_script('wkg-openmap-leaflet');
		wp_enqueue_script('wkg-openmap-geocoder');
		wp_enqueue_script('wkg-openmap-gesture_handler');
	}

	public function render(array $attributes, $content) {
		ob_start ();
		// var_export($attributes);
		$id = isset($attributes['id']) ? $attributes['id'] : uniqid('wkg');
		$map_height = $attributes['map_height'] ? $attributes['map_height'] : 50;
		$additionnalClasses = array();
		if (!empty($attributes['align'])) {
			$additionnalClasses[] = 'align'.$attributes['align'];
		}
		?>
		<div class="<?php echo $this->getFrontClasses($additionnalClasses); ?>" id="<?php echo $id; ?>" style="background-color: #eeeeee; padding-bottom: <?php echo $map_height; ?>%;"></div>
		<script type="text/javascript">
			let attributes = <?php echo json_encode($attributes); ?>;
			console.log("attributes : ", attributes);
			let markers = attributes.markers ? JSON.parse(attributes.markers) : [];
			let config = WKG_OpenStreetMap.getConfig(attributes);
			let map = L.map(document.getElementById(attributes.id), config.mapConfigs);
			if (config.mapStyleConfig) {
				let mapLayer = new L.TileLayer(config.mapStyleConfig.url, config.mapStyleConfig.params)
				map.addLayer(mapLayer)
			}
			if (markers && markers.length > 0) {
				markers.map((marker, i) => {
					let mapMarker = L.marker([marker.lat, marker.lng], {title: marker.title, alt: marker.address}).addTo(map);
					// mapMarker.bindTooltip(marker.title);
					mapMarker.bindPopup('<h2 class="markerPopupTitle">'+marker.title+'</h2><p class="markerPopupInfo">'+(marker.address ? marker.address : 'aucune adresse')+'</p>');
				});
			}
		</script>
		<?php return ob_get_clean();
	}
}
new WKG_Module_Block_openmap();
