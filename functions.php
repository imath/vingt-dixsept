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
	 * Get Palette colors
	 *
	 * @since  1.2.0
	 *
	 * @return array The Palette colors.
	 */
	public function get_colors() {
		$colors = array(
			'#000' => array(
				'name'  => __( 'Noir', 'vingt-dixsept' ),
				'color' => '#000',
				'slug'  => 'vingt-dixsept-black',
			),
			'#222' => array(
				'name'  => __( 'Très sombre', 'vingt-dixsept' ),
				'color' => '#222',
				'slug'  => 'vingt-dixsept-very-dusky',
			),
			'#333' => array(
				'name'  => __( 'Sombre', 'vingt-dixsept' ),
				'color' => '#333',
				'slug'  => 'vingt-dixsept-dusky',
			),
			'#666' => array(
				'name'  => __( 'Très foncé', 'vingt-dixsept' ),
				'color' => '#666',
				'slug'  => 'vingt-dixsept-darker',
			),
			'#767676' => array(
				'name'  => __( 'Foncé', 'vingt-dixsept' ),
				'color' => '#767676',
				'slug'  => 'vingt-dixsept-dark',
			),
			'#bbb' => array(
				'name'  => __( 'Assez clair', 'vingt-dixsept' ),
				'color' => '#bbb',
				'slug'  => 'vingt-dixsept-soft',
			),
			'#ddd' => array(
				'name'  => __( 'Clair', 'vingt-dixsept' ),
				'color' => '#ddd',
				'slug'  => 'vingt-dixsept-softer',
			),
			'#eee' => array(
				'name'  => __( 'Très clair', 'vingt-dixsept' ),
				'color' => '#eee',
				'slug'  => 'vingt-dixsept-softest',
			),
			'#fff' => array(
				'name'  => __( 'Blanc', 'vingt-dixsept' ),
				'color' => '#fff',
				'slug'  => 'vingt-dixsept-white',
			),
		);

		if ( 'custom' !== get_theme_mod( 'colorscheme', 'light' ) ) {
			return $colors;
		}

		$hue = absint( get_theme_mod( 'colorscheme_hue', 250 ) );

		/**
		 * Filter Twenty Seventeen default saturation level.
		 *
		 * @since Twenty Seventeen 1.0
		 *
		 * @param int $saturation Color saturation level.
		 */
		$saturation         = absint( apply_filters( 'twentyseventeen_custom_colors_saturation', 50 ) );
		$reduced_saturation = ( .8 * $saturation ) . '%';
		$saturation         = $saturation . '%';
		$base_hsl           = 'hsl( ' . $hue . ', %1$s, %2$s )';
		$custom_colors      = array(
			'#000'    => sprintf( $base_hsl, $saturation, '0%' ),
			'#222'    => sprintf( $base_hsl, $saturation, '13%' ),
			'#333'    => sprintf( $base_hsl, $reduced_saturation, '20%' ),
			'#666'    => sprintf( $base_hsl, $saturation, '40%' ),
			'#767676' => sprintf( $base_hsl, $saturation, '46%' ),
			'#bbb'    => sprintf( $base_hsl, $saturation, '73%' ),
			'#ddd'    => sprintf( $base_hsl, $saturation, '87%' ),
			'#eee'    => sprintf( $base_hsl, $saturation, '93%' ),
			'#fff'    => sprintf( $base_hsl, $saturation, '100%' ),
		);

		foreach ( $custom_colors as $kc => $vc ) {
			$colors[ $kc ]['color'] = $vc;
		}

		return $colors;
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

		// Add support for Palette custom colors
		add_theme_support( 'editor-color-palette', $this->get_colors() );
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
