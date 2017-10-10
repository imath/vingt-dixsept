<?php
/**
 * Login footer's template
 *
 * @package Vingt DixSept\template-parts
 *
 * @since 1.1.0
 */

	if ( 'activate' !== vingt_dixsept_login_get_action() ) :

		if ( ! is_multisite() || is_customize_preview() ) : ?>
			<p id="nav">
				<?php vingt_dixsept_login_navigation(); ?>
			</p>
		<?php endif; ?>

		<p id="backtoblog">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php printf( _x( '&larr; Retour vers %s', 'site', 'vingt-dixsept' ),
					get_bloginfo( 'title', 'display' )
				); ?>
			</a>
		</p>

	<?php endif ; ?>

	</div><!-- #login -->

	<?php
	/**
	* Fires in the login page footer.
	*
	* @since WordPress 3.1.0
	*/
	do_action( 'login_footer' );

	if ( is_customize_preview() ) :
	// Output the footer scripts for the customizer.
	wp_footer();

	endif; ?>

	<div class="clear"></div>

</body>
</html>
