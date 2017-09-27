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
