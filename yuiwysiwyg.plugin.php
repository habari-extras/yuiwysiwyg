<?php

class YUIWYSIWYG extends Plugin
{

	public function action_admin_header($theme)
	{
		if ( ( $theme->page == 'publish' ) ) { // && User::identify()->info->yuiwysiwyg_activate ) {
			Plugins::act('add_yuieditor_admin');
		}
	}

	public function action_add_yuieditor_admin()
	{

		Stack::add('admin_header_javascript', 'http://yui.yahooapis.com/combo?2.8.2r1/build/yahoo-dom-event/yahoo-dom-event.js&2.8.2r1/build/container/container_core-min.js&2.8.2r1/build/menu/menu-min.js&2.8.2r1/build/element/element-min.js&2.8.2r1/build/button/button-min.js&2.8.2r1/build/editor/editor-min.js', 'yui_editor', 'jquery');
//		Stack::add('admin_header_javascript', 'http://yui.yahooapis.com/2.8.2r1/build/yahoo-dom-event/yahoo-dom-event.js', 'yui_dom', 'jquery');
//		Stack::add('admin_header_javascript', 'http://yui.yahooapis.com/2.8.2r1/build/element/element-min.js ', 'yui_element', 'yui_dom');
//		Stack::add('admin_header_javascript', 'http://yui.yahooapis.com/2.8.2r1/build/container/container_core-min.js', 'yui_container', 'yui_element');
//		Stack::add('admin_header_javascript', 'http://yui.yahooapis.com/2.8.2r1/build/menu/menu-min.js', 'yui_menu', 'yui_container');
//		Stack::add('admin_header_javascript', 'http://yui.yahooapis.com/2.8.2r1/build/button/button-min.js', 'yui_button', 'yui_container');
//		Stack::add('admin_header_javascript', 'http://yui.yahooapis.com/2.8.2r1/build/editor/editor-min.js', 'yui_editor', 'yui_container');
		Stack::add('admin_stylesheet', array('http://yui.yahooapis.com/2.8.2r1/build/assets/skins/sam/skin.css', 'screen'), 'yui_editor_header');
		Stack::add('admin_stylesheet', array($this->get_url(true) . 'editor.css', 'screen' ), 'more_yuieditor', 'yui_editor_header' );

		$js = <<<YUIWYSIWYG
			$('label[for=content]').hide();
			var myEditor;
			$(function(){
				$('body').addClass('yui-skin-sam');
				$('#content').attr('rows', 'auto');

		    var Dom = YAHOO.util.Dom,
		        Event = YAHOO.util.Event;

		    var myConfig = {
		        height: '300px',
		        width: '100%',
		        animate: true,
		        dompath: true,
		        focusAtStart: true,
				handleSubmit: true
		    };

		    var state = 'off';
		    YAHOO.log('Set state to off..', 'info', 'example');

		    YAHOO.log('Create the Editor..', 'info', 'example');
		    myEditor = new YAHOO.widget.Editor('content', myConfig);
		    myEditor.on('toolbarLoaded', function() {
		        var codeConfig = {
		            type: 'push', label: 'Edit HTML Code', value: 'editcode'
		        };
		        YAHOO.log('Create the (editcode) Button', 'info', 'example');
		        this.toolbar.addButtonToGroup(codeConfig, 'insertitem');

		        this.toolbar.on('editcodeClick', function() {
		            var ta = this.get('element'),
		                iframe = this.get('iframe').get('element');

		            if (state == 'on') {
		                state = 'off';
		                this.toolbar.set('disabled', false);
		                YAHOO.log('Show the Editor', 'info', 'example');
		                YAHOO.log('Inject the HTML from the textarea into the editor', 'info', 'example');
		                this.setEditorHTML(ta.value);
		                if (!this.browser.ie) {
		                    this._setDesignMode('on');
		                }

		                Dom.removeClass(iframe, 'editor-hidden');
		                Dom.addClass(ta, 'editor-hidden');
		                this.show();
		                this._focusWindow();
		            } else {
		                state = 'on';
		                YAHOO.log('Show the Code Editor', 'info', 'example');
		                this.cleanHTML();
		                YAHOO.log('Save the Editors HTML', 'info', 'example');
		                Dom.addClass(iframe, 'editor-hidden');
		                Dom.removeClass(ta, 'editor-hidden');
		                this.toolbar.set('disabled', true);
		                this.toolbar.getButtonByValue('editcode').set('disabled', false);
		                this.toolbar.selectButton('editcode');
		                this.dompath.innerHTML = 'Editing HTML Code';
		                this.hide();
		            }
		            return false;
		        }, this, true);

		        this.on('cleanHTML', function(ev) {
		            YAHOO.log('cleanHTML callback fired..', 'info', 'example');
		            this.get('element').value = ev.html;
		        }, this, true);

		        this.on('afterRender', function() {
		            var wrapper = this.get('editor_wrapper');
		            wrapper.appendChild(this.get('element'));
		            this.setStyle('width', '100%');
		            this.setStyle('height', '100%');
		            this.setStyle('visibility', '');
		            this.setStyle('top', '');
		            this.setStyle('left', '');
		            this.setStyle('position', '');

		            this.addClass('editor-hidden');
		        }, this, true);
		    }, myEditor, true);
		    myEditor.render();
					});
			habari.editor = {
				insertSelection: function(value) {
					myEditor.execCommand('inserthtml', value);
				}
			}
YUIWYSIWYG;
		Stack::add( 'admin_footer_javascript', $js, 'yui_editor_footer', 'jquery' );
	}

	public function action_add_yuieditor_template()
	{
		Stack::add('template_header_javascript', $this->get_url() . '/jwysiwyg/jquery.wysiwyg.js');
		Stack::add('admin_stylesheet', array('http://yui.yahooapis.com/2.8.2r1/build/assets/skins/sam/skin.css', 'screen'), 'yui_editor');
	}

	/**
	 * Add the configuration to the user page
	 **/
	public function action_form_user( $form, $user )
	{
		$fieldset = $form->append( 'wrapper', 'yuiwysiwyg', 'JWYSIWYG' );
		$fieldset->class = 'container settings';
		$fieldset->append( 'static', 'yuiwysiwyg', '<h2>YUI WYSIWYG</h2>' );

		$activate = $fieldset->append( 'checkbox', 'yuiwysiwyg_activate', 'null:null', _t('Enable YUI WYSIWYG:'), 'optionscontrol_checkbox' );
		$activate->class[] = 'item clear';
		$activate->value = $user->info->yuiwysiwyg_activate;

		$form->move_before( $fieldset, $form->page_controls );
	}

	/**
	 * Save authentication fields
	 **/
	public function filter_adminhandler_post_user_fields( $fields )
	{
		$fields[] = 'yuiwysiwyg_activate';

		return $fields;
	}

}

?>
