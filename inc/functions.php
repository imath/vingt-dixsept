<?php
/**
 * Custom functions
 *
 * @package Vingt DixSept\inc
 *
 * @since  1.0.0
 */

/**
 * Gets the min suffix for CSS & JS.
 *
 * @since 1.0.0
 *
 * @return string The min suffix for CSS & JS.
 */
function vingt_dixsept_js_css_suffix() {
	$min = '.min';

	if ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG )  {
		$min = '';
	}

	/**
	 * Filter here to edit the min suffix.
	 *
	 * @since 1.0.0
	 *
	 * @param string $min The min suffix for CSS & JS.
	 */
	return apply_filters( 'vingt_dixsept_js_css_suffix_suffix', $min );
}

/**
 * Enqueues the Child Theme's specific CSS.
 *
 * @since 1.0.0
 */
function vingt_dixsept_enqueue_styles() {
	$min = vingt_dixsept_js_css_suffix();
	$vs  = vingt_dixsept();

	// Register the TwentySeventeen stylesheet to use it as a dependancy.
	wp_register_style( 'parent-style', get_template_directory_uri() . '/style.css' );

	// Enqueue the Child theme's stylesheet.
	wp_enqueue_style(
		'child-style',
		get_stylesheet_directory_uri() . "/assets/css/style{$min}.css",
		array( 'parent-style' ),
		$vs->version
	);
}
add_action( 'wp_enqueue_scripts', 'vingt_dixsept_enqueue_styles' );

/**
 * Use this Child Theme's default header instead of the TwentySeventeen's one.
 *
 * @since 1.0.0
 *
 * @param  array  $args The custom header arguments.
 * @return array        The custom header arguments.
 */
function vingt_dixsept_custom_header_args( $args = array() ) {
	$args['default-image'] = get_theme_file_uri( '/assets/images/header.jpg' );

	return $args;
}
add_filter( 'twentyseventeen_custom_header_args', 'vingt_dixsept_custom_header_args', 10, 1 );

/**
 * Registers a private Post Type to use for the email sample.
 *
 * @since 1.0.0
 */
function vingt_dixsept_register_email_type() {
	register_post_type(
		'vingt_dixsept_email',
		array(
			'label'              => 'vingt_dixsept_email',
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => false,
			'show_in_menu'       => false,
			'show_in_nav_menus'  => false,
			'query_var'          => false,
			'rewrite'            => false,
			'has_archive'        => false,
			'hierarchical'       => true,
		)
	);
}
add_action( 'init', 'vingt_dixsept_register_email_type' );

/**
 * Uses a template to render emails
 *
 * @since  1.0.0
 *
 * @param  string       $text The text of the email.
 * @return string|false       The html text for the email, or false.
 */
function vingt_dixsept_email_set_html_content( $text ) {
	if ( empty( $text ) ) {
		return false;
	}

	ob_start();
	get_template_part( 'email' );
	$email_template = ob_get_clean();

	if ( empty( $email_template ) ) {
		return false;
	}

	// Make sure the link to set or reset the password
	// will be clickable in text/html
	if ( did_action( 'retrieve_password_key' ) ) {
		preg_match( '/<(.+?)>/', $text, $match );

		if ( ! empty( $match[1] ) ) {

			$login_url = wp_login_url();
			$link      = "\n" . '<a href="' . $match[1] . '">' . $login_url . '</a>';

			if ( preg_match( '/[^<]' . addcslashes( $login_url, '/' ) . '/', $text ) ) {
				$text = preg_replace( '/[^<]' . addcslashes( $login_url, '/' ) . '/', $link, $text );
			} else {
				$text .= $link;
			}

			$text = str_replace( $match[0], '', $text );
		}
	}

	$pagetitle = esc_attr( get_bloginfo( 'name', 'display' ) );
	$content   = apply_filters( 'the_content', $text );

	// Make links clickable
	$content = make_clickable( $content );

	$email = str_replace( '{{pagetitle}}', $pagetitle, $email_template );
	$email = str_replace( '{{content}}',   $content,   $email          );

	return $email;
}

/**
 * Uses a multipart/alternate email.
 *
 * NB: follow the progress made on
 * https://core.trac.wordpress.org/ticket/15448
 *
 * @since 1.0.0
 *
 * @param PHPMailer $phpmailer The Mailer class.
 */
function vingt_dixsept_email( PHPMailer $phpmailer ) {
	if ( empty( $phpmailer->Body ) ) {
		return;
	}

	$html_content = vingt_dixsept_email_set_html_content( $phpmailer->Body );

	if ( $html_content ) {
		$phpmailer->AltBody = $phpmailer->Body;
		$phpmailer->Body    = $html_content;
	}
}
add_action( 'phpmailer_init', 'vingt_dixsept_email', 10, 1 );

