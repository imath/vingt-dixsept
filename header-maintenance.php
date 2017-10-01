<?php
/**
 * Maintenance page header.
 *
 * @package Vingt DixSept
 *
 * @since 1.0.0
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php wp_head(); ?>
</head>

<body <?php body_class( 'home twentyseventeen-front-page' ); ?>>
<div id="page" class="site">

	<header id="masthead" class="site-header" role="banner">

		<?php get_template_part( 'template-parts/header/header', 'image' ); ?>

		<div class="navigation-top">
			<div class="wrap">

				<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Menu supérieur', 'vingt-dixsept' ); ?>">

					<button class="menu-toggle"></button>

					<h1><?php vingt_dixsept_the_maintenance_title(); ?></h1>

					<a href="<?php echo esc_url( wp_login_url() ); ?>" class="login-link"><svg class="icon icon-wordpress" aria-hidden="true" role="img"> <use href="#icon-wordpress" xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-wordpress"></use> </svg><span class="screen-reader-text"><?php esc_html_e( 'Se connecter', 'vingt-dixsept' ); ?></span></a>
				</nav>
			</div><!-- .wrap -->
		</div><!-- .navigation-top -->

	</header><!-- #masthead -->
