<?php
/**
 * Email body's template
 * Based on https://github.com/InterNations/antwort
 *
 * @package Vingt DixSept\template-parts
 *
 * @since 1.0.0
 */
?>
<body style="margin:0; padding:0;" bgcolor="#F0F0F0" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

	<!-- 100% background wrapper (grey background) -->
	<table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" bgcolor="#F0F0F0">
		<tr>
			<td align="center" valign="top" bgcolor="#F0F0F0" style="background-color: #F0F0F0;">

				<br>

				<!-- 600px container (white background) -->
				<table border="0" width="600" cellpadding="0" cellspacing="0" class="container" style="width:600px;max-width:600px;">
					<tr>
						<td class="container-padding header" align="left" style="font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;padding-bottom:12px;padding-left:24px;padding-right:24px">
							<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<?php if ( ! get_theme_mod( 'disable_email_logo' ) ) : ?>
										<td width="60px" height="60px"><?php vingt_dixsept_email_logo() ;?></td>
										<td height="60px" style="font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;font-size:48px;font-weight:bold;vertical-align:middle;padding-left:16px;color:#555"><?php bloginfo( 'name' ); ?></td>
									<?php else : ?>
										<td height="60px" style="font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;font-size:48px;font-weight:bold;vertical-align:middle;padding-left:8px;color:#555"><?php bloginfo( 'name' ); ?></td>
									<?php endif; ?>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class="container-padding content" align="left" style="padding-left:24px;padding-right:24px;padding-top:12px;padding-bottom:12px;<?php echo ( 'dark' === get_theme_mod( 'colorscheme' ) ) ? 'background-color:#222' : 'background-color:#fff'; ?>; border-top: 8px solid <?php vingt_dixsept_email_line_color(); ?>;">
<div class="body-text" style="font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;font-size:14px;line-height:20px;text-align:left;color:<?php vingt_dixsept_email_text_color(); ?>">
	<?php if ( is_customize_preview() && get_the_content() ) :

		the_content();

	else : ?>

		{{content}}

	<?php endif ; ?>
</div>
						</td>
					</tr>
					<tr>
						<td class="container-padding footer-text" align="left" style="font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;font-size:12px;line-height:16px;color:#aaaaaa;padding-left:24px;padding-right:24px">
							<br><br>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#aaaaaa"><?php echo esc_html( home_url() ); ?></a><br>
							<br><br>
						</td>
					</tr>
				</table>
				<!--/600px container -->
			</td>
		</tr>
	</table>
	<!--/100% background wrapper-->

	<?php if ( is_customize_preview() ) :
		// Output the footer scripts for the customizer.
		wp_footer();

	endif; ?>

</body>
</html>
