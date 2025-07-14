/**
 * UpSolution Element: List Filter
 */
! function( $, _undefined ) {
	"use strict";

	const abs = Math.abs;
	const max = Math.max;
	const min = Math.min;
	const urlManager = $ush.urlManager();
	const PREFIX_FOR_URL_PARAM = '_';
	const RANGE_VALUES_BY_DEFAULT = [ 0, 1000 ];
	const DELETE_FILTER = null;

	var hasFacetedFilters;

	/**
	 * @param {String} values The values.
	 * @return {[]} Returns an array of range values.
	 */
	function parseValues( values ) {
		values = $ush.toString( values );
		if ( ! values || ! values.includes( '-' ) ) {
			return RANGE_VALUES_BY_DEFAULT;
		}
		return values.split( '-' ).map( $ush.parseFloat );
	}

	/**
	 * @param {Node} container.
	 */
	function usListFilter( container ) {
		const self = this;

		// Bondable events
		self._events = {
			applyFilterToList: $ush.debounce( self.applyFilterToList.bind( self ), 1 ),
			checkScreenStates: $ush.debounce( self.checkScreenStates.bind( self ), 10 ),
			closeMobileFilters: self.closeMobileFilters.bind( self ),
			getItemValues: self.getItemValues.bind( self ),
			hideItemDropdown: self.hideItemDropdown.bind( self ),
			openMobileFilters: self.openMobileFilters.bind( self ),
			resetItemValues: self.resetItemValues.bind( self ),
			searchItemValues: self.searchItemValues.bind( self ),
			toggleItemDropdown: self.toggleItemDropdown.bind( self ),
			toggleItemSection: self.toggleItemSection.bind( self ),
		};

		// Elements
		self.$container = $( container );
		self.$pageContent = $( 'main#page-content' );

		if ( ! self.isVisible() ) {
			return;
		}

		// Private "Variables"
		self.data = {
			mobileWidth: 600,
			listSelectorToFilter: null,
			ajaxData: {},
		};
		self.$filters = {};
		self.result = {};
		self.lastResult; // default value "undefined"
		self.xhr; // XMLHttpRequests instance
		self.isFacetedFiltering = self.$container.hasClass( 'faceted_filtering' );

		// Get element settings
		if ( self.$container.is( '[onclick]' ) ) {
			$.extend( self.data, self.$container[0].onclick() || {} );
		}

		// Init DatePicker https://api.jqueryui.com/datepicker/
		$( '.type_date_picker', self.$container ).each( ( _, filter ) => {
			var $start = $( 'input:eq(0)', filter ),
				$end = $( 'input:eq(1)', filter ),
				$startContainer = $start.parent(),
				$endContainer = $start.parent(),
				startOptions = {},
				endOptions = {};

			if ( $startContainer.is( '[onclick]' ) ) {
				startOptions = $startContainer[0].onclick() || {};
			}
			if ( $endContainer.is( '[onclick]' ) ) {
				endOptions = $endContainer[0].onclick() || {};
			}

			$start.datepicker( $.extend( true, {
				isRTL: $ush.isRtl(),
				dateFormat: $start.data( 'date-format' ),
				beforeShow: ( _, inst ) => {
					inst.dpDiv.addClass( 'for_list_filter' );
				},
				onSelect: () => {
					$start.trigger( 'change' );
				},
				onClose: ( _, inst ) => {
					$end.datepicker( 'option', 'minDate', inst.input.datepicker( 'getDate' ) || null );
				},
			}, startOptions ) );

			$end.datepicker( $.extend( true, {
				isRTL: $ush.isRtl(),
				dateFormat: $end.data( 'date-format' ),
				beforeShow: ( _, inst ) => {
					inst.dpDiv.addClass( 'for_list_filter' );
				},
				onSelect: () => {
					$start.trigger( 'change' );
				},
				onClose: ( _, inst ) => {
					$start.datepicker( 'option', 'maxDate', inst.input.datepicker( 'getDate' ) || null );
				},
			}, endOptions ) );
		} );

		// Init Range Slider https://api.jqueryui.com/slider/
		$( '.type_range_slider', self.$container ).each( ( _, filter ) => {
			function showFormattedResult( _, ui ) {
				$( '.for_min_value, .for_max_value', filter ).each( ( i, node ) => {
					$( node ).html( self.numberFormat( ui.values[ i ], opts ) );
				} );
			}
			var $slider = $( '.ui-slider', filter );
			var opts = {
				slider: {
					animate: true,
					min: RANGE_VALUES_BY_DEFAULT[0],
					max: RANGE_VALUES_BY_DEFAULT[1],
					range: true,
					step: 10,
					values: RANGE_VALUES_BY_DEFAULT,
					slide: showFormattedResult,
					change: showFormattedResult,
					stop: $ush.debounce( ( _, ui ) => {
						$( 'input[type=hidden]', filter )
							.val( ui.values.join( '-' ) )
							.trigger( 'change' );
					} ),
				},
				unitFormat: '%d', // example: $0 000.00
				numberFormat: null, // example: 0 000.00
			};
			if ( $slider.is( '[onclick]' ) ) {
				opts = $.extend( true, opts, $slider[0].onclick() || {} );
			}
			$slider.removeAttr( 'onclick' )
				.slider( opts.slider )
				.fixSlider();
		} );

		// Setup the UI
		if ( self.changeURLParams() ) {
			$( '[data-name]', self.container ).each( ( _, filter ) => {

				const $filter = $( filter );
				const compare = $ush.toString( $filter.data( 'value-compare' ) );

				var name = $filter.data( 'name' );

				if ( compare ) {
					name += `|${compare}`;
				}
				self.$filters[ name ] = $filter;
			});
			self.setupFields();
			urlManager.on( 'popstate', () => {
				self.setupFields();
				self.applyFilterToList();
			} );
		}

		// Faceted Filtering
		if ( self.isFacetedFiltering && ! hasFacetedFilters ) {

			hasFacetedFilters = true;

			self._events.itemsLoaded = ( _, $items, applyFilter ) => {
				if ( applyFilter && self.isVisible() ) {
					self.setPostCount( self.firstListData().facetedFilter.post_count );
				}
			};
			$us.$document.on( 'usPostList.itemsLoaded', self._events.itemsLoaded );

			var listFilters = {};
			$.each( self.$filters, ( name, $filter ) => {
				listFilters[ name ] = $ush.toString( $filter.usMod( 'type' ) );
			} );
			listFilters = JSON.stringify( listFilters );

			self.listToFilter().trigger( 'usListFilter', { list_filters: listFilters } );

			self.$container.addClass( 'loading' );

			self.xhr = $.ajax( {
				type: 'post',
				url: $us.ajaxUrl,
				dataType: 'json',
				cache: false,
				data: $.extend( true,
					{
						list_filters: listFilters,
						_s: urlManager.get( '_s' ), // value from List Search
 					},
					self.firstListData().facetedFilter,
					self.result,
					self.data.ajaxData
				),
				success: function( res ) {
					if ( ! res.success ) {
						console.error( res.data.message );
					}
					self.setPostCount( res.success ? res.data : {} );
				},
				complete: function() {
					self.$container.removeClass( 'loading' );
				}
			} );
		}

		// Events
		$( '.w-filter-item', self.$container )
			.on( 'change', 'input:not([name=search_values]), select', self._events.getItemValues )
			.on( 'input change', 'input[name=search_values]', self._events.searchItemValues )
			.on( 'click', '.w-filter-item-reset', self._events.resetItemValues )
			.on( 'click', '.w-filter-item-title', self._events.toggleItemDropdown )
			.on( 'click', '.w-filter-item-title', self._events.toggleItemSection );
		self.$container
			.on( 'click', '.w-filter-opener', self._events.openMobileFilters )
			.on( 'click', '.w-filter-list-closer, .w-filter-button-submit', self._events.closeMobileFilters );
		$us.$window
			.on( 'resize', self._events.checkScreenStates );

		// Hide dropdowns of all items on click outside any item title
		if ( self.$container.hasClass( 'drop_on_click' ) ) {
			$us.$document.on( 'click', self._events.hideItemDropdown );
		}

		self.on( 'applyFilterToList', self._events.applyFilterToList );

		self.checkScreenStates();
		self.сheckActiveFilters();
	}

	// List Filter API
	$.extend( usListFilter.prototype, $ush.mixinEvents, {

		/**
		 * Titles as toggles.
		 *
		 * @return {Boolean}
		 */
		titlesAsToggles: function() {
			return this.$container.hasClass( 'mod_toggle' );
		},

		/**
		 * Enabled URL.
		 *
		 * @return {Boolean} True if enabled url, False otherwise.
		 */
		changeURLParams: function() {
			return this.$container.hasClass( 'change_url_params' );
		},

		/**
		 * Determines if visible.
		 *
		 * @return {Boolean} True if visible, False otherwise.
		 */
		isVisible: function() {
			return this.$container.is( ':visible' );
		},

		/**
		 * Setup fields.
		 */
		setupFields: function() {
			const self = this;
			$.each( self.$filters, ( name, $filter ) => {
				self.resetFields( $filter );

				name = PREFIX_FOR_URL_PARAM + name;
				if ( ! urlManager.has( name ) ) {
					delete self.result[ name ];
					return;
				}

				var values = $ush.toString( urlManager.get( name ) );
				values.split( ',' ).map( ( value, i ) => {
					if ( $filter.hasClass( 'type_dropdown' ) ) {
						$( `select`, $filter ).val( value );

					} else if ( $filter.hasClass( 'type_date_picker' ) ) {
						var $input = $( `input:eq(${i})`, $filter );
						if ( $input.length && /\d{4}-\d{2}-\d{2}/.test( value ) ) {
							$input.val( $.datepicker.formatDate( $input.data( 'date-format' ), $.datepicker.parseDate( 'yy-mm-dd', value ) ) );
						}

					} else if ( $filter.hasClass( 'type_range_input' ) ) {
						if ( /([\.?\d]+)-([\.?\d]+)/.test( value ) ) {
							$( 'input', $filter ).each( ( i, input ) => { input.value = parseValues( value )[ i ] } );
						}

					} else if ( $filter.hasClass( 'type_range_slider' ) ) {
						if ( ! self.isFacetedFiltering && /([\.?\d]+)-([\.?\d]+)/.test( value ) ) {
							$( '.ui-slider', $filter ).slider( 'values', parseValues( value ) );
							$( `input[type=hidden]`, $filter ).val( value );
						}

						// For type_checkbox and type_radio
					} else {
						$( `input[value="${value}"]`, $filter ).prop( 'checked', true );
					}
				} );

				self.result[ name ] = values;

				$filter
					.addClass( 'has_value' )
					.toggleClass( 'expand', self.titlesAsToggles() );
			} );

			self.showSelectedValues();
		},

		/**
		 * Search field to narrow choices.
		 *
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		searchItemValues: function( e ) {

			const $filter = $( e.delegateTarget );
			const $items = $( '[data-value]', $filter );
			const value = $ush.toLowerCase( e.target.value ).trim();

			$items
				.filter( ( _, node ) => { return ! $( 'input', node ).is( ':checked' ) } )
				.toggleClass( 'hidden', !! value );

			if ( $filter.hasClass( 'type_radio' ) ) {
				const $buttonAnyValue = $( '[data-value="*"]:first', $filter );
				if ( ! $( 'input', $buttonAnyValue ).is(':checked') ) {
					$buttonAnyValue
						.toggleClass( 'hidden', ! $ush.toLowerCase( $buttonAnyValue.text() ).includes( value ) );
				}
			}

			if ( value ) {
				$items
					.filter( ( _, node ) => { return $ush.toLowerCase( $( node ).text() ).includes( value ) } )
					.removeClass( 'hidden' )
					.length;
			}

			$( '.w-filter-item-message', $filter ).toggleClass( 'hidden', $items.is( ':visible' ) );
		},

		/**
		 * Get result from single filter item.
		 *
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		getItemValues: function( e ) {
			const self = this;

			const $filter = $( e.delegateTarget );
			const compare = $filter.data( 'value-compare' );

			var name = PREFIX_FOR_URL_PARAM + $ush.toString( $filter.data( 'name' ) ),
				value = e.target.value;

			if ( compare ) {
				name += `|${compare}`;
			}

			// TYPE: Checkboxes
			if ( $filter.hasClass( 'type_checkbox' ) ) {
				var values = [];
				$( 'input:checked', $filter ).each( ( _, input ) => {
					values.push( input.value );
				});

				if ( ! values.length ) {
					self.result[ name ] = DELETE_FILTER;
				} else {
					self.result[ name ] = values.toString();
				}

				// TYPE: Date Picker
			} else if ( $filter.hasClass( 'type_date_picker' ) ) {
				var values = [];
				$( 'input.hasDatepicker', $filter ).each( ( i, input ) => {
					values[ i ] = $.datepicker.formatDate( 'yy-mm-dd', $( input ).datepicker( 'getDate' ) );
				} );

				if ( ! values.length ) {
					self.result[ name ] = DELETE_FILTER;
				} else {
					self.result[ name ] = values.toString();
				}

				// TYPE: Range input
			} else if ( $filter.hasClass( 'type_range_input' ) ) {
				var defaultValues = [], values = [];
				$( 'input', $filter ).each( ( i, input ) => {
					defaultValues[ i ] = input.dataset.value;
					values[ i ] = input.value || defaultValues[ i ];
				} );
				if ( ! values.length || values.toString() === defaultValues.toString() ) {
					self.result[ name ] = DELETE_FILTER;
				} else {
					self.result[ name ] = values.join( '-' );
				}

				// TYPE: Radio buttons and Dropdown
			} else {
				if ( $ush.rawurldecode( value ) === '*' ) {
					self.result[ name ] = DELETE_FILTER;
				} else {
					self.result[ name ] = value;
				}
			}

			const hasValue = !! self.result[ name ];

			$filter
				.toggleClass( 'has_value', hasValue )
				.toggleClass( 'expand', hasValue && self.titlesAsToggles() );

			if ( self.isFacetedFiltering ) {
				$filter.siblings().addClass( 'loading' );
			}

			self.trigger( 'applyFilterToList' );
			self.showSelectedValues();
		},

		/**
		 * Get the List for filtering by CSS selector
		 *
		 * @return {Node} Returns the node of the found Post List.
		 */
		listToFilter: function() {
			const self = this;
			var $lists;

			// Multiple lists can be used
			if ( self.data.listSelectorToFilter ) {
				$lists = $( self.data.listSelectorToFilter, self.$pageContent );

			} else {
				$lists = $( `
					.w-grid.us_post_list:visible,
					.w-grid.us_product_list:visible,
					.w-grid-none:visible
				`, self.$pageContent ).first();
			}

			if ( $lists.hasClass( 'w-grid-none' ) ) {
				$lists = $lists.prev();
			}

			return $lists;
		},

		/**
		 * Get data from first Post List.
		 *
		 * @return {{}}
		 */
		firstListData: function() {
			return $ush.toPlainObject( ( this.listToFilter().first().data( 'usPostList' ) || {} ).data );
		},

		/**
		 * Formats a number to the desired format.
		 *
		 * @param {Number|String} value
		 * @param {{}} options
		 * @return {String}
		 */
		numberFormat: function( value, options ) {
			const self = this;
			const defaultOpts = {
				unitFormat: '%d', // example: $0 000.00
				numberFormat: null, // example: 0 000.00
			};

			value = $ush.toString( value );
			options = $.extend( defaultOpts, $ush.toPlainObject( options ) );

			if ( options.numberFormat ) {
				var numberFormat = $ush.toPlainObject( options.numberFormat ),
					decimals = $ush.parseInt( abs( numberFormat.decimals ) );
				if ( decimals ) {
					value = $ush.toString( $ush.parseFloat( value ).toFixed( decimals ) )
						.replace( /^(\d+)(\.)(\d+)$/, '$1' + numberFormat.decimal_separator + '$3' );
				}
				value = value.replace( /\B(?=(\d{3})+(?!\d))/g, numberFormat.thousand_separator );
			}

			return $ush.toString( options.unitFormat ).replace( '%d', value );
		},

		/**
		 * Set the post count.
		 *
		 * @param {{}|undefined} data
		 */
		setPostCount: function( data ) {
			const self = this;
			if ( ! $.isPlainObject( data ) ) {
				data = {};
			}

			$.each( self.$filters, ( filterName, filter ) => {
				const $filter = $( filter );
				const name = $filter.data('name')
				const currentData = $ush.clone( data[ name ] || {} );
				const isRadioButtons = $filter.hasClass( 'type_radio' );
				const isRangeSlider = $filter.hasClass( 'type_range_slider' );

				// For "Date Values Range" = yearly
				if ( $filter.hasClass( 'range_by_year' ) ) {
					for ( const k in currentData ) {
						const year = $ush.toString( k ).substring( 0, 4 );
						currentData[ year ] = $ush.parseInt( currentData[ year ] ) + currentData[ k ];
					}
				}

				var numActiveValues = 0;

				// TYPE: Checkboxes and Radio buttons
				if ( $filter.hasClass( 'type_checkbox' ) || isRadioButtons ) {
					const compare = $filter.data( 'value-compare' );

					$( '[data-value]', filter ).each( ( _, node ) => {

						const $node = $( node );
						const value = $node.data( 'value' );

						if ( isRadioButtons && value === '*' ) {
							return;
						}

						var postCount = 0;

						// For "Numeric Values Range" = num
						if ( compare == 'between' ) {
							const rangeValues = value.split( '-' ).map( $ush.parseFloat );
							$.each( data[ filterName.split('|')[0] ] || {}, ( val, count ) => {
								if ( val >= rangeValues[0] && val <= rangeValues[1] ) {
									postCount += count;
								}
							} );

						} else {
							postCount = $ush.parseInt( currentData[ value ] );
						}

						if ( postCount ) {
							numActiveValues++;
						}

						$node
							.toggleClass( 'disabled', postCount === 0 )
							.data( 'post-count', postCount )
							.find( '.w-filter-item-value-amount' )
							.text( postCount );
					} );

					// TYPE: Dropdown
				} else if ( $filter.hasClass( 'type_dropdown' ) ) {
					$( 'option[data-label-template]', filter ).each( ( _, node ) => {
						const $node = $( node );
						const postCount = $ush.parseInt( currentData[ node.value ] );

						if ( postCount ) {
							numActiveValues++;
						}

						$node
							.text( $ush.toString( $node.data( 'label-template' ) ).replace( '%d', postCount ) )
							.prop( 'disabled', postCount === 0 )
							.toggleClass( 'disabled', postCount === 0 )
					} );

					// TYPE: Range Input/Slider
				} else if ( isRangeSlider || $filter.hasClass( 'type_range_input' ) ) {

					const minValue = $ush.parseFloat( currentData[0] );
					const maxValue = $ush.parseFloat( currentData[1] );
					const newValues = [ minValue, maxValue ];
					const currentValues = urlManager.get( `_${filterName}` );

					if ( isRangeSlider ) {

						$( '.ui-slider', $filter ).slider( 'option', {
							min: minValue,
							max: maxValue,
							values: currentValues ? parseValues( currentValues ) : newValues,
						} );

						$( `input[type=hidden]`, $filter ).val( newValues.join( '-' ) );

						// For Range Input
					} else {
						const opts = $( '.for_range_input_options', filter )[0].onclick() || {};

						$( '.for_min_value, .for_max_value', filter ).each( ( i, node ) => {
							const formattedValue = self.numberFormat( newValues[ i ], opts );

							$( node ).attr( 'placeholder', $ush.fromCharCode( formattedValue ) );
						} );
					}

					if ( minValue ) {
						numActiveValues++;
					}
					if ( maxValue ) {
						numActiveValues++;
					}

					// other types
				} else {
					numActiveValues = 1;
				}

				$filter.removeClass( 'loading' );
				$filter.toggleClass( 'disabled', numActiveValues < 1 );

			} );
		},

		/**
		 * Reset values of single item
		 *
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		resetItemValues: function( e ) {
			const self = this;

			e.stopPropagation();
			e.preventDefault();

			const $filter = $( e.delegateTarget );
			const compare = $filter.data( 'value-compare' );

			var name = PREFIX_FOR_URL_PARAM + $filter.data( 'name' );

			if ( compare ) {
				name += `|${compare}`;
			}

			self.result[ name ] = DELETE_FILTER;

			self.trigger( 'applyFilterToList' );
			self.resetFields( $filter );
		},

		/**
		 * Reset filter fields.
		 *
		 * @param {Node} $filter
		 */
		resetFields: function( $filter ) {
			const self = this;

			if ( $filter.hasClass( 'type_checkbox' ) ) {
				$( 'input[type=checkbox]', $filter ).prop( 'checked', false );

			} else if ( $filter.hasClass( 'type_radio' ) ) {
				$( 'input[type=radio]', $filter ).prop( 'checked', false );
				$( 'input[value="%2A"]', $filter ).prop( 'checked', true ); // check only the "*" value

			} else if ( $filter.hasClass( 'type_dropdown' ) ) {
				$( 'select', $filter ).prop( 'selectedIndex', 0 );

			} else if (
				$filter.hasClass( 'type_date_picker' )
				|| $filter.hasClass( 'type_range_input' )
			) {
				$( 'input', $filter ).val( '' );

			} else if ( $filter.hasClass( 'type_range_slider' ) ) {
				var $input = $( 'input[type=hidden]', $filter ),
					values = [
						$input.attr( 'min' ),
						$input.attr( 'max' )
					];
				$( '.ui-slider', $filter ).slider( 'values', values.map( $ush.parseFloat ) );
			}

			if ( self.$container.hasClass( 'mod_dropdown' ) ) {
				$( '.w-filter-item-title span', $filter ).text( '' );
			}

			$filter.removeClass( 'has_value' );

			$( 'input[name="search_values"]', $filter ).val( '' );
			$( '.w-filter-item-value', $filter ).removeClass( 'hidden' );
		},

		/**
		 * Apply filters to first Post/Product List.
		 *
		 * @event handler
		 */
		applyFilterToList: function() {
			const self = this;
			if (
				! $ush.isUndefined( self.lastResult )
				&& $ush.comparePlainObject( self.result, self.lastResult )
			) {
				return;
			}
			self.lastResult = $ush.clone( self.result );

			self.сheckActiveFilters();

			if ( self.changeURLParams() ) {
				urlManager.set( self.result );
				urlManager.push( {} );
			}

			self.listToFilter().trigger( 'usListFilter', self.result );
		},

		/**
		 * Toggle a filter item section.
		 *
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		toggleItemSection: function( e ) {
			if ( this.titlesAsToggles() ) {
				const $filter = $( e.delegateTarget );
				$filter.toggleClass( 'expand', ! $filter.hasClass( 'expand' ) );
			}
		},

		/**
		 * Toggle a filter item section.
		 *
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		toggleItemDropdown: function( e ) {
			if ( this.$container.hasClass( 'mod_dropdown' ) ) {
				const $filter = $( e.delegateTarget );
				$filter.toggleClass( 'dropped', ! $filter.hasClass( 'dropped' ) );
			}
		},

		/**
		 * Open mobile version.
		 *
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		openMobileFilters: function( e ) {
			$us.$body.addClass( 'us_filter_open' );
			this.$container.addClass( 'open_for_mobile' );
		},

		/**
		 * Close mobile version.
		 *
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		closeMobileFilters: function() {
			$us.$body.removeClass( 'us_filter_open' );
			this.$container.removeClass( 'open_for_mobile' );
		},

		/**
		 * Shows the selected values.
		 */
		showSelectedValues: function() {
			const self = this;
			if ( ! self.$container.hasClass( 'mod_dropdown' ) ) {
				return;
			}
			for ( const key in self.result ) {
				const name = ( key.charAt(0) === '_' )
					? key.substring(1)
					: key;
				var value = self.result[ key ];
				if ( ( self.lastResult || {} )[ key ] === value || $ush.isUndefined( value ) ) {
					continue
				}
				const $filter = self.$filters[ name ];
				const $label = $( '.w-filter-item-title span', $filter );
				if ( value === null ) {
					$label.text( '' );

				} else if ( $filter.hasClass( 'type_dropdown' ) ) {
					$label.text( ': ' + $( `option[value="${value}"]`, $filter ).text() );

				} else if ( $filter.hasClass( 'type_range_slider' ) || $filter.hasClass( 'type_range_input' ) ) {
					$label.text( `: ${self.result[ key ]}` );

				} else if ( $filter.hasClass( 'type_date_picker' ) ) {
					const values = [];
					$( 'input.hasDatepicker', $filter ).each( ( _, input ) => {
						if ( input.value ) {
							values.push( input.value );
						}
					} );
					$label.text( ': ' + values.join( ' - ' ) );

				} else {
					if ( value.includes( ',' ) ) {
						value = value.split( ',' ).length;
					} else {
						value = $( `[data-value="${value}"] .w-filter-item-value-label`, $filter ).text();
					}
					$label.text( `: ${value}` );
				}
			}
		},

		/**
		 * Hide dropped content of every filter item with Dropdown layout.
		 *
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		hideItemDropdown: function( e ) {
			const self = this;
			const $openedFilters = $( '.w-filter-item.dropped', self.$container );
			if ( ! $openedFilters.length ) {
				return;
			}
			$openedFilters.each( ( _, node ) => {
				const $node = $( node );
				if ( ! $node.is( e.target ) && $node.has( e.target ).length === 0 ) {
					$node.removeClass( 'dropped' );
				}
			} );
		},

		/**
		 * Check screen states.
		 *
		 * @event handler
		 */
		checkScreenStates: function() {
			const self = this;
			const isMobile = $ush.parseInt( window.innerWidth ) <= $ush.parseInt( self.data.mobileWidth );

			if ( ! self.$container.hasClass( `state_${ isMobile ? 'mobile' : 'desktop' }` ) ) {
				self.$container.usMod( 'state', isMobile ? 'mobile' : 'desktop' );
				if ( ! isMobile ) {
					$us.$body.removeClass( 'us_filter_open' );
					self.$container.removeClass( 'open_for_mobile' );
				}
			}
		},

		/**
		 * Check active filters.
		 */
		сheckActiveFilters: function() {
			const self = this;
			self.$container.toggleClass( 'active', $( '.has_value:first', self.$container ).length > 0 );
		}

	} );

	$.fn.usListFilter = function() {
		return this.each( ( _, node ) => {
			$( node ).data( 'usListFilter', new usListFilter( node ) );
		} );
	};

	$( () => $( '.w-filter.for_list' ).usListFilter() );

}( jQuery );


! function( $, _undefined ) {
	"use strict";

	// Fixes shortcomings of standard functionality.
	$.fn.fixSlider = function() {
		this.each( ( _, node ) => {
			const inst = $( node ).slider( 'instance' );

			inst._original_refreshValue = inst._refreshValue;

			// 1. The maximum value is displayed only as a multiple of the step, and what is specified.
			inst._calculateNewMax = function() {
				this.max = this.options.max;
			};

			// 2. If the minimum and maximum values are equal, then an error occurs in the interface.
			inst._refreshValue = function() {
				const self = this;

				self._original_refreshValue();

				if ( self._hasMultipleValues() ) {
					var isFixed = false;
					self.handles.each( ( i, handle ) => {
						const valPercent = ( self.values( i ) - self._valueMin() ) / ( self._valueMax() - self._valueMin() ) * 100;
						if ( isNaN( valPercent ) ) {
							$( handle ).css( 'left', `${i*100}%` );
							isFixed = true;
						}
					});
					if ( isFixed ) {
						self.range.css( { left: 0, width: '100%' } );
					}
				}
			};
		} );
	};

}( jQuery );
