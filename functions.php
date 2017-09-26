<?php
/**
 * Custom functions
 *
 * @package Vingt DixSept
 * 
 * @since  1.0.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

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
