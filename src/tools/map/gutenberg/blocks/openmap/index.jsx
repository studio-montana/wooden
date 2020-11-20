import { __ } from '@wordpress/i18n'
const { registerBlockType } = wp.blocks
const { Component, Fragment } = wp.element
const { InspectorControls, InnerBlocks } = wp.blockEditor
const { PanelBody, PanelRow, TextControl, SelectControl, RangeControl, CheckboxControl, Button, Notice, TextareaControl } = wp.components

registerBlockType('wkg/openmap', {
	title: 'Open Street Map',
	category: 'common',
	icon: 'location-alt',
	keywords: [ 'map', 'google maps', 'open street map', 'carte', 'leaflet' ],
	supports: {
		html: false,
		reusable: false,
		align: ['full', 'wide']
	},
	attributes: {
		id: {
			type: 'string',
		},
		map_style: {
			type: 'string',
			default: 'wikimedia',
		},
		map_height: {
			type: 'number',
			default: 50,
		},
		map_zoom: {
			type: 'number',
			default: 1,
		},
		map_zoomctrl: {
			type: 'boolean',
			default: true,
		},
		map_lat: {
			type: 'number',
			default: 1,
		},
		map_lng: {
			type: 'number',
			default: 1,
		},
		markers: {
			type: 'string',
			default: '[]',
		}
	},
	edit: function (props) {
		// console.log('attributes : ', props.attributes)
		props.attributes.id = 'wkg' + props.clientId
		props.className += " wkg-editor wkg-item"
		if (props.isSelected) {
			props.className += " wgk-is-selected"
		}
		return (
			<div className={props.className}>
				<BlockComponent
					attributes={props.attributes}
					isSelected={props.isSelected}
					onChange={attributes => props.setAttributes(attributes)}
				/>
			</div>
		)
	},
	save: function (props) {
		return null
	}
})

