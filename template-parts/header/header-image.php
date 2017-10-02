<?php
/**
 * Image header's template part
 *
 * @package Vingt DixSept\template-parts\header
 *
 * @since 1.0.0
 */

?>
<div class="custom-header">

		<div class="custom-header-media">
			<?php the_custom_header_markup(); ?>
		</div>

	<?php get_template_part( 'template-parts/header/site', 'branding' );

	vingt_dixsept_display_maintenance_mode(); ?>

</div><!-- .custom-header -->
