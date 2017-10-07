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
 * @since  1.1.0 Adds a section for the Login screen.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function vingt_dixsept_customize_register( $wp_customize ) {
	// Theme email section.
	$wp_customize->add_section( 'theme_email', array(
		'title'    => __( 'Modèle d’email', 'vingt-dixsept' ),
		'priority' => 125, // Before Theme options.
	) );

	// Allow the admin to disable the email logo
	$wp_customize->add_setting( 'disable_email_logo', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'disable_email_logo', array(
		'label'           => __( 'Intégrer le logo du site dans l’e-mail', 'vingt-dixsept' ),
		'section'         => 'theme_email',
		'type'            => 'radio',
		'choices'         => array(
			0 => __( 'Oui', 'vingt-dixsept' ),
			1 => __( 'Non', 'vingt-dixsept' ),
		),
		'active_callback' => 'vingt_dixsept_has_custom_logo',
	) );

	// Allow the admin to disable the email sitename
	$wp_customize->add_setting( 'disable_email_sitename', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'disable_email_sitename', array(
		'label'           => __( 'Intégrer le nom du site dans l’e-mail', 'vingt-dixsept' ),
		'section'         => 'theme_email',
		'type'            => 'radio',
		'choices'         => array(
			0 => __( 'Oui', 'vingt-dixsept' ),
			1 => __( 'Non', 'vingt-dixsept' ),
		),
	) );

	// Allow the admin to disable the email social links
	$wp_customize->add_setting( 'disable_social_menu', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'disable_social_menu', array(
		'label'           => __( 'Intégrer les liens sociaux dans l’e-mail', 'vingt-dixsept' ),
		'section'         => 'theme_email',
		'type'            => 'radio',
		'choices'         => array(
			0 => __( 'Oui', 'vingt-dixsept' ),
			1 => __( 'Non', 'vingt-dixsept' ),
		),
		'active_callback' => 'vingt_dixsept_has_social_menu',
	) );

	// Default values for the following settings.
	$colorsheme = $wp_customize->get_setting( 'colorscheme' )->value();
	$d_bg_color   = '#FFF';
	$d_line_color = '#222';

	if ( 'light' !== $colorsheme ) {
		$d_bg_color   = '#222';
		$d_line_color = '#333';
	}

	// Allow the admin to customize the header's background color.
	$wp_customize->add_setting( 'header_background_color', array(
		'default'           => $d_bg_color,
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_background_color', array(
		'label'       => __( 'Couleur d’arrière plan de l’en-tête', 'vingt-dixsept' ),
		'section'     => 'theme_email',
	) ) );

	// Allow the admin to customize the header's underline color.
	$wp_customize->add_setting( 'header_line_color', array(
		'default'           => $d_line_color,
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_line_color', array(
		'label'       => __( 'Couleur de soulignement de l’en-tête', 'vingt-dixsept' ),
		'section'     => 'theme_email',
	) ) );

	// Maintenance page
	$wp_customize->add_setting( 'maintenance_mode', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( 'maintenance_mode', array(
		'label'       => __( 'Maintenance', 'vingt-dixsept' ),
		'section'     => 'theme_options',
		'type'        => 'radio',
		'choices'     => array(
			0 => __( 'Pas de maintenance.', 'vingt-dixsept' ),
			1 => __( 'Maintenance en cours.', 'vingt-dixsept' ),
		),
	) );

	$wp_customize->selective_refresh->add_partial( 'maintenance_mode', array(
		'selector'         => '#maintenance-mode',
		'render_callback'  => 'vingt_dixsept_display_maintenance_mode_info',
		'fallback_refresh' => true,
	) );

	// Theme login section.
	$wp_customize->add_section( 'theme_login', array(
		'title'    => __( 'Formulaire de connexion', 'vingt-dixsept' ),
		'priority' => 135, // After Theme options.
	) );

	// Allow the admin to enable the login logo
	$wp_customize->add_setting( 'enable_login_logo', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'enable_login_logo', array(
		'label'           => __( 'Remplacer le logo de WordPress par celui du site', 'vingt-dixsept' ),
		'section'         => 'theme_login',
		'type'            => 'radio',
		'choices'         => array(
			0 => __( 'Non', 'vingt-dixsept' ),
			1 => __( 'Oui', 'vingt-dixsept' ),

		),
		'active_callback' => 'vingt_dixsept_has_site_icon',
	) );
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
 * Is there a social menu for the site ?
 *
 * @since 1.0.0.
 *
 * @return bool True if a social menu is activated. False otherwise.
 */
function vingt_dixsept_has_social_menu() {
	return (bool) has_nav_menu( 'social' );
}

/**
 * Is there site icon for the site ?
 *
 * @since 1.1.0.
 *
 * @return bool True if a site icon is activated. False otherwise.
 */
function vingt_dixsept_has_site_icon() {
	return (bool) get_site_icon_url( 84 );
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
		'emailUrl'  => esc_url_raw( get_permalink( get_option( 'vingt_dixsept_email_id' ) ) ),
		'loginlUrl' => esc_url_raw( get_permalink( get_option( 'vingt_dixsept_login_id' ) ) ),
	) );
}
add_action( 'customize_controls_enqueue_scripts', 'vingt_dixsept_customize_control_js' );
