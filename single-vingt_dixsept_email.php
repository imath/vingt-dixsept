<?php
/**
 * Template used when previewing the email.
 *
 * @package Vingt DixSept
 *
 * @since 1.0.0
 */
get_template_part( 'template-parts/email', 'header' );

while ( have_posts() ) : the_post();

	get_template_part( 'template-parts/email', 'body' );

endwhile; // End of the loop.