class BlockComponent extends Component {
	constructor(props) {
		super(props)
		this.state = {...this.props.attributes, ...{
			// block specifics state attributes
			addNew: false,
		}}
		this.map = null
		this.mapLayer = null
	}
	onChange (obj) {
		this.setState(obj)
		this.props.onChange(obj)
	}
	componentDidMount () {
		this.updateMap()
	}
	componentDidUpdate (prevState) {
		this.updateMap()
	}
	updateMap () {
		let config = WKG_OpenStreetMap.getConfig(this.state);
		if (this.map) {
			this.map.eachLayer((layer) => {
				this.map.removeLayer(layer);
			});
			this.map.panTo(config.mapConfigs.center);
			this.map.setZoom(config.mapConfigs.zoom);
		} else {
			this.map = L.map(document.getElementById(this.state.id), config.mapConfigs);
			this.map.on('dragend', (e) => {
				const newCenter = this.map.getCenter();
				this.onChange({map_lat: newCenter.lat, map_lng: newCenter.lng});
			});
			this.map.on('zoomend', (e) => {
				const newZoom = this.map.getZoom();
				if (this.state.map_zoom !== newZoom) {
					this.onChange({map_zoom: newZoom});
				}
			});
		}
		if (config.mapStyleConfig) {
			this.mapLayer = new L.TileLayer(config.mapStyleConfig.url, config.mapStyleConfig.params)
			this.map.addLayer(this.mapLayer)
		}
		const markers = JSON.parse(this.state.markers);
		if (markers.length > 0) {
			markers.map((marker, i) => {
				let mapMarker = L.marker([marker.lat, marker.lng], {title: marker.title, alt: marker.address}).addTo(this.map);
				// mapMarker.bindTooltip(marker.title);
				mapMarker.bindPopup('<h2 class="markerPopupTitle">'+marker.title+'</h2><p class="markerPopupInfo">'+(marker.address ? marker.address : "aucune adresse")+'</p>');
			});
		}
	}
	addMarker (marker) {
		let markers = JSON.parse(this.state.markers);
		marker.id = this.getUniqId(markers);
		markers.push(marker);
		this.onChange({markers: JSON.stringify(markers)});
		this.setState({addNew: false});
	}
	addMarkersFromJson (markersToAdd) {
		let markers = JSON.parse(this.state.markers);
		markersToAdd.map((marker, i) => {
			marker.id = this.getUniqId(markers, marker.id ? marker.id : 0);
			marker.lat = parseFloat(marker.lat);
			marker.lng = parseFloat(marker.lng);
			markers.push(marker);
		});
		// markers = [...markers, ...markersToAdd];
		this.onChange({markers: JSON.stringify(markers)});
		this.setState({addJson: false});
	}
	cancelAddMarker () {
		this.setState({addNew: false});
	}
	cancelAddMarkersFromJson () {
		this.setState({addJson: false});
	}
	updateMarker (marker) {
		let markers = JSON.parse(this.state.markers);
		const foundIndex = markers.findIndex(item => parseInt(item.id) == parseInt(marker.id));
		markers[foundIndex] = marker;
		this.onChange({markers: JSON.stringify(markers)});
	}
	removeMarker (marker) {
		let markers = JSON.parse(this.state.markers);
		markers = markers.filter(item => parseInt(item.id) !== parseInt(marker.id));
		this.onChange({markers: JSON.stringify(markers)});
	}
	getUniqId (existingMarkers, id = 0) {
		const foundIndex = existingMarkers.findIndex(item => parseInt(item.id) == parseInt(id));
		if (foundIndex > -1) {
			return this.getUniqId(existingMarkers, (id + 1));
		} else {
			return id;
		}
	}
	renderMarkersManager () {
		const markers = JSON.parse(this.state.markers);
		return (
			<div className="markers-manager">
				{ !this.state.addNew && !this.state.addJson && (
					<Fragment>
						<Button className="add-new-btn" isSecondary onClick={() => this.setState({addNew: true})}>Ajouter un nouveau point</Button>
						<Button className="add-json-btn" isSecondary onClick={() => this.setState({addJson: true})}>Charger un JSON</Button>
					</Fragment>
				) }
				<Button className="close" isSecondary onClick={() => this.setState({showMarkersManager: false})}>Fermer</Button>
				{ this.state.addNew && (
					<div className="add-new">
						<MarkerItemEdit onChange={(markerItem) => this.addMarker(markerItem)} onCancel={() => this.cancelAddMarker()} />
					</div>
				) }
				{ this.state.addJson && (
					<div className="add-json">
						<MarkersFromJson onChange={(markers) => this.addMarkersFromJson(markers)} onCancel={() => this.cancelAddMarkersFromJson()} />
					</div>
				) }
				<ul>
					{ markers.length > 0 && (
						<Fragment>
							{markers.map((marker, i) => {
								 return (
									 <li key={marker.id}>
									 	<MarkerItemEdit onChange={(markerItem) => this.updateMarker(markerItem)} onRemove={(markerItem) => this.removeMarker(markerItem)} marker={marker} />
									 </li>
									)
							})}
						</Fragment>
					) }
				</ul>
			</div>
		)
	}
	render () {
		const markers = JSON.parse(this.state.markers);
		const markerManagerButton = markers.length > 0 ? "Gérer les points sur la carte" : "Ajouter des points sur la carte";
		return (
			<Fragment>
				<div className="wkg-content">
					{ !this.state.showMarkersManager && ( <Button className="show-markers-manager" isSecondary onClick={() => this.setState({showMarkersManager: true})}>{markerManagerButton}</Button> ) }
					{ this.state.showMarkersManager && ( this.renderMarkersManager() ) }
					<div style={{ backgroundColor: '#eeeeee', paddingBottom: this.state.map_height + '%'}} id={this.state.id} className="openmap-container"></div>
				</div>
				<InspectorControls>
					<PanelBody className="wkg-plugin-panelbody" title="Réglages" initialOpen={ true }>
						<PanelRow className="wkg-plugin-panelrow">
							<SelectControl
								label="Style de carte"
								value={this.state.map_style}
								options={[
									{label: 'Wikimedia', value: 'wikimedia'},
									{label: 'OpenStreetMap Mapnik', value: 'openstreetmap_mapnik'},
									{label: 'OpenMapSG Default', value: 'openmapsg_default'},
									{label: 'OpenMapSG Night', value: 'openmapsg_night'},
									{label: 'Carto DB Positron', value: 'cartodb_positron'},
									{label: 'World Imagery', value: 'world_imagery'},
									{label: 'OpenStreetMap DE', value: 'openstreetmap_de'},
									{label: 'OpenTopoMap', value: 'opentopomap'},
									{label: 'Stamen Toner', value: 'stamen_toner'},
									{label: 'Stamen Toner Light', value: 'stamen_toner_light'},
									{label: 'Stamen Terrain', value: 'stamen_terrain'},
									{label: 'Stamen Watercolor', value: 'stamen_watercolor'},
								]}
								onChange={map_style => this.onChange({map_style})}
							/>
						</PanelRow>
						<PanelRow className="wkg-plugin-panelrow">
							<SelectControl
								label="Hauteur par rapport à la largeur"
								value={this.state.map_height}
								options={[
									{label: '20%', value: 20},
									{label: '30%', value: 30},
									{label: '40%', value: 40},
									{label: '50%', value: 50},
									{label: '60%', value: 60},
									{label: '70%', value: 70},
									{label: '80%', value: 80},
									{label: '90%', value: 90},
									{label: '100%', value: 100},
								]}
								onChange={map_height => this.onChange({map_height: parseInt(map_height)})}
							/>
						</PanelRow>
						<PanelRow className="wkg-plugin-panelrow">
							<RangeControl
								label="Niveau de zoom"
								value={this.state.map_zoom}
								min={1}
								max={20}
								onChange={map_zoom => this.onChange({map_zoom: parseInt(map_zoom)})}
							/>
						</PanelRow>
						<PanelRow className="wkg-plugin-panelrow">
							<CheckboxControl
								label="Zoom avec la touche 'ctrl' seulement"
								checked={this.state.map_zoomctrl === true || this.state.map_zoomctrl === 'true'}
								onChange={map_zoomctrl => this.onChange({map_zoomctrl})}
							/>
						</PanelRow>
					</PanelBody>
					<PanelBody className="wkg-plugin-panelbody" title="Position" initialOpen={ true }>
						<PanelRow className="wkg-plugin-panelrow">
							<TextControl
								label="Latitude du centre de la carte"
								value={this.state.map_lat}
								type="number"
								onChange={map_lat => this.onChange({map_lat: parseFloat(map_lat)})}
							/>
						</PanelRow>
						<PanelRow className="wkg-plugin-panelrow">
							<TextControl
								label="Longitude du centre de la carte"
								value={this.state.map_lng}
								type="number"
								onChange={map_lng => this.onChange({map_lng: parseFloat(map_lng)})}
							/>
						</PanelRow>
					</PanelBody>
				</InspectorControls>
			</Fragment>
		)
	}
}

