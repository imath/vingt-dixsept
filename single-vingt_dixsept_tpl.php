<?php
/**
 * Template used when previewing Vingt DixSept specific templates.
 *
 * @package Vingt DixSept
 *
 * @since 1.1.0
 */
vingt_dixsept_get_template_part( 'header' );

while ( have_posts() ) : the_post();

	vingt_dixsept_get_template_part( 'body' );

endwhile; // End of the loop.
