<?php
/**
 * Maintenance page template.
 *
 * @package Vingt DixSept
 *
 * @since 1.0.0
 */

get_header( 'maintenance' );

	if ( vingt_dixsept_has_maintenance_content() ) : ?>
		<div class="site-content-contain">
			<div id="content" class="site-content">
				<div class="wrap">
					<div id="primary" class="content-area">
						<main id="main" class="site-main" role="main">

							<?php vingt_dixsept_the_maintenance_content(); ?>

						</main><!-- #main -->
					</div><!-- #primary -->
				</div><!-- .wrap -->
			</div><!-- #content -->
		</div><!-- .site-content-contain -->

	<?php endif ; ?>

</div><!-- #page -->
<?php wp_footer(); ?>

</body>
</html>
