const WKG_OpenStreetMap = {
	getConfig: function (attributes) {
		var style = attributes.map_style || null;
		var zoom = attributes.map_zoom || 1;
		var zoomable = attributes.map_zoomctrl || false;
		var latitude = attributes.map_lat || 12;
		var longitude = attributes.map_lng || 15;
		var showAttribution = attributes.map_show_attribution || false;

		var mapConfigs = {
			center: [latitude, longitude],
			trackResize: true,
			zoom: zoom,
			gestureHandling: zoomable === true || zoomable === 'true',
			attributionControl: false
		};
		const tileConfigs = WKG_OpenStreetMap.tileConfigs;
		var mapStyleConfig = typeof tileConfigs[style] === 'undefined' ? tileConfigs['wikimedia'] : tileConfigs[style];
		if (showAttribution === true || showAttribution === 'true') {
			mapConfigs.attributionControl = true;
		}

		return {
			mapConfigs,
			mapStyleConfig,
		};
	},
	tileConfigs: {
		wikimedia: {
			url: 'https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}{r}.png',
			params: {
				minZoom: 1,
				maxZoom: 19,
				attribution: '<a href="https://wikimediafoundation.org/wiki/Maps_Terms_of_Use">Wikimedia</a> | Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
			}
		},
		openstreetmap_de: {
			url: 'https://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png',
			params: {
				maxZoom: 18,
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
			}
		},
		openmapsg_default: {
			url: 'https://maps-{s}.onemap.sg/v3/Default/{z}/{x}/{y}.png',
			params: {
				minZoom: 11,
				maxZoom: 18,
				bounds: [[1.56073, 104.11475], [1.16, 103.502]],
				attribution: '<img src="https://docs.onemap.sg/maps/images/oneMap64-01.png" style="height:20px;width:20px;"/> New OneMap | Map data &copy; contributors, <a href="http://SLA.gov.sg">Singapore Land Authority</a>'
			}
		},
		openmapsg__night: {
			url: 'https://maps-{s}.onemap.sg/v3/Night/{z}/{x}/{y}.png',
			params: {
				minZoom: 11,
				maxZoom: 18,
				bounds: [[1.56073, 104.11475], [1.16, 103.502]],
				attribution: '<img src="https://docs.onemap.sg/maps/images/oneMap64-01.png" style="height:20px;width:20px;"/> New OneMap | Map data &copy; contributors, <a href="http://SLA.gov.sg">Singapore Land Authority</a>'
			}
		},
		openstreetmap_mapnik: {
			url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
			params: {
				maxZoom: 17,
				attribution: 'Map data: &copy; <a href="http://www.openptmap.org">OpenPtMap</a> contributors'
			}
		},
		cartodb_positron: {
			url: 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
			params: {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
				subdomains: 'abcd',
				maxZoom: 19
			}
		},
		world_imagery: {
			url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
			params: {
				attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
			}
		},
		opentopomap: {
			url: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
			params: {
				maxZoom: 17,
				attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'
			}
		},
		stamen_toner: {
			url: 'https://stamen-tiles-{s}.a.ssl.fastly.net/toner/{z}/{x}/{y}{r}.{ext}',
			params: {
				minZoom: 0,
				maxZoom: 20,
				subdomains: 'abcd',
				ext: 'png',
				attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> | Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
			}
		},
		stamen_toner_light: {
			url: 'https://stamen-tiles-{s}.a.ssl.fastly.net/toner-lite/{z}/{x}/{y}{r}.{ext}',
			params: {
				minZoom: 0,
				maxZoom: 20,
				subdomains: 'abcd',
				ext: 'png',
				attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> | Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
			}
		},
		stamen_terrain: {
			url: 'https://stamen-tiles-{s}.a.ssl.fastly.net/terrain/{z}/{x}/{y}{r}.{ext}',
			params: {
				minZoom: 0,
				maxZoom: 18,
				subdomains: 'abcd',
				ext: 'png',
				attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> | Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
			}
		},
		stamen_watercolor: {
			url: 'https://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.{ext}',
			params: {
				minZoom: 1,
				maxZoom: 16,
				subdomains: 'abcd',
				ext: 'jpg',
				attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> | Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
			}
		}
	}
};
