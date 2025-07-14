/**
 * PostSettingsPanelViewBackendEditor extends the base PostSettingsPanelView for the backend editor.
 *
 * It extends the save method to mark storage as changed.
 */

( function () {
	'use strict';
	if ( _.isUndefined( window.vc ) ) {
		window.vc = {};
	}

	vc.PostSettingsPanelViewBackendEditor = vc.PostSettingsPanelView.extend({
		render: function () {
			this.trigger( 'render' );
			this.trigger( 'afterRender' );
			return this;
		},
		save: function () {
			vc.PostSettingsPanelViewBackendEditor.__super__.save.call( this );
			vc.storage.isChanged = true;
		}
	});

})( window.jQuery );
