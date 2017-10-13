/* Used during a network user/site signup */

( function( document ) {

	/**
	 * Waits for the window to be loaded.
	 *
	 * @return {Void}
	 */
	window.addEventListener( 'load', function() {
		var loaded = false, userHelp = {}, signupForm;

		if ( loaded ) {
			return;
		}

		// Get the form thanks to its ID.
		signupForm = document.querySelector( '#setupform' );

		for ( var child in signupForm.childNodes ) {
			// Reorganize the Blog form.
			if ( document.querySelector( '#blogname' ) ) {
				// The first paragraph contains the Domain informations.
				if ( 'P' === signupForm.childNodes[ child ].nodeName && ! userHelp.blogname ) {
					userHelp.blogname = signupForm.childNodes[ child ];
					userHelp.blogname.innerHTML = userHelp.blogname.innerHTML.replace( '(', '<p class="signup-site-url">' ).replace( ')', '</p>' );
					userHelp.blogname.setAttribute( 'class', 'signup-toggle-info' );

					if ( 'BR' === signupForm.childNodes[ child ].previousSibling.nodeName ) {
						signupForm.childNodes[ child ].previousSibling.remove();
					}

				// We need to add a class to the site language container
				} else {
					var grandsons = signupForm.childNodes[ child ].childNodes;

					if ( grandsons ) {
						grandsons.forEach( function( grandson ) {
							if ( ! grandson.attributes ) {
								return;
							} else if ( 'site-language' === grandson.getAttribute( 'id' ) ) {
								signupForm.childNodes[ child ].setAttribute( 'class', 'signup-site-language' );
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

			// Reorganize the User form.
			} else if ( document.querySelector( '#user_name' ) ) {
				if ( signupForm.childNodes[ child ].nodeName && 'BR' === signupForm.childNodes[ child ].nodeName ) {
					var b = signupForm.childNodes[ child ], t = signupForm.childNodes[ child ].nextSibling
					    p = signupForm.childNodes[ child ].previousSibling;

					if ( t && p.getAttribute( 'name' ) ) {
						var key = p.getAttribute( 'name' );

						userHelp[ key ] = document.createElement( 'p' );
						userHelp[ key ].innerHTML = t.nodeValue.replace( '(', '' ).replace( ')', '' );
						userHelp[ key ].setAttribute( 'class', 'signup-toggle-info' );
						signupForm.insertBefore( userHelp[ key ], b );

						b.remove();
						t.remove();
					}
				}
			}
		}

		if ( userHelp ) {
			signupForm.setAttribute( 'style', 'position:relative;' );

			Object.keys( userHelp ).forEach( function( k ) {
				document.querySelector( 'input[name=' + k + ']' ).addEventListener( 'focus', function( event ) {
					if ( 'signup-toggle-info' === event.currentTarget.nextSibling.getAttribute( 'class' ) ) {
						event.currentTarget.nextSibling.setAttribute( 'class', 'signup-toggle-info show' );
						event.currentTarget.nextSibling.setAttribute( 'style', 'top:' + Number( event.currentTarget.offsetTop + 30 ) + 'px;width:' + Number( event.currentTarget.clientWidth - 10 ) + 'px;' );
					}
				} );

				document.querySelector( 'input[name=' + k + ']' ).addEventListener( 'blur', function( event ) {
					if ( 'signup-toggle-info show' === event.currentTarget.nextSibling.getAttribute( 'class' ) ) {
						event.currentTarget.nextSibling.setAttribute( 'class', 'signup-toggle-info' );
						event.currentTarget.nextSibling.setAttribute( 'style', '' );
					}
				} );
			} );
		}

		loaded = true;
	} );
} )( window.document );
