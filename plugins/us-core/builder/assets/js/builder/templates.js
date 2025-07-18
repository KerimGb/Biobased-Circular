/**
 * Available spaces:
 *
 * _window.$usb - Basic object for mounting and initializing all extensions of the builder
 * _window.$usbcore - Auxiliary functions for the builder and his extensions
 * _window.$ush - US Helper Library
 *
 * Note: Double underscore `__funcname` is introduced for functions that are created through `$ush.debounce(...)`.
 */
! function( $, undefined ) {

	const _window = window;

	if ( ! _window.$usb ) {
		return;
	}

	_window.$ush = _window.$ush || {};
	_window.$usbcore = _window.$usbcore || {};

	/**
	 * @type {{}} Private temp data
	 */
	var _$tmp = {
		$categorySections: {}, // list section categories
		isLoaded: {}, // this param will be True when templates are loaded by category {id}:{status}. Example: `fn:true`
	};

	/**
	 * @class Templates - Functionality of importing and adding rows from provided templates
	 * @param {String} container The container
	 */
	function Templates( container ) {
		const self = this;

		// Private "Variables"
		self.name = 'templates';

		// Bondable events
		self._events = {
			clickTabTemplates: self._clickTabTemplates.bind( self ),
			expandCategory: self._expandCategory.bind( self ),
		}

		$( () => {

			// Elements
			self.$container = $( container );
			self.$tabButton = $( '.usb_action_show_templates', $usb.$panel );
			self.$error = $( '.usb-templates-error', self.$container );

			// Events
			$usb.$panel
				.on( 'click', '.usb-template-title', self._events.expandCategory )
				.on( 'click', '.usb_action_show_templates', self._events.clickTabTemplates );
		} );
	}

	// Templates API
	$.extend( Templates.prototype, $ush.mixinEvents, {
		/**
		 * Determines if ready
		 *
		 * @return {Boolean} True if ready, False otherwise
		 */
		isReady: function() {
			return ! $ush.isUndefined( this.$container );
		},

		/**
		 * Determines if show
		 *
		 * @return {Boolean} True if show, False otherwise
		 */
		isShow: function() {
			// TODO: Optimize ".is( ':visible' )"
			return this.$container.is( ':visible' );
		},

		/**
		 * Determines whether the specified identifier is template
		 *
		 * @param @param {String} id Shortcode's usbid, e.g. "import_template:1"
		 * @return {Boolean} True if the specified id is template, False otherwise
		 */
		isTemplate: function( id ) {
			const self = this;
			if ( $usb.builder.isValidId( id ) ) {
				id = $usb.builder.getElmType( id );
			}
			return id === 'import_template';
		},

		/**
		 * Check the config load
		 *
		 * @return {Boolean} Returns true if config is loaded, otherwise false
		 */
		configIsLoaded: function() {
			return ! $.isEmptyObject( _$tmp.$categorySections );
		},

		/**
		 * Check the category is load
		 *
		 * @param {String} categoryId The category id
		 * @return {Boolean} Returns true if the category is loaded, otherwise false
		 */
		categoryIsLoaded: function( categoryId ) {
			return _$tmp.isLoaded.hasOwnProperty( categoryId );
		},

		/**
		 * Check the load of templates in the category
		 *
		 * @param {String} categoryId The category id
		 * @return {Boolean} Returns true if the category templates are loaded, otherwise false
		 */
		templateIsLoaded: function( categoryId ) {
			return this.categoryIsLoaded( categoryId ) && _$tmp.isLoaded[ categoryId ];
		},

		/**
		 * Handler for click on tab button
		 *
		 * @event handler
		 */
		_clickTabTemplates: function() {
			const self = this;
			// Load the config if it is not loaded
			if ( ! self.configIsLoaded() ) {
				self._loadConfig();
			}
			// Init Drag & Drop
			if ( $usb.licenseIsActivated() ) {
				$usb.builder.initDragDrop();
			}
			// Set the "Add Elements" button to active in the header
			if ( $usb.find( 'builderPanel' ) ) {
				$usb.builderPanel.$actionAddElms.addClass( 'active' );
			}
			// Collapse all template categories
			$( '.usb-template', self.$container ).removeClass( 'expand' );
		},

		/**
		 * Handler for expand category
		 *
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM
		 */
		_expandCategory: function( e ) {
			const self = this;
			const categoryId = $( e.currentTarget ).parent().data( 'template-category-id' );
			// If it was not possible to load the category id, then exit the method
			if ( ! categoryId ) {
				return;
			}
			// Check and preload category templates
			if ( $usb.licenseIsActivated() ) {
				self._loadTemplates( categoryId );
			}
			// If click on an expand category, then collapse the category
			const $activeTemplate = $( '.usb-template.expand', self.$container );
			if ( $activeTemplate.data( 'template-category-id' ) === categoryId ) {
				$activeTemplate.removeClass( 'expand' );
				return;
			}
			/**
			 * Show category section by categoryId
			 *
			 * @param {String} categoryId The category id
			 */
			function showSectionById( categoryId ) {
				$( '.usb-template', self.$container )
					.removeClass( 'expand' )
					.filter( `[data-template-category-id="${categoryId}"]` )
					.addClass( 'expand' );
			};
			// After load show category templates
			if ( ! self.configIsLoaded() ) {
				self.one( 'configLoaded', showSectionById.bind( self, categoryId ) );
			} else {
				showSectionById( categoryId );
			}
		},

		/**
		 * Load templates config
		 */
		_loadConfig: function() {
			const self = this;

			if ( self.configIsLoaded() ) {
				return;
			}

			$usb.panel.showPreloader();

			$usb.ajax( 'templates.loadConfig', {
				data: {
					_nonce: $usb.config( '_nonce' ),
					action: $usb.config( 'action_get_templates_config' ),
				},
				success: ( res ) => {
					if ( ! res.success || ! $.isPlainObject( res.data ) ) {
						self.$error.addClass( 'active' );
						return;
					}

					for ( categoryId in res.data ) {
						// If the category section is loaded then skip the iteration
						if ( _$tmp.$categorySections[ categoryId ] ) {
							continue;
						}

						// Get category section
						const categorySection = res.data[ categoryId ];
						if ( categorySection ) {
							self.$container.append( categorySection );
							_$tmp.$categorySections[ categoryId ] = $( categorySection );
						}
					}

					self.trigger( 'configLoaded' );
				},
				complete: () => {
					$usb.panel.hidePreloader();
				},
			} );
		},

		/**
		 * Check and preload category templates
		 *
		 * @param {String} categoryId The category id
		 */
		_loadTemplates: function( categoryId ) {
			const self = this;

			if (
				$ush.isUndefined( categoryId )
				|| categoryId == ''
				|| self.templateIsLoaded( categoryId )
			) {
				return;
			}

			$usb.ajax( 'templates.loadTemplates', {
				data:{
					_nonce: $usb.config( '_nonce' ),
					action: $usb.config( 'action_preload_template_category' ),
					template_category_id: categoryId,
				},
				success: ( res ) => {
					// Saved the result in any case, to understand whether there was a download or not
					_$tmp.isLoaded[ categoryId ] = res.success;

					if ( ! res.success ) {
						return;
					}

					// Set parameter for render shortcode start
					self.trigger( 'templatesLoaded', [ categoryId ] );
				},
			} );
		},

		/**
		 * Insert template in content and preview.
		 *
		 * @param {String} categoryId The template category id.
		 * @param {String} templateId The unique template id in the category.
		 * @param {String} parentId ID of the element's parent element.
		 * @param {Number} currentIndex Position of the element inside the parent.
		 */
		insertTemplate: function( categoryId, templateId, parentId, currentIndex ) {
			const self = this;

			// Check if the templates category id is correct
			if ( ! categoryId ) {
				$usb.log( 'Error: Template category ID is not set', args );
				return;
			}

			// Check if the template id is correct
			if ( ! templateId ) {
				$usb.log( 'Error: Template ID is not set', args );
				return;
			}

			// Check if the parent container is correct
			if ( ! $usb.builder.isMainContainer( parentId ) ) {
				$usb.log( 'Error: Invalid parent container, templates can only be added to mainContainer', args );
				return;
			}

			const insert = $usb.builder.getInsertPosition( parentId, currentIndex );

			/**
			 * @type {Function} Get template data
			 */
			const _getTemplateData = function() {
				// Get html shortcode code and set on preview page
				$usb.postMessage( 'showPreloader', [
					insert.parent,
					insert.position,
				] );

				$usb.builder.renderShortcode( 'templates.insertTemplate', {
					data: {
						template_category_id: categoryId,
						template_id: templateId,
						isReturnContent: true // returns the content for the page (shortcodes)
					},
					success: ( res ) => {
						$usb.postMessage( 'hidePreloader', insert.parent );

						// Check the correctness of the answer and the availability of data
						if ( ! res.success || ! res.data.content || ! res.data.html ) {
							return;
						}

						// Update IDs in content
						const newData = $usb.builder.updateIdsInContent( res.data.content, res.data.html );

						// Adds shortcode to content
						if ( ! $usb.builder._addShortcodeToContent( parentId, currentIndex, newData.content ) ) {
							return false;
						}

						// Adds new template to preview page
						$usb.postMessage( 'insertElm', [ insert.parent, insert.position, newData.html ] );

						// Add the first row to the history and open for edit
						if ( $usb.builder.isRow( newData.firstElmId ) ) {
							// Commit to save changes to history
							$usb.history.commitChange( newData.firstElmId, _CHANGED_ACTION_.CREATE );
						}

						// Note: A timeout is used to release the main thread.
						$ush.timeout( () => {
							$usb.builder.updateElementsList();
						}, 0 );

						$usb.trigger( 'builder.contentChange' );
					}
				} );
			};

			// Determines if current category shortcodes loaded
			if ( ! self.templateIsLoaded( categoryId ) ) {
				self.off( 'templatesLoaded' )
					.one( 'templatesLoaded', ( _categoryId ) => {
						if ( categoryId == _categoryId ) {
							_getTemplateData();
						}
					} );
				if ( self.categoryIsLoaded( categoryId ) ) {
					$usb.log( 'Error: Failed to load template category:', [ categoryId ] );
				}
				return;
			}
			_getTemplateData();
		}
	} );

	// Export API
	$usb.templates = new Templates( /* container */'#usb-templates' );

} ( jQuery );
