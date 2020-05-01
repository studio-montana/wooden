const { registerPlugin } = wp.plugins
const { __ } = wp.i18n
const { Component, Fragment } = wp.element
const { withSelect, withDispatch } = wp.data
const { dateI18n } = wp.date
const { compose } = wp.compose
const { PluginDocumentSettingPanel } = wp.editPost
const { PanelBody, PanelRow, SelectControl, Button, Icon } = wp.components
import WKG_Entity_Selector from 'wkgcomponents/entity-selector'

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
        <PanelRow className="wkg-plugin-panelrow">
          <SelectControl label={__('Type', 'wooden')} value={this.props._breadcrumb_meta_type} onChange={(value) => this.props.on_meta_change({_breadcrumb_meta_type: value})} options={[
            { value: 'classic', label: __('Classic', 'wooden') },
            { value: 'customized', label: __('Customized', 'wooden') },
          ]} />
        </PanelRow>
        {this.props._breadcrumb_meta_type === 'customized' && (
          <PanelRow className="wkg-plugin-panelrow">
            <div className="wkg-info">{__('Compose your custom breacrumb', 'wooden')}</div>
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
  getUniqueKey (key = 0) {
    if (this.props.items.filter(item => item.key === key).length > 0) {
      key += 1
      return this.getUniqueKey(key)
    }
    return key
  }
  async addItem () {
    this.props.onChange([...this.props.items, {key: this.getUniqueKey(), type: 'post', id: 0}])
  }
  async removeItem (key) {
    this.props.onChange(this.props.items.filter(item => item.key !== key))
  }
  updateItem (updatedItem) {
    this.props.onChange(this.props.items.map(item => {
      return item.key === updatedItem.key ? updatedItem : item
    }))
  }
	render () {
    let items = []
    for (var item of this.props.items) {
      if (!item.key && item.key !== 0) {
        console.warn('BreadcrumbItemsSelector - item\'s key is missing : ', item)
      } else {
        items.push(<BreadcrumbItemsSelectorItem key={item.key} item={item} onRemove={(key) => this.removeItem(key)} onChange={(item) => this.updateItem(item)} />)
      }
    }
		return (
			<Fragment>
        <div className="wrapper">
          <div className="items">
            <div className="item item-home">
              <Icon icon="arrow-down-alt" className="item-arrow" />
              {__('Home', 'wooden')}
            </div>
            {items}
          </div>
          <Button className="add" isSecondary onClick={() => this.addItem()}>{__('Add breadcrumb item', 'wooden')}</Button>
        </div>
			</Fragment>
		)
	}
}

class BreadcrumbItemsSelectorItem extends Component {
  constructor(props) {
		super(props)
    this.state = {
      yop: null
    }
	}
	render () {
		return (
			<Fragment>
        <div className="item">
          <Icon icon="arrow-down-alt" className="item-arrow" />
          <WKG_Entity_Selector
            value={{type: this.props.item.type, id: this.props.item.id}}
            onChange={(entity) => this.props.onChange({...this.props.item, ...entity})}
          />
          <Button className="remove" isSecondary onClick={() => this.props.onRemove(this.props.item.key)}>X</Button>
        </div>
			</Fragment>
		)
	}
}
