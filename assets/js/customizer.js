/**
 * Customizer script
 *
 * Open the email view when the corresponding section is expended.
 *
 * Credits Weston Ruter
 * @see  https://make.xwp.co/2016/07/21/navigating-to-a-url-in-the-customizer-preview-when-a-section-is-expanded/
 */

/* global vingtDixsept */

( function( $, api ) {
	var previousUrl, clearPreviousUrl, setPreviewUrl, previewUrlValue;

	clearPreviousUrl = function() {
		previousUrl = null;
	};

	setPreviewUrl = function( url, isExpanded ) {
		if ( ! url ) {
			return null;
		}

		if ( isExpanded ) {
			previousUrl = previewUrlValue.get();
			previewUrlValue.set( url );
			previewUrlValue.bind( clearPreviousUrl );

		} else {
			previewUrlValue.unbind( clearPreviousUrl );

			if ( previousUrl ) {
				previewUrlValue.set( previousUrl );
			}
		}
	};

	api.section( 'theme_email', function( section ) {
		previewUrlValue = api.previewer.previewUrl;

		section.expanded.bind( function( isExpanded ) {
			setPreviewUrl( vingtDixsept.emailUrl, isExpanded );
		} );
	} );

	api.section( 'theme_login', function( section ) {
		previewUrlValue = api.previewer.previewUrl;

		section.expanded.bind( function( isExpanded ) {
			setPreviewUrl( vingtDixsept.loginlUrl, isExpanded );
		} );
	} );

} )( jQuery, wp.customize );
