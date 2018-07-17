<?php
/**
 * Custom functions
 *
 * @package Vingt DixSept\inc
 *
 * @since  1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

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

	// Adapt Embed height and width according to available space.
	wp_add_inline_script( 'twentyseventeen-global', '
		( function( $ ) {
			$.each( $( \'.wp-block-embed\' ), function( i, figure ) {
				if ( $( figure ).hasClass( \'alignfull\' ) || $( figure ).hasClass( \'alignwide\' ) ) {
					var iframe = $( figure ).find( $( \'iframe\' ) ), aspectRatio;

					if ( iframe.width() !== iframe.prop( \'width\' ) ) {
						aspectRatio = iframe.prop( \'width\' ) / iframe.prop( \'height\' );
						iframe.prop( \'height\', iframe.width() / aspectRatio );
						iframe.prop( \'width\', iframe.width() );
					}
				}
			} );
		} )( jQuery );
	' );
}
add_action( 'wp_enqueue_scripts', 'vingt_dixsept_enqueue_styles' );

/**
 * Enqueues the Child Theme's specific CSS for Gutenberg blocks.
 *
 * @since 1.2.0
 */
function vingt_dixsept_enqueue_blocks_style() {
	$current_screen = null;

	if ( function_exists( 'get_current_screen' ) ) {
		$current_screen = get_current_screen();
	}

	$min = vingt_dixsept_js_css_suffix();
	$vs  = vingt_dixsept();

	// Editor context.
	if ( ! empty( $current_screen->post_type ) ) {
		return;
	}

	// Front-end only.
	wp_enqueue_style(
		'vingt-dixsept-blocks-style',
		get_stylesheet_directory_uri() . "/assets/css/blocks{$min}.css",
		array( 'wp-core-blocks' ),
		$vs->version
	);

	// Block custom colors.
	$block_colors = '';
	$colors       = $vs->get_colors();

	foreach ( $colors as $color ) {
		$block_colors .= sprintf( '
			.has-%1$s-background-color,
			.colors-custom .has-%1$s-background-color {
				background-color: %2$s;
			}

			.has-%1$s-color,
			.colors-custom .has-%1$s-color {
				color: %2$s;
			}
			%3$s
		', $color['slug'], $color['color'], "\n" );
	}

	// Buttons
	$block_colors .= sprintf( '
		.wp-block-button .wp-block-button__link:not(.has-background) {
			background-color: %1$s;
		}

		.wp-block-button .wp-block-button__link:not(.has-background):hover {
			background-color: %2$s;
		}
		%3$s
	', $colors['#222']['color'], $colors['#767676']['color'], "\n" );

	// Add Palette custom colors
	wp_add_inline_style( 'vingt-dixsept-blocks-style', $block_colors );
}
add_action( 'enqueue_block_assets', 'vingt_dixsept_enqueue_blocks_style' );

/**
 * Enqueues the Gutenberg editor styles.
 *
 * @since 1.2.0
 */
function vingt_dixsept_enqueue_editor_style() {
	$font_urls = twentyseventeen_fonts_url();
	$min       = vingt_dixsept_js_css_suffix();
	$vs        = vingt_dixsept();
	$deps      = array();

	if ( $font_urls && ! wp_style_is( 'twentyseventeen-fonts', 'registered' ) ) {
		wp_register_style(
			'twentyseventeen-fonts',
			$font_urls,
			array(),
			null
		);

		$deps = array( 'twentyseventeen-fonts' );
	}

	wp_enqueue_style(
		'vingt-dixsept-editor-style',
		get_stylesheet_directory_uri() . "/assets/css/editor{$min}.css",
		$deps,
		$vs->version
	);

	// Custom colors for headings
	$colors = $vs->get_colors();

	$heading_colors = sprintf( '
		.edit-post-visual-editor h1,
		.edit-post-visual-editor .editor-post-title__input,
		.edit-post-visual-editor h3,
		.edit-post-visual-editor h4,
		.edit-post-visual-editor h6 {
			color: %1$s;
		}

		.edit-post-visual-editor h2,
		.wp-block-pullquote p,
		.edit-post-visual-editor .wp-block-quote p {
			color: %2$s;
		}

		.edit-post-visual-editor h5 {
			color: %3$s;
		}
	', $colors['#333']['color'], $colors['#666']['color'], $colors['#767676']['color'] );

	// Add Palette custom colors
	wp_add_inline_style( 'vingt-dixsept-editor-style', $heading_colors );
}
add_action( 'enqueue_block_editor_assets', 'vingt_dixsept_enqueue_editor_style' );

/**
 * Enqueues the Theme's embed CSS.
 *
 * @since 1.0.0
 */
function vingt_dixsept_enqueue_embed_styles() {
	$min  = vingt_dixsept_js_css_suffix();
	$vs   = vingt_dixsept();

	wp_register_style( 'vingt-dixsept-fonts', twentyseventeen_fonts_url(), array(), null );

	// Enqueue the theme's Embed stylesheet.
	wp_enqueue_style(
		'embed-style',
		get_stylesheet_directory_uri() . "/assets/css/embed{$min}.css",
		array( 'vingt-dixsept-fonts' ),
		$vs->version
	);

	if ( 'custom' === get_theme_mod( 'colorscheme' ) ) {
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

		// Title & Body custom colors
		$title_color =  $hue . ', ' . $saturation . ', 0%';
		$body_color  = $hue . ', ' . $reduced_saturation . ', 20%';

		wp_add_inline_style( 'embed-style', sprintf( '
			body.colors-custom .wp-embed {
				color: hsl( %1$s );
			}

			body.colors-custom .wp-embed-heading a {
				color: hsl( %2$s );
			}
		', $body_color, $title_color ) );
	}
}
add_action( 'enqueue_embed_scripts', 'vingt_dixsept_enqueue_embed_styles' );

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
 * @since 1.1.0 Use a more generic post type for the customizer templates.
 */
function vingt_dixsept_register_email_type() {
	register_post_type( 'vingt_dixsept_tpl', array(
		'label'              => 'vingt_dixsept_template',
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => false,
		'show_in_menu'       => false,
		'show_in_nav_menus'  => false,
		'query_var'          => false,
		'rewrite'            => false,
		'has_archive'        => false,
		'hierarchical'       => true,
	) );
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

	// Make sure the Post won't be embed.
	add_filter( 'pre_oembed_result', '__return_false' );

	$pagetitle = esc_attr( get_bloginfo( 'name', 'display' ) );
	$content   = apply_filters( 'the_content', $text );

	remove_filter( 'pre_oembed_result', '__return_false' );

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
 * Outputs a specific customizer template.
 *
 * @since  1.1.0
 *
 * @param  string $part The part for the template to use.
 * @return string      The output for the template part.
 */
function vingt_dixsept_get_template_part( $part = '' ) {
	if ( ! is_singular( 'vingt_dixsept_tpl' ) || ! $part ) {
		return '';
	}

	$template = get_queried_object();

	if ( empty( $template->post_mime_type ) ) {
		return '';
	}

	return get_template_part( sprintf( 'template-parts/%s', $template->post_mime_type ), $part );
}

/**
 * Checks if the email logo should be used.
 *
 * @since  1.0.0
 *
 * @return boolean True if the email logo should be used. False otherwise.
 */
function vingt_dixsept_use_email_logo() {
	return (bool) has_custom_logo() && ! get_theme_mod( 'disable_email_logo' );
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
		$colorscheme = get_theme_mod( 'colorscheme', 'light' );

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
 * Gets the output for the PNG icon file.
 *
 * @since  1.0.0
 *
 * @param  string $icon The icon ID.
 * @return string       The output for the PNG icon file.
 */
function vingt_dixsept_get_png( $icon = '' ) {
	if ( ! $icon ) {
		return;
	}

	$src = get_theme_file_uri( "/assets/images/icon-{$icon}.png" );
	$alt = ucfirst( $icon );

	if ( 'Chain' === $alt ) {
		$alt = esc_attr__( 'Lien utile', 'vingt-dixsept' );
	}

	return sprintf( '<img src="%1$s" alt="%2$s" class="social-link" height="16">', $src, $alt );
}

/**
 * Displays PNG icons in social links menu.
 *
 * @since  1.0.0
 *
 * @param  string  $item_output The menu item output.
 * @param  WP_Post $item        Menu item object.
 * @param  int     $depth       Depth of the menu.
 * @param  array   $args        wp_nav_menu() arguments.
 * @return string               The menu item output with social icon.
 */
function vingt_dixsept_email_nav_menu_social_icons( $item_output, $item, $depth, $args ) {
	// Get supported social icons.
	$social_icons = twentyseventeen_social_links_icons();

	// Change PNG icon inside social links menu if there is supported URL.
	if ( 'social' === $args->theme_location ) {
		foreach ( $social_icons as $attr => $value ) {
			if ( false !== strpos( $item_output, $attr ) ) {
				$item_output = str_replace( $args->link_after, '</span>' . vingt_dixsept_get_png( $value ), $item_output );
			}
		}
	}

	return $item_output;
}

/**
 * Adds the social icons filter.
 *
 * @since 1.0.0
 */
function vingt_dixsept_email_add_filter() {
	remove_filter( 'walker_nav_menu_start_el', 'twentyseventeen_nav_menu_social_icons', 10, 4 );
	add_filter( 'walker_nav_menu_start_el', 'vingt_dixsept_email_nav_menu_social_icons', 10, 4 );
}

/**
 * Removes the social icons filter.
 *
 * @since 1.0.0
 */
function vingt_dixsept_email_remove_filter() {
	add_filter( 'walker_nav_menu_start_el', 'twentyseventeen_nav_menu_social_icons', 10, 4 );
	remove_filter( 'walker_nav_menu_start_el', 'vingt_dixsept_email_nav_menu_social_icons', 10, 4 );
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
	$css = apply_filters( 'vingt_dixsept_email_get_css', sprintf( '%1$semail%2$s.css',
		get_theme_file_path( 'assets/css/' ),
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
			.footer-text img.social-link { height: 16px; }
		';
	}
}

/**
 * Outputs the embed thumbnail.
 *
 * @since  1.0.0
 */
function vingt_dixsept_the_thumbnail_embed() {
	$thumbnail = get_header_image_tag();

	if ( has_post_thumbnail() ) {
		$thumbnail = get_the_post_thumbnail( get_the_ID(), 'post-thumbnail' );

	} elseif ( 'attachment' === get_post_type() && wp_attachment_is_image() ) {
		// Let's avoid the image to be displayed twice.
		remove_filter( 'the_excerpt_embed', 'wp_embed_excerpt_attachment' );

		$thumbnail = wp_get_attachment_image( get_the_ID(), 'full' );
	}

	if ( ! $thumbnail ) {
		return;
	}

	printf( '<div class="wp-embed-featured-image rectangular">
		<a href="%1$s" target="_top">%2$s</a>
	</div>', esc_url( apply_filters( 'the_permalink', get_permalink() ) ), $thumbnail );
}

/**
 * Adds a container to inform about the maintenance mode inside the customizer only.
 *
 * @since 1.0.0
 */
function vingt_dixsept_display_maintenance_mode() {
	if ( ! is_customize_preview() ) {
		return;
	}

	?>
	<div id="maintenance-mode"><?php vingt_dixsept_display_maintenance_mode_info(); ?></div>
	<?php
}

/**
 * Partial render callback for the maintenance mode.
 *
 * @since 1.0.0
 */
function vingt_dixsept_display_maintenance_mode_info() {
	if ( ! get_theme_mod( 'maintenance_mode' ) ) {
		$class = 'off';
		$icon  = twentyseventeen_get_svg( array( 'icon' => 'play' ) );
	} else {
		$class = 'on';
		$icon  = twentyseventeen_get_svg( array( 'icon' => 'pause' ) );
	}

	printf( '<div class="%1$s">%2$s</div>', $class, $icon );
}

/**
 * Makes sure the Posts query only contains the Maintenance page.
 *
 * @since 1.0.0
 *
 * @param  null   $return   A null value to use the regular WP Query.
 * @param  WP_Query $wq     The WP Query object.
 * @return null|array       Null if not on front end.
 *                          An array containing a Maintenance Post otherwise.
 */
function vingt_dixsept_maintenance_posts_pre_query( $return = null, WP_Query $wq ) {
	if ( ! $wq->is_main_query() || true === $wq->get( 'suppress_filters' ) || is_admin() ) {
		return $return;
	}

	// Set the queried object to avoid notices
	$wq->queried_object = get_post( (object) array(
		'ID'             => 0,
		'comment_status' => 'closed',
		'comment_count'  => 0,
		'post_type'      => 'maintenance',
		'post_title'     => __( 'Site en cours de maintenance', 'vingt-dixsept' ),
	) );

	$wq->queried_object_id = $wq->queried_object->ID;

	// Set the Posts list to be limited to our custom post.
	$posts = array( $wq->queried_object );

	// Reset some WP Query properties
	$wq->found_posts   = 1;
	$wq->max_num_pages = 1;
	$wq->posts         = $posts;
	$wq->post          = $wq->queried_object;
	$wq->post_count    = 1;

	foreach ( array(
		'is_home'       => true,
		'is_page'       => true,
		'is_single'     => false,
		'is_archive'    => false,
		'is_tax'        => false,
	) as $key => $conditional_tag ) {
		$wq->{$key} = (bool) $conditional_tag;
	}

	return $wq->posts;
}

/**
 * Gets the maintenance template file path.
 *
 * @since  1.0.0
 *
 * @return string The maintenance template file path.
 */
function vingt_dixsept_get_maintenance_template() {
	return get_theme_file_path( 'page-maintenance.php' );
}

/**
 * Checks if the theme is activated on the main site.
 *
 * @since 1.1.0
 *
 * @return boolean True if the theme is activated on the main site.
 *                 False otherwise.
 */
function vingt_dixsept_is_main_site() {
	return (int) get_current_network_id() === (int) get_current_blog_id();
}

/**
 * Gets the active type of signups.
 *
 * @since  1.1.0
 *
 * @return string The active type of signups.
 */
function vingt_dixsept_active_signup() {
	/**
	 * Filters the type of site sign-up.
	 *
	 * @since WordPress 3.0.0
	 *
	 * @param string $active_signup String that returns registration type. The value can be
	 *                              'all', 'none', 'blog', or 'user'.
	 */
	return apply_filters( 'wpmu_active_signup', get_site_option( 'registration', 'none' ) );
}

/**
 * Apply the signup/activate header hooks when requested.
 *
 * @since 1.1.0
 *
 * @param  string $hook The name of the hook to call.
 */
function vingt_dixsept_ms_register_header( $hook = 'do_signup_header' ) {
	add_action( 'login_head', $hook );
	add_action( 'login_head', 'wp_no_robots' );
	?>
	<meta name="viewport" content="width=device-width" />
	<?php
}

/**
 * Returns a 'none' string.
 *
 * @since 1.1.0
 *
 * @return string 'none'.
 */
function vingt_dixsept__return_none() {
	return 'none';
}

/**
 * Inits the Maintenance mode if required.
 *
 * @since  1.0.0
 */
function vingt_dixsept_maintenance_init() {
	if ( is_admin() || current_user_can( 'maintenance_mode' ) ) {
		return;
	}

	if ( ! get_theme_mod( 'maintenance_mode' ) ) {
		return;
	}

	// Neutralize signups.
	add_filter( 'option_users_can_register', '__return_zero' );

	// Neutralize Multisite signups.
	if ( is_multisite() && vingt_dixsept_is_main_site() ) {
		add_filter( 'site_option_registration', 'vingt_dixsept__return_none' );
	}

	// Use the maintenance template
	add_filter( 'template_include', 'vingt_dixsept_get_maintenance_template', 12 );

	// Make sure nobody is filtering this anymore.
	remove_all_filters( 'posts_pre_query' );

	// Set the maintenance post.
	add_filter( 'posts_pre_query', 'vingt_dixsept_maintenance_posts_pre_query', 10, 2 );
}
add_action( 'after_setup_theme', 'vingt_dixsept_maintenance_init', 20 );

/**
 * Maps The maintenance mode capability.
 *
 * Allow the admin to set the 'maintenance_mode' cap to some users or roles
 * in case he wants to get feedbacks from them.
 *
 * @since 1.0.0
 *
 * @param  array   $caps    Capabilities for meta capability.
 * @param  string  $cap     Capability name.
 * @param  integer $user_id User id.
 * @param  mixed   $args    Arguments.
 * @return array            Actual capabilities for meta capability.
 */
function vingt_dixsept_map_meta_caps( $caps = array(), $cap = '', $user_id = 0, $args = array() ) {
	if ( 'maintenance_mode' !== $cap ) {
		return $caps;

	// Fallback to Admin only if the current user does not have the maintenance mode cap.
	} elseif ( empty( wp_get_current_user()->allcaps['maintenance_mode'] ) ) {
		$caps = array( 'manage_options' );
	}

	return $caps;
}
add_filter( 'map_meta_cap', 'vingt_dixsept_map_meta_caps', 10, 4 );

/**
 * Outputs the Maintenance page title.
 *
 * @since 1.0.0
 */
function vingt_dixsept_the_maintenance_title() {
	$page = get_queried_object();

	echo apply_filters( 'the_title', $page->post_title, $page->ID );
}

/**
 * Checks if the Maintenance page has a content to display.
 *
 * @since 1.0.0
 */
function vingt_dixsept_has_maintenance_content() {
	$page = get_queried_object();

	return ! empty( $page->post_content );
}

/**
 * Outputs the Maintenance page content.
 *
 * @since 1.0.0
 */
function vingt_dixsept_the_maintenance_content() {
	$page = get_queried_object();

	echo apply_filters( 'the_content', $page->post_content );
}

/**
 * Use the Site's url for the login logo.
 *
 * @since  1.1.0
 *
 * @param  string $url URL of the login logo link.
 * @return string      URL of the login logo link.
 */
function vingt_dixsept_login_logo_url( $url = '' ) {
	if ( ! vingt_dixsept_is_main_site() ) {
		return $url;
	}

	return home_url( '/' );
}
add_filter( 'login_headerurl', 'vingt_dixsept_login_logo_url' );

/**
 * Gets the login's preview screen action.
 *
 * @since  1.1.0
 *
 * @return string The form action type.
 */
function vingt_dixsept_login_get_action() {
	$action = 'login';

	if ( isset( $_REQUEST['action'] ) ) {
		$action = $_REQUEST['action'];
	} else {
		$url_parts = explode( '/', wp_parse_url( $_SERVER['REQUEST_URI'] )['path'] );

		if ( 'wp-signup.php' === end( $url_parts ) ) {
			$action = 'register';
		} elseif ( 'wp-activate.php' === end( $url_parts ) ) {
			$action = 'activate';
		}
	}

	return apply_filters( 'vingt_dixsept_login_get_action', $action );
}

/**
 * Outputs the login's preview screen document title.
 *
 * @since 1.1.0
 */
function vingt_dixsept_login_document_title() {
	$separator = '&lsaquo;';

	if ( is_rtl() ) {
		$separator = '&rsaquo;';
	}

	$title  = __( 'Connexion', 'vingt-dixsept' );
	$action = vingt_dixsept_login_get_action();

	if ( 'lostpassword' === $action ) {
		$title  = __( 'Mot de passe oublié', 'vingt-dixsept' );
	} elseif ( 'register' === $action ) {
		$title  = __( 'Inscription', 'vingt-dixsept' );
	} elseif ( 'activate' === $action ) {
		$title  = __( 'Activation', 'vingt-dixsept' );
	}

	return printf( '%1$s %2$s %3$s',
		get_bloginfo( 'name', 'display' ),
		$separator,
		esc_html( $title )
	);
}

/**
 * Outputs the login's preview screen submit button value.
 *
 * @since 1.1.0
 */
function vingt_dixsept_login_submit_title() {
	esc_attr_e( 'Se connecter', 'vingt-dixsept' );
}

/**
 * Outputs the login's preview screen body classes.
 *
 * @since  1.1.0
 */
function vingt_dixsept_login_classes() {
	$action  = vingt_dixsept_login_get_action();
	$classes = array(
		'login-action-' . $action,
		'wp-core-ui',
		'rtl',
		'locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_locale() ) ) ),
	);

	if ( ! is_rtl() ) {
		unset( $classes[2] );
	}

	if ( isset( $_POST['stage'] ) ) {
		array_push( $classes, sanitize_html_class( $_POST['stage'] ) );
	}

	/**
	 * Filters the login page body classes.
	 *
	 * @since WordPress 3.5.0
	 *
	 * @param array  $classes An array of body classes.
	 * @param string $action  The action that brought the visitor to the login page.
	 */
	$classes = apply_filters( 'login_body_class', $classes, $action );

	echo 'login ' . join( ' ', $classes );
}

/**
 * Outputs the login's preview screen header url.
 *
 * @since  1.1.0
 */
function vingt_dixsept_login_url() {
	printf( '%s', esc_url(
		/**
		 * Filters link URL of the header logo above login form.
		 *
		 * @since WordPress 2.1.0
		 *
		 * @param string $login_header_url Login header logo URL.
		 */
		apply_filters( 'login_headerurl', __( 'https://fr.wordpress.org/', 'vingt-dixsept' ) )
	) );
}

/**
 * Outputs the login's preview screen header title.
 *
 * @since  1.1.0
 */
function vingt_dixsept_login_title() {
	printf( '%s', esc_attr(
		/**
		 * Filters the title attribute of the header logo above login form.
		 *
		 * @since WordPress 2.1.0
		 *
		 * @param string $login_header_title Login header logo title attribute.
		 */
		apply_filters( 'login_headertitle', __( 'Propulsé par WordPress', 'vingt-dixsept' ) )
	) );
}

/**
 * Outputs the Login navigation.
 *
 * @since  1.1.0
 */
function vingt_dixsept_login_navigation() {
	$navlinks = array();

	$action       = vingt_dixsept_login_get_action();
	$registration = get_option( 'users_can_register' );
	$register     = '';

	if ( is_customize_preview() ) {
		$url = get_permalink( get_option( 'vingt_dixsept_login_id' ) );
	} else {
		$url = wp_login_url();
	}

	// urls
	$login = sprintf( '<a href="%1$s">%2$s</a>',
		esc_url( $url ),
		esc_html__( 'Connexion', 'vingt-dixsept' )
	);

	$lostpass = sprintf( '<a href="%1$s">%2$s</a>',
		esc_url( add_query_arg( 'action', 'lostpassword', $url ) ),
		esc_html__( 'Mot de passe oublié ?', 'vingt-dixsept' )
	);

	if ( $registration ) {
		$register = sprintf( '<a href="%1$s">%2$s</a>',
			esc_url( add_query_arg( 'action', 'register', $url ) ),
			esc_html__( 'Inscription', 'vingt-dixsept' )
		);
	}

	if ( 'login' === $action ) {
		array_push( $navlinks, $lostpass );

		if ( $register ) {
			array_unshift( $navlinks, $register );
		}
	} elseif ( 'lostpassword' === $action ) {
		array_push( $navlinks, $login );

		if ( $register ) {
			array_push( $navlinks, $register );
		}
	} elseif ( 'register' === $action ) {
		$navlinks = array( $login, $lostpass );
	}

	if ( ! $navlinks ) {
		return;
	}

	echo join( ' | ', $navlinks );
}

/**
 * Checks if the login logo should be used.
 *
 * @since  1.1.0
 *
 * @return boolean True if the login logo should be used. False otherwise.
 */
function vingt_dixsept_use_login_logo() {
	return has_site_icon() && (bool) get_theme_mod( 'enable_login_logo' );
}

/**
 * Customize the login screen look and feel.
 *
 * @since 1.1.0
 *
 * @return string CSS Outut.
 */
function vingt_dixsept_login_style() {
	$logo_rule = '';

	if ( vingt_dixsept_use_login_logo() ) {
		$logo_rule = sprintf( '
			#login h1 a {
				background-image: none, url(%s);
			}
		', esc_url_raw( get_site_icon_url( 84 ) ) );
	}

	$important = '';

	if ( is_customize_preview() ) {
		$important = ' !important';
	}

	$color_rule  = '';
	$colors      = vingt_dixsept_email_get_scheme_colors();
	$colorscheme = get_theme_mod( 'colorscheme', 'light' );
	$formcolor   = '#FFF';
	$labelcolor  = '#72777c';
	$linkcolor   = '#555d66';

	// Load the selected colorscheme.
	if ( isset( $colors[ $colorscheme ] ) ) {
		$color = $colors[ $colorscheme ];

		$bgcolor  = $color['body_link'];
		$txtcolor = $color['title_bg'];

		if ( 'dark' === $colorscheme ) {
			$txtcolor   = $color['body_bg'];
			$formcolor  = $color['separator'];
			$labelcolor = $linkcolor = '#FFF';
		}

		$color_rule = sprintf( '
			#login p.submit .button-primary.button-large,
			#login p.submit .button-primary[disabled],
			#login p.submit .button-primary:disabled {
				color: %1$s%3$s;
				background-color: %2$s%3$s;
				border-color: %2$s%3$s;
				-webkit-box-shadow: none%3$s;
				box-shadow: none%3$s;
				text-shadow: none%3$s;
			}

			#login p.submit .button-primary.button-large:hover,
			#login p.submit .button-primary:disabled:hover {
				color: %2$s%3$s;
				background-color: %1$s%3$s;
				border-color: %2$s%3$s;
			}

			a:focus {
				color: %1$s%3$s;
				-webkit-box-shadow: none%3$s;
				box-shadow: none%3$s;
			}

			#login input[type="text"]:focus,
			#login input[type="password"]:focus {
				border-color: %2$s%3$s;
				-webkit-box-shadow: none%3$s;
				box-shadow: none%3$s;
			}

			#login form {
				background-color: %4$s;
			}

			#login label {
				color: %5$s;
			}
		', $txtcolor, $bgcolor, $important, $formcolor, $labelcolor );
	}

	$custom_header_rule = '';

	if ( get_theme_mod( 'enable_login_custom_header' ) ) {
		$custom_header = get_custom_header();

		$custom_header_rule = sprintf( '
			body.login {
				background-image: url( %s );
				background-size: cover;
				background-repeat: no-repeat;
			}

			body.login p#nav, body.login p#backtoblog {
				background: %2$s;
				-webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.13);
				box-shadow: 0 1px 3px rgba(0, 0, 0, 0.13);
				padding: 8px 24px;
				margin-top: 0;
			}

			body.login p#nav a, body.login p#backtoblog a {
				color: %3$s;
			}
		', $custom_header->url, $formcolor, $linkcolor );
	}

	$ms_rule = '';

	if ( ( did_action( 'before_signup_header' ) || did_action( 'activate_header' ) ) && vingt_dixsept_is_main_site() ) {
		$ms_css = file_get_contents( sprintf( '%1$sms-register%2$s.css',
			get_theme_file_path( 'assets/css/' ),
			vingt_dixsept_js_css_suffix()
		) );

		$ms_rule = sprintf( $ms_css, $txtcolor, $bgcolor, $important, $formcolor, $labelcolor, '100%' );
	}

	wp_add_inline_style( 'login', sprintf( '
		%1$s

		%2$s

		%3$s

		%4$s
	', $logo_rule, $color_rule, $custom_header_rule, $ms_rule ) );
}
add_action( 'login_enqueue_scripts', 'vingt_dixsept_login_style', 9 );

/**
 * Enqueues a specific script to improve the Blog registration form.
 *
 * @since 1.1.0
 */
function vingt_dixsept_signup_form_enqueue_js() {
	if ( ! vingt_dixsept_is_main_site() ) {
		return;
	}

	$min = vingt_dixsept_js_css_suffix();
	$vs  = vingt_dixsept();

	wp_enqueue_script( 'vingt_dixsept-signup-form', get_stylesheet_directory_uri() . "/assets/js/signup-form{$min}.js", array(), $vs->version, true );
}
add_action( 'signup_blogform',     'vingt_dixsept_signup_form_enqueue_js' );
add_action( 'signup_extra_fields', 'vingt_dixsept_signup_form_enqueue_js' );

/**
 * Make sure there's a version of the site icon for the login logo
 *
 * @since 1.1.0
 *
 * @param  array $icon_sizes The list of allowed icon sizes in Pixels.
 * @return array             The list of allowed icon sizes in Pixels.
 */
function vingt_dixsept_logo_size( $icon_sizes = array() ) {
	return array_merge( $icon_sizes, array( 84 ) );
}
add_filter( 'site_icon_image_sizes', 'vingt_dixsept_logo_size', 10, 1 );

/**
 * Upgrade the theme db version
 *
 * @since  1.0.0
 * @since  1.1.0 Edits the post type used to store custom templates.
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

	$common_attributes = array(
		'comment_status' => 'closed',
		'ping_status'    => 'closed',
		'post_status'    => 'private',
		'post_content'   => '',
		'post_type'      => 'vingt_dixsept_tpl',
	);

	$email_post_id = (int) get_option( 'vingt_dixsept_email_id', 0 );

	// Install 1.0.0 if needed
	if ( (float) $db_version < 1.0 ) {
		// Create the email private post.
		if ( ! $email_post_id ) {
			$email_post_id = wp_insert_post( wp_parse_args( array(
				'post_mime_type' => 'email',
				'post_title'     => __( 'Modèle d’e-mail', 'vingt-dixsept' ),
				'post_content'   => sprintf( '<p>%1$s</p><p>%2$s</p><p>%3$s</p>',
					__( 'Vous pouvez personnaliser le gabarit utilisé pour envoyer les e-mails de WordPress.', 'vingt-dixsept' ),
					__( 'Pour cela utilisez la colonne latérale pour spécifier vos préférences.', 'vingt-dixsept' ),
					__( 'Voici comment seront affichés les <a href="#">liens</a> contenus dans certains e-mails.', 'vingt-dixsept' )
				),
			), $common_attributes ) );

			update_option( 'vingt_dixsept_email_id', $email_post_id );
		}
	}

	// Upgrade to 1.1.0 if needed
	if ( (float) $db_version < 1.1 ) {
		$email_post = get_post( $email_post_id );

		// Set the Email Post type to the global template one.
		if ( ! empty( $email_post->ID ) && 'vingt_dixsept_tpl' !== get_post_type( $email_post ) ) {
			$email_post->post_type      = 'vingt_dixsept_tpl';
			$email_post->post_mime_type = 'email';

			wp_update_post( $email_post );
		}

		$login_post_id = (int) get_option( 'vingt_dixsept_login_id', 0 );

		// Create the login private post.
		if ( ! $login_post_id ) {
			$login_post_id = wp_insert_post( wp_parse_args( array(
				'post_mime_type' => 'login',
				'post_title'     => __( 'Formulaire de connexion', 'vingt-dixsept' ),
				'post_content'   => sprintf( '<p>%1s</p>',
					__( 'Cet article est utilisé pour personnaliser l’apparence du formulaire de connexion.', 'vingt-dixsept' )
				),
			), $common_attributes ) );

			update_option( 'vingt_dixsept_login_id', $login_post_id );
		}
	}

	// Update version.
	update_option( 'vingt_dixsept_version', $version );
}
add_action( 'admin_init', 'vingt_dixsept_upgrade', 1000 );

/**
 * Unregister the "sidebar-1" if Gutenberg is active.
 *
 * @since 1.2.0
 */
function vingt_dixsept_unregister_sidebar_1() {
	unregister_sidebar( 'sidebar-1' );
}
add_action( 'widgets_init', 'vingt_dixsept_unregister_sidebar_1', 11 );

/**
 * Force the "sidebar-1" to be inactive if Gutenberg is.
 *
 * @since 1.2.0
 *
 * @param  boolean $is_active     Whether the sidebar is active or not.
 * @param  string  $sidebar_index The sidebar ID.
 * @return boolean                Whether the "sidebar-1" is active or not.
 */
function vingt_dixsept_gutenberg_sidebar_1( $is_active = false, $sidebar_index = '' ) {
	if ( $is_active && 'sidebar-1' === $sidebar_index ) {
		$is_active = ! vingt_dixsept()->is_gutenberg_active;
	}

	return $is_active;
}
add_filter( 'is_active_sidebar', 'vingt_dixsept_gutenberg_sidebar_1', 10, 2 );

/**
 * Add a class to the body tag to inform Gutenberg is active.
 *
 * @since 1.2.0
 *
 * @param  array $classes The classes of the body tag.
 * @return array          The classes of the body tag.
 */
function vingt_dixsept_body_classes( $classes = array() ) {
	if ( vingt_dixsept()->is_gutenberg_active ) {
		$classes[] = 'is-gutenberg-active';
	}

	return $classes;
}
add_filter( 'body_class', 'vingt_dixsept_body_classes' );

/**
 * Force embed tweets to be centered.
 *
 * @since  1.2.0
 */
function vingt_dixsept_fetch_url( $provider = '' ) {
	if ( false !== strpos( $provider, 'https://publish.twitter.com/oembed' ) ) {
		$provider = add_query_arg( 'align', 'center', $provider );
	}

	return $provider;
}
add_filter( 'oembed_fetch_url', 'vingt_dixsept_fetch_url', 10, 1 );
