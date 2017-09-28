<?php
/**
 * Custom functions
 *
 * @package Vingt DixSept\inc
 *
 * @since  1.0.0
 */

function vingt_dixsept_enqueue_styles() {
	wp_register_style( 'parent-style', get_template_directory_uri() . '/style.css' );

	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/assets/css/style.css', array( 'parent-style' ) );
}
add_action( 'wp_enqueue_scripts', 'vingt_dixsept_enqueue_styles' );

function vingt_dixsept_custom_header_args( $args = array() ) {
	$args['default-image'] = get_theme_file_uri( '/assets/images/header.jpg' );

	return $args;
}
add_filter( 'twentyseventeen_custom_header_args', 'vingt_dixsept_custom_header_args', 10, 1 );

function vingt_dixsept_js_css_suffix() {
	$min = '.min';

	if ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG )  {
		$min = '';
	}

	return apply_filters( 'vingt_dixsept_js_css_suffix_suffix', $min );
}

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
 * Display the site logo into the email.
 *
 * @since  1.0.0
 *
 * @return string HTML Output.
 */
function vingt_dixsept_email_logo() {
	if ( ! has_custom_logo() ) {
		return;
	}
	?>
	<div id="site-logo">
		<?php the_custom_logo(); ?>
	</div>
	<?php
}

function vingt_dixsept_email_line_color() {
	echo get_theme_mod( 'email_header_line_color', 'default' );
}

function vingt_dixsept_email_text_color() {
	$color         = get_theme_mod( 'email_body_text_color', 'default' );
	$default_color = vingt_dixsept_email_get_default_color();

	if ( '#333' !== $default_color && $color === '#555' ) {
		$color = $default_color;
	}

	echo $color;
}

function vingt_dixsept_email_get_default_color() {
	$colorscheme = get_theme_mod( 'colorscheme' );
	$hexcolor    = '#333';

	if ( ! $colorscheme || 'light' === $colorscheme ) {
		return $hexcolor;
	}

	if ( 'dark' === $colorscheme ) {
		$hexcolor = '#eee';
	}

	return $hexcolor;
}

function vingt_dixsept_email_print_css() {
	/**
	 * Filter here to replace the base email stylerules.
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

	$default_color = vingt_dixsept_email_get_default_color();
	$link_color    = get_theme_mod( 'email_body_link_color' );

	if ( ! $link_color && '#333' !== $default_color ) {
		$link_color = $default_color;
	}

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
