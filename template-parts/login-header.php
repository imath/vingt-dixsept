<?php
/**
 * Login header's template
 *
 * @package Vingt DixSept\template-parts
 *
 * @since 1.0.0
 */
?>
<!DOCTYPE html>
	<!--[if IE 8]>
		<html xmlns="http://www.w3.org/1999/xhtml" class="ie8" <?php language_attributes(); ?>>
	<![endif]-->
	<!--[if !(IE 8) ]><!-->
		<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<!--<![endif]-->
	<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<title><?php vingt_dixsept_login_document_title(); ?></title>
	<?php

	wp_enqueue_style( 'login' );

	/**
	 * Enqueue scripts and styles for the login page.
	 *
	 * @since WordPress 3.1.0
	 */
	do_action( 'login_enqueue_scripts' );

	/**
	 * Fires in the login page header after scripts are enqueued.
	 *
	 * @since WordPress 2.1.0
	 */
	do_action( 'login_head' ); ?>

	</head>
	<body class="<?php vingt_dixsept_login_classes(); ?>">
	<?php
	/**
	 * Fires in the login page header after the body tag is opened.
	 *
	 * @since 4.6.0
	 */
	do_action( 'login_header' ); ?>

	<div id="login">
		<h1>
			<a href="<?php vingt_dixsept_login_url(); ?>" title="<?php vingt_dixsept_login_title(); ?>" tabindex="-1"><?php bloginfo( 'name' ); ?></a>
		</h1>
