<?php
/**
 * Email header's template
 * Based on https://github.com/InterNations/antwort
 *
 * @package Vingt DixSept\template-parts
 *
 * @since 1.0.0
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"> <!-- So that mobile will display zoomed in -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- enable media queries for windows phone 8 -->
	<meta name="format-detection" content="telephone=no"> <!-- disable auto telephone linking in iOS -->

	<?php if ( is_customize_preview() ) :
		// Output the header scripts for the customizer.
		wp_head();

	else : ?>

		<title>{{pagetitle}}</title>

	<?php endif; ?>

	<style type="text/css">
		<?php vingt_dixsept_email_print_css(); ?>
	</style>

	<?php
	/**
	 * Hook here to only include css/scripts for the email template.
	 *
	 * @since 1.0.0
	 */
	do_action( 'vingt_dixsept_email_enqueue_scripts' ); ?>
</head>
