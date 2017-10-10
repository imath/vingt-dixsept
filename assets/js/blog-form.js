/* Used during a network site signup */

( function( document ) {

	/**
	 * Waits for the window to be loaded.
	 *
	 * @return {Void}
	 */
	window.addEventListener( 'load', function() {
		var loaded = false, domainInfo, blogForm;

		if ( loaded ) {
			return;
		}

		// Get the form thanks to its ID.
		blogForm = document.querySelector( '#setupform' );

		for ( var child in blogForm.childNodes ) {
			// The first paragraph contains the Domain informations.
			if ( 'P' === blogForm.childNodes[ child ].nodeName && ! domainInfo ) {
				domainInfo = blogForm.childNodes[ child ];
				domainInfo.innerHTML = domainInfo.innerHTML.replace( '(', '<p class="signup-site-url">' ).replace( ')', '</p>' );

			// We need to add a class to the site language container
			} else {
				var grandsons = blogForm.childNodes[ child ].childNodes;

				if ( grandsons ) {
					grandsons.forEach( function( grandson ) {
						if ( ! grandson.attributes ) {
							return;
						} else if ( 'site-language' === grandson.getAttribute( 'id' ) ) {
							blogForm.childNodes[ child ].setAttribute( 'class', 'signup-site-language' );
						} else if ( 'privacy-intro' === grandson.getAttribute( 'class' ) ) {
							grandson.childNodes.forEach( function( grandgrandson ) {
								if ( grandgrandson.attributes && 'blog_public_on' === grandgrandson.getAttribute( 'for' ) && ! grandgrandson.getAttribute( 'class' ) ) {
									grandgrandson.innerHTML = grandgrandson.nextSibling.nodeValue;
									grandgrandson.nextSibling.remove();
								}
							} );
						}
					} );
				}
			}
		}

		if ( domainInfo ) {
			blogForm.setAttribute( 'style', 'position:relative;' );
			domainInfo.setAttribute( 'class', 'signup-toggle-info' );

			document.querySelector( '#blogname' ).addEventListener( 'focus', function( event ) {
				domainInfo.setAttribute( 'class', 'signup-toggle-info show' );
				domainInfo.setAttribute( 'style', 'top:' + Number( document.getElementById( 'blogname' ).offsetTop + 30 ) + 'px;width:' + Number( document.getElementById( 'blogname' ).clientWidth - 10 ) + 'px;' );
			} );

			document.querySelector( '#blogname' ).addEventListener( 'blur', function() {
				domainInfo.setAttribute( 'class', 'signup-toggle-info' );
				domainInfo.setAttribute( 'style', '' );
			} );
		}

		loaded = true;
	} );
} )( window.document );
