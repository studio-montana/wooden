const { registerPlugin } = wp.plugins
const { __ } = wp.i18n
const { Component, Fragment } = wp.element
const { withSelect, withDispatch } = wp.data
const { dateI18n } = wp.date
const { compose } = wp.compose
const { PluginDocumentSettingPanel } = wp.editPost
const { PanelBody, PanelRow, SelectControl, Button } = wp.components
import WKG_Media_Selector from 'wkgcomponents/media-selector'

registerPlugin('wooden-plugin-breadcrumbmeta', {
  icon: '',
  render: (props) => {
    return (
      <PluginDocumentSettingPanel name="wooden-plugin-breadcrumbmeta" title={__('Breadcrumb', 'wooden')} className="wkg-document-setting-panel wooden-plugin-breadcrumbmeta">
          <PluginComponent />
      </PluginDocumentSettingPanel>
    )
  }
})

class PluginComponent_Base extends Component {
	constructor(props) {
		super(props)
	}
	render () {
		return (
			<Fragment>
        <PanelRow>
          <SelectControl label={__('Type', 'wooden')} value={this.props._breadcrumb_meta_type} onChange={(value) => this.props.on_meta_change({_breadcrumb_meta_type: value})} options={[
            { value: 'classic', label: __('Classic', 'wooden') },
            { value: 'customized', label: __('Customized', 'wooden') },
          ]} />
        </PanelRow>
        {this.props._breadcrumb_meta_type === 'customized' && (
          <PanelRow>
            <BreadcrumbItemsSelector items={this.props._breadcrumb_meta_items || []} onChange={(value) => this.props.on_meta_change({_breadcrumb_meta_items: value})} />
          </PanelRow>
        )}
			</Fragment>
		)
	}
}

const applyWithSelect = withSelect(select => {
  let core_editor_store = select('core/editor')
  return {
    _breadcrumb_meta_type:   core_editor_store.getEditedPostAttribute('meta')['_breadcrumb_meta_type'],
    _breadcrumb_meta_items:  core_editor_store.getEditedPostAttribute('meta')['_breadcrumb_meta_items'],
  }
})

const applyWithDispatch = withDispatch(dispatch => {
  let core_editor_store = dispatch('core/editor')
  return {
    on_meta_change: (meta) => {
      console.log('on_meta_change : ', meta)
      core_editor_store.editPost({meta})
    },
  }
})

const PluginComponent = compose(
    applyWithSelect,
    applyWithDispatch,
)(PluginComponent_Base)

const styles = {}

class BreadcrumbItemsSelector extends Component {
  constructor(props) {
		super(props)
	}
  getUniqueId (id = 0) {
    if (this.props.items.filter(item => item.id === id).length > 0) {
      id += 1
      return this.getUniqueId(id)
    }
    return id
  }
  async addItem () {
    this.props.onChange([...this.props.items, {id: this.getUniqueId(), type: 'post', value: ''}])
  }
  async removeItem (id) {
    this.props.onChange(this.props.items.filter(item => item.id !== id))
  }
  updateItem (updatedItem) {
    this.props.onChange(this.props.items.map(item => {
      return item.id === updatedItem.id ? updatedItem : item
    }))
  }
	render () {
    let items = []
    for (var item of this.props.items) {
      items.push(<BreadcrumbItemsSelectorItem key={item.id} item={item} onRemove={(id) => this.removeItem(id)} onChange={(item) => this.updateItem(item)} />)
    }
		return (
			<Fragment>
        <div style={bc_styles.items}>
          <div style={bc_styles.item}>{__('Home', 'wooden')}</div>
          {items}
        </div>
        <Button isSecondary onClick={() => this.addItem()}>{__('Add breadcrumb item', 'wooden')}</Button>
			</Fragment>
		)
	}
}

class BreadcrumbItemsSelectorItem extends Component {
  constructor(props) {
		super(props)
	}
	render () {
    /**
     <WKG_SelectAnyControl
       available_posttypes={['post', 'page']}
       available_taxonomies={['category', 'tag']}
       value={this.props.item.value}
       onChange={value => this.props.onChange(value)}
     />
     */
		return (
			<Fragment>
        <div style={bc_styles.item}>
          {this.props.item.value}
          <SelectControl label={__('Type', 'wooden')} value={this.props.item.type} onChange={(value) => this.props.onChange({...this.props.item, ...{ type: value }})} options={[
            { value: 'post', label: __('Post', 'wooden') },
            { value: 'tax', label: __('Tax', 'wooden') },
          ]} />
          <Button isSecondary onClick={() => this.props.onRemove(this.props.item.id)}>{__('Remove', 'wooden')}</Button>
        </div>
			</Fragment>
		)
	}
}

const bc_styles = {
  items: {
    display: 'block',
  },
  item: {
    display: 'block',
  }
}
