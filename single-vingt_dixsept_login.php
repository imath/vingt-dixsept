<?php
/**
 * Template used when previewing the WordPress Login form.
 *
 * @package Vingt DixSept
 *
 * @since 1.0.0
 */
?>
<body>
	<p>A remplacer en fonction des cas par :</p>
	<ul>
		<li>le formulaire de connexion</li>
		<li>le formulaire de réinitialisation du mot de passe</li>
		<li>le formulaire d’inscription</li>
	</ul>

	<?php if ( is_customize_preview() ) :
		// Output the footer scripts for the customizer.
		wp_footer();

	endif; ?>

</body>
</html>