class MarkerItemEdit  extends Component {
	constructor(props) {
		super(props)
		this.state = {
			editMode: !this.props.marker,
			isNew: !this.props.marker,
			notice: false,
			title: '',
			address: '',
			lat: '',
			lng: '',
		}
	}
	componentDidMount () {
		this.loadFields();
	}
	save() {
		let marker = this.state.isNew ? {} : this.props.marker;
		// validation
		const errors = this.validate();
		if (errors) {
			this.setState({notice: errors.join(', ')});
		} else {
			marker = {...marker, ...{
				title: this.state.title,
				address: this.state.address,
				lat: parseFloat(this.state.lat),
				lng: parseFloat(this.state.lng)
			}};
			this.props.onChange(marker);
			this.setState({
				notice: false,
			});
			if (!this.state.isNew) {
				// change to view mode
				this.setState({
					editMode: false,
				});
			} else {
				// clear fields
				this.clearFields();
			}
		}
	}
	validate () {
		let errors = [];
		if (!this.state.title || this.state.title == '') {
			errors.push("Vous devez saisir un titre");
		}
		if (!this.state.lat || this.state.lat == '') {
			errors.push("Vous devez saisir une latitude");
		}
		if (!this.state.lng || this.state.lng == '') {
			errors.push("Vous devez saisir une longitude");
		}
		if (errors.length > 0) {
			return errors;
		}
		return false;
	}
	cancel () {
		if (this.state.isNew) {
			this.clearFields(); // clear fields for new marker canceled
		} else {
			this.setState({editMode: false}); // close form
			this.loadFields(); // load initial values in fields for update marker canceled
		}
		if (this.props.onCancel) {
			this.props.onCancel();
		}
	}
	remove() {
		if (confirm("Voulez-vous vraiment supprimer ce poitn de la carte ?")) {
			this.props.onRemove(this.props.marker);
		}
	}
	loadFields () {
		this.setState({
			title: this.props.marker ? this.props.marker.title : '',
			address: this.props.marker ? this.props.marker.address : '',
			lat: this.props.marker ? this.props.marker.lat : '',
			lng: this.props.marker ? this.props.marker.lng : '',
		});
	}
	clearFields () {
		this.setState({
			title: '',
			address: '',
			lat: '',
			lng: '',
		});
	}
	render () {
		return (
			<div className="marker-item-edit">
				{ !this.state.editMode && (
					<div className="markerText">
						<div className="infos">
							{this.state.title} - {this.state.address} ({this.state.lat}/{this.state.lng})
						</div>
						<div className="ctrl">
							<Button isSecondary onClick={() => this.setState({editMode: true})}>Modifier</Button>
						</div>
					</div>
				) }
				{ this.state.editMode && (
					<div className="editForm">
						{ this.state.isNew && (<div className="formTitle">Nouveau point sur la carte</div>) }
						<div className="fields-group">
							<TextControl value={this.state.title} placeholder="Titre" onChange={(title) => this.setState({title})} />
							<TextControl value={this.state.address} placeholder="Adresse" onChange={(address) => this.setState({address})} />
						</div>
						<div className="fields-group">
							<TextControl type="number" min="0" max="99" step="0.00000000000001" value={this.state.lat} placeholder="Latitude" onChange={(lat) => this.setState({lat})} />
							<TextControl type="number" min="0" max="99" step="0.00000000000001" value={this.state.lng} placeholder="Longitude" onChange={(lng) => this.setState({lng})} />
						</div>
						<div className="ctrl">
							<Button isSecondary onClick={() => this.save()}>Valider</Button>
							<Button isSecondary onClick={() => this.cancel()}>Annuler</Button>
						</div>
						{ this.state.notice && (
							<Notice status="error" onRemove={ () => this.setState({notice: false}) }>{ this.state.notice }</Notice>
						) }
					</div>
				) }
				{ !this.state.isNew && !this.state.editMode && (
					<Button className="btn-remove" isDestructive onClick={() => this.remove()}>X</Button>
				) }
			</div>
		)
	}
}

