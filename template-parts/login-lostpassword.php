<?php
/**
 * Login's lost password form customizer template
 *
 * @package Vingt DixSept\template-parts
 *
 * @since 1.1.0
 */
?>

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
