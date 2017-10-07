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
 */
function vingt_dixsept_register_email_type() {
	$common_attributes = array(
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
	);

	// @todo replace this with a unique 'template' post type.
	register_post_type( 'vingt_dixsept_email', $common_attributes );

	$common_attributes['label'] = 'vingt_dixsept_login';
	register_post_type( 'vingt_dixsept_login', $common_attributes );
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
	$css = apply_filters( 'vingt_dixsept_email_email_get_css', sprintf( '%1$semail%2$s.css',
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
 * Inits the Maintenance mode if required.
 *
 * @since  1.0.0
 */
function vingt_dixsept_maintenance_init() {
	if ( is_admin() || current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! get_theme_mod( 'maintenance_mode' ) ) {
		return;
	}

	// Neutralize signups.
	add_filter( 'option_users_can_register', '__return_zero' );

	// Use the maintenance template
	add_filter( 'template_include', 'vingt_dixsept_get_maintenance_template', 12 );

	// Make sure nobody is filtering this anymore.
	remove_all_filters( 'posts_pre_query' );

	// Set the maintenance post.
	add_filter( 'posts_pre_query', 'vingt_dixsept_maintenance_posts_pre_query', 10, 2 );
}
add_action( 'after_setup_theme', 'vingt_dixsept_maintenance_init', 20 );

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

	$common_attributes = array(
		'comment_status' => 'closed',
		'ping_status'    => 'closed',
		'post_status'    => 'private',
		'post_content'   => '',
	);

	// Install 1.0.0 if needed
	if ( (float) $db_version < 1.0 ) {
		$email_post_id = (int) get_option( 'vingt_dixsept_email_id', 0 );

		if ( ! $email_post_id ) {
			$email_post_id = wp_insert_post( wp_parse_args( array(
				'post_title'     => __( 'Modèle d’e-mail', 'vingt-dixsept' ),
				'post_type'      => 'vingt_dixsept_email',
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
		$login_post_id = (int) get_option( 'vingt_dixsept_login_id', 0 );

		if ( ! $login_post_id ) {
			$login_post_id = wp_insert_post( wp_parse_args( array(
				'post_title'     => __( 'Formulaire de connexion', 'vingt-dixsept' ),
				'post_type'      => 'vingt_dixsept_login',
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
