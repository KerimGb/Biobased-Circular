/**
 * USOF Field: Editor
 */
! function( $, _undefined ) {

	const _window = window;

	if ( $ush.isUndefined( _window.$usof ) ) {
		return;
	}

	$usof.field[ 'editor' ] = {
		/**
		 * Initializes the object.
		 */
		init: function() {
			const self = this;

			// Elements
			self.$container = $( '.usof-editor', self.$row );

			// Delete template
			$( 'script.usof-editor-template', self.$container )
				.remove();

			// Variables
			self.originalEditorId = self.$input.data( 'editor-id' ) || 'usof_editor';
			self.originalEditorSettings = _window.tinyMCEPreInit.mceInit[ self.originalEditorId ] || {};
			self.editorSettings = {};

			// Load editor settings
			var $settings = $( '.usof-editor-settings', self.$row );
			if ( $settings.is( '[onclick]' ) ) {
				self.editorSettings = $settings[0].onclick() || {};
			}
			$settings.remove();

			// Since there could be several instances of the field with same original ID, ...
			// ... adding random part to the ID
			self.editorId = self.originalEditorId + $ush.uniqid();
			self.$input.attr( 'id', self.editorId );

			// Bondable events.
			self._events = {
				changeField: self.changeField.bind( self ),
				changeTinymceContent: self.changeTinymceContent.bind( self )
			};

			// Events
			self.$container
				.on( ( self.isLiveBuilder() ? 'input' : 'change' ), 'textarea', self._events.changeField );

			// Init
			self.initEditor();
		},

		/**
		 * Init WP Editor
		 *
		 * @docs https://www.tiny.cloud/docs/tinymce/latest/apis/tinymce.root/
		 */
		initEditor: function() {
			const self = this;

			if ( ! _window.wp || ! _window.wp.editor ) {
				return;
			}

			const currentEditorSettings = {
				quicktags: true,
				tinymce: self.editorSettings.tinymce || {},
				mediaButtons: ! $ush.isUndefined( self.editorSettings.media_buttons )
					? self.editorSettings.media_buttons
					: true
			};
			const qtSettings = {
				id: self.editorId,
				buttons: "strong,em,link,block,del,ins,img,ul,ol,li,code,more,close",
			};
			const settingsFields = [
				'content_css',
				'toolbar1',
				'toolbar2',
				'toolbar3',
				'toolbar4',
				'theme',
				'skin',
				'language',
				'formats',
				'relative_urls',
				'remove_script_host',
				'convert_urls',
				'browser_spellcheck',
				'fix_list_elements',
				'entities',
				'entity_encoding',
				'keep_styles',
				'resize',
				'menubar',
				'branding',
				'preview_styles',
				'end_container_on_empty_block',
				'wpeditimage_html5_captions',
				'wp_lang_attr',
				'wp_keep_scroll_position',
				'wp_shortcut_labels',
				'plugins',
				'wpautop',
				'indent',
				'tabfocus_elements',
				'textcolor_map',
				'textcolor_rows',
			];

			// At initialization, add monitoring for content changes
			_window.tinymce.on( 'AddEditor', ( e ) => {

				const editor = e.editor;
				const contentChangeEvents = 'NodeChange ' + ( self.isLiveBuilder() ? 'input' : 'change' );

				if ( editor.id !== self.editorId ) {
					return;
				}

				editor
					.off( contentChangeEvents )
					.on( contentChangeEvents, self._events.changeTinymceContent );

				// Correction for editors on the Live Builder page
				if ( self.isLiveBuilder() ) {
					editor
						// Delegating an event to an internal event controller.
						.on( 'keydown', self.trigger.bind( self, 'tinyMCE.Keydown' ) )
						// Disable Undo Redo in usbuilder, there is a change manager.
						.on( 'BeforeAddUndo', ( e ) => {
							// Return true if the link is being edited,
							// otherwise errors may occur in the editor
							return ! $ush.isUndefined( e.originalEvent ); // false
						} );
				}
			}, /* prepend */true );

			settingsFields.forEach( ( key ) => {
				if ( ! $ush.isUndefined( self.originalEditorSettings[ key ] ) ) {
					currentEditorSettings.tinymce[ key ] = self.originalEditorSettings[ key ];
				}
			} );

			// We will not execute the installer since it is mostly used by third-party plugins,
			// for example WPML, at the moment the standard functionality is enough for us.
			currentEditorSettings.tinymce.setup = () => {};

			// Destroy old instances
			( _window.tinymce.editors || [] ).map( ( instance ) => {
				if ( $ush.toString( instance.id ).indexOf( self.originalEditorId ) === 0 ) {
					instance.destroy();
				}
			} );

			_window.wp.editor.initialize( self.editorId, currentEditorSettings );
			_window.quicktags( qtSettings );

			// Switch to Visual mode
			self.switchEditor( 'tinymce' );
		},

		/**
		 * Switches the editor between Visual and Text mode.
		 *
		 * @param {String} modeThe mode
		 */
		switchEditor: function( mode ) {
			const self = this;
			if ( $ush.toString( mode ).toLowerCase() === 'tinymce' ) {
				mode = 'tmce';
			} else {
				mode = 'html';
			}
			$( `#${self.editorId}-${mode}`, self.$container ).trigger( 'click' );
		},

		/**
		 * Field change event
		 *
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM
		 */
		changeField: function( e ) {
			this.trigger( 'change', e.currentTarget.value );
		},

		/**
		 * Content change handler in TinyMCE
		 *
		 * @event handler
		 * @param {Event} e TinyMCE Event
		 */
		changeTinymceContent: function( e ) {
			const self = this

			// Making sure both values are string and do not match each other
			const mceValue = _window.tinymce.get( self.editorId ).getContent();
			const currentValue = self.getValue();

			// If they are same, breaking following execution
			if ( currentValue === mceValue ) {
				return;
			}

			// If they are different, saving the changes in our value field and triggering change event
			self.$input.val( mceValue );
			self.trigger( 'change', mceValue );
		},

		/**
		 * Set value.
		 *
		 * @param {String} value The value
		 * @param {Boolean} quiet The quiet mode
		 */
		setValue: function( value, quiet ) {
			const self = this;

			self.$input.val( value );

			// Set value to tinyMCE
			if ( _window.tinyMCE && self.editorId ) {
				_window.tinyMCE.get( self.editorId ).on( 'init', function() {
					this.setContent( value );
				} );
			}

			if ( quiet ) {
				self.trigger( 'change', value );
			}
		},

		/**
		 * Get value.
		 *
		 * @return {String} The value
		 */
		getValue: function() {
			return this.$input.val();
		}
	};
}( jQuery );
