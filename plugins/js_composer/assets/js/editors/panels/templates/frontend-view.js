/**
 * @since 4.4
 * TemplatesPanelViewFrontend extends the backend templates view for frontend editor.
 *
 * It customizes template loading and rendering for the frontend editor interface.
 */

( function ( $ ) {
	'use strict';
	if ( _.isUndefined( window.vc ) ) {
		window.vc = {};
	}

	vc.TemplatesPanelViewFrontend = vc.TemplatesPanelViewBackend.extend({
		template_load_action: 'vc_frontend_load_template',
		loadUrl: false,
		initialize: function () {
			this.loadUrl = vc.$frame.attr( 'src' );
			vc.TemplatesPanelViewFrontend.__super__.initialize.call( this );
		},
		render: function () {
			return vc.TemplatesPanelViewFrontend.__super__.render.call( this );
		},
		renderTemplate: function ( html ) {
			// Render template for frontend
			var template, data;
			_.each( $( html ), function ( element ) {
				if ( 'vc_template-data' === element.id ) {
					try {
						data = JSON.parse( element.innerHTML );
					} catch ( err ) {
						if ( window.console && window.console.warn ) {
							window.console.warn( 'renderTemplate error', err );
						}
					}
				}
				if ( 'vc_template-html' === element.id ) {
					template = element.innerHTML;
				}
			});
			// todo check this message appearing: #48591595835639
			if ( template && data && vc.builder.buildFromTemplate( template, data ) ) {
				this.showMessage( window.i18nLocale.template_added_with_id, 'error' );
			} else {
				this.showMessage( window.i18nLocale.template_added, 'success' );
			}
			vc.closeActivePanel();
		}
	});

})( window.jQuery );