class MarkersFromJson extends Component {
	constructor(props) {
		super(props)
		this.state = {
			jsonText: '',
			notice: false,
		}
	}

	save() {
		// parse JSON
		let markers = this.state.jsonText.replace(/\r\n/g, '').replace(/\r/g, '').replace(/\n/g, '');
		try {
      markers = JSON.parse(markers);
    } catch(e) {
      alert("Format JSON invalide : " + e);
			return false;
    }
		// validation
		const errors = this.validate(markers);
		if (errors) {
			this.setState({notice: errors.join(', ')});
		} else {
			this.props.onChange(markers);
			this.setState({
				notice: false,
			});
			// clear fields
			this.clearFields();
		}
	}
	validate (markers) {
		let errors = [];
		markers.map((marker, i) => {
			if (!marker.title || marker.title == '') {
				errors.push("Titre manquant");
			} else if (!marker.lat || marker.lat == '') {
				errors.push("Latitude manquante");
			} else if (!marker.lng || marker.lng == '') {
				errors.push("Longitude manquante");
			}
		});
		if (errors.length > 0) {
			return errors;
		}
		return false;
	}
	cancel () {
		this.clearFields();
		if (this.props.onCancel) {
			this.props.onCancel();
		}
	}
	clearFields () {
		this.setState({jsonText: ''});
	}
	render () {
		return (
			<div className="markers-from-json">
				<TextareaControl
	        label="JSON"
	        help="Saisissez votre JSON"
	        value={ this.state.jsonText }
	        onChange={ ( jsonText ) => this.setState( { jsonText } ) }
		    />
				<div className="ctrl">
					<Button isSecondary onClick={() => this.save()}>Valider</Button>
					<Button isSecondary onClick={() => this.cancel()}>Annuler</Button>
				</div>
				{ this.state.notice && (
					<Notice status="error" onRemove={ () => this.setState({notice: false}) }>{ this.state.notice }</Notice>
				) }
			</div>
		)
	}
}

const styles = {}
