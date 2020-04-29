import classnames from 'classnames'
const { registerBlockType } = wp.blocks
const { Component, Fragment } = wp.element
const { InspectorControls, MediaUpload } = wp.blockEditor
const { PanelBody, PanelRow, Button, SelectControl, RangeControl, TextControl, ResizableBox, ToggleControl } = wp.components
const { compose, withInstanceId } = wp.compose
const { withSelect, withDispatch } = wp.data
const { apiFetch } = wp

/* Masonry documentation :
- https://www.npmjs.com/package/react-masonry-component
- https://www.npmjs.com/package/masonry-layout
- https://masonry.desandro.com/options.html#element-sizing
*/
import Masonry from 'react-masonry-component'

const MIN_SPACER_HEIGHT = 0
const MAX_SPACER_HEIGHT = 120

/**
 * Wall Block
 * 
 */
registerBlockType('wkg/wall', {
	title: 'WKG Wall',
	category: 'common',
	icon: 'media-spreadsheet',
	supports: {
		html: false,
		reusable: false,
		align: ['full', 'wide'],
	},
	attributes: {
		id: {
			type: 'string',
		},
		items: {
			type: 'string', // as object => https://developer.wordpress.org/block-editor/developers/block-api/block-attributes/#considerations
			default: '[]'
		},
		items_source: {
			type: 'string',
			default: '_none'
		},
		items_query: {
			type: 'string', // as object => https://developer.wordpress.org/block-editor/developers/block-api/block-attributes/#considerations
			default: JSON.stringify({
				orderby: 'date',
				order: 'desc',
				numberposts: -1,
				terms: [],
				post_parent: -1
			})
		},
		display: {
			type: 'string',
			default: 'grid'
		},
		thumbsize: {
			type: 'string',
			default: 'large'
		},
		format: {
			type: 'string',
			default: 'square'
		},
		filter: {
			type: 'string',
			default: 'none'
		},
		filter_multiple: {
			type: 'boolean',
			default: false
		},
		columns: {
			type: 'number',
			default: 3
		},
		maxwidth: {
			type: 'string',
			default: '25%',
		},
		maxwidth_custom: {
			type: 'number',
			default: 250,
		},
		maxheight: {
			type: 'string',
			default: 'auto',
		},
		maxheight_custom: {
			type: 'number',
			default: 250,
		},
		margin_horizontal: {
			type: 'number',
			default: 3
		},
		margin_vertical: {
			type: 'number',
			default: 3
		},
		space_before: {
			type: 'number',
			default: 0
		},
		space_after: {
			type: 'number',
			default: 0
		}
	},
	edit: function (props) {
		// console.log('attributes : ', props.attributes)
		props.attributes.id = 'wkg' + props.clientId
		props.className += " wkg-editor wkg-item"
		return (
			<div className={props.className}>
				<h3 className="wkg-title">
					Wall
					<span className="wkg-info">(Affiche un mur d'éléments)</span>
				</h3>
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

/**
 * Wall Component
 * @extends Component
 */
class BlockComponent_Base extends Component {
	constructor(props) {
		super(props)
		this.state = {...this.props.attributes, ...{
			post_type_options: [], // static
			term_options: [], // static
			loading: false,
			items_parents_options: [], // dynamic
			items_terms_options: [], // dynamic
		}}
	}
	componentDidMount () {
		this.load_items()
	}
	componentDidUpdate (prevProps, prevState) {
		let update_content = prevState.items_source !== this.state.items_source || prevState.items_query !== this.state.items_query || prevState.thumbsize !== this.state.thumbsize
		if (update_content) {
			this.load_items()
		}
	}
	get_items (default_value = null) {
		let items = default_value
		if (this.state.items) {
			try {
				items = JSON.parse(this.state.items)
			} catch(e) {
				console.error(e)
			}
		}
		return items
	}
	get_items_query (attribute = null, default_value = null) {
		let items_query = null
		if (this.state.items_query) {
			try {
				items_query = JSON.parse(this.state.items_query)
			} catch(e) {
				console.error(e)
			}
			if (items_query && attribute) {
				return items_query[attribute] ? items_query[attribute] : default_value
			}
			return items_query ? items_query : default_value
		}
		return default_value
	}
	async load_items () {
		await this.setState({loading: true})
		const items_source = this.state.items_source ? this.state.items_source : null
		if (items_source && items_source !== 'attachment' && items_source !== '_custom') {
			// Pour une source dynamique, on fetch notre rest-api pour récupérer les éléments nécessaires :
			// - les items à afficher (en fonction des critères de requêtage)
			// - les termes de taxonomies du type de contenu
			// - les parents (dans le cas d'un type de contenu hierarchique)
			const orderby = this.get_items_query('orderby')
			const order = this.get_items_query('order')
			const numberposts = this.get_items_query('numberposts')
			const terms = this.get_items_query('terms')
			const post_parent = this.get_items_query('post_parent')
			let args = {
				post_type: items_source,
				orderby,
				order,
				numberposts,
				thumbsize: this.state.thumbsize,
				post_parent,
				terms,
			}
			const query_string = Object.keys(args).map(function(key) {
        return key + '=' + args[key];
      }).join('&');
			apiFetch({ path: '/wkg/v1/wall/items/?' + query_string }).then(items => {
				this.setState({
					loading: false,
					items_parents_options: items.items_parents,
					items_terms_options: items.items_terms,
				})
				this.onDataItemsChange(items.items)
			}).catch(() => {
				this.setState({loading: false})
			})
		} else if (items_source && (items_source === 'attachment' || items_source === '_custom')) {
			// Pour une source statique, on vide les champs de requête, plus loin le système affichera l'interface de sélection
			this.setState({
				loading: false,
				items_parents_options: [],
				items_terms_options: [],
			})
		} else {
			// Aucune source, on vide tout
			this.setState({
				loading: false,
				items_parents_options: [],
				items_terms_options: [],
			})
			this.onDataItemsChange(null)
		}
	}
	onDataItemsChange (data_items) {
		if (data_items) {
			let newItems = []
			for (var i = 0; i < data_items.length; i++) {
				const data_item = data_items[i]
				let existing_items = this.get_items([])
				if (isset(existing_items[i])) {
					newItems.push({...existing_items[i], ...{data: data_item}})
				} else {
					newItems.push({id: i + 1, data: data_item})
				}
			}
			this.onChange({items: newItems})
		} else {
			this.onChange({items: []})
		}
	}
	onImagesChange (images) {
		let data_items = []
		if (images) {
			for (var i = 0; i < images.length; i++) {
				data_items.push({...images[i], ...{post_type: 'attachment'}}) // post_type is important for further tests
			}
		}
		this.onDataItemsChange(data_items)
	}
	onChange (obj) {
		if (obj.items_source !== undefined) {
			// if source changes to 'attachment' or '_custom' : clear items
			if (obj.items_source === 'attachment' || obj.items_source === '_custom') {
				obj.items = []
			}
		}
		if (obj.items !== undefined) {
			try {
				obj.items = JSON.stringify(obj.items)
			} catch(e) {
				console.error(e)
			}
		}
		if (obj.items_query !== undefined) {
			try {
				obj.items_query = JSON.stringify(obj.items_query)
			} catch(e) {
				console.error(e)
			}
		}
		this.setState(obj)
		this.props.onChange(obj)
	}
	onItemChange (itemId, changedItem) {
		let items = this.get_items([])
		items = items.map((item) => {
			if (item.id === itemId) {
				return {...item, ...changedItem}
			}
			return item
		})
		this.onChange({items})
	}
	render_IC__items_source () {
		let items_source_options = [{value: '_none', label: 'Chargement...'}]
		if (this.props.post_types) {
			items_source_options = [{value: '_none', label: 'Choisissez...'}]
			for (var post_type of this.props.post_types) {
				if (post_type.slug !== 'wp_block') {
					items_source_options.push({value: post_type.slug, label: post_type.name})
				}
			}
			items_source_options.push({value: '_custom', label: 'Personnalisée'})
		}
		const items_source = this.state.items_source ? this.state.items_source : 0
		return (
			<PanelRow className="wkg-ic-panelrow">
				<div style={{marginBottom: '12px'}} className="wkg-info">
					Choisissez la source des éléments à afficher dans le block.
				</div>
				<SelectControl label="Source" value={items_source} options={items_source_options} onChange={(items_source) => this.onChange({items_source})} />
			</PanelRow>
		)
	}
	render_IC__items_request () {
		const items_source = this.state.items_source ? this.state.items_source : null
		if (items_source && items_source !== '_none' && items_source !== 'attachment' && items_source !== '_custom') {
			let orderby_options = [
				{value: 'none', label: 'Aucun tri'},
				{value: 'ID', label: 'Id'},
				{value: 'author', label: 'Auteur'},
				{value: 'title', label: 'Titre'},
				{value: 'date', label: 'Date'},
				{value: 'modified', label: 'Date de modification'},
				{value: 'rand', label: 'Aléatoire'},
				{value: 'menu_order', label: 'Numéro d\'ordre'},
			]
			let order_options = [
				{value: 'desc', label: 'Descandant'},
				{value: 'asc', label: 'Ascendant'},
			]
			let render_items_parents_options = null
			if (this.state.items_parents_options) {
				render_items_parents_options = (
					<PanelRow className="wkg-ic-panelrow">
						<SelectControl label="Fils de" value={this.get_items_query('post_parent')} options={this.state.items_parents_options} onChange={(post_parent) => this.onChange({items_query: {...this.get_items_query(), ...{post_parent}}})} />
					</PanelRow>
				)
			}
			let render_items_terms_options = null
			if (this.state.items_terms_options) {
				render_items_terms_options = (
					<PanelRow className="wkg-ic-panelrow">
						<SelectControl label="Filter sur" value={this.get_items_query('terms')} options={this.state.items_terms_options} onChange={(terms) => this.onChange({items_query: {...this.get_items_query(), ...{terms}}})} />
					</PanelRow>
				)
			}
			let numberposts = this.get_items_query('numberposts')
			let render_numberposts = [(
				<PanelRow className="wkg-ic-panelrow">
					<ToggleControl label="Limiter le nombre d'élément ?" checked={(numberposts >= 0)} onChange={() => this.onChange({items_query: {...this.get_items_query(), ...{numberposts: numberposts >= 0 ? -1 : 5}}})} />
				</PanelRow>
			)]
			if (numberposts >= 0) {
				render_numberposts.push((
					<PanelRow className="wkg-ic-panelrow">
						<TextControl type="number" label="Limite" value={numberposts} onChange={(numberposts) => this.onChange({items_query: {...this.get_items_query(), ...{numberposts: parseInt(numberposts)}}})} />
					</PanelRow>
				))
			}
			return (
				<Fragment>
					{render_items_parents_options}
					{render_items_terms_options}
					<PanelRow className="wkg-ic-panelrow">
						<SelectControl label="Trier par" value={this.get_items_query('orderby')} options={orderby_options} onChange={(orderby) => this.onChange({items_query: {...this.get_items_query(), ...{orderby}}})} />
					</PanelRow>
					<PanelRow className="wkg-ic-panelrow">
						<SelectControl label="Tri" value={this.get_items_query('order')} options={order_options} onChange={(order) => this.onChange({items_query: {...this.get_items_query(), ...{order}}})} />
					</PanelRow>
					{render_numberposts}
				</Fragment>
			)
		} else if (items_source && items_source === 'attachment') {
			const items = this.get_items([])
			const images = items.map((item) => {
				if (item.data && item.data.post_type === 'attachment') {
					return item.data.id
				}
			})
			const hasImages = images && images.length > 0
			return (
				<PanelRow className="wkg-ic-panelrow">
					<div style={{marginBottom: '12px'}} className="wkg-info">
						Choisissez vos images ou modifiez-les à partir d'ici.
					</div>
					<MediaUpload
						gallery={true}
						onSelect={(images) => this.onImagesChange(images)}
						allowedTypes={['image']}
						multiple={true}
						value={hasImages ? images : undefined}
						render={({ open }) => (
							<Button
								isSecondary
								isLarge
								onClick={ (e) => {
									e.stopPropagation()
									open()
								}}
							>
								{hasImages ? 'Modifier la galerie' : 'Créer la galerie'}
							</Button>
						)}
					/>
				</PanelRow>
			)
		} else if (items_source && items_source === '_custom') {
			return (
				<PanelRow className="wkg-ic-panelrow">
					Cette fonctionnalité est en cours de développement, merci de votre compréhension.
				</PanelRow>
			)
		}
		return null
	}
	render_IC__display () {
		let options = [
			{value: 'grid', label: 'Grille'},
			{value: 'masonry', label: 'Masonry'}
		]
		return (
			<PanelRow className="wkg-ic-panelrow">
				<SelectControl label="Affichage" value={this.state.display} options={options} onChange={(display) => this.onChange({display})} />
			</PanelRow>
		)
	}
	render_IC__filter () {
		const items_source = this.state.items_source ? this.state.items_source : null
		let options = [
			{value: 'none', label: 'Aucun filtre'},
			{value: 'taxonomy', label: 'Taxonomies'},
			{value: 'search', label: 'Champs de recherche'}
		]
		let render_filter_multiple = null
		if (this.state.filter && this.state.filter === 'taxonomy') {
			render_filter_multiple = (
				<PanelRow className="wkg-ic-panelrow">
					<ToggleControl label="Sélection multiple des filtres" checked={this.state.filter_multiple} onChange={() => this.onChange({filter_multiple : !this.state.filter_multiple})} />
				</PanelRow>
			)
		}
		return (
			<Fragment>
				<PanelRow className="wkg-ic-panelrow">
					<SelectControl label="Afficher un filtre" value={this.state.filter} options={options} onChange={(filter) => this.onChange({filter})} />
				</PanelRow>
				{render_filter_multiple}
			</Fragment>
		)
	}
	render_IC__format () {
		if (this.state.display === 'grid') {
			let options = [
				{value: 'square', label: 'Carré'},
				{value: 'landscape', label: 'Paysage'},
				{value: 'portrait', label: 'Portrait'}
			]
			return (
				<PanelRow className="wkg-ic-panelrow">
					<SelectControl label="Format" value={this.state.format} options={options} onChange={(format) => this.onChange({format})} />
				</PanelRow>
			)
		}
		return null
	}
	render_IC__thumbsize () {
		let options = [
			{value: 'large', label: 'Large'},
			{value: 'medium', label: 'Moyenne'},
			{value: 'small', label: 'Petite'},
			{value: 'thumbnail', label: 'Vignette'},
			{value: 'full', label: 'Originale'}
		]
		return (
			<PanelRow className="wkg-ic-panelrow">
				<SelectControl label="Taille des images" value={this.state.thumbsize} options={options} onChange={(thumbsize) => this.onChange({thumbsize})} />
			</PanelRow>
		)
	}
	render_IC__columns () {
		if (this.state.display === 'grid') {
			const min = 1
			const max = 6
			return (
				<PanelRow className="wkg-ic-panelrow">
					<RangeControl
						label="Colonnes"
						min={ min }
						max={ max }
						separatorType={ 'none' }
						value={ this.state.columns }
						onChange={(columns) => this.onChange({columns})}
						step={ 1 }
					/>
				</PanelRow>
			)
		}
		return null
	}
	render_IC__maxwidth () {
		if (this.state.display === 'masonry') {
			const maxwidth_options = [
				{value: '100%', label: '100%'},
				{value: '50%', label: '50%'},
				{value: '33.333333%', label: '33.33%'},
				{value: '25%', label: '25%'},
				{value: '20%', label: '20%'},
				{value: '16.666666%', label: '16.66%'},
				{value: 'custom', label: 'Personnalisée'},
			]
			let render_maxwidth_custom = null
			if (this.state.maxwidth === 'custom') {
				render_maxwidth_custom = (
					<PanelRow className="wkg-ic-panelrow">
						<TextControl label="Largeur max. personnalisée (px)" type="number" value={this.state.maxwidth_custom} onChange={(maxwidth_custom) => this.onChange({maxwidth_custom})} />
					</PanelRow>
				)
			}
			return (
				<Fragment>
					<PanelRow className="wkg-ic-panelrow">
						<SelectControl
							label="Largeur max."
							value={ this.state.maxwidth }
							onChange={(maxwidth) => this.onChange({maxwidth})}
							options={maxwidth_options}
						/>
					</PanelRow>
					{render_maxwidth_custom}
				</Fragment>
			)
		}
		return null
	}
	render_IC__maxheight () {
		if (this.state.display === 'masonry') {
			const maxheight_options = [
				{value: 'auto', label: 'Auto'},
				{value: 'custom', label: 'Personnalisée'},
			]
			let render_maxheight_custom = null
			if (this.state.maxheight === 'custom') {
				render_maxheight_custom = (
					<PanelRow className="wkg-ic-panelrow">
						<TextControl label="Hauteur max. personnalisée (px)" type="number" value={this.state.maxheight_custom} onChange={(maxheight_custom) => this.onChange({maxheight_custom})} />
					</PanelRow>
				)
			}
			return (
				<Fragment>
					<PanelRow className="wkg-ic-panelrow">
						<SelectControl
							label="Hauteur max."
							value={ this.state.maxheight }
							onChange={(maxheight) => this.onChange({maxheight})}
							options={maxheight_options}
						/>
					</PanelRow>
					{render_maxheight_custom}
				</Fragment>
			)
		}
		return null
	}
	render_IC__margins () {
		const min = 0
		const max = 60
		return (
			<PanelRow className="wkg-ic-panelrow">
				<RangeControl
					label="Marges horizontales (px)"
					min={ min }
					max={ max }
					value={ this.state.margin_horizontal }
					onChange={(margin_horizontal) => this.onChange({margin_horizontal})}
					step={ 1 }
				/>
				<RangeControl
					label="Marges verticales (px)"
					min={ min }
					max={ max }
					value={ this.state.margin_vertical }
					onChange={(margin_vertical) => this.onChange({margin_vertical})}
					step={ 1 }
				/>
			</PanelRow>
		)
	}
	render_IC_spacer () {
		return (
			<Fragment>
				<PanelRow className="wkg-ic-panelrow">
					<RangeControl
						label="Espace avant (px)"
						min={ MIN_SPACER_HEIGHT }
						max={ MAX_SPACER_HEIGHT }
						separatorType={ 'none' }
						value={ this.state.space_before }
						onChange={(space_before) => this.onChange({space_before})}
					/>
				</PanelRow>
				<PanelRow className="wkg-ic-panelrow">
					<RangeControl
						label="Espace après (px)"
						min={ MIN_SPACER_HEIGHT }
						max={ MAX_SPACER_HEIGHT }
						separatorType={ 'none' }
						value={ this.state.space_after }
						onChange={(space_after) => this.onChange({space_after})}
					/>
				</PanelRow>
			</Fragment>
		)
	}
	render_IC () {
		let loading = this.state.loading ? (<div className="wall-loading"></div>) : null
		return (
			<InspectorControls className="wkg-ic">
				<PanelBody title="Choix des élements" initialOpen={ true } className="wkg-ic-panelbody">
					{this.render_IC__items_source()}
					{this.render_IC__items_request()}
					{loading}
				</PanelBody>
				<PanelBody title="Présentation des éléments" initialOpen={ false } className="wkg-ic-panelbody">
					{this.render_IC__display()}
					{this.render_IC__format()}
					{this.render_IC__thumbsize()}
					{this.render_IC__columns()}
					{this.render_IC__maxwidth()}
					{this.render_IC__maxheight()}
					{this.render_IC__margins()}
					{loading}
				</PanelBody>
				<PanelBody title="Autres..." initialOpen={ false } className="wkg-ic-panelbody">
					{this.render_IC_spacer()}
					{this.render_IC__filter()}
					{loading}
				</PanelBody>
			</InspectorControls>
		)
	}
	render_content() {
		const items_source = this.state.items_source ? this.state.items_source : null
		const items = this.get_items([])
		let columns = this.state.columns ? parseInt(this.state.columns) : 1
		let margin_horizontal = this.state.margin_horizontal ? parseInt(this.state.margin_horizontal) : 0
		let margin_vertical = this.state.margin_vertical ? parseInt(this.state.margin_vertical) : 0
		let loading = this.state.loading ? (<div className="wall-loading"></div>) : null

		let ulClassColumns = ''
		if (this.state.display === 'masonry') {
			ulClassColumns = this.state.maxwidth !== 'custom' && this.state.maxwidth.includes('%') ? 'columns-' + Math.floor(100 / parseFloat(this.state.maxwidth.replace('%', ''))) : ''
		} else {
			ulClassColumns = 'columns-' + columns
		}
		let ulClasses = classnames('wall', this.state.display, ulClassColumns)

		// masonry
		let masonry_grid_li = null
		let masonry_options = {}
		if (this.state.display === 'masonry') {
			if (this.state.maxwidth === 'custom') {
				masonry_options.columnWidth = this.state.maxwidth_custom
			} else {
				// ce li sert à Masonry à déterminer la largeur de base pour l'affichage en pourcentage
				// https://masonry.desandro.com/options.html#element-sizing
				masonry_grid_li = (
					<li className="masonry-grid-li" style={{width: this.state.maxwidth}}></li>
				)
				masonry_options.percentPosition = true
			}
		} else {
			// ce li sert à Masonry à déterminer la largeur de base pour l'affichage en pourcentage
			// https://masonry.desandro.com/options.html#element-sizing
			masonry_grid_li = (
				<li className="masonry-grid-li" style={{width: (100 / columns) + '%'}}></li>
			)
			masonry_options.percentPosition = true
		}
		// filters
		let render_filter = null
		if (this.state.filter === 'taxonomy') {
			render_filter = (
				<div className="wall-filter taxonomy">
					{this.state.items_terms_options.map((item) => {
						if (!item.disabled) {
							return (
								<span className="filter-item">{item.label}</span>
							)
						}
					})}
				</div>
			)
		} else if (this.state.filter === 'search') {
			render_filter = (
				<div className="wall-filter search"><input type="search" disabled placeholder="filtre" /></div>
			)
		}

		return (
			<div className="content">

				<ResizableBox
					style={styles.spacer}
					className={ classnames({'is-selected': this.props.isSelected}) }
					size={{height: this.state.space_before}}
					minHeight={ MIN_SPACER_HEIGHT }
					enable={ { top: true, right: false, bottom: false, left: false, topRight: false, bottomRight: false, bottomLeft: false, topLeft: false, } }
					isSelected={ this.props.isSelected }
					onResizeStart={ this.props.onResizeStart }
					onResizeStop={ ( event, direction, elt, delta ) => {
						this.props.onResizeStop()
						const space = Math.min(parseInt(this.state.space_before + delta.height, 10 ), MAX_SPACER_HEIGHT)
						this.onChange({space_before: space})
					}}
				/>

				{render_filter}

				<Masonry
					className={ulClasses} // default ''
					elementType={'ul'} // default 'div'
					options={masonry_options} // default {}
					disableImagesLoaded={false} // default false
					updateOnEachImageLoad={false} // default false and works only if disableImagesLoaded is false
					imagesLoadedOptions={{}} // default {}
					style={{width: 'calc(100% + ' + margin_horizontal + 'px)', marginLeft: '-' + margin_horizontal + 'px', marginTop: '-' + margin_vertical + 'px'}}
				>
					{masonry_grid_li}
					{items.map((item) =>
		        <BlockComponent_Item
							onChange={(itemId, item) => this.onItemChange(itemId, item)}
							key={item.id}
							itemId={item.id}
							item={item}
							display={this.state.display}
							format={this.state.format}
							columns={this.state.columns}
							maxwidth={this.state.maxwidth !== 'custom' ? this.state.maxwidth : this.state.maxwidth_custom + 'px'}
							maxheight={this.state.maxheight === 'auto' ? this.state.maxheight : this.state.maxheight_custom + 'px'}
							spacing={{horizontal: margin_horizontal, vertical: margin_vertical}}
							thumbsize={this.state.thumbsize}
						/>
		      )}
				</Masonry>

				{loading}

				<ResizableBox
					style={styles.spacer}
					className={ classnames({'is-selected': this.props.isSelected}) }
					size={{height: this.state.space_after}}
					minHeight={ MIN_SPACER_HEIGHT }
					enable={ { top: false, right: false, bottom: true, left: false, topRight: false, bottomRight: false, bottomLeft: false, topLeft: false, } }
					isSelected={ this.props.isSelected }
					onResizeStart={ this.props.onResizeStart }
					onResizeStop={ ( event, direction, elt, delta ) => {
						this.props.onResizeStop()
						const space = Math.min(parseInt(this.state.space_after + delta.height, 10 ), MAX_SPACER_HEIGHT)
						this.onChange({space_after: space})
					}}
				/>
			</div>
		)
	}
	render () {
		return (
			<div>
				<div className="wkg-content">
					{this.render_content()}
					{this.render_IC()}
				</div>
			</div>
		)
	}
}

const BlockComponent = compose([withSelect(select => {
	const posts_options = select('wkg/commons').getPostsOptions()
	const terms_options = select('wkg/commons').getTermsOptions()
	const { getPostTypes } = select('core')
  return {
		post_types: getPostTypes(),
		posts_options,
		terms_options
	}
}), withDispatch((dispatch) => {
	const { createNotice } = dispatch('core/notices')
	const { toggleSelection } = dispatch('core/block-editor')
	return {
		noticeError: (message) => createNotice('error', message),
		onResizeStart: () => toggleSelection( false ),
		onResizeStop: () => toggleSelection( true ),
	}
}), withInstanceId])(BlockComponent_Base)

/**
 * Wall Item Component
 * @extends Component
 */
class BlockComponent_Item extends Component {
	constructor(props) {
		super(props)
		this.state = {}
	}
	componentDidMount () {
		if (this.props.itemId === undefined) {
			throw new Error('BlockComponent_Item must have \'itemId\' props')
		}
		if (this.props.item === undefined) {
			throw new Error('BlockComponent_Item must have \'item\' props')
		}
	}
	onChange (obj) {
		this.props.onChange(this.props.itemId, {...this.props.item, ...obj})
	}
	render_content () {
		if (this.props.item.data.post_type === 'attachment') {
			let image = null
			if (this.props.item.data.sizes && this.props.thumbsize && this.props.item.data.sizes[this.props.thumbsize]) {
				image = this.props.item.data.sizes[this.props.thumbsize]
			} else if (this.props.item.data.sizes && this.props.item.data.sizes['full']) {
				image = this.props.item.data.sizes['full']
			} else {
				image = {url: this.props.item.data.url}
			}
			const src = image.url
			const width = image.width ? image.width : null
			const height = image.height ? image.height : null
			return (
				<img className="thumb" src={src} width={width} height={height} />
			)
		} else {
			const custom_content = this.props.item.custom_content ? this.props.item.custom_content : 'thumb'
			if (custom_content === 'thumb' && this.props.item.data && this.props.item.data.thumb && this.props.item.data.thumb.source_url) {
				const src = this.props.item.data.thumb.source_url
				const width = this.props.item.data.thumb.width
				const height = this.props.item.data.thumb.height
				return (
					<img className="thumb" src={src} width={width} height={height} />
				)
			}
			let title = this.props.item.data && this.props.item.data.title ? this.props.item.data.title : null
			if (this.props.item.custom_title) {
				title = this.props.item.custom_title
			}
			const excerpt = this.props.item.data && this.props.item.data.excerpt ? this.props.item.data.excerpt : null
			return (
				<div>
					<h4 className="title">{title}</h4>
					<div className="excerpt">{excerpt}</div>
				</div>
			)
		}
	}
	render () {
		// --- props
		const columns = this.props.columns ? parseInt(this.props.columns) : 1
		const custom_columns = this.props.item.custom_columns ? parseInt(this.props.item.custom_columns) : 1
		const custom_lines = this.props.item.custom_lines ? parseInt(this.props.item.custom_lines) : 1
		const custom_content = this.props.item.custom_content ? this.props.item.custom_content : 'thumb'
		const custom_title = this.props.item.custom_title ? this.props.item.custom_title : ''
		const custom_link = this.props.item.custom_link ? this.props.item.custom_link : ''

		// --- item styles
		let itemStyle = {}
		let itemContentStyle = {
			width: '100%',
		}
		let itemInnerStyle = {
		  position: 'relative',
			marginLeft : this.props.spacing.horizontal + 'px',
			marginTop: this.props.spacing.vertical + 'px'
		}
		if (this.props.display === 'masonry') {
			itemStyle = {...itemStyle, ...{
				width: '100%',
				height: 'auto',
				maxWidth: this.props.maxwidth,
				maxHeight: this.props.maxheight !== 'auto' ? this.props.maxheight : 'none'
			}}
		} else {
			let paddingBottom = 100
			if (this.props.format === 'portrait') {
				paddingBottom *= 1.618
			} else if (this.props.format === 'landscape') {
				paddingBottom /= 1.618
			}
			paddingBottom = (paddingBottom * custom_lines) / custom_columns
			const width = (100 / columns) * custom_columns
			itemStyle = {...itemStyle, ...{width: width + '%'}}
			itemContentStyle = {
				width: '100%',
				height: 0,
				paddingBottom: paddingBottom + '%',
			}
			itemInnerStyle = {...itemInnerStyle, ...{
				position: 'absolute',
				left : '0px',
				right: '0px',
				top: '0px',
				bottom: '0px',
				overflow: 'hidden',
			}}
		}

		// --- options
		let size_options = []
		for (var i = 0; i < parseInt(this.props.columns); i++) {
			size_options.push({value: i + 1, label: '' + (i + 1)})
		}
		let content_options = [
			{value: 'thumb', label: 'Vignette'},
			{value: 'content', label: 'Contenu'}
		]
		let render_customcontent = null
		if (this.props.item.data.post_type !== 'attachment') {
			render_customcontent = (
				<div className="control-item">
					<label><i className="dashicons dashicons-welcome-view-site"></i></label>
					<SelectControl options={content_options} value={custom_content} onChange={(custom_content) => this.onChange({custom_content})} />
				</div>
			)
		}
		let render_size_options = null
		if (this.props.display === 'grid') {
			render_size_options = (
				<Fragment>
					<div className="control-item">
						<label><i className="dashicons dashicons-image-flip-horizontal"></i></label>
						<SelectControl options={size_options} value={custom_columns} onChange={(custom_columns) => this.onChange({custom_columns})} />
					</div>
					<div className="control-item">
						<label><i className="dashicons dashicons-image-flip-vertical"></i></label>
						<SelectControl options={size_options} value={custom_lines} onChange={(custom_lines) => this.onChange({custom_lines})} />
					</div>
				</Fragment>
			)
		}
		return (
			<li className="wall-item" style={itemStyle}>
				<div className="wall-item-content" style={itemContentStyle}>
					<div className="wall-item-inner" style={itemInnerStyle}>
						<div className="wall-item-control">
							{render_size_options}
							{render_customcontent}
							<div className="control-item">
								<label><i className="dashicons dashicons-edit"></i></label>
								<TextControl value={custom_title} onChange={(custom_title) => this.onChange({custom_title})} placeholder="Titre personnalisé" />
							</div>
							<div className="control-item">
								<label><i className="dashicons dashicons-external"></i></label>
								<TextControl value={custom_link} onChange={(custom_link) => this.onChange({custom_link})} placeholder="Lien personnalisé" />
							</div>
						</div>
						{this.render_content()}
					</div>
				</div>
			</li>
		)
	}
}

const styles = {
	spacer: {
		backgroundColor: '#f7f7f7',
	}
}