/**
 * Makes sure the Logo is 60px wide or tall into the email's header.
 *
 * @since 1.0.0
 *
 * @param  array  $image An array containing the src, width and height in pixels of the image.
 * @return array         An array containing the src, width and height in pixels of the image.
 */
function vingt_dixsept_email_logo_size( $image = array() ) {
	if ( isset( $image[1] ) && isset( $image[2] ) ) {
		$width  = $image[1];
		$height = $image[2];

		if ( $width > $height ) {
			$image[2] = floor( ( $height/ $width ) * 60 );
			$image[1] = 60;
		} else {
			$image[1] = floor( ( $width / $height ) * 60 );
			$image[2] = 60;
		}
	}

	return $image;
}

/**
 * Displays the site logo into the email.
 *
 * @since  1.0.0
 */
function vingt_dixsept_email_logo() {
	if ( ! has_custom_logo() ) {
		return;
	}

	// Filter just before the custom logo tag to control its size in pixels.
	add_filter( 'wp_get_attachment_image_src', 'vingt_dixsept_email_logo_size', 10, 1 );
	?>
	<div id="site-logo">
		<?php the_custom_logo(); ?>
	</div>
	<?php

	// Stop filtering once it's no more needed.
	remove_filter( 'wp_get_attachment_image_src', 'vingt_dixsept_email_logo_size', 10, 1 );
}

/**
 * Displays the site name into the email.
 *
 * @since  1.0.0
 */
function vingt_dixsept_email_sitename() {
	$name = get_bloginfo( 'name' );

	if ( ! $name ) {
		return;
	}

	echo esc_html( $name );
}

/**
 * Gets a color map for the TwentySeventeen colorscheme.
 *
 * @since 1.0.0
 *
 * @return array The color map for the TwentySeventeen colorscheme.
 */
function vingt_dixsept_email_get_scheme_colors() {
	$hue = absint( get_theme_mod( 'colorscheme_hue', 250 ) );

	/**
	 * Filter Twenty Seventeen default saturation level.
	 *
	 * @since Twenty Seventeen 1.0
	 *
	 * @param int $saturation Color saturation level.
	 */
	$saturation = absint( apply_filters( 'twentyseventeen_custom_colors_saturation', 50 ) );
	$reduced_saturation = ( .8 * $saturation ) . '%';
	$saturation = $saturation . '%';
	$base_hsl   = 'hsl( ' . $hue . ', %1$s, %2$s )';

	/**
	 * Use this filter to edit the color map.
	 *
	 * @since 1.0.0
	 *
	 * @param array $value The color map for the TwentySeventeen colorscheme.
	 */
	return apply_filters( 'vingt_dixsept_email_get_scheme_colors',  array(
		'light' => array(
			'title_text' => '#333',
			'title_bg'   => '#FFF',
			'separator'  => '#222',
			'body_text'  => '#333',
			'body_link'  => '#222',
			'body_bg'    => '#FFF',
		),
		'dark' => array(
			'title_text' => '#FFF',
			'title_bg'   => '#222',
			'separator'  => '#333',
			'body_text'  => '#333',
			'body_link'  => '#222',
			'body_bg'    => '#FFF',
		),
		'custom' => array(
			'title_text' => sprintf( $base_hsl, $reduced_saturation, '20%' ),
			'title_bg'   => '#FFF',
			'separator'  => sprintf( $base_hsl, $saturation, '20%' ),
			'body_text'  => '#333',
			'body_link'  => sprintf( $base_hsl, $saturation, '20%' ),
			'body_bg'    => '#FFF',
		),
	) );
}

/**
 * Gets a specific area's color or all areas colors.
 *
 * @since 1.0.0
 *
 * @param  string $part The specific area's color key.
 * @return array        A specific area's color or all areas colors.
 */
function vingt_dixsept_email_colors( $part = '' ) {
	$vds = vingt_dixsept();
	$sc  = vingt_dixsept_email_get_scheme_colors();

	if ( ! isset( $vds->email_colors ) ) {
		$colorscheme = get_theme_mod( 'colorscheme', 'default' );

		$vds->email_colors = $sc[ $colorscheme ];
	}

	if ( ! $part ) {
		return $vds->email_colors;
	}

	if ( ! isset( $vds->email_colors[ $part ] ) ) {
		return '#FFF';
	}

	return $vds->email_colors[ $part ];
}

/**
 * Outputs the Email's title color.
 *
 * @since 1.0.0
 */
