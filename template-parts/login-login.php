<?php
/**
 * Login form customizer template
 *
 * @package Vingt DixSept\template-parts
 *
 * @since 1.1.0
 */
?>

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
