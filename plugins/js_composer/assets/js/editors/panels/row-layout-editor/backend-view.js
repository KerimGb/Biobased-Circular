/**
 * RowLayoutEditorPanelViewBackend extends the row layout editor for backend operations.
 *
 * It customizes layout handling and builder integration for the backend editor.
 */

( function ( $ ) {
	'use strict';
	if ( _.isUndefined( window.vc ) ) {
		window.vc = {};
	}

	vc.RowLayoutEditorPanelViewBackend = vc.RowLayoutEditorPanelView.extend({
		builder: function () {
			if ( !this.builder ) {
				this.builder = vc.storage;
			}
			return this.builder;
		},
		isBuildComplete: function () {
			return true;
		},
		setLayout: function ( e ) {
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}
			var $control = $( e.currentTarget ),
				layout = $control.attr( 'data-cells' ),
				columns = this.model.view.convertRowColumns( layout );
			this.$input.val( columns.join( ' + ' ) );
		}
	});

})( window.jQuery );
