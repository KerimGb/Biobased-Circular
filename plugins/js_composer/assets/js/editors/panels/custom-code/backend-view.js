/**
 * CustomCodeViewBackendEditor extends the base CustomCodePanelView for the backend editor.
 *
 * It extends the save method to mark storage as changed and sets alert if custom code data differs from saved data.
 */

( function () {
	'use strict';
	if ( _.isUndefined( window.vc ) ) {
		window.vc = {};
	}

	vc.CustomCodeViewBackendEditor = vc.CustomCodePanelView.extend({
		render: function () {
			this.trigger( 'render' );
			this.trigger( 'afterRender' );
			this.setEditor();
			return this;
		},
		/**
         * Set alert if custom code data differs from saved data.
         *
         * @deprecated
         */
		setAlertOnDataChange: function () {
			if ( this.editor_css && vc.saved_custom_css !== this.editor_css.getValue() && window.tinymce ) {
				window.switchEditors.go( 'content', 'tmce' );
				window.setTimeout( function () {
					window.tinymce.get( 'content' ).isNotDirty = false;
				}, 1000 );
			}
		},
		save: function () {
			vc.CustomCodeViewBackendEditor.__super__.save.call( this );
			vc.storage.isChanged = true;
		}
	});

})( window.jQuery );
