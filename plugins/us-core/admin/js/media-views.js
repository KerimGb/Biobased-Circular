;( function( $, _undefined ) {

	window.wp = window.wp || {};
	window.usCurrentPostId = window.usCurrentPostId || 0;

	const media = wp.media;
	const curAttachmentsBrowser = media.view.AttachmentsBrowser;

	// Set current post ID for the builder
	if ( usCurrentPostId ) {
		media.model.settings.post.id = usCurrentPostId;
	}

	media.view.AttachmentFilters.Taxonomy = media.view.AttachmentFilters.extend( {
		tagName: 'select',
		createFilters: function() {
			const self = this;
			let filters = {};

			_.each( self.options.termList || {}, ( term, key ) => {
				const term_id = term['term_id'];
				const term_name = $( '<div/>' ).html( term['term_name'] ).text();

				filters[ term_id ] = {
					text: term_name,
					priority: key + 2
				};
				filters[ term_id ]['props'] = {};
				filters[ term_id ]['props'][ self.options.taxonomy ] = term_id;

			} );

			filters.all = {
				text: self.options.termListTitle,
				priority: 1
			};
			filters['all']['props'] = {};
			filters['all']['props'][ self.options.taxonomy ] = null;

			self.filters = filters;
		}
	} );

	media.view.AttachmentsBrowser = media.view.AttachmentsBrowser.extend( {
		createToolbar: function() {
			const self = this;

			// Set default action for Live Builder
			if ( usCurrentPostId ) {
				self.options.filters = 'uploaded';
			}

			curAttachmentsBrowser.prototype.createToolbar.apply( self, arguments );

			let i = 1;
			$.each( us_media_categories_taxonomies, ( taxonomy, values ) => {
				if ( values.term_list && self.options.filters ) {
					self.toolbar.set( `${taxonomy}-filter`, new media.view.AttachmentFilters.Taxonomy( {
						controller: self.controller,
						model: self.collection.props,
						priority: - 80 + 10 * i++,
						taxonomy: taxonomy,
						termList: values.term_list,
						termListTitle: values.list_title,
						className: 'attachment-filters for_us_media'
					} ).render() );
				}
			} );
		}
	} );

	$.extend( wp.Uploader.prototype, {
		success: function( file_attachment ) {
			const data = {
				action: 'us_ajax_set_category_on_upload',
				post_id: file_attachment.attributes.id,
				category: $( ".attachment-filters.for_us_media" ).val(),
			};
			$.post( ajaxurl, data, $.noop );

		}
	} );

} )( jQuery );
