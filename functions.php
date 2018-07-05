<?php
/**
 * Bootstrap class.
 *
 * @package Vingt DixSept
 *
 * @since  1.0.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Theme Bootstrap class
 *
 * @since  1.0.0
 */
final class VingtDixSept {
	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Initialize the theme
	 *
	 * @since  1.0.0
	 */
	private function __construct() {
		$this->globals();
		$this->inc();
		$this->setup_supports();
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since  1.0.0
	 */
	public static function start() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Set some globals for the theme
	 *
	 * @since  1.0.0
	 * @since  1.2.0 Add a global to check if Gutenberg is active.
	 */
	private function globals() {
		$this->version             = '1.2.0-alpha';
		$this->is_gutenberg_active = function_exists( 'the_gutenberg_project' );
	}

	/**
	 * Include required files
	 *
	 * @since  1.0.0
	 */
	private function inc() {
		// Custom functions
		require_once get_theme_file_path( '/inc/functions.php' );

		// Customizer additions.
		require_once get_theme_file_path( '/inc/customizer.php' );

		// Translations
		load_theme_textdomain( 'vingt-dixsept', get_theme_file_path( '/languages' ) );
	}

	/**
	 * Set up Gutenberg supports.
	 *
	 * @since 1.2.0
	 */
	private function setup_supports() {
		if ( ! $this->is_gutenberg_active ) {
			return;
		}

		// Adding support for core block visual styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );
	}
}
/**
 * Start the ClusterTheme
 *
 * @since  1.0.0
 *
 * @return ClusterTheme The Theme main instance.
 */
function vingt_dixsept() {
	return VingtDixSept::start();
}
add_action( 'after_setup_theme', 'vingt_dixsept' );
