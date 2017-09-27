<?php
/**
 * Customizer functions
 *
 * @package Vingt DixSept\inc
 *
 * @since  1.0.0
 */


 // Exit if accessed directly.
 defined( 'ABSPATH' ) || exit;

/**
 * Add custom settings for the Theme Customizer.
 *
 * @since  1.0.0
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function vingt_dixsept_customize_register( $wp_customize ) {
	// Theme email section.
	$wp_customize->add_section( 'theme_email', array(
		'title'    => __( 'Modèle d\'email', 'vingt-dixsept' ),
		'priority' => 125, // Before Theme options.
	) );

	// Allow the admin to disable the email logo
	$wp_customize->add_setting( 'disable_email_logo', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'disable_email_logo', array(
		'label'           => __( 'Intégrer le logo du site dans l\'email', 'vingt-dixsept' ),
		'section'         => 'theme_email',
		'type'            => 'radio',
		'choices'         => array(
			0 => __( 'Oui', 'vingt-dixsept' ),
			1 => __( 'Non', 'vingt-dixsept' ),
		),
		'active_callback' => 'vingt_dixsept_has_custom_logo',
	) );

	// Allow the admin to customize the header's under line color.
	$wp_customize->add_setting( 'email_header_line_color', array(
		'default'           => '#ccc',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'email_header_line_color', array(
		'label'       => __( 'Couleur de soulignement de l\'entête', 'vingt-dixsept' ),
		'section'     => 'theme_email',
	) ) );

	// Allow the admin to customize the links color.
	$wp_customize->add_setting( 'email_body_link_color', array(
		'default'           => '#222',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'email_body_link_color', array(
		'label'       => __( 'Couleur des liens', 'vingt-dixsept' ),
		'section'     => 'theme_email',
	) ) );

	// Allow the admin to customize the text color.
	$wp_customize->add_setting( 'email_body_text_color', array(
		'default'           => '#555',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'email_body_text_color', array(
		'label'       => __( 'Couleur du texte', 'vingt-dixsept' ),
		'section'     => 'theme_email',
	) ) );
}
 add_action( 'customize_register', 'vingt_dixsept_customize_register'  );

/**
 * Is there a custom logo for the site ?
 *
 * @since 1.0.0.
 *
 * @return bool True if a custom logo is activated. False otherwise.
 */
function vingt_dixsept_has_custom_logo() {
	return (bool) has_custom_logo();
}

/**
 * Load our custom controle JS File and global vars.
 *
 * @since 1.0.0
 */
function vingt_dixsept_customize_control_js() {
	$min = vingt_dixsept_js_css_suffix();
  $vs  = vingt_dixsept();

	wp_enqueue_script ( 'vingt_dixsept-customizer-control', get_stylesheet_directory_uri() . "/assets/js/customizer{$min}.js", array(), $vs->version, true );
	wp_localize_script( 'vingt_dixsept-customizer-control', 'vingtDixsept', array(
		'emailUrl' => esc_url_raw( get_permalink( get_option( 'vingt_dixsept_email_id' ) ) ),
	) );
}
 add_action( 'customize_controls_enqueue_scripts', 'vingt_dixsept_customize_control_js' );
