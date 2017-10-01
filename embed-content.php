<?php
/**
 * Embed content template used when displaying embeds.
 *
 * @package Vingt DixSept
 *
 * @since 1.0.0
 */
?>
	<div <?php post_class( 'wp-embed' ); ?>>

		<?php vingt_dixsept_the_thumbnail_embed(); ?>

		<p class="wp-embed-heading">
			<a href="<?php the_permalink(); ?>" target="_top">
				<?php the_title(); ?>
			</a>
		</p>

		<div class="wp-embed-excerpt"><?php the_excerpt_embed(); ?></div>

		<?php
		/**
		 * Prints additional content after the embed excerpt.
		 *
		 * @since WordPress 4.4.0
		 */
		do_action( 'embed_content' );
		?>

		<div class="wp-embed-footer">
			<?php the_embed_site_title() ?>

			<div class="wp-embed-meta">
				<?php
				/**
				 * Prints additional meta content in the embed template.
				 *
				 * @since WordPress 4.4.0
				 */
				do_action( 'embed_content_meta');
				?>
			</div>
		</div>
	</div>
<?php
