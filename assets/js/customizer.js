/**
 * Customizer script
 *
 * Open the email view when the corresponding section is expended.
 *
 * Credits Weston Ruter
 * @see  https://make.xwp.co/2016/07/21/navigating-to-a-url-in-the-customizer-preview-when-a-section-is-expanded/
 */

( function( $, api ) {
	var previousUrl, clearPreviousUrl, previewUrlValue;

	clearPreviousUrl = function() {
		previousUrl = null;
	};

	api.section( 'theme_email', function( section ) {
		previewUrlValue = api.previewer.previewUrl;

		section.expanded.bind( function( isExpanded ) {
			var url;

			if ( isExpanded ) {
				url = vingtDixsept.emailUrl;
				previousUrl = previewUrlValue.get();
				previewUrlValue.set( url );
				previewUrlValue.bind( clearPreviousUrl );

			} else {
				previewUrlValue.unbind( clearPreviousUrl );

				if ( previousUrl ) {
					previewUrlValue.set( previousUrl );
				}
			}
		} );
	} );

} )( jQuery, wp.customize );
