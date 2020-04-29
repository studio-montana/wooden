const { registerPlugin } = wp.plugins
const { __ } = wp.i18n
const { Component, Fragment } = wp.element
const { withSelect, withDispatch } = wp.data
const { dateI18n } = wp.date
const { compose } = wp.compose
const { PluginDocumentSettingPanel } = wp.editPost
const { PanelBody, PanelRow, Popover, Button, ToggleControl, TextControl, DateTimePicker } = wp.components
import WKG_Media_Selector from 'wkgcomponents/media-selector'

registerPlugin('wooden-plugin-eventmeta', {
  icon: 'calendar',
  render: (props) => {
    return (
      <PluginDocumentSettingPanel name="wooden-plugin-eventmeta" title={__('Event attributes', 'wooden')} className="wkg-document-setting-panel wooden-plugin-eventmeta">
          <PluginComponent />
      </PluginDocumentSettingPanel>
    )
  }
})

class PluginComponent_Base extends Component {
	constructor(props) {
		super(props)
    this.state = {
      editDateBegin: false,
      useDateEnd: this.props._event_meta_date_end !== null,
      editDateEnd: false
    }
	}
  prettyDate (date) {
    return dateI18n('d M Y \\à H \\h i \\m\\i\\n', date)
  }
	render () {
		return (
			<Fragment>
        <PanelRow>
          <span>{__('Begin', 'wooden')}</span>
          <Button isLink onClick={() => this.setState({editDateBegin: !this.state.editDateBegin})}>
            {this.prettyDate(this.props._event_meta_date_begin)}<br />
          </Button>
          {this.state.editDateBegin && (
            <Popover onClose={(e) => this.setState({editDateBegin: false})}>
              <DateTimePicker label={__('Begin date', 'wooden')} currentDate={this.props._event_meta_date_begin} onChange={(date) => this.props.on_meta_change({'_event_meta_date_begin': (new Date(date).getTime() / 1000)}) } />
            </Popover>
          )}
        </PanelRow>
        <PanelRow>
          <ToggleControl checked={this.state.useDateEnd} onChange={() => {
            this.setState({useDateEnd: !this.state.useDateEnd})
            if (this.state.useDateEnd === true) {
              this.props.on_meta_change({'_event_meta_date_end': null})
            } else {
              this.props.on_meta_change({'_event_meta_date_end': ((this.props._event_meta_date_begin.getTime() / 1000) + 3600)})
            }
          }} label={__('set end date', 'wooden')} />
        </PanelRow>
        {this.state.useDateEnd && (
          <PanelRow>
            <span>{__('End', 'wooden')}</span>
            <Button isLink onClick={() => this.setState({editDateEnd: !this.state.editDateEnd})}>
              {this.prettyDate(this.props._event_meta_date_end)}<br />
            </Button>
            {this.state.editDateEnd && (
              <Popover onClose={(e) => this.setState({editDateEnd: false})}>
                <DateTimePicker label={__('End date', 'wooden')} currentDate={this.props._event_meta_date_end} onChange={(date) => this.props.on_meta_change({'_event_meta_date_end': (new Date(date).getTime() / 1000)}) } />
              </Popover>
            )}
          </PanelRow>
        )}
        <hr />
        <PanelRow>
          <span>{__('Address', 'wooden')}</span>
          <TextControl value={this.props._event_meta_locate_address} onChange={value => this.props.on_meta_change({'_event_meta_locate_address': value})} />
        </PanelRow>
        <PanelRow>
          <span>{__('CP', 'wooden')}</span>
          <TextControl value={this.props._event_meta_locate_cp} onChange={value => this.props.on_meta_change({'_event_meta_locate_cp': value})} />
        </PanelRow>
        <PanelRow>
          <span>{__('City', 'wooden')}</span>
          <TextControl value={this.props._event_meta_locate_city} onChange={value => this.props.on_meta_change({'_event_meta_locate_city': value})} />
        </PanelRow>
        <PanelRow>
          <span>{__('Country', 'wooden')}</span>
          <TextControl value={this.props._event_meta_locate_country} onChange={value => this.props.on_meta_change({'_event_meta_locate_country': value})} />
        </PanelRow>
			</Fragment>
		)
	}
}

const applyWithSelect = withSelect(select => {
  let core_editor_store = select('core/editor')
  // IMPORTANT : On multiplie le timestamp UNIX (que l'on préfère) par 1000 car il est exprimé en secondes alors que le time Javascript est exprimé en millisecondes
  // https://stackoverflow.com/questions/847185/convert-a-unix-timestamp-to-time-in-javascript
  return {
    _event_meta_date_begin:       core_editor_store.getEditedPostAttribute('meta')['_event_meta_date_begin'] ? new Date(core_editor_store.getEditedPostAttribute('meta')['_event_meta_date_begin'] * 1000) : new Date(),
    _event_meta_date_end:         core_editor_store.getEditedPostAttribute('meta')['_event_meta_date_end'] ? new Date(core_editor_store.getEditedPostAttribute('meta')['_event_meta_date_end'] * 1000) : null,
    _event_meta_locate_address:   core_editor_store.getEditedPostAttribute('meta')['_event_meta_locate_address'],
    _event_meta_locate_cp:        core_editor_store.getEditedPostAttribute('meta')['_event_meta_locate_cp'],
    _event_meta_locate_city:      core_editor_store.getEditedPostAttribute('meta')['_event_meta_locate_city'],
    _event_meta_locate_country:   core_editor_store.getEditedPostAttribute('meta')['_event_meta_locate_country'],
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
