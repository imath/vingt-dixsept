<?php
/**
 * Login body's template
 *
 * @package Vingt DixSept\template-parts
 *
 * @since 1.0.0
 */
?>
		<?php if ( 'login' === vingt_dixsept_login_get_action() ) : ?>
			<form name="loginform" id="loginform">
				<p>
					<label for="user_login"><?php esc_html_e( 'Nom d’utilisateur ou adresse e-mail', 'vingt-dixsept' ); ?><br />
						<input type="text" name="log" id="user_login" class="input" disabled="disabled" size="20"/>
					</label>
				</p>
				<p>
					<label for="user_pass"><?php esc_html_e( 'Mot de passe', 'vingt-dixsept' ); ?><br />
						<input type="password" name="pwd" id="user_pass" class="input" disabled="disabled" size="20" />
					</label>
				</p>

				<?php
				/**
				 * Fires following the 'Password' field in the login form.
				 *
				 * @since WordPress 2.1.0
				 */
				do_action( 'login_form' ); ?>

				<p class="forgetmenot">
					<label for="rememberme"><input name="rememberme" type="checkbox" id="rememberme" value="forever" disabled="disabled"/> <?php esc_html_e( 'Se souvenir de moi', 'vingt-dixsept' ); ?></label>
				</p>

				<p class="submit">
					<input type="submit" name="wp-submit" id="wp-submit" disabled="disabled" class="button button-primary button-large" value="<?php vingt_dixsept_login_submit_title(); ?>" />
				</p>
			</form>
		<?php elseif ( 'lostpassword' === vingt_dixsept_login_get_action() ) : ?>
			<form name="lostpasswordform" id="lostpasswordform">
				<p>
					<label for="user_login" >
						<?php esc_html_e( 'Nom d’utilisateur ou adresse e-mail', 'vingt-dixsept' ); ?><br />
						<input type="text" name="user_login" id="user_login" class="input" disabled="disabled" size="20" />
					</label>
				</p>
				<?php
				/**
				 * Fires inside the lostpassword form tags, before the hidden fields.
				 *
				 * @since WordPress 2.1.0
				 */
				do_action( 'lostpassword_form' ); ?>

				<p class="submit">
					<input type="submit" name="wp-submit" id="wp-submit" disabled="disabled" class="button button-primary button-large" value="<?php esc_attr_e( 'Générer un mot de passe', 'vingt-dixsept' ); ?>" />
				</p>
			</form>
		<?php elseif ( 'register' === vingt_dixsept_login_get_action() ) : ?>
			 <form name="registerform" id="registerform">
				<p>
					<label for="user_login"><?php esc_html_e( 'Identifiant', 'vingt-dixsept' ); ?><br />
						<input type="text" name="user_login" id="user_login" class="input" disabled="disabled" size="20" />
					</label>
				</p>
				<p>
					<label for="user_email"><?php esc_html_e( 'Adresse de messagerie', 'vingt-dixsept' ); ?><br />
						<input type="email" name="user_email" id="user_email" class="input" disabled="disabled" size="25" />
					</label>
				</p>

				<?php
				/**
				 * Fires following the 'Email' field in the user registration form.
				 *
				 * @since WordPress 2.1.0
				 */
				do_action( 'register_form' ); ?>

				<p id="reg_passmail">
					<?php esc_html_e( 'La confirmation d’inscription vous sera envoyée par e-mail.', 'vingt-dixsept' ); ?>
				</p>

				<br class="clear" />

				<p class="submit">
					<input type="submit" name="wp-submit" id="wp-submit" disabled="disabled" class="button button-primary button-large" value="<?php esc_attr_e( 'Inscription', 'vingt-dixsept' ); ?>" />
				</p>
			</form>
		<?php endif; ?>

		<p id="nav">
			<?php vingt_dixsept_login_navigation(); ?>
		</p>

		<p id="backtoblog">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php printf( _x( '&larr; Retour vers %s', 'site', 'vingt-dixsept' ),
					get_bloginfo( 'title', 'display' )
				); ?>
			</a>
		</p>

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