function vingt_dixsept_email_title_text_color() {
	$title_color = get_theme_mod( 'header_textcolor', 'blank' );

	if ( ! $title_color || 'blank' === $title_color ) {
		$title_color = vingt_dixsept_email_colors( 'title_text' );
	} else {
		$title_color = '#' . $title_color;
	}

	echo $title_color;
}

/**
 * Outputs the Email's title background color.
 *
 * @since 1.0.0
 */
function vingt_dixsept_email_title_bg_color() {
	$header_bg_color = get_theme_mod( 'header_background_color' );

	if ( ! $header_bg_color || in_array( $header_bg_color, array( '#222', '#FFF' ), true ) ) {
		$header_bg_color = vingt_dixsept_email_colors( 'title_bg' );
	}

	echo $header_bg_color;
}

/**
 * Outputs the Email's header underline color.
 *
 * @since 1.0.0
 */
function vingt_dixsept_email_separator_color() {
	$separator_color = get_theme_mod( 'header_line_color' );

	if ( ! $separator_color || in_array( $separator_color, array( '#222', '#333' ), true ) ) {
		$separator_color = vingt_dixsept_email_colors( 'separator' );
	}

	echo $separator_color;
}

/**
 * Outputs the Email's body text color.
 *
 * @since 1.0.0
 */
function vingt_dixsept_email_body_text_color() {
	echo vingt_dixsept_email_colors( 'body_text' );
}

/**
 * Outputs the Email's link color.
 *
 * @since 1.0.0
 */
function vingt_dixsept_email_body_link_color() {
	echo vingt_dixsept_email_colors( 'body_link' );
}

/**
 * Outputs the Email's body background color.
 *
 * @since 1.0.0
 */
function vingt_dixsept_email_body_bg_color() {
	echo vingt_dixsept_email_colors( 'body_bg' );
}

/**
 * Prints the content of the CSS file used to style the emails.
 *
 * @since 1.0.0
 */
function vingt_dixsept_email_print_css() {
	/**
	 * Filter here to replace the base email CSS rules.
	 *
	 * @since 1.0.0
	 *
	 * @param string Absolute file to the css file.
	 */
	$css = apply_filters( 'vingt_dixsept_email_email_get_css', sprintf( '/%1$semail%2$s.css',
		get_theme_file_path( '/assets' ),
		vingt_dixsept_js_css_suffix()
	) );

	// Directly insert it into the email template.
	if ( $css && file_exists( $css ) ) {
		include( $css );
	}

	$link_color = vingt_dixsept_email_colors( 'body_link' );

	// Add css overrides for the links
	if ( '#222' !== $link_color ) {
		printf( '
			a,
			a:hover,
			a:visited,
			a:active {
				color: %s;
			}
		', esc_attr( $link_color ) );
	}

	// Add css overrides for the text color of the header
	if ( is_customize_preview() ) {
		echo '
			tr { border-bottom: none; }
			table { margin: 0; }
			a { text-decoration: underline !important; }
			.container-padding.header a.custom-logo-link { padding: 0; }
		';
	}
}

/**
 * Upgrade the theme db version
 *
 * @since  1.0.0
 */
function vingt_dixsept_upgrade() {
	if ( is_customize_preview() ) {
		return;
	}

	$db_version = get_option( 'vingt_dixsept_version', 0 );
	$version    = vingt_dixsept()->version;

	if ( ! version_compare( $db_version, $version, '<' ) ) {
		return;
	}

	$email_post_id = (int) get_option( 'vingt_dixsept_email_id', 0 );

	if ( ! $email_post_id ) {
		$email_post_id = wp_insert_post( array(
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_status'    => 'private',
			'post_title'     => __( 'Modèle d\'email', 'vingt-dixsept' ),
			'post_type'      => 'vingt_dixsept_email',
			'post_content'   => sprintf( '<p>%1$s</p><p>%2$s</p><p>%3$s</p>',
				__( 'Vous pouvez personnaliser le gabarit qui sera utilisé pour les emails envoyés par WordPress.', 'vingt-dixsept' ),
				__( 'Pour cela utilisez la barre latérale pour spécifier vos préférences.', 'vingt-dixsept' ),
				__( 'Voici comment seront affichés les <a href="#">liens</a> contenus dans certains emails.', 'vingt-dixsept' )
			),
		) );

		update_option( 'vingt_dixsept_email_id', $email_post_id );
	}

	// Update version.
	update_option( 'vingt_dixsept_version', $version );
}
add_action( 'admin_init', 'vingt_dixsept_upgrade', 1000 );
