/**
 * UpSolution Element: Dropdown
 */
( function( $ ) {
	"use strict";
	$.fn.wDropdown = function() {
		return this.each( function() {
			var $self = $( this ),
				$current = $self.find( '.w-dropdown-current' ),
				$anchors = $self.find( 'a' ),
				openEventName = 'click',
				closeEventName = 'mouseup mousewheel DOMMouseScroll touchstart focusout',
				justOpened = false;
			if ( $self.hasClass( 'open_on_hover' ) ) {
				openEventName = 'mouseenter';
				closeEventName = 'mouseleave';
			}
			var closeList = function() {
				$self.removeClass( 'opened' );
				$us.$window.off( closeEventName, closeListEvent );
			};
			var closeListEvent = function( e ) {
				if ( closeEventName != 'mouseleave' && $self.has( e.target ).length !== 0 ) {
					return;
				}
				e.stopPropagation();
				e.preventDefault();
				closeList();
			};
			var openList = function() {
				$self.addClass( 'opened' );
				if ( closeEventName == 'mouseleave' ) {
					$self.on( closeEventName, closeListEvent );
				} else {
					$us.$window.on( closeEventName, closeListEvent );
				}

				justOpened = true;
				$ush.timeout( function() {
					justOpened = false;
				}, 500 );
			};
			var openListEvent = function( e ) {
				if ( openEventName == 'click' && $self.hasClass( 'opened' ) && ! justOpened ) {
					closeList();
					return;
				}
				openList();
			};

			$current.on( openEventName, openListEvent );
			$self
				.on( 'click', 'a[href$="#"]', function( e ) { e.preventDefault() } )
				.on( 'keydown', function( e ) {
					const keyCode = e.keyCode || e.which;
					if ( keyCode == $ush.TAB_KEYCODE ) {
						var $target = $( e.target ) || {},
							index = $anchors.index( $target );
						if ( e.shiftKey ) {
							if ( index === 0 ) {
								closeList();
							}
						} else {
							if ( index === $anchors.length - 1 ) {
								closeList();
							}
						}
					}
					if ( keyCode == $ush.ESC_KEYCODE ) {
						closeList();
					}
				} );
		} );
	};
	$( function() {
		$( '.w-dropdown' ).wDropdown();
	} );
} )( jQuery );
