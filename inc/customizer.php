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
	// Makes sure the Email Template is available for preview
	if ( get_option( 'vingt_dixsept_email_id' ) ) {
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
	}

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

	// Makes sure the Login Template is available for preview
	if ( get_option( 'vingt_dixsept_login_id' ) ) {
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

		// Allow the admin to enable the login custom header
		$wp_customize->add_setting( 'enable_login_custom_header', array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );

		$wp_customize->add_control( 'enable_login_custom_header', array(
			'label'           => __( 'Intégrer l’arrière plan du site.', 'vingt-dixsept' ),
			'section'         => 'theme_login',
			'type'            => 'radio',
			'choices'         => array(
				0 => __( 'Non', 'vingt-dixsept' ),
				1 => __( 'Oui', 'vingt-dixsept' ),
			)
		) );
	}
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
 * Print WP Head scripts and styles of the customizer only.
 *
 * @since 1.1.0
 */
function vingt_dixsept_login_enqueue_customize_scripts() {
	if ( ! is_customize_preview() ) {
		return;
	}

	foreach ( array(
		'_wp_render_title_tag'            => 1,
		'wp_resource_hints'               => 2,
		'feed_links'                      => 2,
		'feed_links_extra'                => 3,
		'rsd_link'                        => 10,
		'wlwmanifest_link'                => 10,
		'adjacent_posts_rel_link_wp_head' => 10,
		'locale_stylesheet'               => 10,
		'noindex'                         => 1,
		'print_emoji_detection_script'    => 7,
		'wp_print_styles'                 => 8,
		'wp_print_head_scripts'           => 9,
		'wp_generator'                    => 10,
		'rel_canonical'                   => 10,
		'wp_shortlink_wp_head'            => 10,
		'wp_custom_css_cb'                => 101,
		'wp_site_icon'                    => 99,
		) as $hook => $priority ) {
		remove_action( 'wp_head', $hook, $priority );
	}

	wp_head();

	// Dequeue unneeded scripts
	foreach ( wp_scripts()->queue as $script ) {
		if ( 0 !== strpos( $script, 'customize-preview' ) && 0 !== strpos( $script, 'customize-selective-refresh' ) ) {
			wp_dequeue_script( $script );
		}
	}

	// Dequeue unneeded styles
	foreach ( wp_styles()->queue as $style ) {
		if ( 0 !== strpos( $style, 'customize-preview' ) && 0 !== strpos( $style, 'login' ) ) {
			wp_dequeue_style( $style );
		}
	}

	// Print remaining styles and scripts
	wp_print_styles();
	wp_print_head_scripts();
}
add_action( 'login_enqueue_scripts', 'vingt_dixsept_login_enqueue_customize_scripts' );

/**
 * Load our custom controle JS File and global vars.
 *
 * @since 1.0.0
 */
function vingt_dixsept_customize_control_js() {
	$min = vingt_dixsept_js_css_suffix();
  $vs  = vingt_dixsept();

	$email_tpl = (int) get_option( 'vingt_dixsept_email_id' );
	$login_tpl = (int) get_option( 'vingt_dixsept_login_id' );

	if ( ! $email_tpl || ! $login_tpl ) {
		return;
	}

	wp_enqueue_script ( 'vingt_dixsept-customizer-control', get_stylesheet_directory_uri() . "/assets/js/customizer{$min}.js", array(), $vs->version, true );
	wp_localize_script( 'vingt_dixsept-customizer-control', 'vingtDixsept', array(
		'emailUrl'  => esc_url_raw( get_permalink( $email_tpl ) ),
		'loginlUrl' => esc_url_raw( get_permalink( $login_tpl ) ),
	) );
}
add_action( 'customize_controls_enqueue_scripts', 'vingt_dixsept_customize_control_js' );
